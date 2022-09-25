<?php namespace App\Interfaces;

interface RequestValidationInterface {

  public function isRequestValid(array $request, array $rules): object;

} 