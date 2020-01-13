@component('profiles.activities.activity')
    @slot('heading')
        {{ $profileUser->name }} published
        "<a href="{{ $activity->subject->path() }}">{{ $activity->subject->title }}</a>"
    @endslot

    @slot('content')
        {{ $activity->subject->exerpt }}
    @endslot
@endcomponent