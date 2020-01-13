@extends('layouts.app')

@section('head')
    <link href="{{ asset('css/trix.css') }}" rel="stylesheet">
@endsection

@section('content')
    <thread-view :thread="{{ $thread }}" inline-template>
        <div class="container">
            <div class="row">
                <div class="col-md-8" v-cloak>
                    @include ('threads._question')

                    <replies></replies>
                </div>

                <div class="col-md-4">
                    @include ('layouts._sidebar')
                </div>
            </div>
        </div>
    </thread-view>
@endsection