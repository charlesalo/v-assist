<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the todos.
     */
    public function index()
    {
        // Retrieve all todos
        return response()->json(Todo::all(), 200);
    }

    /**
     * Store a newly created todo in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',  // Foreign key to users table
            'todo' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,completed',  // Status must be either 'pending' or 'completed'
        ]);

        // Create a new todo entry
        $todo = Todo::create($validated);

        return response()->json($todo, 201);
    }

    /**
     * Display the specified todo.
     */
    public function show(string $id)
    {
        // Find todo by ID or return 404
        $todo = Todo::findOrFail($id);
        return response()->json($todo, 200);
    }

    /**
     * Update the specified todo in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the todo by ID
        $todo = Todo::findOrFail($id);

        // Validate input
        $validated = $request->validate([
            'todo' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,completed',  // Status validation
        ]);

        // Update the todo with validated data
        $todo->update($validated);
        
        return response()->json($todo, 200);
    }

    /**
     * Remove the specified todo from storage.
     */
    public function destroy(string $id)
    {
        // Find the todo by ID and delete
        $todo = Todo::findOrFail($id);
        $todo->delete();

        return response()->json(null, 204);
    }
}
