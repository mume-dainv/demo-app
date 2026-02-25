<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BaseApiController extends Controller
{
    protected function sendResponse($data, $message = 'success', $statusCode = ResponseAlias::HTTP_OK)
    {
        $data = [
            'data' => $data,
            'message' => $message,
        ];
        return response()->json($data, $statusCode);
    }

    protected function sendResponseWithPaginate($data,$paginate, $message = 'success', $statusCode = ResponseAlias::HTTP_OK)
    {
        $data = [
            'data' => $data,
            'current_page' => $paginate->currentPage(),
            'last_page' => $paginate->lastPage(),
            'message' => $message,
        ];
        return response()->json($data, $statusCode);
    }

    protected function sendResponseWithCookie($data, $cookie = [],$message = 'success', $statusCode = ResponseAlias::HTTP_OK)
    {
        $data = [
            'data' => $data,
            'message' => $message,
        ];
        return response()->json($data, $statusCode)->cookie(...$cookie);
    }

    protected function sendErrorResponse($data, $message = 'error', $statusCode = ResponseAlias::HTTP_BAD_REQUEST)
    {
        $data = [
            'data' => $data,
            'message' => $message,
        ];
        return response()->json($data, $statusCode);
    }
}
