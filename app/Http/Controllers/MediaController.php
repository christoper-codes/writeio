<?php

namespace App\Http\Controllers;

use App\Actions\Media\UploadMediaImageAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $url = (new UploadMediaImageAction)->execute(file: $request->file('image'));

        return response()->json(['url' => $url]);
    }
}
