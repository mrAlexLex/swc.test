<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_user_can_create_task(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/tasks', [
                'title' => 'Test Task',
                'description' => 'Test task description',
                'status' => 'planned',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'status_label',
                    'completion_date',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'title' => 'Test Task',
                    'description' => 'Test task description',
                    'status' => 'planned',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test task description',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_view_all_tasks(): void
    {
        Task::factory()->count(5)->forUser($this->user)->create();
        Task::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'status_label',
                        'completion_date',
                        'user',
                        'attachments',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(8, 'data');
    }

    public function test_user_can_filter_tasks_by_user_id(): void
    {
        Task::factory()->count(5)->forUser($this->user)->create();
        Task::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/tasks?user_id=' . $this->user->id);

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_user_can_view_single_task(): void
    {
        $task = Task::factory()->forUser($this->user)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'status_label',
                    'completion_date',
                    'user',
                    'attachments',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                ],
            ]);
    }

    public function test_user_can_update_task(): void
    {
        $task = Task::factory()->forUser($this->user)->create([
            'title' => 'Original Title',
            'status' => TaskStatus::PLANNED,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Updated Title',
                'status' => 'in_progress',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'status_label',
                    'user',
                    'attachments',
                ],
            ])
            ->assertJson([
                'data' => [
                    'title' => 'Updated Title',
                    'status' => 'in_progress',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 'in_progress',
        ]);
    }

    public function test_user_can_delete_task(): void
    {
        $task = Task::factory()->forUser($this->user)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task deleted successfully.',
            ]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_tasks_can_be_filtered_by_status(): void
    {
        Task::factory()->count(3)->planned()->create();
        Task::factory()->count(2)->inProgress()->create();
        Task::factory()->count(1)->done()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/tasks?status=planned');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/tasks?status=in_progress');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_tasks_can_be_filtered_by_completion_date(): void
    {
        $specificDate = now()->addDays(7)->format('Y-m-d');

        Task::factory()->forUser($this->user)->create([
            'completion_date' => $specificDate,
        ]);
        Task::factory()->forUser($this->user)->create([
            'completion_date' => now()->addDays(14)->format('Y-m-d'),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/tasks?completion_date={$specificDate}&user_id={$this->user->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_tasks_can_be_filtered_by_completion_date_range(): void
    {
        Task::factory()->forUser($this->user)->create([
            'completion_date' => now()->addDays(5)->format('Y-m-d'),
        ]);
        Task::factory()->forUser($this->user)->create([
            'completion_date' => now()->addDays(10)->format('Y-m-d'),
        ]);
        Task::factory()->forUser($this->user)->create([
            'completion_date' => now()->addDays(20)->format('Y-m-d'),
        ]);

        $from = now()->addDays(3)->format('Y-m-d');
        $to = now()->addDays(12)->format('Y-m-d');

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/tasks?completion_date_from={$from}&completion_date_to={$to}&user_id={$this->user->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_task_validation_errors(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/tasks', [
                'title' => '',
                'description' => '',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed.',
            ])
            ->assertJsonValidationErrors(['title', 'description']);
    }

    public function test_task_creation_requires_authentication(): void
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test description',
        ]);

        $response->assertStatus(401);
    }

    public function test_tasks_are_paginated(): void
    {
        Task::factory()->count(30)->forUser($this->user)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/tasks?per_page=10&user_id=' . $this->user->id);

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    public function test_tasks_can_be_sorted(): void
    {
        Task::factory()->forUser($this->user)->create(['title' => 'A Task']);
        Task::factory()->forUser($this->user)->create(['title' => 'B Task']);
        Task::factory()->forUser($this->user)->create(['title' => 'C Task']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/tasks?sort_by=title&sort_direction=asc&user_id=' . $this->user->id);

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals('A Task', $data[0]['title']);
        $this->assertEquals('B Task', $data[1]['title']);
        $this->assertEquals('C Task', $data[2]['title']);
    }

    public function test_task_not_found_returns_404(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/tasks/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }
}
