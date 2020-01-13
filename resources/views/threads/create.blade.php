@extends('layouts.app')

@section ('head')
    <link href="{{ asset('css/trix.css') }}" rel="stylesheet">
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title border-bottom pb-4 mb-4">{{ __('Create New Thread') }}</h5>

                        <form method="POST" action="{{ route('threads.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="category_id">Category:</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value=""></option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" class="form-control" id="title" name="title"
                                       value="{{ old('title') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="content">Content:</label>
                                <wysiwyg name="content"></wysiwyg>
                            </div>

                            <div class="form-group">
                                <div class="g-recaptcha" data-sitekey="6LcqR8wUAAAAALSmQXq8Lo6NEKTp-GaKO6AyZbdB"></div>
                            </div>

                            <div>
                                <button type="submit" class="btn btn-primary">Publish</button>
                            </div>

                            @if (count($errors))
                                <ul class="alert alert-danger mt-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </form>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                @include ('layouts._sidebar')
            </div>
        </div>
    </div>
@endsection