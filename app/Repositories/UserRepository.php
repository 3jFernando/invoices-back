<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{

  public function storeUser(object $userData): object
  {
    $user = new User();

    $user->name = $userData->name;
    $user->email = $userData->email;
    $user->password = bcrypt($userData->password);

    $user->save();

    return $user;
  }
}
