<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    // 呼び出されたタイミングで $userの中身が決定する
    // $user = $favorites;
    
    public function counts($user) {
        // new User; <- Userクラスをインスタンス化、でも一瞬で消えてしまう
        
        $count_microposts = $user->microposts()->count();
        $count_followings = $user->followings()->count();
        $count_followers = $user->followers()->count();
        $count_favorites = $user->favorites()->count();
        
        return [
            'count_microposts' => $count_microposts,
            'count_followings' => $count_followings,
            'count_followers' => $count_followers,
            'count_favorites' => $count_favorites,
        ];
    }
    
}
