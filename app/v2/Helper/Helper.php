<?php

namespace App\V2\Helper;

class Helper
{
  public function strpad($value, $qty) {
    return str_pad($value, $qty, "0", STR_PAD_LEFT);
  }
}