@component ('profiles.activities.activity')

    @slot('heading')

        <p>{{ $profileUser->name }} replied to <a href="{{ $activity->subject->thread->path() }}">{{ $activity->subject->thread->title }}</a></p>

    @endslot

    @slot('body')
        {!! $activity->subject->body !!}
    @endslot
@endcomponent
