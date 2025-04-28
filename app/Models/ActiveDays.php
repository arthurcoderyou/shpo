<?php

namespace App\Models;

use App\Events\DayStatusUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActiveDays extends Model
{

    use SoftDeletes;

    /**
     *  
     $table->string('day'); // e.g., Monday, Tuesday, etc.
        $table->boolean('is_active')->default(true);
        $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->softDeletes();


     * 
     */

    protected $table = "active_days";
    protected $fillable = [
        'id',
        'day',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_at',
    ];




    



    // protected $dispatchesEvents = [
    //     'updated' => DayStatusUpdated::class,
    // ];

    // // or you can use a boot method
    // protected static function boot()
    // {
    //     parent::boot();

    //     // this logs values if the model has changes on each column of it 
    //     static::updated(function ($model) {
    //         if ($model->isDirty('is_active')) {
    //             event(new DayStatusUpdated(day: $model));
    //         }
    //     });
    // }



    // protected static function booted()
    // {

    //     parent::boot();

    //     static::created(function ($day) {
    //         event(new \App\Events\ProjectTimerUpdated());
    //     });
    
    //     static::updated(function ($day) {
    //         event(new \App\Events\ProjectTimerUpdated());
    //     });
    // }



}
