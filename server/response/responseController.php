<?php

namespace response;

class responseController
{
    /**
     * Send a JSON success response and STOP execution.
     * (The original version kept running after calling this, which meant
     * error() often fired right after success() and corrupted the response.)
     */
    protected function success(string $message, $data = [], int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);
        exit;
    }

    protected function error(string $message, int $code = 400): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message,
        ]);
        exit;
    }
}
