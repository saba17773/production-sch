<?php

namespace App\V2\Module;

use App\V2\Module\ModuleAPI;

class ModuleController
{
  public function __construct()
  {
    $this->module = new ModuleAPI();
  }

  public function getAll()
  {
    try {
      //code...
      echo json_encode($this->module->getAll());
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
