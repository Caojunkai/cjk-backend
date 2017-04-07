<?php

namespace App\Http\Controllers\Home;

use App\Http\Models\Category;
use App\Http\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    //执行jwt认证
    public function __construct()
    {
        $this->middleware('jwt.auth',[
            'except' => [
                'index',
                'show',
                'topics',
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
        $query = Category::orderBy('created_at','asc');
        return $this->pagination($query->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->failure(400005);
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int                     $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $request->merge(['category' => $id]);
        $this->validate($request,['category' => 'exists:categories,id']);
        $data = Category::find($id);
        return $this->success($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //TODO
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //TODO
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //TODO
    }

    public function topics(Request $request,$id){
        $query = Topic::where('category_id',$id);
        return $this->pagination($query->paginate());
    }
}
