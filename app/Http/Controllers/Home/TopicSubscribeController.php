<?php

namespace App\Http\Controllers\Home;

use App\Http\Models\Topic;
use App\Http\Models\TopicSubscriber;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TopicSubscribeController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', [
            'except' => [
                'subscribers'
            ]
        ]);
    }

    public function subscribers(Request $request, $id)
    {
        $request->merge(['topic' => $id]);
        $this->validate($request, ['topic' => 'exists:topics,id']);
        $subscribers = TopicSubscriber::with('user')->where('topic_id', $id)->paginate();
        return $this->pagination($subscribers);
    }

    public function subscribe(Request $request, $id)
    {
        $request->merge(['topic' => $id]);
        $this->validate($request, ['topic' => 'exists:topics,id']);
        $topicSubscriber = TopicSubscriber::firstOrCreate([
            'user_id' => Auth::id(),
            'topic_id' => $id
        ]);
        $subscriber_count = TopicSubscriber::where('topic_id',$id)->count();
        $topic = Topic::find($id)->update(['subscriber_count'=>$subscriber_count]);
        if ($topic)
            return $this->success();
        return $this->formatResponseMsg(500006);
    }

    public function unsubscribe(Request $request,$id){
        $request->merge(['topic' => $id]);
        $this->validate($request, ['topic' => 'exists:topics,id']);
        $topicSubscriber = TopicSubscriber::where('user_id',Auth::id())->where('topic_id',$id)->delete();
        return $this->success();
    }
}
