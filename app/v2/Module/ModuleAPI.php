<?php

namespace App\V2\Module;

use App\Common\Sql;
use App\V2\Database\Connector;

class ModuleAPI
{
  public function __construct()
  {
    $this->sql = new Sql();
    $this->conn = new Connector();
  }

  public function getAll()
  {
    try {
      //code...
      return $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT * FROM ModuleMaster"
      );
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
