<?php

namespace App\Models;

use App\Events\ProjectDocumentDeleted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectDiscussion extends Model
{
    use SoftDeletes;
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
        'project_document_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function project_document()
    {
        return $this->belongsTo(ProjectDocument::class,'project_document_id');
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


    /**
     * Get all of the project_discussion_mentions for the ProjectDiscussion
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_discussion_mentions() // : HasMany
    {
        return $this->hasMany(ProjectDiscussionMentions::class, 'project_discussion_id', 'id');
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
