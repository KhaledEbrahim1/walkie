<?php

namespace App\Http\Controllers\Web;

use App\Models\admin;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Http\Request;
use Intervention\Image\Image;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class adminController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return back();
        }
        return view('dashboard.pages.sign-in');
    }

    public function storelogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'max:254'],
            'password' => ['required'],
        ]);
        $credentials = $request->only('email', 'password');
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->intended('/admin/');

        }
        return redirect()->back()->with('message', 'Login details are not valid!')->withInput($request->only('email'));
    }

    public function update(Request $request, admin $admin)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'max:254'],
            'password' => ['required'],
        ]);

        $admin->update($request->validated());

        return $admin;
    }

    public function destroy(admin $admin)
    {
        Session::flush();
        Auth::guard('admin')->logout();

        return redirect()->route('login');
    }

    public function product(){

        $Businesses = Business::get();
        return view('dashboard.pages.Add-Product',compact('Businesses'));
    }



    public function add_product(Request $request){

        $Business = Business::findOrFail($request->Businesses_id);

        $product= new Product();

        $product ->title = $request->product_title;
        $product->business_id=$Business->id;

        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/ProductImages');
//            $destinationPath = 'images';
            $imageUrl = asset('ProductImages/' . $name);
           $image->move($destinationPath , $name);
            $product->images = $imageUrl;
        }
       $product->save();

        return back()
        ->with('success', 'product uploded successfully.');

    }
    public function business()
    {
       return view('dashboard.pages.add-business');
    }
    public function add_business(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'unique:posts,slug',
            'location'=>'required',
            'city' => 'required',
            'images' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);


        if ($validator->fails()) {
            $res = [
                'Success' => false,
                'Message' => $validator->errors()->first()
            ];
            return response()->json($res, 200);
        }

        $post = new Business();
        $post->name = $request->name;
        $post->address = $request->address;
        $post->location = $request->location;
        $post->city = $request->city;
        // Handle image upload

        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('B-images');
//            $destinationPath = 'images';
            $imageUrl = asset('B-images/' . $name);
            $image->move($destinationPath , $name);
            $post->images = $imageUrl;
        }
        $post->save();

        return back()
        ->with('success', 'Business added successfully.');


    }


}
