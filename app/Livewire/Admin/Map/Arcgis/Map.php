<?php

namespace App\Livewire\Admin\Map\Arcgis;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Map extends Component
{
    public function render()
    {

        $token = Http::asForm()->post('https://www.arcgis.com/sharing/rest/oauth2/token/', [
            'client_id' => config('services.arcgis.client_id'),
            'client_secret' => config('services.arcgis.client_secret'),
            'grant_type' => 'client_credentials',
            'f' => 'json',
        ])->json();

       

        return view('livewire.admin.map.arcgis.map',[
            'token' => $token['access_token'],
            'apiKey' => config('services.arcgis.api_key'),
        ]);
    }
}
