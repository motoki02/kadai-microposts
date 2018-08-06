<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class MicropostsController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $micropostsには microposts テーブルのデータがMicropostモデルのインスタンスが複数入った配列の形式で代入される
        // $microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);

        // $data = [
        //     'user' => $user,
        //     'microposts' => $microposts,
        // ];
        
        // $data += ['microposts_count' => 1];
        // $data = $data + ['microposts_count' => 1];
        // ['user' => $user, 'microposts' => $microposts, 'microposts_count' => 1]
        
        // ViewファイルをHTMLに変換する
        // $userをusers.showの中で$customerという名前で使用したい！
        // $customer?いきなりでてきた
        
        // 普通の配列の概念
        // [部屋番号 => 中の住人]
        // $view = view('users.show', ['user' => $user, 'microposts' => $microposts, 'microposts_count' => 1]);
        // // ユーザーにHTMLを返してあげる
        // return $view;
        
        
        $data = [];
        if (\Auth::check()) {
            // データベースからログイン中の会員の情報を取得し、Userモデルのインスタンスとして$userに代入
            $user = \Auth::user();
            
            // $micropostsには microposts テーブルのデータがMicropostモデルのインスタンスが複数入った配列の形式で代入される
            $microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'user' => $user,
                'microposts' => $microposts,
            ];
            $data += $this->counts($user);
            return view('users.show', $data);
        }else {
            return view('welcome');
        }
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:191',
        ]);

        $request->user()->microposts()->create([
            'content' => $request->content,
        ]);

        return redirect()->back();
    }
    public function destroy($id)
    {
        $micropost = \App\Micropost::find($id);

        if (\Auth::id() === $micropost->user_id) {
            $micropost->delete();
        }

        return redirect()->back();
    }
}
