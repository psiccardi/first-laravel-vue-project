<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\ErrorHandler;
use App\Models\Post;
use App\Models\Role;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    //
    public function getOperator(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'page' => 'required|integer',
            'limit' => 'required|integer',
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $posts = Post::with(['user', 'user.role'])->where(function ($query) use ($request) {
                    if (!empty($request->title)) {
                        $query->where('title', 'LIKE', '%' . $request->title . '%');
                    }

                    if (!empty($request->user()->id)) {
                        $query->where('user_id', $request->user()->id);
                    }
                })
                ->orderBy('created_at', 'desc')
                ->skip($request->page * $request->limit)
                ->take($request->limit)
                ->get()
            ;

            return response()->json($posts);
        } catch (\Exception $e) {
            return ErrorHandler::handleApiError(__METHOD__, $e);
        }
    }

    public function getAdmin(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'page' => 'required|integer',
            'limit' => 'required|integer',
            'title' => 'nullable|string',
            'user_id' => 'nullable|integer',
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $posts = Post::with(['user', 'user.role'])->where(function ($query) use ($request) {
                    if (!empty($request->title)) {
                        $query->where('title', 'LIKE', '%' . $request->title . '%');
                    }

                    if (!empty($request->user_id)) {
                        $query->where('user_id', $request->user_id);
                    }
                })
                ->orderBy('created_at', 'desc')
                ->skip($request->page * $request->limit)
                ->take($request->limit)
                ->get()
            ;

            return response()->json($posts);
        } catch (\Exception $e) {
            return ErrorHandler::handleApiError(__METHOD__, $e);
        }
    }
}
