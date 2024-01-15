<?php

namespace App\Controllers;

use App\Services\TrackingService;

class TrackingController
{
	public function searchByBarcode()
	{
		echo (new TrackingService)->searchByBarcode();
	}

	public function searchByBarcode2()
	{
		echo (new TrackingService)->searchByBarcode2();
	}

	public function searchByBarcodeLine()
	{
		echo (new TrackingService)->searchByBarcodeLine();
	}
}