<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    /**
    Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->enum('type', ['private', 'public'])->default('public');
            $table->foreignId('forum_id')->constrained('forums')->onDelete('cascade'); // Connects to Forum
            $table->foreignId('created_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            
            $table->timestamps();
        });
    */

    protected $table = 'discussions';

    protected $fillable = [
        'title',
        'description',
        'status',
        'type',
        'forum_id',
        'created_by',
        'updated_by',   
    ];

    /**
     * Get the Forum
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function forum() 
    {
        return $this->belongsTo(Forum::class, 'forum_id', );
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
