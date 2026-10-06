<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('notifications.index'))->assertRedirect('/login');
    }

    public function test_user_can_view_notifications(): void
    {
        $user = User::factory()->create();

        Notification::create([
            'user_id' => $user->id,
            'type' => 'TestNotification',
            'data' => ['message' => 'テスト通知'],
        ]);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertViewIs('notifications.index')
            ->assertSee('テスト通知');
    }

    public function test_notifications_are_marked_read_on_view(): void
    {
        $user = User::factory()->create();

        Notification::create([
            'user_id' => $user->id,
            'type' => 'TestNotification',
            'data' => ['message' => 'unread'],
            'read_at' => null,
        ]);

        $this->actingAs($user)->get(route('notifications.index'));

        $this->assertDatabaseMissing('notifications', [
            'user_id' => $user->id,
            'read_at' => null,
        ]);
    }

    public function test_unread_scope_returns_only_unread(): void
    {
        $user = User::factory()->create();

        Notification::create(['user_id' => $user->id, 'type' => 'A', 'data' => [], 'read_at' => null]);
        Notification::create(['user_id' => $user->id, 'type' => 'B', 'data' => [], 'read_at' => now()]);

        $unread = Notification::unread()->where('user_id', $user->id)->get();
        $this->assertCount(1, $unread);
        $this->assertEquals('A', $unread->first()->type);
    }
}
