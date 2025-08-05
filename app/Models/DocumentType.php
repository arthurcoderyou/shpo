<?php

namespace App\Models;
 
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{

    use SoftDeletes;

    
    
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
        'order',
    ];


    protected static function booted()
    {

        parent::boot();
        
        static::created(function ($documentType) {
            // event(new  \App\Events\DocumentTypeCreated($documentType));

            try {
                event(new \App\Events\DocumentTypeCreated($documentType,auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch DocumentTypeCreated event: ' . $e->getMessage(), [
                    'documentType' => $documentType->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            


        });

        static::updated(function ($documentType) {
            // event(new  \App\Events\DocumentTypeUpdated($documentType));


            try {
                event(new \App\Events\DocumentTypeUpdated($documentType,auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch DocumentTypeUpdated event: ' . $e->getMessage(), [
                    'documentType' => $documentType->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        });

        static::deleted(function ($documentType) {
            // event(new  \App\Events\DocumentTypeDeleted($documentType));

            try {
                event(new \App\Events\DocumentTypeDeleted($documentType));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch DocumentTypeDeleted event: ' . $e->getMessage(), [
                    'documentType' => $documentType->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        });
    }



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


    public function reviewers()
    {
        return $this->hasMany(Reviewer::class, 'document_type_id');
    }

}
