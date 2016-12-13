<?php

namespace App\Http\Controllers\Home;

use App\Http\Models\ArticleVote;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleVoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth',[
            'except' => 'index'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$id)
    {
        $request->merge(['article_id'=>$id]);
        $this->validate($request,['article_id' => 'exists:articles,id']);
        $articleVotes = ArticleVote::where('article_id',$id)->paginate();
        return $this->pagination($articleVotes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $request->merge(['article_id'=>$id]);
        $this->validate($request,['article_id' => 'exists:articles,id']);
        $articleVote = ArticleVote::where('article_id', $id)->where('user_id', Auth::id());
        if ($articleVote->exists()){
            $result = $articleVote->forceDelete();
        }else{
            $result = ArticleVote::create([
                'user_id' => Auth::id(),
                'article_id' => $id
            ]);
        }
        if ($result){
            //更新数据
            $upvote_count   = ArticleVote::where(['article_id' => $id])->count();
            DB::table('articles')->where('id', $id)->update([
                'upvote_count'   => $upvote_count,
            ]);
        }
        return $this->success();
    }

}
