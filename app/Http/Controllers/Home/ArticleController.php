<?php

namespace App\Http\Controllers\Home;

use App\Http\Models\Article;
use App\Http\Models\Tag;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', [
            'except' => [
                'index',
                'show'
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $columns = [
            'articles.id',
            'articles.short_id',
            'articles.user_id',
            'articles.topic_id',
            'articles.type',
            'articles.link',
            'articles.title',
            'articles.summary',
            'articles.image_url',
            'articles.location',
            'articles.longitude',
            'articles.latitude',
            'articles.upvote_count',
            'articles.downvote_count',
            'articles.view_count',
            'articles.comment_count',
            'articles.published_at',
        ];
        $category = $request->query('category');
        $slug = $request->query('slug');
        $articles = Article::select($columns)->with(['user','topic','tags']);
        if (!$category)
            $category = 'popular';
        $dt = Carbon::now();
        $dt->subDay(6);
        switch ($category) {
            case 'popular' :
                $articles->where('published_at','>=',$dt)->orderby('upvote_count','desc');
                break;
            case 'latest' :
                $articles->orderby('published_at','desc');
                break;
            default :
                $articles->leftJoin('topics','articles.topic_id','=','topics.id');
                switch ($slug){
                    case 'latest' :
                        $articles->where('topics.category_id',$category)->orderby('articles.published_at','desc');
                        break;
                    default :
                        $articles->where('topics.category_id',$category)->where('articles.published_at','>=',$dt)->orderby('upvote_count','desc');
                        break;
                }
                break;
        }
        return $this->pagination($articles->paginate());
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params = $request->except(['tags']);

        $this->validate($request, [
            'topic_id'    => 'required|exists:topics,id',
            'title'       => 'required|min:2|max:255',
            'content'     => 'min:2',
            'author'      => 'min:2',
            'author_link' => 'url',
            'source'      => 'min:2',
            'source_link' => 'url',
        ]);
        $params = array_merge($params,['user_id' => Auth::id()]);
        $article = Article::create($params);
        if ($article){
            if ($request->exists('tags')){
                $tags = $request->input('tags');
                if (!is_array($tags)){
                    $tags = explode(',',$tags);
                }
                $article_tags = [];
                foreach ($tags as $key => $value){
                    if (strlen($value) == 0)
                        continue;
                    $tag = Tag::firstOrCreate(['name' => (string)$value]);
                    array_push($article_tags,$tag);
                }
                $article->tags()->saveMany($article_tags);
            }
            return $this->success($article);
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
    public function show(Request $request,$id)
    {
        $request->merge(['article_id' => $id]);
        $this->validate($request,['article_id' => 'exists:articles,id,deleted_at,NULL']);
        $article = Article::with(['topic', 'topic.user', 'tags'])->where('id',$id)->first();
        if ($article->update(['view_count'=> ++$article->view_count])){
            return $this->success($article);
        }
        return $this->formatResponseMsg(500001);
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
        $request->merge(['article' => $id]);
        $rules = [
            'article' => 'exists:articles,id,user_id,' . Auth::id(),
        ];
        $this->validate($request,$rules);
        $params = $request->only([
            'topic_id',
            'type',
            'title',
            'summary',
            'content_format',
            'content',
            'image_url',
            'location',
            'longitude',
            'latitude',
        ]);
        $article = Article::find($id);
        if ($article){
            $article->update($params);
            if ($request->exists('tags')){
                $tags = $request->input('tags');
                if (!is_array($tags)){
                    $tags = explode(',',$tags);
                }
                $article_tags = [];
                foreach ($tags as $key => $value){
                    if (strlen($value) == 0)
                        continue;
                    $tag = Tag::firstOrCreate(['name' => (string)$value]);
                    array_push($article_tags,$tag->id);
                }
                $article->tags()->sync($article_tags);
            }
            return $this->success($article);
        }
        return $this->formatResponseMsg(500007);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $request->merge(['article_id' => $id]);
        $this->validate($request,['article_id' => 'exists:articles,id']);
        $article = Article::find($id);
        $article->tags()->detach();
        $article->delete();
    }
}
