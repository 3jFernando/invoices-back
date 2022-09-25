<?php

namespace App\Interfaces;

interface ResponseInterface
{
  public function sendResponse(string $status, string $message, $response, int $statusCode): Object;
}
