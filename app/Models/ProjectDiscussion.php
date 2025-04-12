<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDiscussion extends Model
{
    /*
        Schema::create('project_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            $table->foreignId('parent_id')->nullable()->constrained('project_discussions')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('body');
            $table->boolean('is_private')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    */

    protected $table = "project_discussions";
    protected $fillable = [
        'project_id',
        'parent_id',
        'title',
        'body',
        'is_private',
        'created_by',
        'updated_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class,'updated_by');
    }

    public function parent()
    {
        return $this->belongsTo(ProjectDiscussion::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ProjectDiscussion::class, 'parent_id')->with('replies');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }


    public function deleteWithReplies()
    {
        foreach ($this->replies as $reply) {
            $reply->deleteWithReplies();
        }

        $this->delete();
    }


}
