<div class="thread card mb-5">
    <div class="card-header bg-transparent border-0 d-flex align-items-center">
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

    <div class="card-body" v-if="editing">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" v-model="form.title">
        </div>
        <div class="form-group">
            <label for="content">Content:</label>
            <wysiwyg name="content" v-model="form.content"></wysiwyg>
        </div>
        <div>
            <button class="btn btn-primary" @click="update">Update</button>
            <button class="btn btn-secondary ml-1" @click="resetForm">Cancel</button>
        </div>
    </div>

    <div class="card-body" v-else>
        <h1 class="h3 mb-4" :class="{'thread-locked': locked}" v-text="title"></h1>

        <div class="content trix-content" v-html="content"></div>
    </div>

    <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center text-muted font-size-sm">
        <div class="meta d-flex align-items-center mr-2">
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
            <span v-show="locked">
                <i class="fas fa-lock"></i>
                Locked
            </span>
        </div>

        <div class="actions" v-if="signedIn">
            <div class="dropup">
                <button 
                    type="button" 
                    class="btn btn-outline-primary btn-sm text-nowrap" 
                    data-toggle="dropdown" 
                    aria-haspopup="true" 
                    aria-expanded="false"
                    title="Actions"
                >
                    <i class="fas fa-bars"></i>
                    <span class="d-lg-none">Actions</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <button
                        type="button"
                        class="dropdown-item"
                        v-on:click="editing = true"
                        v-if="authorize('owns', thread)"
                    >
                        <i class="far fa-edit fa-fw"></i>
                        Edit
                    </button>
                    <button
                        type="button"
                        class="dropdown-item"
                        v-on:click="destroy"
                        v-if="authorize('owns', thread)"
                    >
                        <i class="far fa-trash-alt fa-fw"></i>
                        Delete
                    </button>
                    <button
                        type="button"
                        class="dropdown-item"
                        v-if="authorize('isAdmin')"
                        v-on:click="toggleLock"
                        v-html="lockHtml"
                    ></button>
                    <button
                        type="button"
                        class="dropdown-item"
                        v-on:click="toggleSubscription"
                        v-html="subscribeHtml"
                    ></button>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer bg-transparent border-0" v-if="this.bestReply">
        <best-reply :reply="this.bestReply"></best-reply>
    </div>

</div>