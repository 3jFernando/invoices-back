<?php

namespace App\Adapters;

use App\Interfaces\ResponseInterface;

class ResponseAdapter implements ResponseInterface
{

  public function sendResponse(string $status, string $message, $response, int $statusCode): object
  {
    return response()->json([
      "status" => $status,
      "message" => $message,
      "response" => $response
    ], $statusCode);
  }
}
