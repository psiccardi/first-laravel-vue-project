<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Classes\ErrorHandler;
use App\Models\Role;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function delete(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            "id" => 'required|integer'
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $request->user();
            $post = Post::where("id", $request->id)->first();

            if (empty($post)) {
                return response()->json([
                    "error" => __("errors.post_not_found")
                ], Response::HTTP_NOT_FOUND);
            }

            if ($user->role_id !== Role::ADMINISTRATOR && $post->user_id !== $user->id) {
                return response()->json([
                    "error" => __("errors.permission_denied")
                ], Response::HTTP_UNAUTHORIZED);
            }

            $post->delete();

            return response()->json([
                'id' => $request->id
            ]);
        } catch (\Exception $e) {
            return ErrorHandler::handleApiError(__METHOD__, $e);
        }
    }

    public function update(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            "id" => 'required|integer',
            'title' => 'required|string',
            'content' => 'required|string'
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $request->user();
            $post = Post::where("id", $request->id)->first();

            if (empty($post)) {
                return response()->json([
                    "error" => __("errors.post_not_found")
                ], Response::HTTP_NOT_FOUND);
            }

            if ($user->role_id !== Role::ADMINISTRATOR && $post->user_id !== $user->id) {
                return response()->json([
                    "error" => __("errors.permission_denied")
                ], Response::HTTP_UNAUTHORIZED);
            }

            $post->title = $request->title;
            $post->content = $request->content;
            $post->save();
            $post->loadAllData();
            return response()->json($post);
        } catch (\Exception $e) {
            return ErrorHandler::handleApiError(__METHOD__, $e);
        }
    }

    public function create(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            "title" => 'required|string',
            "content" => 'required|string',
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $request->user();
            $post = new Post();
            $post->title = $request->title;
            $post->content = $request->content;
            $post->user_id = $user->id;
            $post->save();
            $post->user->role;
            return response()->json($post);
        } catch (\Exception $e) {
            return ErrorHandler::handleApiError(__METHOD__, $e);
        }
    }
}
