<?php  

namespace App\Controllers;

use App\Services\OnhandService;

class OnhandController
{
	public function all()
	{
		echo (new OnhandService)->all();
	}

	public function getGreentireHold()
	{
		echo (new OnhandService)->getGreentireHold();
	}

	public function getFinalHold()
	{
		echo (new OnhandService)->getFinalHold();
	}

	public function updateOnhand($item_code, $type)
	{
		if ((new OnhandService)->updateOnhand($item_code, $type) === false) {
			return false;
		} else {
			return true;
		}
	}
}