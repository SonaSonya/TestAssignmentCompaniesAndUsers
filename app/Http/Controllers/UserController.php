<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\Phone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return ['data' => $users];
    }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     return 'create';
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name'    => ['required', 'string', 'min:3', 'max:40'],
                'last_name'     => ['required', 'string', 'min:3', 'max:40'],
                'phone_number'  => ['required', 'string', new Phone],
                'avatar'        => ['nullable', File::types(['jpg', 'png'])->max(2 * 1024)],
            ]);
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }

        $user = new User;
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->phone_number = $validated['phone_number'];

        if (array_key_exists('avatar', $validated)) {
            $image_path = 'avatars/' . time() . $validated['avatar']->getClientOriginalName();
            Storage::put($image_path, $validated['avatar']);
            $user->avatar = $image_path;
        }

        $request_result = $user->save();
        
        return ['request_result' => $request_result];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $validated = validator(['id' => $id], ['id' => ['required', 'integer', 'exists:users,id']])->validate();
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }

        $user = User::find($validated['id']);

        return ['data' => $user];
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(string $id)
    // {
    //     return 'edit';
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated_request = $request->validate([
                'first_name'    => ['required', 'string', 'min:3', 'max:40'],
                'last_name'     => ['required', 'string', 'min:3', 'max:40'],
                'phone_number'  => ['required', 'string', new Phone],
                'avatar'        => ['nullable', File::types(['jpg', 'png'])->max(2 * 1024)],
            ]);

            $validated_id = validator(['id' => $id], ['id' => ['required', 'integer', 'exists:users,id']])->validate();

            $validated = array_merge($validated_request, $validated_id);
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }

        $user = User::find($validated['id']);
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->phone_number = $validated['phone_number'];
        //$user->avatar = $validated['avatar'];

        $request_result = $user->save();
        
        return ['request_result' => $request_result];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $validated = validator(['id' => $id], ['id' => ['required', 'integer', 'exists:users,id']])->validate();
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }

        $user = User::find($validated['id']);
        $request_result = $user->delete();

        return ['request_result' => $request_result];
    }
}
