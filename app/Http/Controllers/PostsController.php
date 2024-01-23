<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\ErrorHandler;
use App\Models\Post;
use App\Models\Role;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    //
    public function getAdmin(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'page' => 'required|integer',
            'limit' => 'required|integer',
            'title' => 'nullable|integer',
            'user_id' => 'nullable|integer',
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $posts = Post::where(function ($query) use ($request) {
                if (!empty($request->title)) {
                    $query = $query->where('title', $request->title);
                }

                if (!empty($request->user_id)) {
                    $query = $query->where('user_id', $request->user_id);
                }

                $query = $query
                    ->skip($request->page * $request->limit)
                    ->orderBy('created_at', 'DESC')
                    ->take($request->limit)
                ;
            })->get();

            return response()->json($posts);
        } catch (\Exception $e) {
            return ErrorHandler::handleApiError(__METHOD__, $e);
        }
    }
}
