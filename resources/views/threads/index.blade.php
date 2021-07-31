@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @forelse($threads as $thread)
                    <div class="card-body">
                        <div class="card" >
                            <div class="card-header">
                                <div class="level">
                                    <h5 class="flex">
                                    <a href="{{$thread->path()}}">
                                        @if(auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                                            <strong>
                                                {{$thread->title}}
                                            </strong>
                                        @else
                                            {{$thread->title}}
                                        @endif
                                    </a>
                                    </h5>

                                    <a href="{{$thread->path()}}">
                                        {{ $thread->replies_count}} {{ Illuminate\Support\Str::pluralStudly('Reply', $thread->replies_count) }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                {{$thread->body}}
                            </div>
                        </div>
                    </div>
                @empty
                    <p>There are no relevent results at this time.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
