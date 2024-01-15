<?php

namespace App\Services;

use Wattanar\Sqlsrv;
use App\Components\Database;
use App\Components\Utils;

class WarehouseService
{
	public function all()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn, 
			"SELECT 
			WM.ID,
			WM.Description,
			WM.Type,
			WM.Company,
			WT.Description as TypeName
			FROM WarehouseMaster WM
			LEFT JOIN WarehouseTypeMaster WT 
			ON WM.Type = WT.ID"
		);
	}

	public function create($id, $description, $type)
	{
		$description = trim($description);
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$query = Sqlsrv::insert(
			$conn,
			"UPDATE WarehouseMaster
			SET Description = ?,
			Type = ?,
			Company = ?
			WHERE ID = ?
			IF @@ROWCOUNT = 0
			INSERT INTO WarehouseMaster(
				Description, Company, Type
			) VALUES (?, ?, ?)",
			[
				$description,
				$type,
				$_SESSION["user_company"],
				$id,
				$description, 
				$_SESSION["user_company"],
				$type
			]
		);

		if (!$query) {
			sqlsrv_commit($conn);
			return 404;
		} else {
			sqlsrv_commit($conn);
			return 200;
		}		
	}

	public function update($wh_name, $id)
	{
		$wh_name = trim($wh_name);

		$conn = Database::connect();
		
		if (self::checkWhExist($wh_name) === false) {

			$query = Sqlsrv::update(
				$conn,
				"UPDATE WarehouseMaster 
				SET Description = ?,
				Company = ?
				WHERE ID =?",
				[$wh_name, $_SESSION["user_company"], $id]
			);

			if (!$query) {
				return false;
			}

			return true;
		} else {
			return false;
		}
	}

	public function checkWhExist($wh_name)
	{
		$wh_name = trim($wh_name);

		$conn = Database::connect();

		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM WarehouseMaster WH
			WHERE WH.Description = ?",
			[$wh_name]
		);
	}

	public function receiveToWarehouse($barcode_decode)
	{
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");
		$barcode_decode = trim($barcode_decode);
		if (sqlsrv_begin_transaction($conn) === false) {
			return "begin transaction failed";
		}

		if (!isset($_SESSION["user_location"])) {
			return 'session failed';
		}

		$w = new Utils;
		$week = $w->getWeek($date);

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

		if (!$get_location) {
			sqlsrv_rollback($conn);
			return "Cannot use location";
		}

		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		$trans_id = Utils::genTransId($barcode_decode);

		$move_out_trans = Sqlsrv::insert(
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

		if (!$move_out_trans) {
			sqlsrv_rollback($conn);
			return "insert trans move out error.";
		}

		// Update invent table
		$update_inventtble = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET WarehouseReceiveDate = ?,
			DisposalID = ?,
			WarehouseID = ?,
			LocationID = ?,
			UpdateBy = ?,
			UpdateDate = ?,
			Batch = ?
			WHERE Barcode = ?",
			[
				$date,
				$get_location[0]["DisposalID"],
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReceiveLocation"],
				$_SESSION["user_login"],
				$date,
				$get_barcode_info[0]["Batch"],
				$barcode_decode
			]
		);

		if (!$update_inventtble) {
			sqlsrv_rollback($conn);
			return "update inventtble error.";
		}

		$trans_id = Utils::genTransId($barcode_decode);

		$move_in_trans = Sqlsrv::insert(
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

		if (!$move_in_trans) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
		}

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

		// Move in onhand +1
		$move_in_onhand = Sqlsrv::update(
			$conn,
			"UPDATE Onhand SET QTY += 1
			WHERE CodeID = ?
			AND WarehouseID = ?
			AND LocationID = ?
			AND Company = ?
			AND Batch = ?
			IF @@ROWCOUNT = 0
			INSERT INTO Onhand(
				CodeID,
				WarehouseID,
				LocationID,
				Batch,
				QTY,
				Company
			) VALUES (?, ?, ?, ?, ?, ?)",
			[
				$get_barcode_info[0]["ItemID"],
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReceiveLocation"],
				$_SESSION["user_company"],
				$get_barcode_info[0]["Batch"],

				$get_barcode_info[0]["ItemID"],
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReceiveLocation"],
				$get_barcode_info[0]["Batch"],
				1, // qty
				$_SESSION["user_company"]
			]
		);

		// sqlsrv_rollback($conn);
		// return "in developing..";

		if (!$move_in_onhand) {
			sqlsrv_rollback($conn);
			return "move in onhand error.";
		}

		if ($update_inventtble &&
			$move_out_trans && 
			$move_in_trans && 
			$move_out_onhand &&
			$move_in_onhand) {
			
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return "transaction failed";
		}
	}

	public function createWarehouseType($id, $desc)
	{
		$conn = Database::connect();
		$query = Sqlsrv::insert(
			$conn,
			"UPDATE WarehouseTypeMaster 
			SET Description = ?
			WHERE ID = ?
			IF @@ROWCOUNT = 0
			INSERT INTO WarehouseTypeMaster(Description)
			VALUES(?)",
			[
				$desc,
				$id,
				$desc
			]
		);

		if ($query) {
			return 200;
		} else {
			return "ทำรายการไม่สำเร็จ";
		}
	}

	public function getAllWarehouseType()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM WarehouseTypeMaster"
		);
	}

	public function deleteWarehouseType($id)
	{
		$conn = Database::connect();
		$query =  Sqlsrv::delete(
			$conn,
			"DELETE FROM WarehouseTypeMaster 
			WHERE ID = ?
			AND ID NOT IN (
				SELECT Type FROM WarehouseMaster
				WHERE Type = ?
			)",
			[$id, $id]
		);

		if ($query) {
			return 200;
		} else {
			return 404;
		}
	}

	public function getReportSentToWarehouse($time)
	{
		$sqltime = '';
		foreach ($time as $v) {
			$sqltime .= ' (T.WarehouseTransReceiveDate BETWEEN ' . $v . ') OR ';
		}
		$sqltime = trim($sqltime, ' OR ');
		$sql = "SELECT
				Z.ItemID,
				Z.CuringCode,
				Z.NameTH,
				Z.Batch,
				SUM(Z.QTY)[QTY]
			FROM 
			(
				SELECT 
					CCM.ItemID,
					T.CuringCode,
					I.NameTH,
					(
							SELECT IT.Batch FROM InventTrans IT
							WHERE IT.DocumentTypeID = 1 
							AND IT.DisposalID = 5 -- transit
							AND IT.Barcode = T.Barcode
					) [Batch],
					T.QTY
				FROM InventTable T
				LEFT JOIN CureCodeMaster CCM ON CCM.ID = T.CuringCode
				LEFT JOIN ItemMaster I ON CCM.ItemID = I.ID AND CCM.ID = T.CuringCode
				WHERE $sqltime
			) Z 
			GROUP BY
				Z.ItemID,
				Z.CuringCode,
				Z.NameTH,
				Z.Batch,
				Z.QTY";
				// return $sql;
		// return trim($sqltime, ' OR ');
		header('Content-Type: application/json');
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn, 
			$sql
		);
	}

	public function getReportReceiveToWarehouse($shift, $time, $datewarehouse, $brand)
	{
		$sqltime = '';
		foreach ($time as $v) {
			$sqltime .= ' (T.WarehouseReceiveDate BETWEEN ' . $v . ') OR ';
		}
		$sqltime = trim($sqltime, ' OR ');
		$sql = "SELECT 
			CCM.ItemID,
			T.CuringCode,
			I.NameTH,
			T.Batch,
			SUM(T.QTY)[QTY],
			I.Brand,
			I.Pattern
			FROM InventTable T
				LEFT JOIN CureCodeMaster CCM ON CCM.ID = T.CuringCode
				LEFT JOIN ItemMaster I ON CCM.ItemID = I.ID AND CCM.ID = T.CuringCode
				LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
			WHERE DisposalID <> 16 
				AND T.WarehouseTransReceiveDate IS NOT NULL
				AND T.WarehouseReceiveDate IS NOT NULL
				and ($sqltime)
				and B.BrandID IN ($brand)
			group by CCM.ItemID
				,T.CuringCode 
				,I.NameTH
				,T.Batch
				,I.Brand
				,I.Pattern";
		header('Content-Type: application/json');
		$conn = Database::connect();
		// return $sql;
		return Sqlsrv::queryJson(
			$conn, 
			$sql
		);
	}

	public function FGWithdrawPDF($dateinter)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn, 
			"SELECT  
				J.InventJournalID
				,J.ItemID
				,CONVERT(time,J.CreateDate)[time_create]
				,I.TemplateSerialNo
				,I.ItemID
				,IT.NameTH
				,I.CuringCode
				,R.Description[Note]
				,IJ.EmpCode
				,E.FirstName
				,E.LastName
				,E.DivisionCode
				,D.Description[Department]
				,J.CreateBy
				,ITS.Batch
				,U.Name
				,1[qty]
				,ROW_NUMBER() OVER(ORDER BY name ASC) AS Row
				,S.Description
			FROM InventJournalTrans J
			LEFT JOIN InventTable I ON J.BarcodeID=I.Barcode 
			LEFT JOIN ItemMaster IT ON I.ItemID=IT.ID
			LEFT JOIN RequsitionNote R ON J.RequsitionID=R.ID
			LEFT JOIN InventJournalTable IJ ON J.InventJournalID=IJ.InventJournalID
			LEFT JOIN Employee E ON IJ.EmpCode=E.Code
			LEFT JOIN DivisionMaster D ON E.DivisionCode=D.Code
			LEFT JOIN UserMaster U ON J.CreateBy=U.ID
			LEFT JOIN InventTrans ITS ON ITS.InventJournalID = J.InventJournalID AND J.BarcodeID = ITS.Barcode
			LEFT JOIN Status S ON IJ.Status=S.ID
			WHERE CONVERT(date,J.CreateDate) = ?
			AND IJ.JournalTypeID = 'MOVWH'
			ORDER BY CONVERT(time,J.CreateDate) ASC",
			[
				$dateinter
			]
		);
	}
}