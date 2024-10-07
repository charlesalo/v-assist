<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        // Retrieve all users
        return response()->json(User::all(), 200);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required|min:8',
            'usertype' => 'required|in:admin,user',
        ]);

        // Create a new user with hashed password
        $user = User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'usertype' => $validated['usertype'],
        ]);

        return response()->json($user, 201);
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        // Find user by ID or return 404
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);

        // Validate input
        $validated = $request->validate([
            'username' => 'sometimes|required|unique:users,username,' . $user->id,
            'password' => 'sometimes|required|min:8',
            'usertype' => 'sometimes|required|in:admin,user',
        ]);

        // Update fields and hash the password if provided
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return response()->json($user, 200);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(string $id)
    {
        // Find the user by ID and delete
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }
}
