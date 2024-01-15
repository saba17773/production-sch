<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;
use App\Components\Utils;
use App\Components\Security;
use App\V2\Database\Handler;
use App\V2\Batch\BatchAPI;

class MovementService 
{
	public function printByJournalType($journalId, $mode)
	{
		$conn = Database::connect();

		return Sqlsrv::queryJson(
			$conn,
			"SELECT 
			J.ItemID
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
			FROM InventJournalTrans J
			LEFT JOIN InventTable I ON J.BarcodeID=I.Barcode 
			--AND J.ItemID=I.ItemID
			LEFT JOIN RequsitionNote R ON J.RequsitionID=R.ID
			LEFT JOIN InventJournalTable IJ ON J.InventJournalID=IJ.InventJournalID 
			LEFT JOIN Employee E ON IJ.EmpCode=E.Code
			LEFT JOIN DivisionMaster D ON E.DivisionCode=D.Code
			LEFT JOIN UserMaster U ON J.CreateBy=U.ID
			LEFT JOIN InventTrans ITS ON ITS.InventJournalID = J.InventJournalID AND J.BarcodeID = ITS.Barcode
			LEFT JOIN ItemMaster IT ON ITS.CodeID=IT.ID
			WHERE IJ.JournalTypeID = '$mode'
			AND J.InventJournalID = ?
			ORDER BY CONVERT(time,J.CreateDate) ASC",
			[
				$journalId
			]
		);
	}

	public function allMovementType()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM JournalType"
		);
	}

	public function getLatestJournalTransByJournalId($journalId)
	{
		$conn = Database::connect();
		$query =  Sqlsrv::queryArrayObject(
			$conn,
			"SELECT 
			IT.CuringCode, 
			JT.BarcodeID,
			RN.Description as RN,
			U.Name as CreateBy,
			JT.CreateDate
			FROM InventJournalTrans JT
			LEFT JOIN InventTable IT ON JT.BarcodeID = IT.Barcode
			LEFT JOIN RequsitionNote RN ON RN.ID = JT.RequsitionID
			LEFT JOIN UserMaster U ON U.ID = JT.CreateBy
			WHERE JT.InventJournalID = ?
			ORDER BY JT.CreateDate DESC",
			[$journalId]
		);

		$temp = [];

		foreach ($query as $v) {
			$temp[] = [
				'BarcodeID' => Security::_encode($v->BarcodeID),
				'CuringCode' => $v->CuringCode,
				'RN' => $v->RN,
				'CreateBy' => $v->CreateBy,
				'CreateDate' => $v->CreateDate
			];
		}

		return json_encode($temp);
	}

	public function createNew(
		$barcode, 
		$employee, 
		$disposal, 
		$division, 
		$journalId
	)
	{
		$barcode_decode = Security::_decode($barcode);
		$date = date("Y-m-d H:i:s");
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		// get barcode info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);
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

		// insert invent journal table
		$journalTable = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventJournalTable(
				InventJournalID,
				JournalTypeID,
				Customer,
				Department,
				EmpCode,
				Status,
				CreateDate,
				CreateBy,
				Company
			) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)",
			[
				$journalId, // Invent Journal ID
				"MOV", // journal type id
				"C001", // customer
				$division,
				$employee,
				1, // open
				$date,
				$_SESSION["user_login"],
				$_SESSION["user_company"]
			]
		);

		if ($journalTable) {
			sqlsrv_rollback($conn);
			return "journal taable move in error";
		}

		// generate trans id
		$trans_id = Utils::genTransId(Security::_decode($barcode_decode));

		// insert invent journal trans
		$journalTrans = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventJournalTrans(
				ID,
				InventJournalID,
				ItemID,
				QTY,
				BarcodeID,
				DisposalID,
				CreateDate,
				CreateBy,
				Company
			) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
			[
				$trans_id,
				$journalId,
				"I-0044460",
				1,
				$barcode,
				$disposal,
				$date,
				$_SESSION["user_login"],
				$_SESSION["user_company"]
			]
		);

		if ($journalTrans) {
			sqlsrv_rollback($conn);
			return "journal trans move in error";
		}

		// update invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET DisposalID = ? , 
			WarehouseID = ? , 
			LocationID = ?,
			Status = 4, -- issue
			Company = ?,
			UpdateBy = ?,
			UpdateDate = ?
			WHERE Barcode = ?",
			[
				$disposal,
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$barcode_decode
			]
		);

		if ($update_inventtable) {
			sqlsrv_rollback($conn);
			return "invent table move in error";
		}

		// Generate Trans ID
		$trans_id = Utils::genTransId($barcode_decode);

		// Insert invent Trans
		$insert_inventtrans = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventTrans(
				TransID ,
				Barcode,
				CodeID ,
				Batch,
				DisposalID ,
				DefectID,
				WarehouseID ,
				LocationID,
				QTY ,
				UnitID,
				DocumentTypeID ,
				Company,
				CreateBy ,
				CreateDate
			)VALUES(
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?
			)",
			[
				$trans_id, 
				$barcode_decode,
				$get_barcode_info[0]["ItemID"], // gt code
				$get_barcode_info[0]["Batch"], // batch
				$disposal, // disposal 
				null, // defect
				$get_barcode_info[0]["WarehouseID"], // wh
				$get_barcode_info[0]["LocationID"], // lc 
				1, // qty
				1, // unit
				2, // document id => issue
				$_SESSION["user_login"],
				$_SESSION["user_company"], 
				$date
			]
		);

		if ($insert_inventtrans) {
			sqlsrv_rollback($conn);
			return "invent trans move out error";
		}

		// Update Onhand
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

		if ($move_out_onhand) {
			sqlsrv_rollback($conn);
			return "onhand move out error";
		}

		if ($update_inventtable && 
			$insert_inventtrans && 
			$move_out_onhand) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return "Error";
		}
	}

	public function save($id, $desc)
	{
		$conn = Database::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE JournalType SET Description = ?
			WHERE ID = ?
			IF @@ROWCOUNT = 0
			INSERT INTO JournalType(ID, Description)
			VALUES (?, ?)",
			[
				$desc,
				$id,
				$id,
				$desc
			]
		);

		if ($query) {
			return 200;
		} else {
			return 404;
		}
	}

	public function saveJournalTable($emp, $division, $journal_type = "MOV")
	{
		$conn = Database::connect();

		if ($_SESSION['user_warehouse'] === 3) {
			$journal_type = 'MOVWH';
		} else if ($_SESSION['user_warehouse'] === 2) {
			$journal_type = 'MOV';
		}

		$year = date("Y");
		$date = date("m-d-Y H:i:s");

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}
		
		$updateJournalSequeue = Sqlsrv::update(
			$conn,
			"UPDATE Sequeue SET SeqJournal += 1"
		);

		if (!$updateJournalSequeue) {
			sqlsrv_rollback($conn);
			return "ไม่สามารถอัพเดทได้";
		}

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 SeqJournal FROM Sequeue"
		);

		if (!$query) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "error"
			];
		}

		$getEmployeeInfo = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM Employee
			WHERE Code = ?",
			[$emp]
		);

		$insertInventJournalTable = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventJournalTable(
				InventJournalID,
				JournalTypeID,
				Customer,
				Department,
				EmpCode,
				Status,
				CreateDate,
				CreateBy,
				Company
			) VALUES (?, ?, ?, ?, ?, ?, ?, ? ,?)",
			[
				"W".substr($year,-2)."-".str_pad($query[0]["SeqJournal"], 6, "0",STR_PAD_LEFT),
				$journal_type,
				null,
				$getEmployeeInfo[0]["DivisionCode"],
				$emp,
				1,
				$date,
				$_SESSION["user_login"],
				$_SESSION["user_company"]
			]
		);

		if ($updateJournalSequeue && $insertInventJournalTable) {
			sqlsrv_commit($conn);
			return [
				"status" => 200,
				"journal" => "W".substr($year,-2)."-".str_pad($query[0]["SeqJournal"], 6, "0",STR_PAD_LEFT)
			];
		} else {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "error"
			];
		}

		
	}

	public function saveMovementIssue($barcode, $requsition, $journalId)
	{
		$conn = Database::connect();

		$date = Date('Y-m-d H:i:s');
		$barcode_decode = Security::_decode($barcode);

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$removeLPNIDFrom = sqlsrv_query(
			$conn,
			"UPDATE InventTable 
			SET LPNID = null 
			WHERE Barcode = ?",
			[
				$barcode_decode
			]
		);

		$updateLPNInUse = sqlsrv_query(
			$conn,
			"UPDATE LPNMaster 
			SET QtyInUse -= 1,
			Remain += 1
			WHERE LPNID = (
				SELECT TOP 1 LPNID FROM LPNLine
				WHERE Barcode = ?
			)",
			[
				$barcode_decode
			]
		);

		$removeLPNLineByBarcode = sqlsrv_query(
			$conn,
			"DELETE FROM LPNLine 
			WHERE Barcode = ?",
			[
				$barcode_decode
			]
		);

		$getItem = Sqlsrv::queryArray(
			$conn,
			"SELECT ItemID FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		$transId = Utils::genTransId($barcode_decode);

		// Insert Journal Trans
		$insertJournalTrans = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventJournalTrans(
				ID,
				InventJournalID,
				ItemID,
				QTY,
				BarcodeID,
				RequsitionID,
				CreateDate,
				CreateBy,
				Company
			) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)",
			[
				$transId,
				$journalId,
				$getItem[0]["ItemID"],
				1,
				$barcode_decode,
				$requsition,
				$date,
				$_SESSION["user_login"],
				$_SESSION["user_company"]
			]
		);

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

		// Update Invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable 
			SET DisposalID = ?,
			WarehouseID = ?,
		  LocationID = ?,
			Status = 4, -- issue
			UpdateBy = ?,
			UpdateDate = ?
			WHERE Barcode = ?",
			[
				11, // Movement
				$get_barcode_info[0]["WarehouseID"], // WH 
		    $get_barcode_info[0]["LocationID"], // LC
				$_SESSION["user_login"], 
				$date,
				$barcode_decode
			]
		);

		if (!$update_inventtable) {
			sqlsrv_rollback($conn);
			return "Update InventTable Error";
		}

		// Generate trans id
		$transId = Utils::genTransId($barcode_decode);

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
				InventJournalID,
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?
			)",
			[
				$transId,
				$barcode_decode,
				$getItem[0]["ItemID"],
				$get_barcode_info[0]["Batch"],
				11, // Movement
				null, // defect
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				-1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				2, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$journalId,
				$_SESSION["Shift"]
			]
		);

		if (!$trans_move_out) {
			sqlsrv_rollback($conn);
			return "trans move out error";
		}

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
				$getItem[0]["ItemID"],
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

		if ($trans_move_out &&
			$update_inventtable &&
			$move_out_onhand) {
			
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 404;
		}
	}

	public function allMovementIssue()
	{
		$conn = Database::connect();
		$user_warehouse = $_SESSION["user_warehouse"];
		if ($_SESSION['user_name'] !== 'admin') {
			$warehouse_condition = ' U.Warehouse = ' . $user_warehouse;
		} else {
			$warehouse_condition = '\'A\'=\'A\'';
		}

		$detect = new \Mobile_Detect; 

		if ($detect->isMobile()) {

			// for Mobile
				$sql = "SELECT 
				IJ.InventJournalID,
				IJ.JournalTypeID,
				IJ.Department,
				IJ.EmpCode,
				DV.Description as Division,
				(E.FirstName +' '+E.LastName) as Name,
				S.Description as Status,
				U.Name as CreateBy,
				IJ.CreateDate
				FROM InventJournalTable IJ
				LEFT JOIN DivisionMaster DV ON DV.Code = IJ.Department
				LEFT JOIN Employee E ON IJ.EmpCode = E.Code
				LEFT JOIN Status S ON S.ID = IJ.Status
				LEFT JOIN UserMaster U ON U.ID = IJ.CreateBy
				WHERE S.ID = 1 AND " . $warehouse_condition;
				// echo $sql; exit;
		} else {

			// For Desktop
				$sql = "SELECT IJ.InventJournalID,
				IJ.JournalTypeID,
				IJ.Department,
				IJ.EmpCode,
				DV.Description as Division,
				(E.FirstName +' '+E.LastName) as Name,
				S.Description as Status,
				U.Name as CreateBy,
				IJ.CreateDate,
				CASE 
					WHEN IJ.Status = 3 THEN (SELECT UMS.Name FROM UserMaster UMS WHERE UMS.ID = U.ID)
					ELSE NULL
				END [CompleteBy],
				IJ.CompleteDate
				FROM InventJournalTable IJ
				LEFT JOIN DivisionMaster DV ON DV.Code = IJ.Department
				LEFT JOIN Employee E ON IJ.EmpCode = E.Code
				LEFT JOIN Status S ON S.ID = IJ.Status
				LEFT JOIN UserMaster U ON U.ID = IJ.CreateBy
				LEFT JOIN UserMaster UU ON UU.ID = IJ.CompleteBy";

		}

		// echo $sql; exit;

		return Sqlsrv::queryJson(
			$conn,
			$sql
		);

	}

	public function saveReverseOK($barcode, $auth)
	{
		$barcode_decode = Security::_decode($barcode);

		$date = date("Y-m-d H:i:s");
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		// get barcode info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		); 

		if ($get_barcode_info[0]["PressSide"] === "L") {
			$preess_side_for_update = "L";
		} else {
			$preess_side_for_update = "R";
		}

		$updatePressDateTime = sqlsrv_query(
			$conn,
			"UPDATE PressMaster
			SET $preess_side_for_update = ?
			WHERE ID = ? 
			AND $preess_side_for_update = ?",
			[
				date('Y-m-d H:i:s', strtotime('-10 minute')),
				$get_barcode_info[0]["PressNo"],
				$get_barcode_info[0]["PressSide"]
			]
		);

		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT 
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID,
			L.ReverseReceiveLocation,
			R.WarehouseID as WarehouseReverseReceive,
			R.DisposalID as DisposalReverse
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			LEFT JOIN Location R ON L.ReverseReceiveLocation = R.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		$ddate = new \DateTime($get_barcode_info[0]["DateBuild"]);

		// $week = date("Y") . "-" . $ddate->format("W");
		$week = (new BatchAPI)->getGreentireBatch($barcode_decode);

		$moveToReverseTable = sqlsrv_query(
			$conn,
			"INSERT INTO ReverseTable(
			Barcode,
			DateBuild,
			BuildingNo,
			GT_Code,
			CuringDate,
			CuringCode,
			ItemID,
			Batch,
			QTY,
			Unit,
			PressNo,
			PressSide,
			MoldNo,
			TemplateSerialNo,
			CuredTireReciveDate,
			CuredTireLineNo,
			FinalReceiveDate,
			GateReceiveNo,
			XrayDate,
			XrayNo,
			QTechReceiveDate,
			WarehouseReceiveDate,
			WarehouseTransReceiveDate,
			LoadingDate,
			DONo,
			PickingListID,
			OrderID,
			DisposalID,
			WarehouseID,
			LocationID,
			Status,
			Company,
			UpdateBy,
			UpdateDate,
			CreateBy,
			CreateDate
			)
			SELECT 
			Barcode,
			DateBuild,
			BuildingNo,
			GT_Code,
			CuringDate,
			CuringCode,
			ItemID,
			Batch,
			QTY,
			Unit,
			PressNo,
			PressSide,
			MoldNo,
			TemplateSerialNo,
			CuredTireReciveDate,
			CuredTireLineNo,
			FinalReceiveDate,
			GateReceiveNo,
			XrayDate,
			XrayNo,
			QTechReceiveDate,
			WarehouseReceiveDate,
			WarehouseTransReceiveDate,
			LoadingDate,
			DONo,
			PickingListID,
			OrderID,
			DisposalID,
			WarehouseID,
			LocationID,
			Status,
			Company,
			UpdateBy,
			UpdateDate,
			?,
			?
			FROM InventTable
			WHERE Barcode = ?",
			[
				$_SESSION["user_login"], 
				$date, 
				$barcode_decode
			]
		);

		if (!$moveToReverseTable) {
			sqlsrv_rollback($conn);
			return "move to reverse table error";
		}

		$updateInventTable = sqlsrv_query(
			$conn,
			"UPDATE InventTable 
			SET CuringDate = null,
			CuringCode = null,
			ItemID = null,
			PressNo = null,
			PressSide = null,
			MoldNo = null,
			TemplateSerialNo = null,
			UpdateBy = ?,
			UpdateDate = ?,
			DisposalID = ?,
			WarehouseID = ?,
			LocationID = ?,
			Batch = ?
			WHERE Barcode = ?",
			[
				$_SESSION["user_login"],
				$date,
				$get_location[0]["DisposalReverse"],
				$get_location[0]["WarehouseReverseReceive"],
				$get_location[0]["ReverseReceiveLocation"],
				$week,
				$barcode_decode
			]
		);

		if (!$updateInventTable) {
			sqlsrv_rollback($conn);
			return "update invent table error";
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
				AuthorizeBy
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?
			)",
			[
				$trans_id . 1,
				$barcode_decode,
				$get_barcode_info[0]["ItemID"],
				$get_barcode_info[0]["Batch"],
				13, // reverse
				null, // defect
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				-1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				2, // docs type = issue
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"],
				$auth
			]
		);

		if (!$trans_move_out) {
			sqlsrv_rollback($conn);
			return "trans move out error";
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
				Shift,
				AuthorizeBy
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?
			)",
			[
				$trans_id . 2,
				$barcode_decode,
				$get_barcode_info[0]["GT_Code"],
				$week,
				$get_location[0]["DisposalReverse"],
				null,
				$get_location[0]["WarehouseReverseReceive"],
				$get_location[0]["ReverseReceiveLocation"],
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"],
				$auth
			]
		);

		if (!$trans_move_in) {
			sqlsrv_rollback($conn);
			// return "insert trans move in error.";
			return Database::errors();
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

		// Move in onhand 
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
				$get_barcode_info[0]["GT_Code"],
				$get_location[0]["WarehouseReverseReceive"],
				$get_location[0]["ReverseReceiveLocation"],
				$week,
				$_SESSION["user_company"],
				$get_barcode_info[0]["GT_Code"],
			  $get_location[0]["WarehouseReverseReceive"],
				$get_location[0]["ReverseReceiveLocation"],
				$week,
				1, // qty
				$_SESSION["user_company"]
			]
		);

		// echo Database::errors();
		if (!$move_in_onhand) {
			sqlsrv_rollback($conn);
			return "move in onhand error.";
		}
			
		sqlsrv_commit($conn);
		return 200;

	}

	public function saveReverseScrap($barcode, $defect, $auth)
	{
		$barcode_decode = Security::_decode($barcode);

		$date = date("Y-m-d H:i:s");
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		// get barcode info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		); 

		if ($get_barcode_info[0]["PressSide"] === "L") {
			$preess_side_for_update = "L";
		} else {
			$preess_side_for_update = "R";
		}

		$updatePressDateTime = Sqlsrv::update(
			$conn,
			"UPDATE PressMaster
			SET $preess_side_for_update = ?
			WHERE ID = ? 
			AND $preess_side_for_update = ?",
			[
				date('Y-m-d H:i:s', strtotime('-20 minute')),
				$get_barcode_info[0]["PressNo"],
				$get_barcode_info[0]["PressSide"]
			]
		);

		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT 
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID,
			L.ReverseReceiveLocation,
			R.WarehouseID as WarehouseReverseReceive,
			R.DisposalID as DisposalReverse
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			LEFT JOIN Location R ON L.ReverseReceiveLocation = R.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		$ddate = new \DateTime($get_barcode_info[0]["DateBuild"]);
		// $week = date("Y") . "-" . $ddate->format("W");
		$week = (new BatchAPI)->getGreentireBatch($barcode_decode);

		$getWarehouseAndLocationToHold = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM Location 
			WHERE DisposalID = 10 -- Hold
			AND WarehouseID = 4" // -- Curing Hold
		);

		$moveToReverseTable = sqlsrv_query(
			$conn,
			"INSERT INTO ReverseTable(
			Barcode,
			DateBuild,
			BuildingNo,
			GT_Code,
			CuringDate,
			CuringCode,
			ItemID,
			Batch,
			QTY,
			Unit,
			PressNo,
			PressSide,
			MoldNo,
			TemplateSerialNo,
			CuredTireReciveDate,
			CuredTireLineNo,
			FinalReceiveDate,
			GateReceiveNo,
			XrayDate,
			XrayNo,
			QTechReceiveDate,
			WarehouseReceiveDate,
			WarehouseTransReceiveDate,
			LoadingDate,
			DONo,
			PickingListID,
			OrderID,
			DisposalID,
			WarehouseID,
			LocationID,
			Status,
			Company,
			UpdateBy,
			UpdateDate,
			CreateBy,
			CreateDate
			)
			SELECT 
			Barcode,
			DateBuild,
			BuildingNo,
			GT_Code,
			CuringDate,
			CuringCode,
			ItemID,
			Batch,
			QTY,
			Unit,
			PressNo,
			PressSide,
			MoldNo,
			TemplateSerialNo,
			CuredTireReciveDate,
			CuredTireLineNo,
			FinalReceiveDate,
			GateReceiveNo,
			XrayDate,
			XrayNo,
			QTechReceiveDate,
			WarehouseReceiveDate,
			WarehouseTransReceiveDate,
			LoadingDate,
			DONo,
			PickingListID,
			OrderID,
			DisposalID,
			WarehouseID,
			LocationID,
			Status,
			Company,
			UpdateBy,
			UpdateDate,
			?,
			?
			FROM InventTable
			WHERE Barcode = ?",
			[
				$_SESSION["user_login"], 
				$date, 
				$barcode_decode
			]
		);

		if (!$moveToReverseTable) {
			sqlsrv_rollback($conn);
			return "move to reverse table error";
		}

		$updateInventTable = sqlsrv_query(
			$conn,
			"UPDATE InventTable 
			SET CuringDate = null,
			CuringCode = null,
			ItemID = null,
			PressNo = null,
			PressSide = null,
			MoldNo = null,
			TemplateSerialNo = null,
			UpdateBy = ?,
			UpdateDate = ?,
			DisposalID = ?,
			WarehouseID = ?,
			LocationID = ?,
			Status = 5, -- Hold
			Batch = ?
			WHERE Barcode = ?",
			[
				$_SESSION["user_login"],
				$date,
				$get_location[0]["DisposalReverse"],
				$get_location[0]["WarehouseReverseReceive"],
				$getWarehouseAndLocationToHold[0]["ReverseReceiveLocation"],
				$week,
				$barcode_decode
			]
		);

		if (!$updateInventTable) {
			sqlsrv_rollback($conn);
			return "update invent table error";
		}

		// Generate trans id
		$trans_id = Utils::genTransId($barcode_decode);

		// insert trans move out
		$trans_move_out = sqlsrv_query(
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
				AuthorizeBy
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?
			)",
			[
				$trans_id . 1,
				$barcode_decode,
				$get_barcode_info[0]["ItemID"],
				$get_barcode_info[0]["Batch"],
				13, // reverse
				null, // defect
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				-1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				2, // docs type = issue
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"],
				$auth
			]
		);

		if (!$trans_move_out) {
			sqlsrv_rollback($conn);
			return "trans move out error";
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
				Shift,
				AuthorizeBy
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?
			)",
			[
				$trans_id . 2,
				$barcode_decode,
				$get_barcode_info[0]["GT_Code"],
				$week,
				10, // Hold
				// $get_location[0]["DisposalReverse"],
				$defect,
				$get_location[0]["WarehouseReverseReceive"],
				$getWarehouseAndLocationToHold[0]["ReverseReceiveLocation"],
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"],
				$auth
			]
		);

		if (!$trans_move_in) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
			// return sqlsrv_errors();
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

		// Move in onhand 
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
				$get_barcode_info[0]["GT_Code"],
				$get_location[0]["WarehouseReverseReceive"],
				$getWarehouseAndLocationToHold[0]["ReverseReceiveLocation"],
				$week,
				$_SESSION["user_company"],
				$get_barcode_info[0]["GT_Code"],
			  $get_location[0]["WarehouseReverseReceive"],
				$getWarehouseAndLocationToHold[0]["ReverseReceiveLocation"],
				$week,
				1, // qty
				$_SESSION["user_company"]
			]
		);

		// echo Database::errors();
		if (!$move_in_onhand) {
			sqlsrv_rollback($conn);
			return "move in onhand error.";
		}
			
		sqlsrv_commit($conn);
		return 200;
	}

	public function completeIssue($journalId)
	{
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$isEmpty = Sqlsrv::hasRows(
			$conn,
			"SELECT InventJournalID FROM InventJournalTrans
			WHERE InventJournalID = ?",
			[$journalId]
		);

		if ($isEmpty === false) {
			return "ไม่มีรายการ";
		}

		$update = Sqlsrv::update(
			$conn,
			"UPDATE InventJournalTable 
			SET Status = 3, -- Complete
			CompleteBy = ?,
			CompleteDate = ?
			WHERE InventJournalID = ?",
			[$_SESSION["user_login"], $date, $journalId]
		);

		if ($update) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 404;
		}
	}

}	// End