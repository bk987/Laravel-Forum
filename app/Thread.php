<?php

namespace App;

use App\Events\ThreadReceivedNewReply;
use App\Filters\ThreadFilters;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Thread extends Model
{
    use RecordsActivity;
    
    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The relationships to always eager-load.
     *
     * @var array
     */
    protected $with = ['author', 'category'];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['isSubscribedTo', 'bestReply'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['locked' => 'boolean'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get a string path for the thread.
     *
     * @return string
     */
    public function path()
    {
        return "/threads/{$this->slug}";
    }

    /**
     * Get the author associated with the specified thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Fetch the replies associated with the specified thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Get the category associated with the specified thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Add a reply to the thread.
     *
     * @param  array $reply
     * @return Model
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }

    /**
     * Apply all relevant thread filters.
     *
     * @param  Builder       $query
     * @param  ThreadFilters $filters
     * @return Builder
     */
    public function scopeFilter($query, ThreadFilters $filters)
    {
        return $filters->apply($query);
    }

    /**
     * Fetch the threads that are trending.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeTrending($query)
    {
        return $query->where('created_at', '>', Carbon::now()->subWeek())
            ->where('visits', '>', 0)->orderBy('visits', 'desc');
    }

    /**
     * Subscribe a user to the current thread.
     *
     * @param  int|null $userId
     * @return $this
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?? auth()->id()
        ]);
        return $this;
    }

    /**
     * Unsubscribe a user from the current thread.
     *
     * @param int|null $userId
     * @return void
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?? auth()->id())
            ->delete();
    }

    /**
     * A thread can have many subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Determine if the current user is subscribed to the thread.
     *
     * @return boolean
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    /**
     * Determine if the thread has been updated since the user last read it.
     *
     * @param  User $user
     * @return bool
     */
    public function hasUpdatesFor($user)
    {
        $key = $user->visitedThreadCacheKey($this);
        return $this->updated_at > cache($key);
    }

    /**
     * Access the content attribute.
     *
     * @param  string $content
     * @return string
     */
    public function getContentAttribute($content)
    {
        return \Purify::clean($content);
    }

    /**
     * Access the exerpt attribute.
     *
     * @return string
     */
    public function getExerptAttribute()
    {
        $words = \Str::words($this->content, 30);
        $stripped = trim(preg_replace("/\s+/", ' ', strip_tags(str_replace('<', ' <', $words))));
        return $stripped;
    }

    /**
     * Set the proper slug attribute.
     *
     * @param string $value
     */
    public function setSlugAttribute($value)
    {
        $slug = Str::slug($value);
        $timestamp = $this->created_at->timestamp;
        
        $this->attributes['slug'] = "{$slug}-{$timestamp}";
    }

    /**
     * Mark the given reply as the best answer.
     *
     * @param Reply $reply
     */
    public function markBestReply(Reply $reply)
    {
        $this->update(['best_reply_id' => $reply->id]);
    }

    /**
     * Access the bestReply attribute.
     *
     * @return mixed
     */
    public function getBestReplyAttribute()
    {
        if (!$reply = Reply::find($this->best_reply_id)) {
            return;
        }

        return $reply->setAppends([]);
    }
}
