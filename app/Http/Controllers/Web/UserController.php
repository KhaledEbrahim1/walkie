<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function users()
    {
        $users=User::get();
        return view('dashboard.pages.tables',compact('users'));
    }
    public function reels()
    {
        $reels=Reel::get();
        return view('dashboard.index',compact('reels'));

    }
}
