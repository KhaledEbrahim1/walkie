<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function PostComment(Request $request)
    {
        $request->validate([
            'body' => ['required'],
        ]);
        $request->user();
        $post = Post::findOrFail($request->post_id);
        $comment = $post->comments()->create([
            'body' => $request->body,
            'user_id' => Auth::user()->id
        ]);

        return response()->json(['comment' => $comment], '200');
    }

    public function ReelComment(Request $request)
    {
        $request->validate([
            'body' => ['required'],
        ]);

        $reel = Reel::findOrFail($request->reel_id);
        $comment = $reel->comments()->create([
            'body' => $request->body,
            'user_id' => Auth::user()->id

        ]);

        return response()->json(['comment' => $comment], '200');
    }

    public function show(Comment $comment)
    {
        return $comment;
    }

    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'body' => ['required'],
            'commentable_id' => ['required', 'integer'],
            'commentable_type' => ['required'],
        ]);

        $comment->update($request->validated());

        return $comment;
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json();
    }
}
