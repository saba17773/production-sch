<?php

namespace App\Controllers;

use App\Conpnents\Database as DB;
use Wattanar\Sqlsrv;
use App\Models\Barcode;

class CheckController
{
	public function checkBuild()
	{
		renderView('page/check_build');
	}

	public function checkFinalInspect()
	{
		renderView('page/check_final_inspect');
	}
}