<?php

namespace App\Http\Controllers\Home;

use App\Http\Models\ArticleVote;
use App\Http\Models\Topic;
use App\Http\Models\TopicSubscriber;
use App\Http\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $request->merge(['uid' => $id]);
        $this->validate($request, ['uid' => 'exists:users,id']);
        $user = User::find($id);
        return $this->success($user);
    }

    public function getTopics(Request $request, $id)
    {
        $topics = Topic::where('user_id', $id)->orderby('created_at', 'desc')->paginate(15);
        return $this->pagination($topics);
    }

    public function subscribes(Request $request, $id)
    {
        $subscribes = TopicSubscriber::where('user_id', $id)->with('topics')->orderby('created_at', 'desc')->paginate();
        return $this->pagination($subscribes);
    }


    public function upvotes(Request $request, $id)
    {
        $upvotes = ArticleVote::with('article')->where('user_id', $id)->orderby('created_at', 'desc')->paginate();
        return $this->pagination($upvotes);
    }
}
