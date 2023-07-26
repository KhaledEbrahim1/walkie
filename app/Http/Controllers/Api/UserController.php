<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function show(User $user)
    {

        $user = User::with([
            'posts' => function ($query) {
                $query->withCount(['comments', 'likes']);
            }, 'posts.comments', 'posts.likes',
            'reels' => function ($query) {
                $query->withCount(['comments', 'likes']);
            }, 'reels.comments', 'reels.likes', 'reviews', 'following', 'followers'
        ])
        ->withCount('following','followers')

            ->find($user);

        return response()->json($user);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'max:255',
            'last_name' => 'max:255',
            'email' => 'unique:users,email,' . auth()->id(),
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::findOrFail($request->user_id);
        if ($user->id != auth()->id()) {
            return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
        }
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->bio = $request->input('bio');
        $user->address = $request->input('address');
        $user->phone = $request->input('phone');
        $user->gender = $request->input('gender');
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $name = Str::random(32) . "." . $avatar->getClientOriginalExtension();
            $path = public_path('avatars');
            // $path='avatars';
            $avatarurl = asset('avatars/' . $name);
            $avatar->move($path, $name);
            $user['avatar'] = $avatarurl;
        }
        $user->update();

        $res = [
            'Success' => true,
            'data' => $user,
            'Message' => 'user updated',
        ];
        return response()->json($res, 200);
    }

    public function destroy(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        if ($user->id != auth()->id()) {
            return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
        }
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }

    public function follow(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $isFollowing = Auth::user()->following()->where('followed_user_id', $request->user_id)->exists();
        if ($isFollowing) {
            Auth::user()->following()->detach($request->user_id);
            $message = 'You have unfollowed  ' . $user->full_name;
            $follow = false;
        } else {
            Auth::user()->following()->attach($request->user_id);
            $message = 'You are now following ' . $user->full_name;
            $follow = true;
        }
        return response()->json([
            'Message' => $message,
            'Isfollowing' => $follow
        ]);
    }
}
