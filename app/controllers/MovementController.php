<?php  

namespace App\Controllers;

use App\Services\BarcodeService;
use App\Services\ItemService;
use App\Services\MovementService;
use App\Services\InventService;
use App\Services\FinalService;

class MovementController 
{

	public function allMovementType()
	{
		echo (new MovementService)->allMovementType();
	}

	public function allMovementIssue()
	{
		echo (new MovementService)->allMovementIssue();
	}

	public function getLatestJournalTransByJournalId($journalId)
	{
		echo (new MovementService)->getLatestJournalTransByJournalId($journalId);
	}

	public function saveJournalTable()
	{
		$employee_code = filter_input(INPUT_POST, "employee_code");
		$division = filter_input(INPUT_POST, "division_value");

		$result = (new MovementService)->saveJournalTable($employee_code, $division);

		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "journal" => $result["journal"]]);
		} else {
			echo json_encode(["status" => 404, "message" => "error"]);
		}
	}

	public function save()
	{
		$id = filter_input(INPUT_POST, "id");
		$description = filter_input(INPUT_POST, "description");

		$result = (new MovementService)->save($id, $description);

		if ($result === 200) {
			echo json_encode(["status" => 200, "message" => "ดำเนินการเสร็จสิ้น"]);
		}else {
			echo json_encode(["status" => 404, "message" => "ดำเนินการไม่สำเร็จ"]);
		}
	}

	public function saveMovementIssue()
	{
		$barcode = filter_input(INPUT_POST, "barcode");
		$requsition = filter_input(INPUT_POST, "requsition_value");
		$journalId = filter_input(INPUT_POST, "journalId");

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isReceived($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ไม่เท่ากับ Received."]));
		}

		if ((new ItemService)->isItem($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode มีอยู่ในระบบแล้ว"]));
		}

		if ($_SESSION['user_warehouse'] === 2) { // final
			if ((new FinalService)->isFinalReceiveDateExist($barcode) === false) {
				exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ไม่เท่ากับ Recived to Final."]));
			}

			if ((new InventService)->checkWarehouseTransReceiveDate($barcode) === true) {
				exit(json_encode(["status" => 404, "message" => "Warehouse Trans Receive Date ไม่เป็นค่าว่าง"]));
			}
		} else if($_SESSION['user_warehouse'] === 3) { // FG
			if ((new InventService)->checkWarehouseReceiveDate($barcode) === false) {
				exit(json_encode(["status" => 404, "message" => "ไม่มี Warehouse Receive Date"]));
			}
		}

		$result = (new MovementService)->saveMovementIssue($barcode, $requsition, $journalId);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Movement Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => "Movement Successful!"]));
		}	
	}

	public function saveReverseOK()
	{
		$barcode = filter_input(INPUT_POST, "barcodeForOK");
		$auth = filter_input(INPUT_POST, "auth");

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isReceived($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ไม่เท่ากับ Received."]));
		}

		if ((new ItemService)->isItem($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ยังไม่ได้อบ."]));
		}

		if ((new InventService)->isReverse($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode มีอยู่แล้วในระบบ"]));
		}

		if ((new FinalService)->isFinalReceiveDateExist($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ไม่เท่ากับ Recived to Final."]));
		}

		$result = (new MovementService)->saveReverseOK($barcode, $auth);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Movement Reverse Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $result ]));
		}	
	}

	public function saveReverseScrap()
	{
		$barcode = filter_input(INPUT_POST, "barcode");
		$defect = filter_input(INPUT_POST, "defect");
		$auth = filter_input(INPUT_POST, "auth");

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isReceived($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ไม่เท่ากับ Received."]));
		}

		if ((new ItemService)->isItem($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ยังไม่ได้อบ."]));
		}

		if ((new InventService)->isReverse($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode มีอยู่แล้วในระบบ"]));
		}

		if ((new FinalService)->isFinalReceiveDateExist($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ไม่เท่ากับ Recived to Final."]));
		}

		$result = (new MovementService)->saveReverseScrap($barcode, $defect, $auth);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Movement Reverse Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $result]));
		}	
	}

	public function completeIssue()
	{
		$journalId = filter_input(INPUT_POST, "journalId");

		$result = (new MovementService)->completeIssue($journalId);
		 
		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Complete Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $result]));
		}	
	}

	public function printIssueByJournalID($journalId)
	{
		$movement = new MovementService;

		if (isset($journalId)) {
			$mode = $_GET["mode"];
			$create_date = $_GET["create_date"];
			$title = '';
			$issue = '';
			if ($mode === 'MOV') {
				$title = 'Final Withdrawal Report';
				$issue = 'FM-MP-1.9.3,Issued#1';
			} else {
				$title = 'Finish Good Withdrawal Report';
				$issue = 'FM-MP-1.9.4,Issued#1';
			}

			$response = $movement->printByJournalType($journalId, $mode);

			renderView("page/movement_issue_printing", [
				"datajson" => $response,
				"journalId" => $journalId,
				"create_date" => $create_date,
				"title" => $title,
				"issue" => $issue
			]);
		} else {
			exit("error journal id not found.");
		}
	}

	public function qaReverse() 
	{
		renderView('page/movement_reverse');
	}

}