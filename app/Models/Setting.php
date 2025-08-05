<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{

    use SoftDeletes;
    //setting model 
    /**
     *  $table->longText('key');
            $table->longText('name');
            $table->longText('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
             $table->softDeletes();
     * 
     */


    protected $table = "settings";
    protected $fillable = [
        "name",
        "key",
        "value",    
        "value_type",
        "description",
        "created_by",
        "updated_by",
        
    ];


    /**
     * Value Types:
     * number, text, longtext, selection
     */



    /**
     * Get the user that owns the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    

    /**
     * Get the user that owns the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updator() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }


    // notes on constants, the contant array values should always have the same number of data => key pairs or else it will lead to an error
    public const DEFAULTS = [
        'project_location_bypass' => [
            'name' => 'PROJECT LOCATION BYPASS', 
            'value' => 'No',
            'value_type' => 'selection',
            'description' => 'OPTION FOR ADMIN TO BYPASS THE REQUIREMENT TO ADD PROJECT LOCATION', 
        ],

        'document_upload_location' => [
            'name' => 'DOCUMENT UPLOAD LOCATION',
            'value' => 'local',
            'value_type' => 'selection',
            'description' => 'OPTION FOR ADMIN TO CHANGE DOCUMENT UPLOAD LOCATION', 
        ],

        'project_default_location' => [
            'name' => 'PROJECT DEFAULT LOCATION',
            'value' => 'FRH5+7GX, Barrigada, Guam', 
            'value_type' => 'text',
            'description' => 'Default location for newly created projects', 
        ],

        
        'project_default_latitude' => [
            'name' => 'PROJECT DEFAULT LATITUDE',
            'value' => '13.4796923',
            'value_type' => 'text',
            'description' => 'Default latitude for newly created projects', 
        ],

        'project_default_longitude' => [
            'name' => 'PROJECT DEFAULT LONGITUDE',
            'value' => '144.8094928',
            'value_type' => 'text',
            'description' => 'Default longitude for newly created projects', 
        ],
        // Add more default configs here
    ];


    protected static function getFirstGlobalAdminId(): ?int
    {
        return \App\Models\User::permission('system access global admin')->first()?->id;
    }



    // this is to get the default data of the settings with key defaults based on the default data 
    public static function getOrCreateWithDefaults(string $key): self
    {
        $defaults = static::DEFAULTS[$key] ?? [];

        $userId = static::getFirstGlobalAdminId();

        return static::firstOrCreate(
            ['key' => $key],
            array_merge([
                'name' => ucwords(str_replace('_', ' ', $key)),
                'value' => null,
                'value_type' => 'string',
                'description' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ], $defaults, [
                'created_by' => $defaults['created_by'] ?? $userId,
                'updated_by' => $defaults['updated_by'] ?? $userId,
            ])
        );
    }




    // this is to set the defaults on the settings with your custom default values
    public static function getOrCreateCustomDefaults(string $key, array $defaults = []):self{

        $userId = Auth::user()->id;

        return static::firstOrCreate(
            ['key' => $key],
            array_merge([
                'name' => ucwords(str_replace('_', ' ', $key)),
                'value' => null,
                'value_type' => 'string',
                'description' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ], $defaults, [
                'created_by' => $defaults['created_by'] ?? $userId,
                'updated_by' => $defaults['updated_by'] ?? $userId,
            ])
        );
    }

    

}
