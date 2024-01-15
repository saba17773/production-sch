<?php  

namespace App\Controllers;

use App\Services\CuringService;
use App\Services\InventService;
use App\Services\CureTireService;
use App\Services\BarcodeService;
use App\Components\Security;
use App\Components\Database;

class CuringController
{
	public function curing()
	{
		$curing_code = filter_input(INPUT_POST, "curing_code");
		$template_code = filter_input(INPUT_POST, "template_code");
		$barcode = filter_input(INPUT_POST, "barcode");
		$cure_type = filter_input(INPUT_POST, "cure_type");

		$curcode = explode("@", trim($curing_code));	

		if (count($curcode) != 4) {
			exit(json_encode(["status" => 404, "message" => "Curing Code Format Incorrect!"]));
		}

		// exit(json_encode(["status" => 404, "message" => $curcode]));

		$press_no = $curcode[0];
		$press_side = $curcode[1];
		$mold_no = $curcode[2];
		$curing_code_master = $curcode[3];

		if ((new CuringService)->checkPressNo($press_no) === false ||
			(new CuringService)->checkPressSide($press_side) === false ||
			(new CuringService)->checkMoldNo($mold_no) === false || 
			(new CuringService)->checkCureCode($curing_code_master) === false) {
			
			exit(json_encode(["status" => 404, "message" => "Curing code incorrect."]));
		}

		if ( $cure_type !== 'without_serial' ) { 

			if ((new CuringService)->checkTemplateExist($template_code) === false) {
				exit(json_encode(["status" => 404, "message" => "Serial No. not found."]));
			}

			if ((new CuringService)->checkIsExistInventTable($template_code) === true) {
				exit(json_encode(["status" => 404, "message" => "Template serial number not found."]));
			}

		} else {

			$template_code = null;

			if ( (new CureTireService)->isSetDontCheckSerial($curing_code_master) === false ) {
				exit(json_encode(["status" => 404, "message" => "Don\'t check serial not set"]));
			}
		}

		if ((new InventService)->isExist($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode not build."]));
		}

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode not found."]));
		}

		if ((new BarcodeService)->isRanged($barcode) === true &&
			(new InventService)->isScrap($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode number, status = scrap"]));
		}

		if ((new BarcodeService)->isRanged($barcode) === true &&
			(new InventService)->isCuringCodeNull($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode number already exist."]));
		}

		if ((new BarcodeService)->isReceived($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode number status not Received."]));
		}

		$cure = (new CuringService)->curing($curing_code, $template_code, $barcode);

		if ($cure == 200) {
			exit(json_encode(["status" => 200, "message" => "Curing Successful"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $cure]));
		}
	}

	public function genCuringCode($barcode)
	{
		renderView("page/curing_generator",[
			"barcode" => $barcode
		]);
	}
}