<?php

namespace App\V2\Greentire;

use App\V2\Database\Connector;
use App\Common\Sql;

class GreentireAPI
{
  private $conn = null;
  private $sql = null;

  public function __construct()
  {
    $this->conn = new Connector();
    $this->sql = new Sql();
  }
}
