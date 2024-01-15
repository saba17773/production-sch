<?php

namespace App\Controllers;

use App\Services\BarcodeService;
use App\Services\ScrapService;

class ScrapController 
{
	public function scrap()
	{
		$barcode = filter_input(INPUT_POST, "barcode");
		$defectCode = filter_input(INPUT_POST, "defectCode");
		$ScrapSide = filter_input(INPUT_POST, "position_scrap");

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}
		
		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isScrap($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้ Scrap ไปแล้ว"]));
		}

		$scrap = (new ScrapService)->scrap($barcode, $defectCode, $ScrapSide);

		if ($scrap === 200) {
			exit(json_encode(["status" => 200, "message" => "Scrap Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $scrap]));
		}
	}
}