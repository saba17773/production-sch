<?php

namespace App\Services;

use App\Components\Database;
use App\Components\Utils;
use App\Components\Security;
use App\Services\BarcodeService;
use App\Services\InventService;
use Wattanar\Sqlsrv;
use App\Models\Onhand;
use App\V2\Batch\BatchAPI;
use App\V2\Database\Handler;

class CuringService
{
	public function curing($curing_code, $template_code, $barcode)
	{
		$conn = Database::connect();

		$date = date('Y-m-d H:i:s');

		$curcode = explode("@", trim($curing_code));	

		if (count($curcode) != 4) {
			return "Curing Code Format Incorrect!";
		}

		$press_no = $curcode[0];
		$press_side = $curcode[1];
		$mold_no = $curcode[2];
		$curing_code_master = $curcode[3];
		
		if ((new InventService)->checkGreenTireCodeAndCuringCode($barcode, $curing_code_master) == false) {
			return "Curing code number not match.";
		}
		// return (String)$press_no;
		// 
		if (self::isSkippingDelay() === false) {

			if (substr($press_no, 0, 1) !== "I" && substr($press_no, 0, 1) !== "J") {
				$checkCuringDelay = self::pressSideCuringDelay($press_no, $press_side);
				if ($checkCuringDelay !== true) {
					return "You can curing tire again after " . (10 - (int)$checkCuringDelay) . " minute.";
				}
			}
		}
	

		// if (self::pressSideCuringUpdate($press_no, $press_side) === false) {
		// 	return "Update press side date failed!";
		// }

		$item_id = self::getItemID($curing_code_master)[0]["ItemID"];
		$greentire_id = self::getItemID($curing_code_master)[0]["GreentireID"];

		$user_warehouse = $_SESSION["user_warehouse"];
		$user_location = $_SESSION["user_location"];
		$user_company = $_SESSION["user_company"];
		$user_login = $_SESSION["user_login"];

		// $ddate = new \DateTime();
		// $week = date("Y") . "-" . $ddate->format("W");

		// $week = (new Utils)->getWeek($date);
		$week = (new BatchAPI)->getManualBatch($date, $item_id);

		if (sqlsrv_begin_transaction($conn) === false) {
			return "Cannot connect database.";
		}

		// get user location
		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT 
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		// get barcode info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[Security::_decode($barcode)]
		);

		$update_onhand_gt = Sqlsrv::update(
			$conn,
			"UPDATE Onhand SET QTY -= 1
			WHERE CodeID = ?
			AND WarehouseID = ?
			AND LocationID = ?
			AND Batch = ?
			AND Company =?",
			[
				$greentire_id,
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				$get_barcode_info[0]["Batch"],
				$get_barcode_info[0]["Company"]
			]
		);

		if (!$update_onhand_gt) {
			sqlsrv_rollback($conn);
			return "update onhand move out error.";
		}

		$trans_id = Utils::genTransId(Security::_decode($barcode));

		$insert_form = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventTrans(
				TransID,
				Barcode,
			  	CodeID,
			  	Batch,
			  	DisposalID,
			  	DefectID,
			  	WarehouseID,
			  	LocationID,
			  	QTY,
			  	UnitID,
			  	DocumentTypeID,
			  	Company,
			  	CreateBy,
			  	CreateDate,
			  	Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				$trans_id . 1,
				Security::_decode($barcode),
				$greentire_id,
				$get_barcode_info[0]["Batch"], // batch
				$get_barcode_info[0]["DisposalID"], // disposal id
				null, // defect
				$get_barcode_info[0]["WarehouseID"], // wh
				$get_barcode_info[0]["LocationID"], // location
				-1, // qty
				1, // unit
				2, // docs type
				$user_company,
				$user_login,
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$insert_form) {
			sqlsrv_rollback($conn);
			return "transaction move out error.";
		}

		$update = Sqlsrv::update(
			$conn,
			"UPDATE InventTable 
			SET CuringDate = ?,
			CuringCode = ?,
			ItemID = ?,
  			Batch = ?,
  			PressNo = ?,
  			PressSide = ?,
  			MoldNo = ?,
  			TemplateSerialNo = ?,
  			DisposalID = ?,
		    WarehouseID = ?,
		    LocationID = ?,
		    UpdateBy = ?,
		    UpdateDate = ?,
		    CheckBuild = 1
		    WHERE Barcode = ?",
		    [
		    	$date,
		    	$curing_code_master,
		    	$item_id,
		    	$week,
		    	$press_no,
		    	$press_side,
		    	$mold_no,
		    	$template_code,
		    	$get_location[0]["DisposalID"], // Disposal Curing
		    	$get_location[0]["WarehouseID"], // WH X-ray
		    	$get_location[0]["ReceiveLocation"], // LC X-ray
		    	$_SESSION["user_login"],
		    	$date,
		    	Security::_decode($barcode)
		    ]
		);

		if (!$update) {
			sqlsrv_rollback($conn);
			return "update invent table error ";
		}

		$insert_to = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventTrans(
				TransID,
				Barcode,
				CodeID,
				Batch,
				DisposalID,
				DefectID,
				WarehouseID,
				LocationID,
				QTY,
				UnitID,
				DocumentTypeID,
				Company,
				CreateBy,
				CreateDate,
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				Utils::genTransId(Security::_decode($barcode)) . 2,
				Security::_decode($barcode),
				$item_id,
				$week,
				$get_location[0]["DisposalID"], // disposal id
				null, // defect
				$get_location[0]["WarehouseID"], // wh
				$get_location[0]["ReceiveLocation"], // location
				1, // qty
				1, // unit
				1, // docs type
				$user_company,
				$user_login,
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$insert_to) {
			sqlsrv_rollback($conn);
			return "transaction move in error.";
		}

		$update_onhand_item = Sqlsrv::update(
			$conn,
			"UPDATE Onhand 
			SET QTY += 1
			WHERE CodeID = ?
			AND WarehouseID = ?
			AND LocationID = ?
			AND Batch = ?
			AND Company = ?
			IF @@ROWCOUNT = 0
			INSERT INTO Onhand 
			VALUES (?, ?, ?, ?, ?, ?)",
			[
				$item_id,
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReceiveLocation"],
				$week,
				$_SESSION["user_company"],
				$item_id,
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReceiveLocation"],
				$week,
				1, // qty
				$_SESSION["user_company"]
			]
		);

		if (!$update_onhand_item) {
			sqlsrv_rollback($conn);
			return "update onhand move in error.";
		}

		// ======== UPDATE ONHAND ==============

		if (self::pressSideCuringUpdate($press_no, $press_side) === false) {
			sqlsrv_rollback($conn);
			return "Update press side date failed!";
		}
		
		sqlsrv_commit($conn);
		return 200;
	}

	public function isSkippingDelay()
	{
		$conn = Database::connect();
		return sqlsrv_has_rows(sqlsrv_query(
			$conn,
			"SELECT ID FROM UserMaster
			WHERE SkipingDelay = 1 
			AND ID = ?",
			[
				$_SESSION['user_login']
			]
		));
	}

	public function pressSideCuringUpdate($pressNo, $pressSide)
	{
		$conn = Database::connect();
		
		if (sqlsrv_begin_transaction($conn) === false) {
			return false;
		}

		$date = Date("Y-m-d H:i:s");

		$query = Sqlsrv::update(
			$conn,
			"UPDATE PressMaster SET $pressSide = ?
			WHERE ID  = ?",
			[$date, $pressNo]
		);

		if ($query) {
			sqlsrv_commit($conn);
			return true;
		} else {
			sqlsrv_rollback($conn);
			return false;
		}
	}

	public function pressSideCuringDelay($pressNo, $pressSide)
	{
		$conn = Database::connect();
		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT TOP 1 PM.$pressSide FROM PressMaster PM 
			WHERE DATEDIFF(MINUTE, PM.$pressSide, GETDATE()) >= 10
			AND PM.ID = ?",
			[
				$pressNo
			]
		);

		if ($query) {
			return true;
		} else {
			$remainTime = Sqlsrv::queryArray(
				$conn,
				"SELECT DATEDIFF(MINUTE, PM.$pressSide, GETDATE()) as remain_time
				FROM PressMaster PM 
				WHERE PM.ID = ?",
				[$pressNo]
			);
			return $remainTime[0]["remain_time"];
		}
	}

	public function checkTemplateExist($template_code)
	{
		$affix_template_code = (int)substr($template_code, 3, 9);
		$prefix_template_code = substr($template_code, 0, 3);
		$conn = Database::connect();
		$query = Sqlsrv::hasRows(
				$conn,
				"SELECT TOP 1 * FROM TemplateRegister
				WHERE CONVERT(INT, SUBSTRING(StartBarcode, 4, 9)) <= ?
				AND CONVERT(INT, SUBSTRING(FinishBarcode, 4, 9)) >= ?
				AND SUBSTRING(StartBarcode, 1, 3) = ?
				AND SUBSTRING(FinishBarcode, 1, 3) = ?",
				[
					$affix_template_code, 
					$affix_template_code, 
					$prefix_template_code, 
					$prefix_template_code
				]
			);

		if ($query === true ) {
			return true;
		} else {
			return false;
		}
	}

	public function checkIsExistInventTable($template_code)
	{
		$conn = Database::connect();
		$query = Sqlsrv::hasRows(
				$conn,
				"SELECT TemplateSerialNo FROM InventTable 
				WHERE TemplateSerialNo = ?",
				[$template_code]
			);
		return $query;
	}

	public function checkPressNo($press_no)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM PressMaster
				WHERE ID = ?",
				[$press_no]
			);
	}

	public function checkPressSide($press_side)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM PressArmMaster
				WHERE ID = ?",
				[$press_side]
			);
	}

	public function checkMoldNo($mold_no)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM MoldMaster
				WHERE ID = ?",
				[$mold_no]
			);
	}

	public function checkCureCode($curing_code_master)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM CureCodeMaster
				WHERE ID = ?",
				[$curing_code_master]
			);
	}

	public function isExistInventTrans()
	{
		$user_warehouse = $_SESSION["user_warehouse"];
		$user_location = $_SESSION["user_location"];
		$conn = Database::connect();
		$isExist = Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM InventTable 
				WHERE WarehouseID = ?
				AND LocationID = ?
				AND Barcode = ?",
				[
					$user_warehouse,
					$user_location
				]
			);

		return $isExist;
	}

	public function getItemID($curing_code) 
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 ItemID, GreentireID FROM CureCodeMaster
				WHERE ID = ?",
				[$curing_code]
			);
		return $query;
	}

	public function isCuring($barcode)
	{
		$conn = Database::connect();
		$barcode_decode = Security::_decode($barcode);
		return Sqlsrv::hasRows(
			$conn,
			"SELECT CuringDate FROM InventTable
			WHERE Barcode = ?
			AND CuringDate IS NOT NULL",
			[$barcode_decode]
		);
	}
}