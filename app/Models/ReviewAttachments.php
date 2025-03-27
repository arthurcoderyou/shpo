<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewAttachments extends Model
{
    protected $table = "review_attachments";

    protected $fillable = [
        'attachment',
        'review_id',
        'created_by',
        'updated_by'
    ];
}
