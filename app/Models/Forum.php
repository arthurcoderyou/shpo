<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    /**
      Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade'); // Connects to Project
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });
     * 
     */

    protected $table = 'forums';
    protected $fillable = [
        'project_id',
        'title',
        'description',  
        'created_by',
        'updated_by',
    ];


    /**
     * Get the project that owns the Forum
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project() 
    {
        return $this->belongsTo(Project::class, 'project_id', );
    }


    /**
     * Get the creator of the forum
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()  
    {
        return $this->belongsTo(User::class, 'created_by', );
    }


    /**
     * Get the updater of the forum
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater() 
    {
        return $this->belongsTo(User::class, 'updated_by', );
    }



}
