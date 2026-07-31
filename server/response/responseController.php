<?php

namespace response;

class responseController
{

    protected function sendResponse(int $statusCode, bool $success, string $message, $data = null): void {

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'success' => $success,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response);
        exit();
    }
    protected function success(string $message, $data = null, int $statusCode = 200): void {
        $this->sendResponse($statusCode, true, $message, $data);
    }

    protected function error(string $message, int $statusCode = 400): void {
        $this->sendResponse($statusCode, false, $message);
    }
}