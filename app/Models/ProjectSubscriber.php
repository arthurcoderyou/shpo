<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
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


    public static function boot()
    {
        parent::boot();
        

        static::created(function ($project_subscriber) {
            // event(new  \App\Events\ProjectAttachmentCreated($project_subscriber));

            try {
                event(new \App\Events\ProjectSubscriberCreated($project_subscriber, auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectAttachmentCreated event: ' . $e->getMessage(), [
                    'project_subscriber_id' => $project_subscriber->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }



        });

        static::updated(function ($project_subscriber) {
            // event(new  \App\Events\ProjectAttachmentUpdated($project_subscriber));

            try {
                event(new \App\Events\ProjectSubscriberUpdated($project_subscriber, auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectSubscriberUpdated event: ' . $e->getMessage(), [
                    'project_subscriber_id' => $project_subscriber->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        });

        static::deleted(function ($project_subscriber) {
            // event(new  \App\Events\ProjectAttachmentDeleted($project_subscriber));

            try {
                event(new \App\Events\ProjectSubscriberDeleted($project_subscriber->id, auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectAttachmentDeleted event: ' . $e->getMessage(), [
                    'project_subscriber_id' => $project_subscriber->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
            
        });


    }

    

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
