@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="profile">
                <avatar-form :user="{{ $profileUser }}"></avatar-form>
            
                @forelse ($activities as $date => $activity)
                    <h5 class="mt-5 mb-4 text-muted">{{ $date }}</h5>
            
                    @foreach ($activity as $record)
                        @if (view()->exists("profiles.activities.{$record->type}"))
                            @include ("profiles.activities.{$record->type}", ['activity' => $record])
                        @endif
                    @endforeach
                @empty
                    <p>There is no activity for this user yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection