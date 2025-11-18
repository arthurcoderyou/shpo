<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectCompany extends Model
{

    use SoftDeletes;
    /**
     * Schema::create('project_companies', function (Blueprint $table) {
            $table->id();
            $table->longText('name')->nullable();
            $table->foreignId('project_id')->constrained('projects')->onUpdate('cascade')->onDelete(action: 'cascade')->nullable(); 

            $table->foreignId('created_by')->constrained('users')->onUpdate('cascade')->onDelete(action: 'cascade');
            $table->foreignId('updated_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
     * 
     */

    protected $table = "project_companies";
    protected $fillable = [
        "name",
        "project_id", 
        "created_by",
        "updated_by", 
    ]; 



      /**
     * Get the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project() # : BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
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


}
