<?php

namespace Tests\Feature;

use App\Rules\Recaptcha;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase, MockeryPHPUnitIntegration;

    protected function setUp(): void
    {
        parent::setUp();

        app()->singleton(Recaptcha::class, function () {
            return \Mockery::mock(Recaptcha::class, function ($m) {
                $m->shouldReceive('passes')->andReturn(true);
            });
        });
    }

    public function test_a_guest_cannot_create_new_threads()
    {
        $this->get(route('threads.create'))
            ->assertRedirect(route('login'));

        $this->post(route('threads.store'))
            ->assertRedirect(route('login'));
    }

    public function test_a_user_can_create_new_threads()
    {
        $response = $this->postThread(['title' => 'Some Title', 'content' => 'Some content.']);

        $this->get($response->headers->get('Location'))
            ->assertSee('Some Title')
            ->assertSee('Some content.');
    }

    public function test_a_thread_requires_a_valid_category()
    {
        factory('App\Category', 2)->create();

        $this->postThread(['category_id' => null])
            ->assertSessionHasErrors('category_id');

        $this->postThread(['category_id' => 9999])
            ->assertSessionHasErrors('category_id');

        $this->postThread(['category_id' => 2])
            ->assertSessionDoesntHaveErrors('category_id');
    }

    public function test_a_thread_requires_a_title()
    {
        $this->postThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    public function test_a_thread_requires_some_content()
    {
        $this->postThread(['content' => null])
            ->assertSessionHasErrors('content');
    }

    public function test_a_thread_requires_recaptcha_verification()
    {
        unset(app()[Recaptcha::class]);
        $this->postThread(['g-recaptcha-response' => 'test'])
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    public function test_a_thread_generates_a_unique_slug()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->make(['user_id' => $user->id, 'title' => 'Foo Title']);

        $thread = $this->postJson(
                route('threads.store'), $thread->toArray() + ['g-recaptcha-response' => 'token']
            )
            ->json();
        
        $timestamp = Carbon::parse($thread['created_at'])->timestamp;
        $this->assertEquals("foo-title-{$timestamp}", $thread['slug']);
    }

    protected function postThread($overrides = [])
    {
        $this->be(factory('App\User')->create());
        $thread = factory('App\Thread')->make($overrides);

        return $this->post(route('threads.store'), $thread->toArray() + ['g-recaptcha-response' => 'token']);
    }
}
