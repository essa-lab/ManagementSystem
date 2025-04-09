<?php

namespace App\Http\Controllers;

use App\Helper\SignedUrl;
use App\Http\Requests\UploadImageRequest;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function uploadFile(UploadImageRequest $request)
    {
        $request->validated();
        

        $folder = $request->input('folder', 'files');
        
        // $filename = explode('.',$request->get('file_name'));
        
        $parts = explode('.', $request->get('file_name'));
        $extension = end($parts);

        $filename = $folder . '/' . uniqid($parts[0].'_') .'.'. $extension;

        $url = SignedUrl::generateUrl($filename, $request->get('type'), $request->get('size'), 30);

        return response()->json([
            'url' => $url,
            'filename' => $filename,
        ]);
    }

}
