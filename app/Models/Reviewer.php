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
       'document_type_id',
   ];


   
   protected static function booted()
   {

       parent::boot();
       
       static::created(function ($reviewer) {
           event(new  \App\Events\ReviewerCreated($reviewer));
       });

       static::updated(function ($reviewer) {
           event(new  \App\Events\ReviewerUpdated($reviewer));
       });

       static::deleted(function ($reviewer) {
           event(new  \App\Events\ReviewerDeleted($reviewer));
       });
   }



    /**
     * Get the user that owns the Reviewer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    /**
     * Get the Document Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document_type() # : BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id', );
    }


}
