<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectReferences extends Model
{
    use SoftDeletes;

    /**
     * 
     * Schema::create('project_references', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('project_id'); // The project doing the referencing
            $table->unsignedBigInteger('referenced_project_id'); // The project being referenced 
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('referenced_project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->unique(['project_id', 'referenced_project_id']); // Optional: to avoid duplicate references

            $table->softDeletes();
            $table->timestamps();
        });
     */

    protected $table = "project_references";
    protected $fillable = [
        'project_id',
        'referenced_project_id', 
        'created_by',
        'updated_by', 



    ];


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




    /**
     * Get the project  
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project() # : BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    
    /**
     * Get the referenced_project  
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referenced_project() # : BelongsTo
    {
        return $this->belongsTo(Project::class, 'referenced_project_id', 'id');
    }



}
