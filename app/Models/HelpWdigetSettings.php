<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpWdigetSettings extends Model
{
    /**
     * 
     *    $table->integer('user_id')->nullable();
            $table->boolean('status')->default(false);
            $table->longText('widget')->nullable();

     */

    protected $table = "help_wdiget_settings";
    protected $fillable = [
        'user_id',
        'status',
        'widget',
    ];


    /**
     * Get the user that owns the HelpWdigetSettings
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() // : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }





}
