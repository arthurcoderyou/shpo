<?php

namespace App\LivewireFA;

use Livewire\Component;
use App\Models\UserDeviceLog;

class MarkAsTrusted extends Component
{

    public $show_device_trust_section = false;

    public function mount()
    {
        $user_device_log = UserDeviceLog::getUserDeviceLog();

        // Only show the section if the device is NOT trusted
        $this->show_device_trust_section = !$user_device_log->trusted;
    }

    public function markDeviceAsTrusted($answer)
    {
        $user_device_log = UserDeviceLog::getUserDeviceLog();
        $user_device_log->trusted = $answer === 'yes';
        $user_device_log->save();

        // Hide the section after marking the device
        $this->show_device_trust_section = false;
    }

    public function render()
    {
        return view('livewire.2-f-a.mark-as-trusted');
    }
}
