<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDiscussionMentions extends Model
{
    //  use SoftDeletes;
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

    protected $table = "project_discussion_mentions";
    protected $fillable = [
        'project_discussion_id',
        'user_id', 
        'created_by',
        'updated_by', 
    ];
 

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    } 

    public function updater()
    {
        return $this->belongsTo(User::class,'updated_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
 
    public function project_discussion()
    {
        return $this->belongsTo(ProjectDiscussion::class,'project_discussion_id');
    }

     

}
