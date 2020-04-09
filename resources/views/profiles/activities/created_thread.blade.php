@component ('profiles.activities.activity')

    @slot('heading')

        <p>{{ $profileUser->name }} published <a href="{{ $activity->subject->path() }}">{{ $activity->subject->title }}</a> </p>

    @endslot

    @slot('body')

        {!! $activity->subject->body !!}

    @endslot
@endcomponent
