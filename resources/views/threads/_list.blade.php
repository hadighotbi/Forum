@forelse($threads as $thread)
        <div class="card mb-3" >
            <div class="card-header">
                <div class="level">
                    <div class="flex">
                        <h5>
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

                        <h6>
                            Posted By: <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a>
                        </h6>
                    </div>
                    <a href="{{$thread->path()}}">
                        {{ $thread->replies_count}} {{ Illuminate\Support\Str::pluralStudly('Reply', $thread->replies_count) }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{$thread->body}}
            </div>
            <div class="card-footer">
                {{ $thread->visits()->count()  }}  Visits
            </div>
        </div>
@empty
    <p>There are no relevent results at this time.</p>
@endforelse
