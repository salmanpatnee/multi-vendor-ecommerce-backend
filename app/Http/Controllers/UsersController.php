<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paginate = request('paginate', 10);
        $term     = request('search', '');
        $sortOrder  = request('sortOrder', 'desc');
        $orderBy    = request('orderBy', 'name');

        $users = User::search($term)
            ->orderBy($orderBy, $sortOrder)
            ->paginate($paginate);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $attributes = $request->validated();

        $user = User::create($attributes);

        $user->assignRole($request->role);

        return (new UserResource($user))
            ->additional([
                'message' => 'User created successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $attributes = $request->validated();

        $user->update($attributes);

        $user->syncRoles([]);

        $user->assignRole($attributes['role']);

        return (new UserResource($user))
            ->additional([
                'message' => 'User updated successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response([
            'message' => 'User deleted successfully.',
            'status'  => 'success'
        ], Response::HTTP_OK);
    }

    public function toggleActive(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->update();

        return response([
            'message' => 'Status updated.',
            'status'  => 'success'
        ], Response::HTTP_OK);

    }
}
