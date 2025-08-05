<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
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
       'reviewer_type', // initial, document, final
        // initial reviews the project before approved
        // document reviews the project documents
        // final reviews the project after all documents are approved
   ];


   
   protected static function booted()
   {

       parent::boot();
       
       static::created(function ($reviewer) {
        //    event(new  \App\Events\ReviewerCreated($reviewer));
            // try {
            //     event(new \App\Events\ReviewerCreated($reviewer));
            // } catch (\Throwable $e) {
            //     // Log the error without interrupting the flow
            //     Log::error('Failed to dispatch ReviewerCreated event: ' . $e->getMessage(), [
            //         'reviewer_id' => $reviewer->id,
            //         'trace' => $e->getTraceAsString(),
            //     ]);
            // }
       });

       static::updated(function ($reviewer) {
        //    event(new  \App\Events\ReviewerUpdated($reviewer));

            // try {
            //     event(new \App\Events\ReviewerUpdated($reviewer));
            // } catch (\Throwable $e) {
            //     // Log the error without interrupting the flow
            //     Log::error('Failed to dispatch ReviewerUpdated event: ' . $e->getMessage(), [
            //         'reviewer_id' => $reviewer->id,
            //         'trace' => $e->getTraceAsString(),
            //     ]);
            // }


       });

       static::deleted(function ($reviewer) {
            //    event(new  \App\Events\ReviewerDeleted($reviewer));

        //    try {
        //         event(new \App\Events\ReviewerDeleted($reviewer));
        //     } catch (\Throwable $e) {
        //         // Log the error without interrupting the flow
        //         Log::error('Failed to dispatch ReviewerDeleted event: ' . $e->getMessage(), [
        //             'reviewer_id' => $reviewer->id,
        //             'trace' => $e->getTraceAsString(),
        //         ]);
        //     }

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
