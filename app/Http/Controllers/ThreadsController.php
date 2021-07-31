<?php

namespace App\Http\Controllers;

use App\Filters\ThreadFilters;
use App\Models\Channel;
use App\Models\Thread;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index','show']);
    }

    public function index(Channel $channel , ThreadFilters $filters)
    {
        $threads = $this->getThreads($filters, $channel);
        if(request()->wantsJson()) {
            return $threads;
        }
        return view ('threads.index',compact('threads'));
    }

    public function create()
    {
        return view('threads.create');
    }

    public function store(Request $request)
    {
        //Validation
        $this->validate( $request,[
            'title' => 'required',
            'body' => 'required',
            'channel_id' => 'required|exists:channels,id'
        ]);
        //Create Thread
        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id'=> request('channel_id'),
            'title' => request('title'),
            'body' => request('body')
        ]);
        return redirect($thread->path())->with('flash','Your thread has been published.');  //Redirect to this new Thread
    }

    public function show($channel, Thread $thread)
    {
        if(auth()->check()) {
            auth()->user()->read($thread);
        }
        //Record that the user visited this page.
        //Record a timestamp.

        return view('threads.show',compact('thread'));
    }


    public function edit(Thread $thread)
    {
        //
    }


    public function update(Request $request, Thread $thread)
    {
        //
    }

    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();
        if( request()->wantsJson() ) {
            return response([], 204);
        }
        return redirect('/threads');
    }

    protected function getThreads(ThreadFilters $filters, Channel $channel)
    {
        $threads = Thread::latest()->filter($filters);
        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }
        return $threads->get();
    }

}
