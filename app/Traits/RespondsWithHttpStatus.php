<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait RespondsWithHttpStatus
{
    protected function success($message, $data = [], $status = Response::HTTP_OK)
    {
        return response([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    protected function successWithPagination($message, $data = [], $status = Response::HTTP_OK)
    {
        return response([
            'success' => true,
            'data' => $data['data'] ?? null,
            'links' => $data['links'],
            'meta' => $data['meta'],
            'message' => $message,
        ], $status);
    }

    protected function failure($message, $status = Response::HTTP_BAD_REQUEST)
    {
        return response([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    protected function validationFailure($errors, $status = Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        return response([
            'message' => __("The given data was invalid."),
            "errors" => $errors
        ], $status);
    }


 protected function errorModel(
    string $message = 'Resource not found',
    string $error = 'Not found',
    int $status = Response::HTTP_NOT_FOUND,
    string $field = 'field'
) {
    return response([
        'message' => $message,
        'errors' => [
            $field => [$error], // wrap the error in an array
        ],
    ], $status);
}

 protected function created($message, $status = Response::HTTP_CREATED)
    {
        return response([
            'success' => true,
            'message' => $message,
        ], $status);
    }


}
 