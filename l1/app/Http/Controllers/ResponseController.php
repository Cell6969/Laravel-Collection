<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResponseController extends Controller
{
    public function response(Request $request): Response
    {
        return response("hello response"); // urutannya (response content, status, header)
    }

    public function header(Request $request): Response
    {
        $body = [
            "firstname" => 'don',
            "lastname" => 'var'
        ];

        return response(json_encode($body), Response::HTTP_OK)
            ->header('Content-Type', 'application/json')
            ->withHeaders([
                'Author' => 'XVargan',
                'App' => 'Laravel'
            ]);
    }

    public function responseView(Request $request): Response
    {
        return response()
            ->view('hello', [
                'name' => 'jonathan'
            ]);
    }

    public function responseJson(Request $request): JsonResponse
    {
        $body = [
            "firstname" => 'don',
            "lastname" => 'var'
        ];
        return response()
            ->json($body);
    }

    public function responseFile(Request $request): BinaryFileResponse
    {
        return response()
            ->file(storage_path('app/public/pictures/aida.jpg'));
    }

    public function downloadFile(Request $request): BinaryFileResponse
    {
        return response()
            ->download(storage_path('app/public/pictures/aida.jpg'));
    }
}
