<?php

namespace App\Controllers;

use App\Services\HoldService;
use App\Services\BarcodeService;
use App\Services\UserService;
use App\Components\Security;
use App\Services\InventService;

class HoldController
{
	public function hold()
	{
		$barcode = filter_input(INPUT_POST, 'barcode');
		$defect = filter_input(INPUT_POST, "defect");
		$barcode_decode = (new Security)->_decode($barcode);

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode."]));
		}

		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode."]));
		}

		if ((new BarcodeService)->isHold($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้ Hold ไปแล้ว"]));
		}

		if ((new InventService)->isIssued($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode status = issue"]));
		}

		$hold = (new HoldService)->run($barcode, $defect);

		if ($hold == 200) {
			exit(json_encode(["status" => 200, "message" => "Hold Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $hold]));
		}
	}	

	public function unhold()
	{
		$barcode = filter_input(INPUT_POST, "barcode");
		$auth = filter_input(INPUT_POST, "auth");

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isHold($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ยังไม่ได้ Hold"]));
		}

		$unhold = (new HoldService)->unhold($barcode, $auth);

		if ($unhold == 200) {
			exit(json_encode(["status" => 200, "message" => "Unhold Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $unhold]));
		}
	}

	public function authorize()
	{
		// $barcode = filter_input(INPUT_POST, "barcode");
		// $barcode_decode = (new Security)->_decode($barcode);
		$code = filter_input(INPUT_POST, "code");
		$pass = filter_input(INPUT_POST, "pass");
		$type = filter_input(INPUT_POST, "type");

		if ((new UserService)->isUserBarcodeExist($code) === false) {
			exit(json_encode(["status" => 404, "message" => "User ไม่มีในระบบ"]));
		}

		if ((new UserService)->isAuthorize($code, $pass, $type) === false) {
			exit(json_encode(["status" => 404, "message" => "User ไม่มีสิทธิ์อนุมัติ"]));
		}
		
		// if ((new UserService)->isDepartmentTrue($code, $type) === false) {
		// 	exit(json_encode(["status" => 404, "message" => "Location incorrect."]));
		// }

		exit(json_encode(["status" => 200, "message" => "Authorize successful!"]));
	}
}