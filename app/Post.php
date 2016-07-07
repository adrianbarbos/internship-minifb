<?php

namespace App;

use App\Http\Requests\Request;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public static function boot()
    {
        parent::boot();

        static::creating(function($user){
            $user->user_id = User::whereApiToken(request()->bearerToken())->first()->id;
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'picture'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'isOwner'
    ];

    /**
     * @param $value
     * @return bool
     */
    public function getIsOwnerAttribute($value) {
        return $this->user_id == User::whereApiToken(request()->bearerToken())->first()->id;
    }

    /**
     * Mutate picture url
     *
     * @param $value
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getPictureAttribute($value) {
        return url($value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('App\User')->select(['id', 'name','surname']);
    }
}
