<?php

namespace App\Services;

use App\Components\Security;
use App\Components\Database;
use App\Components\Utils;
use Wattanar\Sqlsrv;
use App\Services\FinalService;

class ScrapService
{
	public function scrap($barcode, $defectCode, $ScrapSide)
	{
		$barcode_decode = Security::_decode($barcode);
		$date = date("Y-m-d H:i:s");

		$conn = Database::connect();
		// Get Barcode Info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);
		// Get User Location
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
		// Check WH Match
		if ($_SESSION["user_warehouse"] != $get_barcode_info[0]["WarehouseID"]) {
			return "User has not permissions!";
			// return $_SESSION["user_warehouse"] . " - " . $get_barcode_info[0]["WarehouseID"];
		}

		if ($get_barcode_info[0]["Status"] !== 5 ) { // 5 = hold
			return "รายการนี้ยังไม่ได้ Hold";
		}

		$_warehouse = $get_barcode_info[0]["WarehouseID"]; // greentire
		
		$_disposal = 2; // scrap

		// setup hold wh,lc,disp
		if($get_barcode_info[0]["CuringDate"] === null) {
			$_item = $get_barcode_info[0]["GT_Code"];
			$_location = 9; // hold gt
			// $_batch = null;
			$_batch = $get_barcode_info[0]["Batch"];
		} else {
			$_item = $get_barcode_info[0]["ItemID"];
			$_location = 11; // hold x ray
			$_batch = $get_barcode_info[0]["Batch"];

			// if final receive data is exist
			if ((new FinalService)->isFinalReceiveDateExist($barcode) === false) {
				return "Barcode not Recived to Final.";
			}
		}

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		// Generate trans id
		$trans_id = Utils::genTransId($barcode_decode);

		// insert trans move out
		$trans_move_out = Sqlsrv::insert(
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
				Shift,
				ScrapSide
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?
			)",
			[
				$trans_id,
				$barcode_decode,
				$_item,
				$_batch,
				$_disposal, //scrap
				$defectCode,
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				-1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				2, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"],
				$ScrapSide
			]
		);

		if (!$trans_move_out) {
			sqlsrv_rollback($conn);
			return "trauns move out error.";
		}

		// Update Invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable 
			SET DisposalID = ?, -- scrap
			WarehouseID = ?,
		    LocationID = ?,
			Status = 4, -- issue
			UpdateBy = ?,
			UpdateDate = ?
			WHERE Barcode = ?",
			[
				$_disposal, // scrap
				$_warehouse, // WH X-ray
		    $get_barcode_info[0]["LocationID"], // LC Trans
				$_SESSION["user_login"], 
				$date,
				$barcode_decode
			]
		);

		if (!$update_inventtable) {
			sqlsrv_rollback($conn);
			return "update invent table error.";
		}

		// Check Batch
		if ($_batch === null) {
			// Update Onhand
			// move out onhand -1
			$move_out_onhand = Sqlsrv::update(
				$conn,
				"UPDATE Onhand 
				SET QTY -= 1
				WHERE CodeID = ?
				AND WarehouseID = ?
				AND LocationID = ?
				AND Batch IS NULL
				AND Company =?",
				[
					$_item,
					$get_barcode_info[0]["WarehouseID"],
					$get_barcode_info[0]["LocationID"],
					$get_barcode_info[0]["Company"]
				]
			);
		} else {
			// Update Onhand
			// move out onhand -1
			$move_out_onhand = Sqlsrv::update(
				$conn,
				"UPDATE Onhand 
				SET QTY -= 1
				WHERE CodeID = ?
				AND WarehouseID = ?
				AND LocationID = ?
				AND Batch = ?
				AND Company =?",
				[
					$_item,
					$get_barcode_info[0]["WarehouseID"],
					$get_barcode_info[0]["LocationID"],
					$get_barcode_info[0]["Batch"],
					$get_barcode_info[0]["Company"]
				]
			);
		}


		if (!$move_out_onhand) {
			sqlsrv_rollback($conn);
			return "move out onhand error.";
		}

		sqlsrv_commit($conn);
	  return 200;
	}
}