<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    // index
    public function index(){

        // // Permission verification 
              
        //     $auth = authorizeWithAdminOverrideForController('system access global admin');

        //     if ($auth !== true) {
        //         return $auth; // This returns a redirect to dashboard
        //     }
 
        // // ./ Permission verification


        return view('admin.map.index');


    }

    // open layer list
    public function open_layer_list(){

        // // Permission verification 
              
        //     $auth = authorizeWithAdminOverrideForController('system access global admin');

        //     if ($auth !== true) {
        //         return $auth; // This returns a redirect to dashboard
        //     }
 
        // // ./ Permission verification



        return view('map.openlayer.index');
    }



    // index
    public function index_arcgis(){

        // Permission verification 
              
            $auth = authorizeWithAdminOverrideForController('system access global admin');

            if ($auth !== true) {
                return $auth; // This returns a redirect to dashboard
            }
 
        // ./ Permission verification



        $token = Http::asForm()->post('https://www.arcgis.com/sharing/rest/oauth2/token/', [
            'client_id' => config('services.arcgis.client_id'),
            'client_secret' => config('services.arcgis.client_secret'),
            'grant_type' => 'client_credentials',
            'f' => 'json',
        ])->json();

        return view('admin.map.index_arc_gis',[
            'token' => $token['access_token'],
            'apiKey' => config('services.arcgis.api_key'),
        ]); 
    }


    
}
