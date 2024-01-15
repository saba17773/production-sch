<?php

namespace App\Controllers;

use App\Services\XrayService;
use App\Services\BarcodeService;
use App\Services\InventService;
use App\Services\FinalService;
use App\Components\Database;

class XrayController
{
	public function issueToWH()
	{
		if (count($_POST) === 0) {
			$_POST = json_decode(file_get_contents('php://input'), true);
		}

		$barcode = $_POST['barcode']; //filter_input(INPUT_POST, "barcode");
		
		if (!isset($_POST['from'])) {
			$from = null;
		} else {
			$from = $_POST['from']; //filter_input(INPUT_POST, "from"); // from fix mouse
		}

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode not found."]));
		}

		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode not found in invent table."]));
		}

		if ((new XrayService)->isItemID($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode not curing."]));
		}

		if ((new InventService)->checkWarehouseTransReceiveData($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode number already exist."]));
		}

		if ((new InventService)->isReceived($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode number status not Received."]));
		}

		// if final receive data is exist
		if ((new FinalService)->isFinalReceiveDateExist($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode not Recived to Final."]));
		}

		$result = (new XrayService)->issueToWH($barcode, $from);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Xray Issue Success!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $result]));
		}
	}
	
}