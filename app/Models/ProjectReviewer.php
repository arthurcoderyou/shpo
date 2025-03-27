<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectReviewer extends Model
{
    protected $table = "project_reviewers";
    protected $fillable  = [
        'order',
        'status', /// true or false, tells if the reviewer is the active reviewer or not
        'project_id',
        'user_id',
        'created_by',
        'updated_by',
        'review_status',
        # ['pending','approved','rejected']
    ];

    /**
     * Get the user that owns the Project
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
    public function user() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }



    public function getReviewStatus(){


        switch($this->review_status){
            case "approved": 
                return '<span class="font-bold text-lime-500">Approved</span>';
                break;
            case "rejected": 
                return '<span class="font-bold text-red-500">Rejected</span>';
                break;

            default: 
                return '<span class="font-bold text-yellow-500">Pending</span>';
                break;

        }

    }





}
