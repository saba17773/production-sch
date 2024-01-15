<?php

namespace App\Services;

use App\Components\Security;
use App\Components\Database;
use Wattanar\Sqlsrv;
use App\Components\Utils;

class FinalService
{
	public function isFinalReceiveDateExist($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM InventTable
			WHERE Barcode = ? 
			AND FinalReceiveDate IS NOT NULL",
			[$barcode_decode]
		);
	}

	public function save($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");
		
		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT 
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID,
			L.ReturnReceiveLocation
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		if (count($get_location) === 0) {
			sqlsrv_rollback($conn);
			return 'User location ไม่ถูกต้อง';
		}

		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		$trans_id = Utils::genTransId($barcode_decode);

		$move_out = Sqlsrv::insert(
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
				$get_barcode_info[0]["ItemID"],
				$get_barcode_info[0]["Batch"],
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

		if (!$move_out) {
			sqlsrv_rollback($conn);
			return "insert trans move out error.";
		}

		// Update Invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable 
			SET
			DisposalID = ?, -- X-ray
			WarehouseID = ?,
		  LocationID = ?,
			Status = 1, -- Receive
			FinalReceiveDate = ?,
			GateReceiveNo = ?,
			UpdateBy = ?,
			UpdateDate = ?
			WHERE Barcode = ?",
			[
				$get_location[0]["DisposalID"],
				$get_location[0]["WarehouseID"], // WH X-ray
		    	$get_location[0]["ReceiveLocation"], // LC Trans
		    $date,
		    null,
				$_SESSION["user_login"], 
				$date,
				$barcode_decode
			]
		);

		if (!$update_inventtable) {
			sqlsrv_rollback($conn);
			return "Update InventTable Error";
		}

		$trans_id = Utils::genTransId($barcode_decode);

		$move_in = Sqlsrv::insert(
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
				$get_barcode_info[0]["ItemID"],
				$get_barcode_info[0]["Batch"],
				$get_location[0]["DisposalID"],
				null,
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReceiveLocation"],
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$move_in) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
		}

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
			AND Company =?
			AND QTY > 0",
			[
				$get_barcode_info[0]["ItemID"],
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				$get_barcode_info[0]["Batch"],
				$get_barcode_info[0]["Company"]
			]
		);

		if (!$move_out_onhand) {
			sqlsrv_rollback($conn);
			return "move out onhand error.";
		}

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
				$get_barcode_info[0]["ItemID"],
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReceiveLocation"],
				$get_barcode_info[0]["Batch"],
				$_SESSION["user_company"],
				$get_barcode_info[0]["ItemID"],
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReceiveLocation"],
				$get_barcode_info[0]["Batch"],
				1, // qty
				$_SESSION["user_company"]
			]
		);

		if (!$move_in_onhand) {
			sqlsrv_rollback($conn);
			return "move in onhand error.";
		}

		if ($update_inventtable &&
			$move_out && 
			$move_in && 
			$move_out_onhand &&
			$move_in_onhand) {
			
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 404;
		}
		// END
	}

	public function saveReturn($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");
		
		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT 
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID,
			L.ReturnReceiveLocation
			FROM Location L
			LEFT JOIN Location LL ON L.ReturnReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		if ($_SESSION["user_warehouse"] === 2) {
			$inventTableStatus = 5; // Hold
		} else if($_SESSION["user_warehouse"] === 3) {
			$inventTableStatus = 1; // Receive
		} else {
			$inventTableStatus = 5;
		}

		if ($_SESSION["user_warehouse"] !== 1) {
			$_code = $get_barcode_info[0]["ItemID"];
		} else {
			$_code = $get_barcode_info[0]["GT_Code"];
		}

		// Update Invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable 
			SET
			DisposalID = ?, -- X-ray
			WarehouseID = ?,
		  LocationID = ?,
			Status = ?,
			UpdateBy = ?,
			UpdateDate = ?
			WHERE Barcode = ?",
			[
				9, // return 
				$get_barcode_info[0]["WarehouseID"], // WH
		    $get_location[0]["ReturnReceiveLocation"],
				//11, // Hold > edit get from ReturnReceiveLocation
				$inventTableStatus,
				$_SESSION["user_login"], 
				$date,
				$barcode_decode
			]
		);

		if (!$update_inventtable) {
			sqlsrv_rollback($conn);
			return "Update InventTable Error";
		}

		$trans_id = Utils::genTransId($barcode_decode);

		$move_in = Sqlsrv::insert(
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
				$_code,
				$get_barcode_info[0]["Batch"],
				9, // Return
				'CUR511', // Return
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReturnReceiveLocation"],
				//11, // Final Hold
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$move_in) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
		}

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
				$_code,
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReturnReceiveLocation"],
				$get_barcode_info[0]["Batch"],
				$_SESSION["user_company"],
				$_code,
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReturnReceiveLocation"],
				$get_barcode_info[0]["Batch"],
				1, // qty
				$_SESSION["user_company"]
			]
		);

		if (!$move_in_onhand) {
			sqlsrv_rollback($conn);
			return "move in onhand error.";
		}

		sqlsrv_commit($conn);
		return 200;
	}
}