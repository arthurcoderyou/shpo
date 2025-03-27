<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    //
    // /*
    // /**
    //  * Run the migrations.
    //  */
    // public function up(): void
    // {
    //     Schema::create('document_types', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('name');
    //         $table->foreignId('created_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade'); 
    //         $table->foreignId('updated_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade'); 
    //         $table->timestamps();
    //     });
    // }

    // */



    protected $table = "document_types";
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];


    /**
     * Get the user that owns the Reviewer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user that owns the Reviewer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updator() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }


    /**
     * Get all of the Project Docuemnt
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_documents()  
    {
        return $this->hasMany(ProjectDocument::class, 'document_type_id', 'id');
    }



}
