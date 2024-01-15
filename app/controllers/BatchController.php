<?php

namespace App\Controllers;

use App\Components\Utils;
use App\Services\BarcodeService;
use App\Services\FinalService;
use App\Services\InventService;
use App\Services\BatchService;

class BatchController
{
	public function render()
	{
		renderView('page/change_batch');
	}

	public function renderUpdateManualBatch() {
		renderView('page/update_manual_batch');
	}

	public function getWeekNormal()
	{
		$datetime = filter_input(INPUT_POST, 'datetime');
		return json_encode(['week' => Utils::getWeekNormal($datetime)]);
	}

	public function saveNewBatch()
	{
		$_date = filter_input(INPUT_POST, '_date');
		$_batch = filter_input(INPUT_POST, '_batch');
		$_barcode = filter_input(INPUT_POST, '_barcode');

		if ($_batch === '' || $_barcode === '') {
			return json_encode(["result" => false, "message" => "กรุณาเลือกข้อมูลให้ครบถ้วน"]);
		}

		if ((new BarcodeService)->isRanged($_barcode) === false) {
			return json_encode(["result" => false, "message" => "Barcode ไม่ถูกต้อง"]);
		}

		if ((new BarcodeService)->isExistInventTable($_barcode) === false) {
			return json_encode(["result" => false, "message" => "Barcode ไม่มีอยู่ในระบบ"]);
		}

		if ((new FinalService)->isFinalReceiveDateExist($_barcode) === false) {
			return json_encode(["result" => false, "message" => "Barcode ยังไม่ได้รับเข้า Final"]);
		}

		if ((new InventService)->isReceived($_barcode) === false) {
			return json_encode(["result" => false, "message" => "Barcode Status ไม่เท่ากับ Receive"]);
		}

		$result = (new BatchService)->saveNewBatch($_batch, $_barcode);

		if ($result === true) {
			return json_encode(['result' => true, 'message' => 'Change Batch Successful!']);
		} else {
			return json_encode(['result' => false, 'message' => $result]);
		}
 
	}
}