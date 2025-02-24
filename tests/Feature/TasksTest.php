<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TasksTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user and task
        $this->user = User::factory()->create();
        $this->task = Task::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_guests_cannot_access_tasks(): void
    {
        // Verify all task routes are protected
        $this->get(route('tasks.index'))->assertRedirect('login');
        $this->get(route('tasks.create'))->assertRedirect('login');
        $this->post(route('tasks.store'), [])->assertRedirect('login');
        $this->get(route('tasks.edit', $this->task))->assertRedirect('login');
        $this->put(route('tasks.update', $this->task), [])->assertRedirect('login');
        $this->delete(route('tasks.destroy', $this->task))->assertRedirect('login');
    }

    public function test_user_can_view_their_tasks(): void
    {
        // Create multiple tasks with different statuses
        Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => TaskStatus::InProgress
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => TaskStatus::Completed
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.index'));

        $response->assertStatus(200)
            ->assertViewIs('tasks.index')
            ->assertViewHas('tasks');

        $tasks = $response->viewData('tasks');
        $this->assertCount(3, $tasks); // Including the setup task
    }

    public function test_user_can_create_task(): void
    {
        $taskData = [
            'title' => 'New Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
        ];

        $this->actingAs($this->user)
            ->post(route('tasks.store'), $taskData)
            ->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Test Task',
            'user_id' => $this->user->id,
            'status' => TaskStatus::Pending,
        ]);
    }

    public function test_user_can_update_their_task(): void
    {
        $updatedData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated Description',
            'status' => 'in_progress',
        ];

        $this->actingAs($this->user)
            ->put(route('tasks.update', $this->task), $updatedData)
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'title' => 'Updated Task Title',
            'status' => TaskStatus::InProgress,
        ]);
    }

    public function test_user_cannot_update_others_tasks(): void
    {
        $otherUser = User::factory()->create();
        $otherTask = Task::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($this->user)
            ->put(route('tasks.update', $otherTask), [
                'title' => 'Updated Task Title',
                'status' => 'pending',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('tasks', [
            'id' => $otherTask->id,
            'title' => 'Updated Task Title',
        ]);
    }

    public function test_user_can_delete_their_task(): void
    {
        $this->actingAs($this->user)
            ->delete(route('tasks.destroy', $this->task))
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseMissing('tasks', ['id' => $this->task->id]);
    }

    public function test_user_cannot_delete_others_tasks(): void
    {
        $otherUser = User::factory()->create();
        $otherTask = Task::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($this->user)
            ->delete(route('tasks.destroy', $otherTask))
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $otherTask->id]);
    }

    public function test_validation_rules_for_creating_task(): void
    {
        // Test required fields
        $r = $this->actingAs($this->user)
            ->post(route('tasks.store'), []);

        $r->assertSessionHasErrors(['title', 'status']);

        // Test title length
        $this->actingAs($this->user)
            ->post(route('tasks.store'), [
                'title' => str_repeat('a', 300),
                'status' => TaskStatus::Pending,
            ])
            ->assertSessionHasErrors(['title']);

        // Test invalid status
        $this->actingAs($this->user)
            ->post(route('tasks.store'), [
                'title' => 'Valid Title',
                'status' => 'invalid_status',
            ])
            ->assertSessionHasErrors(['status']);
    }

    public function test_task_status_transitions(): void
    {
        $this->actingAs($this->user)
            ->put(route('tasks.update', $this->task), [
                'title' => $this->task->title,
                'status' => 'in_progress',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'status' => TaskStatus::InProgress,
        ]);

        $this->put(route('tasks.update', $this->task), [
                'title' => $this->task->title,
                'status' => 'completed'
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'status' => TaskStatus::Completed,
        ]);
    }
}
