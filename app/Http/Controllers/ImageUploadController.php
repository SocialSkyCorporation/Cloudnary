<?php

namespace App\Http\Controllers;

use JD\Cloudder\Facades\Cloudder;
use Illuminate\Http\Request;
use App\Upload;

class ImageUploadController extends Controller
{
    public function home()
    {
        $images = Upload::all();

        return view('home', compact('images'));
    }

    public function uploadImages(Request $request)
    {
        $this->validate($request, [
            'image_name' => 'required|mimes:jpeg,bmp,jpg,png|between:1, 6000',
        ]);

        $image = $request->file('image_name');

        $name = $request->file('image_name')->getClientOriginalName();

        $image_name = $request->file('image_name')->getRealPath();

        Cloudder::upload($image_name, null);

        list($width, $height) = getimagesize($image_name);

        $image_url = Cloudder::show(Cloudder::getPublicId(), ["width" => $width, "height" => $height]);

        // save to upload directory
        $image->move(public_path("uploads"), $name);

        // save images
        $this->saveImages($request, $image_url);

        return redirect()->back()->with('status', 'Image Uploaded Successfully');
    }


    public function saveImages(Request $request, $image_url)
    {
        $image = new Upload();
        $image->image_name = $request->file('image_name')->getClientOriginalName();
        $image->image_url = $image_url;

        $image->save();
    }
}
