<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User; // 追加
use App\MIcropost; //追加

class UsersController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);

        return view('users.index', [
            'users' => $users,
        ]);
    }
    public function show($id)
    {
        $user = User::find($id);
        $microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);

        $data = [
            'user' => $user,
            'microposts' => $microposts,
        ];

        $data += $this->counts($user);

        return view('users.show', $data);
    }
    
    public function followings($id)
    {
        $user = User::find($id);
        $followings = $user->followings()->paginate(10);
        
        $data = [
            'user' => $user,
            'users' => $followings,
        ];
        
        $data += $this->counts($user);
        
        return view('users.followings', $data);
    }
    
    public function followers($id)
    {
        $user = User::find($id);
        $followers = $user->followers()->paginate(10);
        
        $data = [
            'user' => $user,
            'users' => $followers,
        ];
        
        $data += $this->counts($user);
        
        return view('users.followers', $data);
    }
    
    public function favorites($id)
    {
        // Model 1 = 1 Table
        // User <=> users
        // ModelはTableの中のデータをPHPから扱いやすい形式にしたクラスの事
        $user = User::find($id);
        $favorites = $user->favorites()->orderBy('created_at', 'desc')->paginate(10);
        
        // $favoritesの中身はMicropostモデルが複数入っている配列
        // なぜ、それが 74 行目で usersという名称になっているのか
        // 参考にすべきは follow機能ではなく、timelineの方
        $data = [
            'user' => $user,
            'microposts' => $favorites, // $microposts
        ];
        
        // タブ毎にある複数のmicropostsのtimeline/followers/following/favoritesデータの件数を集計している
        // タブ毎にある一人のUserのtimeline/followers/following/favoritesデータの件数を集計している
        $data += $this->counts($user); // micropostsテーブルの複数行分のデータ
        
        // Viewで必要な情報
        // Userの情報 => $user
        // Favoritesしたもの => $microposts (作成時間など):10個分
        // タブ毎のデータの件数
        return view('users.favorites',$data);
    }
    
}
