<?php

namespace App\Adapters;

use App\Interfaces\RequestValidationInterface;
use Illuminate\Support\Facades\Validator;

class RequestValidAdapter implements RequestValidationInterface
{

  public function isRequestValid(array $request, array $rules): object
  {
    $validator = Validator::make($request, $rules);

    if ($validator->fails()) {
      return (object)[
        "status" => false,
        "messages" => $validator->messages()
      ];
    }

    return (object)[
      "status" => true,
      "messages" => "Datos validos"
    ];
  }
}
