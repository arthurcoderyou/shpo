<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTypeReviewer extends Model
{
     
    // Schema::create('document_type_reviewers', function (Blueprint $table) {
    //     $table->id();
    //     $table->foreignId('document_type_id')->constrained()->onDelete('cascade');
    //     $table->foreignId('reviewer_id')->constrained()->onDelete('cascade');
    //     $table->integer('review_order'); // position/order of the reviewer
    //     $table->timestamps();
    // });

    protected $table = "document_type_reviewers";

    protected $fillable = [
        'document_type_id',
        'reviewer_id',
        'review_order'
    ];


    public function reviewer()
    {
        return $this->belongsTo(Reviewer::class);
    }

    public function document_type()
    {
        return $this->belongsTo(DocumentType::class);
    }

     
}
