<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class TodoController extends Controller
{
    /**
     * Display a listing of todos.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Todo::query();

        // Apply filters
        $query->search($request->input('search'))
              ->category($request->input('category'))
              ->completed($request->input('completed'))
              ->priority($request->input('priority'));

        // Apply sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        $allowedSortFields = ['created_at', 'due_date', 'title', 'priority', 'completed'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $todos = $query->get();

        return response()->json([
            'success' => true,
            'data' => $todos,
            'message' => 'Todos retrieved successfully'
        ]);
    }

    /**
     * Store a newly created todo.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'completed' => 'boolean',
            'category' => 'nullable|string|max:50',
            'due_date' => 'nullable|date',
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
        ]);

        $todo = Todo::create($validated);

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo created successfully'
        ], 201);
    }

    /**
     * Display the specified todo.
     */
    public function show(Todo $todo): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo retrieved successfully'
        ]);
    }

    /**
     * Update the specified todo.
     */
    public function update(Request $request, Todo $todo): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'completed' => 'boolean',
            'category' => 'nullable|string|max:50',
            'due_date' => 'nullable|date',
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
        ]);

        $todo->update($validated);

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo updated successfully'
        ]);
    }

    /**
     * Toggle the completion status of a todo.
     */
    public function toggleComplete(Todo $todo): JsonResponse
    {
        $todo->update(['completed' => !$todo->completed]);

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo status toggled successfully'
        ]);
    }

    /**
     * Remove the specified todo.
     */
    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Todo deleted successfully'
        ]);
    }

    /**
     * Get all unique categories.
     */
    public function categories(): JsonResponse
    {
        $categories = Todo::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ]);
    }

    /**
     * Get todo statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Todo::count(),
            'completed' => Todo::where('completed', true)->count(),
            'pending' => Todo::where('completed', false)->count(),
            'overdue' => Todo::where('completed', false)
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->toDateString())
                ->count(),
            'by_priority' => [
                'high' => Todo::where('priority', 'high')->where('completed', false)->count(),
                'medium' => Todo::where('priority', 'medium')->where('completed', false)->count(),
                'low' => Todo::where('priority', 'low')->where('completed', false)->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Stats retrieved successfully'
        ]);
    }
}
