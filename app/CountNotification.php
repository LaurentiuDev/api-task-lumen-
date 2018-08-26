<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
 *
 * @package App
 */
class CountNotification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $table = 'count_notification';

    protected $fillable = [
        'user_id',
        'count'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * Get the user to be notified.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
