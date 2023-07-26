<?php

namespace App\Http\Controllers;

use App\Models\test;
use Illuminate\Http\Request;

class testController extends Controller
{
    public function index()
    {
        return test::all();
    }

    public function store(Request $request)
    {
        $request->validate([

        ]);

        return test::create($request->validated());
    }

    public function show(test $test)
    {
        return $test;
    }

    public function update(Request $request, test $test)
    {
        $request->validate([

        ]);

        $test->update($request->validated());

        return $test;
    }

    public function destroy(test $test)
    {
        $test->delete();

        return response()->json();
    }
}
