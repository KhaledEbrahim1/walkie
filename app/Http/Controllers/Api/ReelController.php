<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReelController extends Controller
{
    public function index()
    {
        $reels = Reel::with('comments', 'likes', 'user')->withCount('likes', 'comments')->get();
        $reels = $reels->map(function ($reel) {
            $isFollowing = auth()->user()->following()->where('users.id', $reel->user->id)->exists();
            $liked = auth()->user()->likedPosts()->where('reel_id', $reel->id)->exists();
            $reel->is_following = $isFollowing;
            $reel->is_liked = $liked;
            return $reel;
        });
        return response()->json(['reels' => $reels], '200');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reel_title' => 'required|max:255',
            'reel_url' => 'required|mimes:mp4,mov,ogg|max:10240',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 200);
        }
        $reel = new Reel();
        $this->extracted($request, $reel);

        $reel->save();
        $res = [
            'Success' => true,
            'data' => $reel,
            'Message' => 'reel added successfully'
        ];
        return response()->json($res, 201);
    }

    /**
     * @param Request $request
     * @param Reel $reel
     * @return void
     */
    public function extracted(Request $request, Reel $reel): void
    {
        $reel->reel_title = $request->reel_title;
        $reel->user_id = Auth::user()->id;
        if ($request->hasFile('reel_url')) {
            $video = $request->file('reel_url');
            $name = time() . '.' . $video->getClientOriginalExtension();
//            $destinationPath='reels';

            $destinationPath = public_path('/reels');
            $reelUrl = asset('reels/' . $name);

            $video->move($destinationPath, $name);
            $reel->reel_url = $reelUrl;
        }
    }

    public function show($id)
    {
        $reel = Reel::findOrFail($id);
        if (!$reel) {
            return response()->json(['error' => 'Reel not found'], 200);
        }

        return response()->json(['reel' => $reel], '200');
    }

    public function update(Request $request)
    {
        $reel = Reel::findOrFail($request->reel_id);

        if ($reel->user_id != auth()->id()) {
            return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
        }
        $validator = Validator::make($request->all(), [
            'reel_title' => 'required|max:255',
            'reel_url' => 'required|mimes:mp4,mov,ogg|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 200);
        }

        if (!$reel) {
            return response()->json(['error' => 'Reel not found'], 404);
        }
        if ($reel->reel_url) {
            $reel_url = public_path('reels/' . $reel->reel_url);
            if (file_exists($reel_url)) unlink($reel_url);
        }
        $this->extracted($request, $reel);

        $reel->update();
        $res = [
            'Success' => true,
            'data' => $reel,
            'Message' => 'reel updated successfully'
        ];
        return response()->json($res, 201);

    }

    public function destroy(Request $request)
    {
        $reel = Reel::findOrFail($request->reel_id);

        if ($reel->user_id != auth()->id()) {
            return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
        }
        if (!$reel) {
            return response()->json(['error' => 'reel not found'], 200);
        }

        // Delete video file if it exists
        if ($reel->reel_url) {
            $reel_url = public_path('/reels/' . $reel->reel_url);
            if (file_exists($reel_url)) unlink($reel_url);
        }

        $reel->delete();

        return response()->json(['success delete' => true], '200');
    }

    public function likeReel(Request $request)
    {
        $user = $request->user();
        $reel = Reel::findOrFail($request->reel_id);
        $liked = $user->likedReels()->where('reel_id', $request->reel_id)->exists();

        if ($liked) {
            $user->likedReels()->detach($reel);
            $user->likedReels()->attach($reel, ['liked' => false]);
            $message = 'You have successfully disliked the reel ' . $reel->reel_title;
            $liked = false;
        } else {
            $user->likedReels()->detach($reel);
            $user->likedReels()->attach($reel, ['liked' => true]);
            $message = 'You have successfully liked the reel ' . $reel->reel_title;
            $liked = true;
        }

        return response()->json([
            'Message' => $message,
            'Isliked' => $liked
        ]);
    }

}
