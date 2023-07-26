<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function Search(Request $request)
    {
        $business = DB::table('businesses')->where('name', 'LIKE', "%$request->search%")->get();

        return response()->json($business);
    }
}
