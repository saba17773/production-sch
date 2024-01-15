<?php

namespace App\Services;

use Wattanar\Sqlsrv;
use App\Components\Database;
use App\Components\Security;
use App\Components\Utils;
use App\Services\FinalService;

class RepairService
{
	public function repair($barcode, $defect_code)
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
			return "Barcode นี้ ยังไม่ได้ Hold";
		}

		$_warehouse = $get_barcode_info[0]["WarehouseID"]; // greentire
		
		$_disposal = 12; // repair

		// setup hold wh,lc,disp
		if($get_barcode_info[0]["CuringDate"] === null) {
			$_item = $get_barcode_info[0]["GT_Code"];
			$_location = 10; // hold gt
			// $_batch = null;
			$_batch = $get_barcode_info[0]["Batch"];
		} else {
			$_item = $get_barcode_info[0]["ItemID"];
			$_location = 12; // hold x ray
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
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				$trans_id . 1,
				$barcode_decode,
				$_item,
				$_batch,
				$get_barcode_info[0]["DisposalID"],
				null,
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				-1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				2, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
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
			SET DisposalID = ?, -- hold
			WarehouseID = ?,
		    LocationID = ?,
			Status = 5, -- Hold
			UpdateBy = ?,
			UpdateDate = ?
			WHERE Barcode = ?",
			[
				$_disposal, // Hold
				$_warehouse, // WH X-ray
		    	$_location, // LC Trans
				$_SESSION["user_login"], 
				$date,
				$barcode_decode
			]
		);

		if (!$update_inventtable) {
			sqlsrv_rollback($conn);
			return "Update InventTable Error";
		}

		// Gen trans id for transaction move in
		$trans_id = Utils::genTransId($barcode_decode);

		// transaction move in 
		$trans_move_in = Sqlsrv::insert(
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
				$trans_id . 2,
				$barcode_decode,
				$_item,
				$_batch,
				$_disposal,
				$defect_code,
				$_warehouse,
				$_location,
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$trans_move_in) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
			// return Database::errors();
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

		if ($_batch === null) {

			$move_in_onhand = Sqlsrv::update(
				$conn,
				"UPDATE Onhand SET QTY += 1
				WHERE CodeID = ?
				AND WarehouseID = ?
				AND LocationID = ?
				AND Batch IS NULL
				AND Company =?
				IF @@ROWCOUNT = 0
				INSERT INTO Onhand 
				VALUES (?, ?, ?, ?, ?, ?)",
				[
					$_item,
					$_warehouse,
					$_location,
					$_SESSION["user_company"],
					$_item,
					$_warehouse,
					$_location,
					null,
					1, // qty
					$_SESSION["user_company"]
				]
			);
		} else {

			$move_in_onhand = Sqlsrv::update(
				$conn,
				"UPDATE Onhand SET QTY += 1
				WHERE CodeID = ?
				AND WarehouseID = ?
				AND LocationID = ?
				AND Batch = ?
				AND Company =?
				IF @@ROWCOUNT = 0
				INSERT INTO Onhand 
				VALUES (?, ?, ?, ?, ?, ?)",
				[
					$_item,
					$_warehouse,
					$_location,
					$_batch,
					$_SESSION["user_company"],
					$_item,
					$_warehouse,
					$_location,
					$_batch,
					1, // qty
					$_SESSION["user_company"]
				]
			);
		}
		
		// echo Database::errors();
		if (!$move_in_onhand) {
			sqlsrv_rollback($conn);
			return "move in onhand error.";
		}

		if ($trans_move_out && 
			$trans_move_in && 
			$update_inventtable &&
			$move_out_onhand && 
			$move_in_onhand) {
			
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return "error";
		}
	}

	public function unrepair($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();

		$date = date("Y-m-d H:i:s");

		// get barcode info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		); 

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

		if ($_SESSION["user_warehouse"] != $get_barcode_info[0]["WarehouseID"]) {
			return "User has not permissions!";
		}

		$_warehouse = $get_barcode_info[0]["WarehouseID"]; // greentire
		
		// setup hold wh,lc,disp
		if($get_barcode_info[0]["CuringDate"] === null) {
			$_item = $get_barcode_info[0]["GT_Code"];
			$_location = 2; //  gt
			// $_batch = null;
			$_batch = $get_barcode_info[0]["Batch"];
			$_disposal = 1; // greentire
		} else {
			$_item = $get_barcode_info[0]["ItemID"];
			$_location = 4; // x ray
			$_batch = $get_barcode_info[0]["Batch"];
			$_disposal = 4; // xray
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
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				$trans_id . 1,
				$barcode_decode,
				$_item,
				$_batch,
				$get_barcode_info[0]["DisposalID"],
				null,
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				-1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				2, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$trans_move_out) {
			sqlsrv_rollback($conn);
			return "trans move out error";
		}

		// Update Invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable 
			SET DisposalID = ?, -- hold
			WarehouseID = ?,
		    LocationID = ?,
			Status = 1, -- Receive
			UpdateBy = ?,
			UpdateDate = ?
			WHERE Barcode = ?",
			[
				$_disposal, // Hold
				$_warehouse, // WH X-ray
		    	$_location, // LC Trans
				$_SESSION["user_login"], 
				$date,
				$barcode_decode
			]
		);

		if (!$update_inventtable) {
			sqlsrv_rollback($conn);
			return "Update InventTable Error";
		}

		// Gen trans id for transaction move in
		$trans_id = Utils::genTransId($barcode_decode);

		// transaction move in 
		$trans_move_in = Sqlsrv::insert(
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
				$trans_id . 2,
				$barcode_decode,
				$_item,
				$_batch,
				$_disposal,
				null,
				$_warehouse,
				$_location,
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$trans_move_in) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
			// return Database::errors();
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

		if ($_batch === null) {

			$move_in_onhand = Sqlsrv::update(
				$conn,
				"UPDATE Onhand SET QTY += 1
				WHERE CodeID = ?
				AND WarehouseID = ?
				AND LocationID = ?
				AND Batch IS NULL
				AND Company =?
				IF @@ROWCOUNT = 0
				INSERT INTO Onhand 
				VALUES (?, ?, ?, ?, ?, ?)",
				[
					$_item,
					$_warehouse,
					$_location,
					$_SESSION["user_company"],
					$_item,
					$_warehouse,
					$_location,
					null,
					1, // qty
					$_SESSION["user_company"]
				]
			);
		} else {

			$move_in_onhand = Sqlsrv::update(
				$conn,
				"UPDATE Onhand SET QTY += 1
				WHERE CodeID = ?
				AND WarehouseID = ?
				AND LocationID = ?
				AND Batch = ?
				AND Company =?
				IF @@ROWCOUNT = 0
				INSERT INTO Onhand 
				VALUES (?, ?, ?, ?, ?, ?)",
				[
					$_item,
					$_warehouse,
					$_location,
					$_batch,
					$_SESSION["user_company"],
					$_item,
					$_warehouse,
					$_location,
					$_batch,
					1, // qty
					$_SESSION["user_company"]
				]
			);
		}
		
		// echo Database::errors();
		if (!$move_in_onhand) {
			sqlsrv_rollback($conn);
			return "move in onhand error.";
		}

		if ($trans_move_out && 
			$trans_move_in && 
			$update_inventtable &&
			$move_out_onhand && 
			$move_in_onhand) {
			
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return "error";
		}
	}
}