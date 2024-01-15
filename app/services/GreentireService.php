<?php

namespace App\Services;

use App\Components\Database;
use App\Components\Security;
use App\Components\Utils;
use Wattanar\Sqlsrv;
use App\Models\Location;
use App\Models\InventTable;
use App\Models\InventTrans;
use App\Models\Onhand;

class GreentireService
{

	public function isExist($greentire_code)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT GCM.ID 
			FROM GreentireCodeMaster GCM
			WHERE GCM.ID = ?",
			[$greentire_code]
		);
	}

	public function dateDifference($date_1 , $date_2 , $differenceFormat = '%i' )
	{
	    $datetime1 = date_create($date_1);
	    $datetime2 = date_create($date_2);
	    
	    $interval = date_diff($datetime1, $datetime2);
	    
	    return $interval->format('%i.%s');
	}

	public function receive($barcode, $building_no, $gt_code, $weight)
	{
		$user_login = $_SESSION["user_login"];
		$user_company = $_SESSION["user_company"];
		$user_warehouse = $_SESSION["user_warehouse"];
		$user_location = $_SESSION["user_location"];

		// $weightPoint = substr($weight, -2);
		// $weight = str_replace($weightPoint, ".".$weightPoint, $weight);

		$date = date("Y-m-d H:i:s");
		// $ddate = new \DateTime();
		// $week = date("Y") . "-" . $ddate->format("W");
		$week = (new Utils)->getWeek($date);

		if ($week === '2017-30') {
			$week = '2017-31';
		}

		$barcode_decode = Security::_decode($barcode);

		$building_no = strtoupper($building_no);
		$gt_code = strtoupper($gt_code);

		

		$conn = Database::connect();

		if (self::isSkippingDelay() === false) {
			$datetime_lockbuild = sqlsrv_query(
				$conn,
				"SELECT LockBuild FROM BuildingMaster
				WHERE ID = ?",
				[$building_no]
			);

			$datetime_lockbuild = Sqlsrv::queryArray(
				$conn,
				"SELECT LockBuild FROM BuildingMaster
				WHERE ID = ?",
				[$building_no]
			);

			$date_diff = self::dateDifference(date('Y-m-d H:i:s'), $datetime_lockbuild[0]['LockBuild']);

			if ((float)$date_diff <= 1.59) {
				return "ต้องรอ 2 นาทีเพื่อทำรายการต่อไป";
			}
		}

		if (sqlsrv_begin_transaction($conn) === false) {
			return "ไม่สามารถเชื่อต่อฐานข้อมูลได้";
		}

		$insert_lockbuild = sqlsrv_query(
			$conn,
			"UPDATE BuildingMaster 
			SET LockBuild = ?
			WHERE ID = ?",
			[date('Y-m-d H:i:s'), $building_no]
		);

		if (!$insert_lockbuild) {
			sqlsrv_rollback($conn);
			return "Update lock build failed!";
		}

		$_location = new Location;
		$_location->ID = $_SESSION['user_location'];
		// get user location
		$get_location = $_location->getUserLocation();

		$_inventtable = new InventTable;
		$_inventtable->Barcode = $barcode_decode;
    $_inventtable->DateBuild = $date;
    $_inventtable->Batch = $week;
    $_inventtable->BuildingNo = $building_no;
    $_inventtable->GT_Code = $gt_code;
    $_inventtable->QTY = 1;
    $_inventtable->Unit = 1;
    $_inventtable->DisposalID = $get_location[0]["DisposalID"];
    $_inventtable->WarehouseID = $get_location[0]["WarehouseID"];
    $_inventtable->LocationID = $get_location[0]["ReceiveLocation"];
    $_inventtable->Status = 1;
    $_inventtable->Company = $_SESSION['user_company'];
    $_inventtable->UpdateBy = $_SESSION['user_login'];
    $_inventtable->UpdateDate = $date;
    $_inventtable->CreateBy = $_SESSION['user_login'];
    $_inventtable->CreateDate = $date;
    $_inventtable->Weight = $weight;

    $create_inventtable = sqlsrv_query(
      $conn,
      "INSERT INTO InventTable(
          Barcode, DateBuild, Batch, BuildingNo, GT_Code,
          QTY, Unit, DisposalID, WarehouseID, LocationID, 
          Status, Company, UpdateBy, UpdateDate, CreateBy,
          CreateDate, Weight
      )VALUES(
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?, ?
      )",
      [
          $_inventtable->Barcode, 
          $_inventtable->DateBuild,
          $_inventtable->Batch,
          $_inventtable->BuildingNo,
          $_inventtable->GT_Code,
          $_inventtable->QTY,
          $_inventtable->Unit,
          $_inventtable->DisposalID,
          $_inventtable->WarehouseID,
          $_inventtable->LocationID,
          $_inventtable->Status,
          $_inventtable->Company,
          $_inventtable->UpdateBy,
          $_inventtable->UpdateDate,
          $_inventtable->CreateBy,
          $_inventtable->CreateDate,
          $_inventtable->Weight
      ]
  	);

    if (!$create_inventtable) {
			sqlsrv_rollback($conn);
			return "insert invent table error.";
		}

		$_inventtrans = new InventTrans;
		$_inventtrans->TransID = Utils::genTransId($barcode_decode) . 1;
		$_inventtrans->Barcode = $barcode_decode;
		$_inventtrans->CodeID = $gt_code;
		$_inventtrans->Batch = $week;
		$_inventtrans->DisposalID = $get_location[0]["DisposalID"];
		$_inventtrans->DefectID = null;
		$_inventtrans->WarehouseID = $get_location[0]["WarehouseID"];
		$_inventtrans->LocationID = $get_location[0]["ReceiveLocation"];
		$_inventtrans->QTY = 1;
		$_inventtrans->UnitID = 1;
		$_inventtrans->DocumentTypeID = 1;
		$_inventtrans->Company = $_SESSION['user_company'];
		$_inventtrans->CreateBy = $_SESSION['user_login'];
		$_inventtrans->CreateDate = $date;
		$_inventtrans->Shift = $_SESSION["Shift"];

  	$create_inventtrans = sqlsrv_query(
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
            InventJournalID,
            AuthorizeBy,
            ScrapSide,
            RefDocId
        ) VALUES(
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?
        )",
        [
            $_inventtrans->TransID,
            $_inventtrans->Barcode,
            $_inventtrans->CodeID,
            $_inventtrans->Batch,
            $_inventtrans->DisposalID,
            $_inventtrans->DefectID,
            $_inventtrans->WarehouseID,
            $_inventtrans->LocationID,
            $_inventtrans->QTY,
            $_inventtrans->UnitID,
            $_inventtrans->DocumentTypeID,
            $_inventtrans->Company,
            $_inventtrans->CreateBy,
            $_inventtrans->CreateDate,
            $_inventtrans->Shift,
            $_inventtrans->InventJournalID,
            $_inventtrans->AuthorizeBy,
            $_inventtrans->ScrapSide,
            $_inventtrans->RefDocId
        ]
    );

		if (!$create_inventtrans) {
			sqlsrv_rollback($conn);
			return "insert invent trans error.";
		}

		$_onhand = new Onhand;
		$_onhand->QTY = 1;
		$_onhand->WarehouseID = $get_location[0]["WarehouseID"];
		$_onhand->LocationID = $get_location[0]["ReceiveLocation"];
		$_onhand->Company = $_SESSION['user_company'];
		$_onhand->CodeID = $gt_code;
		$_onhand->Batch = $week;

		if ($_onhand->isItemExist()) {

			$update_onhand = sqlsrv_query(
        $conn,
        "UPDATE Onhand 
        SET QTY += ?
        WHERE CodeID = ?
        AND WarehouseID = ?
        AND LocationID = ?
        AND Batch = ?
        AND Company = ?",
        [
            $_onhand->QTY,
            $_onhand->CodeID,
            $_onhand->WarehouseID,
            $_onhand->LocationID,
            $_onhand->Batch,
            $_onhand->Company
        ]
      );

			if (!$update_onhand) {
				sqlsrv_rollback($conn);
				return "update onhand error.";
			}
		} else {

			$create_onhand = sqlsrv_query(
        $conn,
        "INSERT INTO Onhand(
            CodeID,
            WarehouseID,
            LocationID,
            Batch,
            QTY,
            Company
        ) VALUES(?, ?, ?, ?, ?, ?)",
        [
            $_onhand->CodeID,
            $_onhand->WarehouseID,
            $_onhand->LocationID,
            $_onhand->Batch,
            $_onhand->QTY,
            $_onhand->Company
        ]
      );

			if (!$create_onhand) {
				sqlsrv_rollback($conn);
				return "create onhand error.";
			}
		}

		sqlsrv_commit($conn);
		return 200;
	}
	
	public function all()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
				$conn,
				"SELECT G.*, ITM.NameTH [ItemName] FROM GreentireCodeMaster G
				left join ItemMaster ITM ON G.ItemNumber = ITM.ID 
				ORDER BY G.ID ASC"
			);

		return $query;
	}

	public function create($id, $description)
	{
		if (self::isExist($id) === true) {
			return false;
		}

		$conn = Database::connect();
		$query = Sqlsrv::insert(
				$conn,
				"INSERT INTO GreentireCodeMaster(ID, Name, Company) VALUES (?, ?, ?)",
				[$id, $description, $_SESSION["user_company"]]
			);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function update($id, $description, $_id)
	{

		$conn = Database::connect();
		$query = Sqlsrv::update(
				$conn,
				"UPDATE GreentireCodeMaster
				SET	Name = ?
		        WHERE ID = ?",
				[
					$description,
					$id
				]
			);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id)
	{
		$conn = Database::connect();
		$q = Sqlsrv::delete(
			$conn,
			"DELETE FROM GreentireCodeMaster GCM
			LEFT JOIN InventTable IT ON IT.GT_Code = GCM.ID
			WHERE GCM.ID = ?
			AND IT.GT_Code IS NULL",
			[$id]
		);
		return $q;
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

	public function updateLockBuild($build_id)
	{
		$conn = Database::connect();
		
		$q = sqlsrv_query(
			$conn,
			"UPDATE BuildingMaster 
			SET LockBuild = ?
			WHERE ID = ?",
			[
				date('Y-m-d H:i:s'),
				$build_id
			]
		);

		if ($q) {
			return true;
		} else {
			return false;
		}
	}

	public function mapItem($_id, $item)
	{
		$conn = Database::connect();

		$query = sqlsrv_query(
			$conn,
			"UPDATE GreentireCodeMaster 
			SET ItemNumber = ? 
			WHERE ID = ?",
			[
				$item,
				$_id
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}
}