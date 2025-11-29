<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that todos can be listed.
     */
    public function test_can_list_todos(): void
    {
        Todo::factory(5)->create();

        $response = $this->getJson('/api/todos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'completed', 'created_at', 'updated_at']
                ],
                'message'
            ]);
    }

    /**
     * Test that a todo can be created.
     */
    public function test_can_create_todo(): void
    {
        $todoData = [
            'title' => 'Test Todo',
            'description' => 'This is a test todo',
            'priority' => 'high',
            'category' => 'Work'
        ];

        $response = $this->postJson('/api/todos', $todoData);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Test Todo']);

        $this->assertDatabaseHas('todos', ['title' => 'Test Todo']);
    }

    /**
     * Test that todo creation validates required fields.
     */
    public function test_todo_creation_requires_title(): void
    {
        $response = $this->postJson('/api/todos', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test that a single todo can be retrieved.
     */
    public function test_can_show_todo(): void
    {
        $todo = Todo::factory()->create();

        $response = $this->getJson("/api/todos/{$todo->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $todo->id]);
    }

    /**
     * Test that a todo can be updated.
     */
    public function test_can_update_todo(): void
    {
        $todo = Todo::factory()->create();

        $response = $this->putJson("/api/todos/{$todo->id}", [
            'title' => 'Updated Title',
            'completed' => true
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Title', 'completed' => true]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title',
            'completed' => true
        ]);
    }

    /**
     * Test that a todo completion status can be toggled.
     */
    public function test_can_toggle_todo_completion(): void
    {
        $todo = Todo::factory()->create(['completed' => false]);

        $response = $this->patchJson("/api/todos/{$todo->id}/toggle");

        $response->assertStatus(200)
            ->assertJsonFragment(['completed' => true]);

        // Toggle again
        $response = $this->patchJson("/api/todos/{$todo->id}/toggle");
        $response->assertJsonFragment(['completed' => false]);
    }

    /**
     * Test that a todo can be deleted.
     */
    public function test_can_delete_todo(): void
    {
        $todo = Todo::factory()->create();

        $response = $this->deleteJson("/api/todos/{$todo->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    /**
     * Test that todos can be filtered by search term.
     */
    public function test_can_filter_todos_by_search(): void
    {
        Todo::factory()->create(['title' => 'Buy groceries']);
        Todo::factory()->create(['title' => 'Clean house']);

        $response = $this->getJson('/api/todos?search=groceries');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /**
     * Test that todos can be filtered by category.
     */
    public function test_can_filter_todos_by_category(): void
    {
        Todo::factory()->create(['category' => 'Work']);
        Todo::factory()->create(['category' => 'Personal']);

        $response = $this->getJson('/api/todos?category=Work');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /**
     * Test that todos can be filtered by completion status.
     */
    public function test_can_filter_todos_by_completion_status(): void
    {
        Todo::factory()->create(['completed' => true]);
        Todo::factory()->create(['completed' => false]);

        $response = $this->getJson('/api/todos?completed=true');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /**
     * Test that todo categories can be retrieved.
     */
    public function test_can_get_categories(): void
    {
        Todo::factory()->create(['category' => 'Work']);
        Todo::factory()->create(['category' => 'Personal']);

        $response = $this->getJson('/api/todos/categories');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'message']);
    }

    /**
     * Test that todo stats can be retrieved.
     */
    public function test_can_get_stats(): void
    {
        Todo::factory(3)->create(['completed' => true]);
        Todo::factory(2)->create(['completed' => false]);

        $response = $this->getJson('/api/todos/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['total', 'completed', 'pending', 'overdue', 'by_priority'],
                'message'
            ])
            ->assertJsonFragment(['total' => 5, 'completed' => 3, 'pending' => 2]);
    }
}
