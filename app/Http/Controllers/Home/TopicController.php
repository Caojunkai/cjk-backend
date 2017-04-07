<?php

namespace App\Http\Controllers\Home;

use App\Http\Models\Article;
use App\Http\Models\Topic;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TopicController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', [
            'except' => [
                'index',
                'show',
                'articles',
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $topics = Topic::orderby('created_at', 'desc')->paginate();
        return $this->pagination($topics);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge(['user_id' => Auth::id()]);
        $params = $request->all();
        $rules = [
            'type' => 'required|',
            'name' => 'required|unique:topics',
            'website' => 'url'
        ];
        $this->validate($request, $rules);
        $topic = Topic::create($params);
        if ($topic) {
            return $this->success($topic);
        }
        return $this->formatResponseMsg(400006);
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
        $request->merge(['topic' => $id]);
        $this->validate($request, ['topic' => 'exists:topics,id']);
        $topic = Topic::with('user')->find($id);
        return $this->success($topic);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->merge(['topic' => $id]);
        $this->validate($request, ['topic' => 'exists:topics,id,user_id']);
        $params = $request->only([
            'category_id',
            'type',
            'source_format',
            'source_link',
            'name',
            'image_url',
            'description',
        ]);
        $topic = Topic::find($id);
        if ($topic) {
            $topic->update($params);
            return $this->success($topic);
        }
        return $this->formatResponseMsg(500006);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        echo "现在不能删除topic";
    }


    public function articles(Request $request, $id)
    {
        $request->merge(['topic' => $id]);
        $this->validate($request, ['topic' => 'exists:topics,id']);
        $articles = Article::where('topic_id', $id)->orderby('created_at', 'desc')->paginate();
        return $this->pagination($articles);
    }



}
