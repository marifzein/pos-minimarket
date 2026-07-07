<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {

        $users = User::latest()->paginate(10);

        return view(
            'users.index',
            compact('users')
        );

    }

    public function create()
    {

        return view('users.create');

    }

    public function store(Request $request)
    {

        $request->validate([

            'name'=>'required',

            'email'=>'required|email|unique:users',

            'role'=>'required',

            'password'=>'required|min:6'

        ]);

        User::create([

            'name'=>$request->name,

            'email'=>$request->email,

            'role'=>$request->role,

            'password'=>Hash::make(
                $request->password
            )

        ]);

        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'User berhasil ditambahkan.'
            );

    }

    public function edit(User $user)
    {

        return view(
            'users.edit',
            compact('user')
        );

    }

    public function update(
        Request $request,
        User $user
    )
    {

        $request->validate([

            'name'=>'required',
            'email'=>'required|email|unique:users,email,'.$user->id,
            'role'=>'required',
            'is_active'=>'required',
            'password'  => 'nullable|min:6',

        ]);

        $data = [

            'name'      => $request->name,

            'email'     => $request->email,

            'role'      => $request->role,

            'is_active' => $request->is_active,

        ];

        // Jika password diisi, update password
        if ($request->filled('password')) {

            $data['password'] = Hash::make($request->password);

        }

        $user->update($data);

        return redirect()

        ->route('users.index')

        ->with(
            'success',
            'User berhasil diperbarui.'
        );

    }

    // reset pwd----------------------------------
    public function resetPassword(User $user)
    {
        $user->update([

            'password'=>Hash::make('87654321')

        ]);

        return back()

            ->with(

                'success',

                'Password berhasil direset menjadi 87654321.'

            );
    }

}