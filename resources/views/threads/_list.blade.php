@forelse ($threads as $thread)
    <div class="card mb-5">
        <div class="card-header bg-white border-0 d-flex align-items-center">
            <div class="flex-grow-1">
                <img src="{{ $thread->author->avatar_path }}" alt="{{ $thread->author->name }}" 
                    width="25" height="25" class="rounded mr-2">

                <span>
                    <a href="{{ route('profile', $thread->author) }}">{{ $thread->author->name }}</a>
                </span>
            </div>

            <span class="text-muted font-size-sm">
                {{ $thread->created_at->diffForHumans() }}
            </span>
        </div>

        <div class="card-body">
        <h4 class="mb-3">
                <a href="{{ $thread->path() }}">
                    @if (auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                        <strong>
                            {{ $thread->title }}
                        </strong>
                    @else
                        {{ $thread->title }}
                    @endif
                </a>
            </h4>
            <div class="content">{{ $thread->exerpt }}</div>
        </div>
        
        <div class="card-footer bg-white border-0 text-muted font-size-sm">
            <div class="meta d-flex align-items-center">
                <a href="{{ route('categories.show', $thread->category->slug) }}" class="btn btn-light btn-sm">
                    <i class="far fa-folder"></i>
                    {{ $thread->category->name }}
                </a>
                <span>
                    <i class="far fa-eye"></i>
                    {{ $thread->visits }} visits
                </span>
                <span>
                    <i class="far fa-comment-dots"></i>
                    {{ $thread->replies_count }} {{ \Str::plural('reply', $thread->replies_count) }}
                </span>
                @if ($thread->locked)
                    <span>
                        <i class="fas fa-lock"></i>
                        Locked
                    </span>
                @endif
            </div>
        </div>
    </div>
@empty
    <p>There are no relevant results at this time.</p>
@endforelse