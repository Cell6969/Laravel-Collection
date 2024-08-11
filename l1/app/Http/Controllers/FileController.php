<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function upload(Request $request): string
    {
        // get file by key
        $picture = $request->file("picture");

        // store the file
        $picture->storePubliclyAs("pictures", $picture->getClientOriginalName(), "public");

        return "OK" . $picture->getClientOriginalName();
    }
}
