<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required',
        ]);

        $user = User::where('username', $attributes['username'])->first();

        if ($user) {
            if (Hash::check($attributes['password'], $user->password)) {
                $token = $user->createToken('token')->accessToken;

                $data['token'] = $token;
                $message       = trans('messages.sessions.success');
                $status        = 'success';

                return response()->json(
                    compact(['data', 'message', 'status']),
                    201
                );
            }

            $data    = null;
            $message = trans('messages.sessions.bad_password');
            $status  = 'failed';

            return response()->json(
                compact(['data', 'message', 'status']),
                423
            );

        }

        $data    = null;
        $message = trans('messages.sessions.not_exists_username');
        $status  = 'failed';

        return response()->json(
            compact(['data', 'message', 'status']),
            423
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        $user = Auth::user();

        $userTokens = $user->tokens()->get();

        foreach ($userTokens as $token) {
            $token->revoke();
        }

        $data    = null;
        $message = trans('message.session.destroy');
        $status  = 'success';

        return response()->json(
            compact(['data', 'message', 'status']),
            202
        );
    }
}
