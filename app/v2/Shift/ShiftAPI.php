<?php

namespace App\V2\Shift;

use App\Common\Sql;
use App\V2\Database\Connector;

class ShiftAPI
{
  public function __construct()
  {
    $this->conn = new Connector();
    $this->sql = new Sql();
  }

  public function getAll()
  {
    try {
      return $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT * FROM ShiftMaster"
      );
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
