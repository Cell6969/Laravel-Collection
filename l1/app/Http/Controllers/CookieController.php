<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CookieController extends Controller
{
    public function createCookie(Request $request): Response
    {
        return response("hello cookie")
            ->cookie("userId", "jonathan", 1000, "/")
            ->cookie("isAdmin", "false", 1000, "/");
    }

    public function getCookie(Request $request): JsonResponse
    {
        return response()
            ->json([
                "userId" => $request->cookie("userId", "guess"),
                "isAdmin" => $request->cookie("isAdmin", "false"),
            ]);
    }

    public function clearCookie(Request $request): Response
    {
        return response("Clear Cookie")
            ->withoutCookie("userId")
            ->withoutCookie("isAdmin");
    }
}
