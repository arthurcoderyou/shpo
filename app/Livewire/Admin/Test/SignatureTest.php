<?php

namespace App\Livewire\Admin\Test;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Signature;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SignatureTest extends Component
{


    // Inputs
    public $signableType;   // e.g., App\Models\ProjectDocument::class
    public $signableId;
    public $signer_name = '';
    public $reason = '';
    public $signatureData = null; // data:image/png;base64,...

    public function mount(string $signableType, int $signableId, ?string $signerName = null) {
        $this->signableType = $signableType;
        $this->signableId   = $signableId;
        $this->signer_name  = $signerName ?? Auth::user()->name;
    }

    public function clearPad() {
        $this->dispatch('signature:clear'); // front-end will clear canvas
    }

    public function save() {
        $this->validate([
            'signatureData' => 'required|string',
            'signer_name'   => 'required|string|max:255',
        ],[
            'signatureData.required' => 'Please enter your signature'
        ]);

        // Strip prefix and decode
        $raw = preg_replace('~^data:image/\w+;base64,~', '', $this->signatureData);
        $bytes = base64_decode($raw, true);
        if ($bytes === false || strlen($bytes) < 200) {
            $this->addError('signatureData', 'Invalid signature data.');
            return;
        }

        $uuid = (string) Str::uuid();
        $path = "signatures/{$uuid}.png";
        Storage::disk('public')->put($path, $bytes);

        // Build tamper-evident HMAC over critical fields
        $payload = implode('|', [
            $this->signableType,
            $this->signableId,
            Auth::id(),
            $this->signer_name,
            $path,
            now()->toIso8601String(),
            request()->ip(),
            request()->userAgent()
        ]);

        $appKey = config('app.key');
        $key = str_starts_with($appKey, 'base64:') ? base64_decode(substr($appKey, 7)) : $appKey;
        $hash = hash_hmac('sha256', $payload, $key);

        Signature::create([
            'signable_type' => $this->signableType,
            'signable_id'   => $this->signableId,
            'user_id'       => Auth::id(),
            'signer_name'   => $this->signer_name,
            'signature_path'=> $path,
            'signed_at'     => now(),
            'ua'            => request()->userAgent(),
            'ip'            => request()->ip(),
            'hash'          => $hash,
            'meta'          => ['reason' => $this->reason],
        ]);

        $this->dispatch('signature:saved');
        $this->reset('signatureData');
    }

    public function with() {
        return [];
    }


    public function getSignaturesProperty(){

        $query = Signature::query();


        return $query->where('user_id',Auth::user()->id)
            ->orderBy('created_at','DESC')
            ->paginate(10);
    }

     // used for the table formatting tools on datetime
    public static function returnFormattedDatetime($datetime){
        $formatted = $datetime
            ? ( $datetime instanceof Carbon
                ? $datetime
                : Carbon::parse($datetime)
              )->format('M d, Y • H:i')
            : null;

        return $formatted;
    }



    public function render()
    {
        return view('livewire.admin.test.signature-test',[
            'signatures' => $this->signatures
        ]);
    }
}
