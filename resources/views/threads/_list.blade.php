@forelse ($threads as $thread)

    <div class="card mb-5">
        <div class="card-header d-flex">
            <div class="flex align-content-start">
                <h4>
                    <a href="{{$thread->path() }}">

                        @if (auth()->check() && $thread->hasUpdatesFor(auth()->user()))

                            <strong>
                                {{ $thread->title }}
                            </strong>

                        @else

                            {{ $thread->title }}

                        @endif

                    </a>
                </h4>

                <h5>Posted By: <a href="{{ route('profiles', $thread->creator) }}">{{ $thread->creator->name }}</a></h5>

            </div>

            <a class="d-flex align-self-end ml-auto" href="{{$thread->path() }}"><strong>{{$thread->replies_count}} {{ Str::plural('reply', $thread->replies_count) }}</strong></a>
        </div>

        <div class="panel-body p-4">
            {{ $thread->body }}
        </div>
    </div>

@empty

    <p>There are no relevent results at this time.</p>

@endforelse
