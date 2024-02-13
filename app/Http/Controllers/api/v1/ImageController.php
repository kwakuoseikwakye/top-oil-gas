<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    public function uploadProfilePicture(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "picture" => "required",
            ],
            [
                "picture.required" => "No picture uploaded",
             ]
        );

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Upload failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {
            $filePath = $request->file("picture")->store("public/avatars");
            if (!$filePath) {
                return response()->json([
                    "ok" => false,
                    "msg" => "File upload failed. Unknown error occured",
                ]);
            }

            return response()->json([
                "ok" => true,
                "msg" => "Upload successful",
                "data" => [
                    "link" => env("IMAGE_BASE_URL") . "/" . str_replace("public", "storage", $filePath),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error("Error uploading file", [
                "request" => $request->__toString(),
                "error" => $e->__toString(),
            ]);

            return response()->json([
                "ok" => false,
                "msg" => "An internal error occured",
            ]);
        }
    }

    public function uploadID(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "file" => "required",
            ],
            [
                "file.required" => "No file uploaded",
             ]
        );

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Upload failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {
            $filePath = $request->file("file")->store("public/ids");
            if (!$filePath) {
                return response()->json([
                    "ok" => false,
                    "msg" => "File upload failed. Unknown error occured",
                ]);
            }

            return response()->json([
                "ok" => true,
                "msg" => "Upload successful",
                "data" => [
                    "link" => env("IMAGE_BASE_URL") . "/" . str_replace("public", "storage", $filePath),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error("Error uploading file", [
                "request" => $request->__toString(),
                "error" => $e->__toString(),
            ]);

            return response()->json([
                "ok" => false,
                "msg" => "An internal error occured",
            ]);
        }
    }

    public function uploadCylinder(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "image" => "required",
            ],
            [
                "image.required" => "No file uploaded",
             ]
        );

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Upload failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {
            $filePath = $request->file("image")->store("public/cylinder");
            if (!$filePath) {
                return response()->json([
                    "ok" => false,
                    "msg" => "File upload failed. Unknown error occured",
                ]);
            }

            return response()->json([
                "ok" => true,
                "msg" => "Upload successful",
                "data" => [
                    "link" => env("IMAGE_BASE_URL") . "/" . str_replace("public", "storage", $filePath),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error("Error uploading file", [
                "request" => $request->__toString(),
                "error" => $e->__toString(),
            ]);

            return response()->json([
                "ok" => false,
                "msg" => "An internal error occured",
            ]);
        }
    }
}