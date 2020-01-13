@auth    
    <a href="{{ route('threads.create') }}" class="btn btn-block btn-primary mb-4">
        {{ __('New Thread') }}
    </a>
@else
    <a href="{{ route('login') }}" class="btn btn-block btn-secondary mb-4">
        {{ __('Login') }}
    </a>
@endauth

@if (!$categories->isEmpty())
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title border-bottom pb-3 mb-3">{{ __('Categories') }}</h5>
            <div>
                @foreach($categories as $category)
                    <a href="{{ route('categories.show', $category->slug) }}" class="d-block py-2"
                        >{{ $category->name }}</a>
                @endforeach
            </div>
        </div>
    </div>
@endif

@if (!$trending->isEmpty())
    <div class="card">
        <div class="card-body">
            <h5 class="card-title border-bottom pb-3 mb-3">{{ __('Trending Threads') }}</h5>
            <div>
                @foreach ($trending as $thread)
                    <a href="{{ url($thread->path()) }}" class="d-block py-2"
                        >{{ $thread->title }}</a>
                @endforeach
            </div>
        </div>
    </div>
@endif
