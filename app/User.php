<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        //既にフォローしているかの確認
        $exist = $this->is_following($userId);
        //自分自身ではないかの確認
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            //既にフォローしていれば何もしない
            return false;
        }else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($userId) {
    return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()-> pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'micropost_favorite', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function favorite($favId)
    {
    // 既にお気に入りしているかの確認
    $exist = $this->is_favorite($favId);
    
    if ($exist) {
        // 既にお気に入りしていれば何もしない
        return false;
    } else {
        // 未お気に入りであればお気に入りする
        $this->favorites()->attach($favId);
        return true;
    }
    }

    public function unfavorite($favId)
    {
        // 既にお気に入りしているかの確認
        $exist = $this->is_favorite($favId);
    
        if ($exist) {
            // 既にお気に入りしていればお気に入りを外す
            $this->favorites()->detach($favId);
            return true;
        } else {
            // 未お気に入りであれば何もしない
            return false;
        }
    }

    public function is_favorite($favId)
    {
        return $this->favorites()->where('micropost_id', $favId)->exists();
    }
    
    public function feed_favorites()
    {
        // [1, 2, 3] <= MicropostのID
        $favorite_micropost_ids = $this->favorites()-> pluck('microposts.id')->toArray();
        // User1がログイン中、[1, 2, 3, 1]
        $favorite_micropost_ids[] = $this->id;
        // [1, 2, 3, 1]の情報をもとにMicropostのuser_id列を検索する
        return Micropost::whereIn('user_id', $favorite_micropost_ids);
    }
}