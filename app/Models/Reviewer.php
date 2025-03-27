<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviewer extends Model
{

    protected $table ="reviewers";
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
   protected $fillable = [
       'order',
       'status',
       'user_id',
       'created_by',
       'updated_by',
   ];


    /**
     * Get the user that owns the Reviewer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


}
