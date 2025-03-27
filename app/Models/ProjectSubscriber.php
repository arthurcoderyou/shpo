<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSubscriber extends Model
{

    // /**
    //  * Run the migrations.
    //  */
    // public function up(): void
    // {
    //     Schema::create('project_subscribers', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('project_id')->constrained('projects')->onUpdate('cascade')->onDelete('cascade');
    //         $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
    //         $table->foreignId('created_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
    //         $table->foreignId('updated_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
    //         $table->timestamps();
    //     });
    // }


    protected $table = "project_subscribers";

    protected $fillable = [ 
        'project_id',
        'user_id',
        'created_by',
        'updated_by'
    ];

    /**
     * Get the project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    /**
     * Get the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


}
