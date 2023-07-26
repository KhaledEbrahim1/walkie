<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('likes','comments', 'comments.user', 'user')->withCount('likes', 'comments')->get();
        $posts = $posts->map(function ($post) {
            $isFollowing = auth()->user()->following()->where('users.id', $post->user->id)->exists();
            $liked = auth()->user()->likedPosts()->where('post_id', $post->id)->exists();
            $post->is_following = $isFollowing;
            $post->is_liked = $liked;
            return $post;
        });

        return response()->json(['posts' => $posts], '200');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'unique:posts,slug',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'video' => 'nullable|mimes:mp4,mov,ogg|max:10240',
        ]);

        if ($validator->fails()) {
            $res = [
                'Success' => false,
                'Message' => $validator->errors()->first()
            ];
            return response()->json($res, 200);
        }

        $post = new Post();
        return $this->extracted($request, $post);
    }

    /**
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function extracted(Request $request, Post $post): JsonResponse
    {
        $post->title = $request->title;
        $slug = Str::slug($post->title);
        $post->slug = $slug;
        $post->description = $request->description;
        $post->user_id = Auth::user()->id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
//            $destinationPath = 'images';
            $imageUrl = asset('images/' . $name);
            $img = Image::make($image->getRealPath());
            $img->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . '/' . $name);
            $post->image_path = $imageUrl;
        }

        // Handle video upload
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $name = time() . '.' . $video->getClientOriginalExtension();
            $destinationPath = public_path('/videos');
//            $destinationPath = 'videos';
            $videoUrl = asset('videos/' . $name);
            $video->move($destinationPath, $name);
            $post->video_path = $videoUrl;
        }

        $post->save();

        $res = [
            'Success' => true,
            'data' => $post,
            'Message' => 'Post upload successfully'
        ];
        return response()->json($res, 201);
    }

    public function update(Request $request)
    {
        $post = Post::findOrFail($request->id);

        if ($post->user_id !== auth()->id()) {
            return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'max:255',
            'slug' => 'unique:posts,slug,' . $post->id,
            'description' => '',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'video' => 'nullable|mimes:mp4,mov,ogg|max:10240',
        ]);

        if ($validator->fails()) {
            $res = [
                'Success' => false,
                'Message' => $validator->errors()->first()
            ];
            return response()->json($res, 200);
        }

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }
        if ($post->image_path) {
            if (Storage::exists('images/' . $post->image_path)) {
                Storage::delete('images/' . $post->image_path);
            }
        }

        // Delete video file if it exists
        if ($post->video_path) {
            if (Storage::exists('videos/' . $post->video_path)) {
                Storage::delete('videos/' . $post->video_path);
            }
        }


        return $this->extracted($request, $post);
    }

    public function destroy(Request $request)
    {
        $post = Post::findOrFail($request->post_id);

        if ($post->user_id !== auth()->id()) {
            return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
        }
        if (File::exists($post->image_path)) {
            File::delete($post->image_path);
        }
        $post->delete();
        $res = [
            'Success' => true,
            'Message' => 'Post has been deleted !'
        ];
        return response()->json($res, 200);
    }

    public function show(Request $request)
    {
        $post = Post::find($request->id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        return response()->json(['post' => $post], '200');
    }

    public function likePost(Request $request)
    {
        $user = $request->user();
        $post = Post::findOrFail($request->post_id);
        $liked = $user->likedPosts()->where('post_id', $request->post_id)->exists();

        if ($liked) {
            $user->likedPosts()->detach($post);
            $user->likedPosts()->attach($post, ['liked' => false]);
            $message = 'You have successfully disliked the post ' . $post->title;
            $liked = false;
        } else {
            $user->likedPosts()->detach($post);
            $user->likedPosts()->attach($post, ['liked' => true]);
            $message = 'You have successfully liked the post ' . $post->title;

            $liked = true;
        }

        return response()->json([
            'Message' => $message,
            'Isliked' => $liked
        ]);
    }


}
