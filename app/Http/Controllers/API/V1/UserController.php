<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json(UserResource::collection($users), 200);
    }

    public function show($id)
    {
        // Logic to show a specific user
    }

    public function store(Request $request)
    {
        // Logic to create a new user
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing user
    }

    public function destroy($id)
    {
        // Logic to delete a user
    }
}
