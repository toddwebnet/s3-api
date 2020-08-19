<?php

namespace App\Http\Controllers;

use App\Services\S3AppService;
use App\Services\S3StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class S3Controller extends Controller
{

    public function dirTree($typeFilter = null)
    {
        $validTypes = ['image'];
        if (!in_array($typeFilter, $validTypes)) {
            $typeFilter = null;
        }

        /** @var S3AppService $s3App */
        $s3App = app()->make(S3AppService::class);
        return response()->json($s3App->dirTree($typeFilter));

    }

    public function fileList(Request $request)
    {
        $dir = $request->post('d');
        $type = $request->post('type');
        if ($dir === null) {
            $dir = '/';
        }

        if(strpos($dir, '/fileman' )===0){
            $dir = substr($dir, 8);
        }
        if ($type === null) {
            $type = 'image';
        }
        /** @var S3AppService $s3App */
        $s3App = app()->make(S3AppService::class);
        return response()->json($s3App->fileList($dir, $type));
    }
}
