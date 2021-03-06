@extends('layouts.app')

@section('header')
    <link rel="stylesheet" href="/css/vendor/jquery.atwho.css">
@endsection

@section('content')
    <thread-view :thread="{{ $thread }}" inline-template>
        <div class="container">
            <div class="row ">
                <div class="col-md-8">
                    <div class="card" style="margin-bottom: 15px;">
                        <div class="card-header">
                            <div class="level">
                                <img src="{{ $thread->creator->avatar_path }}" class="mr-1"alt="{{ $thread->creator->name }}" width="25px" height="25px">
                                <span class="flex">
                                    <a href="/profiles/{{$thread->creator->name}}">{{ $thread->creator->name }}</a>
                                    posted : {{$thread->title}}
                                </span>
                                @can('update', $thread)
                                    <form action="{{$thread->path()}}" method="POST">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-danger">
                                            Delete Thread
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{$thread->body}}</p>
                        </div>
                    </div>

                    <replies @added="repliesCount++" @removed="repliesCount--"></replies>

                </div>

                <div class="col-md-4">
                    <div class="card" style="margin-bottom: 30px">
                        <div class="card-body">
                            <p>This Thread published {{$thread->created_at->diffForHumans()}}
                                by <a href="/profiles/{{$thread->creator->name}}">{{$thread->creator->name}}</a> , and currently has
                                <span v-text="repliesCount"></span> {{Illuminate\Support\Str::pluralStudly('comment', $thread->replies_count)}}.
                            </p>

                            <p>
                                <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}" v-if="signedIn"></subscribe-button>

                                <button class="btn btn-light"
                                        v-if="authorize('isAdmin')"
                                        @click="toggleLock"
                                        v-text="locked ? 'Unlock' : 'Lock'">
                                </button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </thread-view>
@endsection
