<?php

namespace App\Controllers;

use App\Services\BarcodeService;
use App\Services\XrayService;
use App\Services\InventService;
use App\Services\FinalService;
use App\Components\Security;

class FinalController
{
	public function save()
	{
		// $gate = filter_input(INPUT_POST, "gate");
		$gate = null;
		$barcode = filter_input(INPUT_POST, "barcode");

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new XrayService)->isItemID($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้ยังไม่ได้อบ"]));
		}

		if ((new InventService)->isReceived($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้ สถานะไม่เท่ากับ Receive."]));
		}

		if ((new FinalService)->isFinalReceiveDateExist($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "มี Barcode ในระบบแล้ว"]));
		}

		$result = (new FinalService)->save($barcode);

		if ($result === 200) {
			echo json_encode(["status" => 200, "message" => "ดำเนินการเสร็จสิ้น"]);
		} else {
			echo json_encode(["status" => 404, "message" => $result]);
		}
	}

	public function saveReturn()
	{
		$barcode = filter_input(INPUT_POST, "barcode");

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ($_SESSION['user_warehouse'] === 2) {

			$warehouseDesc = 'Final';

			if ((new InventService)->isIssued($barcode) === false) {
				exit(json_encode(["status" => 404, "message" => "Barcode นี้ สถานะไม่เท่ากับ Issue."]));
			}

		} else if ($_SESSION['user_warehouse'] === 3) {

			$warehouseDesc = 'Finish Good';

			// IF InventTable.Status = Confirmed ?
			if ((new InventService)->isStatusConfirmedOrIssue($barcode) === false) {
				exit(json_encode(["status" => 404, "message" => "Barcode นี้ สถานะไม่เท่ากับ Issue หรือ Confirmed"]));
			}

		} else {

			$warehouseDesc = 'Greentire';

			if ((new InventService)->isIssued($barcode) === false) {
				exit(json_encode(["status" => 404, "message" => "Barcode นี้ สถานะไม่เท่ากับ Issue."]));
			}

		}

		if ((new InventService)->isIssuedByWarehouseFinal($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้ไม่ได้เบิกออกจาก " . $warehouseDesc]));
		}

		$result = (new FinalService)->saveReturn($barcode);

		if ($result === 200) {
			echo json_encode(["status" => 200, "message" => "ดำเนินการเสร็จสิ้น"]);
		} else {
			echo json_encode(["status" => 404, "message" => $result]);
		}
	}
}