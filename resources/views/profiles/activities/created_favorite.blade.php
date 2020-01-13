@component('profiles.activities.activity')
    @slot('heading')
        {{ $profileUser->name }} favorited a
        <a href="{{ $activity->subject->favorited->path() }}">reply</a>.
    @endslot

    @slot('content')
        {{ $activity->subject->favorited->content }}
    @endslot
@endcomponent
