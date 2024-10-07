<?php

namespace App\Http\Controllers;

use App\Models\UserDetails;
use Illuminate\Http\Request;

class UserDetailsController extends Controller
{
    /**
     * Display a listing of the user details.
     */
    public function index()
    {
        // Retrieve all user details
        return response()->json(UserDetails::all(), 200);
    }

    /**
     * Store a newly created user detail in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',  // Foreign key to users table
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|email|unique:user_details,email',
            'password' => 'required|min:8',
        ]);

        // Create new user details entry
        $userDetails = UserDetails::create([
            'user_id' => $validated['user_id'],
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),  // Hashing password
        ]);

        return response()->json($userDetails, 201);
    }

    /**
     * Display the specified user detail.
     */
    public function show(string $id)
    {
        // Find user details by ID or return 404
        $userDetails = UserDetails::findOrFail($id);
        return response()->json($userDetails, 200);
    }

    /**
     * Update the specified user detail in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the user details by ID
        $userDetails = UserDetails::findOrFail($id);

        // Validate input
        $validated = $request->validate([
            'firstname' => 'sometimes|required|string|max:50',
            'lastname' => 'sometimes|required|string|max:50',
            'email' => 'sometimes|required|email|unique:user_details,email,' . $userDetails->id,
            'password' => 'sometimes|required|min:8',
        ]);

        // Update fields and hash the password if provided
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $userDetails->update($validated);
        return response()->json($userDetails, 200);
    }

    /**
     * Remove the specified user detail from storage.
     */
    public function destroy(string $id)
    {
        // Find the user details by ID and delete
        $userDetails = UserDetails::findOrFail($id);
        $userDetails->delete();

        return response()->json(null, 204);
    }
}
