<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;


class BusinessController extends Controller
{
    public function index(){

        $business = Business::with('reviews','reviews.user')->get();
        return response()->json($business);
    }

    public function show(Business $business){

        $business = Business::with('reviews','reviews.user','products')->find($business);
        if (!$business) {
            return response()->json(['error' => 'business not found'], 404);
        }

        return response()->json(['business' => $business], '200');
    }

}
