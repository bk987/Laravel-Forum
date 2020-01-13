@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                @include ('threads._list')

                {{ $threads->render() }}
            </div>

            <div class="col-md-4">
                @include ('layouts._sidebar')
            </div>
        </div>
    </div>
@endsection