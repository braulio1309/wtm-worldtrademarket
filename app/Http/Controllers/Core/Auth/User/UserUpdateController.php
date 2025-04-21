<?php

namespace App\Http\Controllers\Core\Auth\User;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\Profile;
use App\Services\Core\Auth\UserService;
use App\Http\Requests\Core\Auth\User\UserSettingRequest as Request;

class UserUpdateController extends Controller
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return Profile::query()
            ->where('user_id', auth()->id())
            ->first();
    }

    public function update(Request $request)
    {
        $filePath = null;
        if ($request->hasFile('document')) {
            $filePath = $request->file('document')->store('uploads', 'public');
        }

        $user = auth()->user(); // Obtiene el usuario autenticado

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->document = $filePath;

        $user->save(); // Guarda los cambios

        Profile::query()->updateOrCreate([
            'user_id' => auth()->id()
        ], array_merge(
            ['user_id' => auth()->id()],
            $request->only('gender', 'date_of_birth', 'address', 'contact')
        ));

        return updated_responses('profile');
    }
}
