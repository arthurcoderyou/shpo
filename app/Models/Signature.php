<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject; // 👈 add this

class Signature extends Model
{
   protected $fillable = [
        'signable_type','signable_id','user_id','signer_name',
        'signature_path','signed_at','ua','ip','hash','meta',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'meta'      => AsArrayObject::class, // stays as ArrayObject
    ];

    public function signable() { return $this->morphTo(); }


    /**
     * Get the user that owns the Signature
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() // : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
