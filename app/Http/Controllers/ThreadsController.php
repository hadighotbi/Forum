<?php

namespace App\Http\Controllers;

use App\Filters\ThreadFilters;
use App\Models\Channel;
use App\Models\Thread;
use App\Models\Trending;
use App\Rules\SpamFree;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index','show']);
    }

    /**
     * @param Channel $channel
     * @param ThreadFilters $filters
     * @param Trending $trending
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Channel $channel , ThreadFilters $filters, Trending $trending)
    {
        $threads = $this->getThreads($filters, $channel);
        if(request()->wantsJson()) {
            return $threads;
        }

        return view ('threads.index',[
            'threads' => $threads,
            'trending' => $trending->get()
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        //Validation
        $this->validate( $request,[
            'title' => ['required',new SpamFree],
            'body' => ['required',new SpamFree],
            'channel_id' => 'required|exists:channels,id'
        ]);

        //Create Thread
        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id'=> request('channel_id'),
            'title' => request('title'),
            'body' => request('body')
        ]);

        if(request()->wantsJson()) {
            return response($thread, 201);
        }
        //Redirect to this new Thread
        return redirect($thread->path())->with('flash','Your thread has been published.');
    }

    /**
     * @param $channel
     * @param Thread $thread
     * @param Trending $trending
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($channel, Thread $thread, Trending $trending)
    {
        if(auth()->check())
        {
            auth()->user()->read($thread);
        }
        $trending->push($thread);
        $thread->visits()->record();

        return view('threads.show',compact('thread'));
    }

    /**
     * @param $channel
     * @param Thread $thread
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();
        if( request()->wantsJson() )
        {
            return response([], 204);  //For Ajax
        }
        return redirect('/threads');
    }

    protected function getThreads(ThreadFilters $filters, Channel $channel)
    {
        $threads = Thread::latest()->filter($filters);
        if ($channel->exists)
        {
            $threads->where('channel_id', $channel->id);
        }
        return $threads->paginate(10);
    }

}
