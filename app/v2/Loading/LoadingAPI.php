<?php

namespace App\V2\Loading;

use App\V2\Database\Connector;
use App\V2\Database\Handler;
use Wattanar\Sqlsrv;
use App\V2\Pallet\PalletAPI;

class LoadingAPI
{
  public function LPNLoading($lpn)
  {
    if ((new PalletAPI)->isRealLPN($lpn) === false) {
      return 'LPN not found';
    }

    if ((new PalletAPI)->isComplete($lpn) === false) {
      return 'LPN status <> complete';
    }
  }
}