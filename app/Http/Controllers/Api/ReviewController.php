<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request){

            $validator = Validator::make($request->all(), [
                'review' => 'required|string',
                'business_id'=>'required',
            ]);

            if ($validator->fails()) {
                $res = [
                    'Success' => false,
                    'Message' => $validator->errors()->first()
                ];
                return response()->json($res, 200);
            }
        $Business = Business::findOrFail($request->business_id);

        $review= new Review();

        $review ->review = $request->review;
        $review->business_id=$Business->id;
        $review->user_id=Auth::user()->id;

        $review->save();

        $res = [
            'Success' => true,
            'data' => $review,
            'Message' => 'review add successfully'
        ];
        return response()->json($res, 201);

    }
}
