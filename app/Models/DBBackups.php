<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DBBackups extends Model
{
    /**
     * d_b_backups 
        Schema::create('d_b_backups', function (Blueprint $table) {
            $table->id();  
            $table->longText('file')->nullable();
            $table->longText('folder')->nullable();
            $table->boolean('emailed_status')->default(false);
            $table->boolean('ftp_copied_status')->default(false);
            $table->timestamps();
        });
    }
     * 
     * 
     */

    protected $table = "d_b_backups";

    protected $fillable = [
        'file',
        'folder',
        'emailed_status',
        'ftp_copied_status',

    ];




}
