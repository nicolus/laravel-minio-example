<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class WelcomeController
{
    public function index()
    {
        ['url' => $uploadUrl] = Storage::disk('s3')
            ->temporaryUploadUrl('test-file.png', now()->addMinutes(5));

        $temporaryUrl = Storage::disk('s3')
            ->temporaryUrl('test-file.png', now()->addSeconds(3));

        return view('welcome', compact('uploadUrl', 'temporaryUrl'));
    }
}
