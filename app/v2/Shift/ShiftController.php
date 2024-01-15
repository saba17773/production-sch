<?php

namespace App\V2\Shift;

use App\V2\Shift\ShiftAPI;

class ShiftController
{
  public function __construct()
  {
    $this->shift = new ShiftAPI();
  }

  public function getAll()
  {
    try {
      //code...
      return json_encode($this->shift->getAll());
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
