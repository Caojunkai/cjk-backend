<?php

namespace App\Http\Controllers\Home;

use App\Http\Models\UserRelationship;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class UserRelationshipController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', [
            'except' => [
                'getFollowing',
                'getFollowers'
            ]
        ]);
    }

    public function getFollowing(Request $request, $id)
    {
        $userRelations = UserRelationship::with('target_user')->where('target_user_id', $id)->orderBy('updated_at', 'desc')->paginate();
        return $this->pagination($userRelations);
    }

    public function following(Request $request,$id)
    {
        if (Auth::id() == (int)$id)
            return $this->formatResponseMsg(400005);
        $request->merge(['uid' => $id]);
        $action = $request->input('action');
        $rules = [
            'uid'   => 'exists:users,id',
            'action' =>'required|in:follow,unfollow'
        ];
        $this->validate($request,$rules);

        $relationship = UserRelationship::withTrashed()->FirstOrCreate([
            'user_id' => Auth::id(),
            'target_user_id' => $id
        ]);

        switch ($action){
            case 'follow' :
                $relationship->restore();
                break;
            case 'unfollow' :
                $relationship->delete();
                break;
            default :
                return $this->formatResponseMsg();
        }
        //更新数据
        $following_count = UserRelationship::where('user_id',Auth::id())->count();
        $follower_count = UserRelationship::where('target_user_id',$id)->count();
        //事务处理
        try {
            DB::transaction(function () use ($id, $follower_count, $following_count) {
                DB::table('users')->where('id', Auth::id())->update(['following_count' => $following_count]);
                DB::table('users')->where('id', $id)->update(['followers_count' => $follower_count]);
            });
        } catch (Exception $e) {
            return $this->formatResponseMsg();
        }
        return $this->success();
    }

    public function getFollowers(Request $request,$id){
        $followers = UserRelationship::with('user')->where('target_user_id',$id)->orderBy('updated_at','desc')->paginate();
        return $this->pagination($followers);

    }
}
