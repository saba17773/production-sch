<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class CureTireService
{
	public function all()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson($conn, "SELECT * FROM CureCodeMaster");
	}

	public function create($id_name,$des_name,$item_name,$gt_name)
	{
		
		$conn = Database::connect();
		if (self::checkWhExist($des_name,$item_name,$gt_name,$id_name) === false) {
			$query = Sqlsrv::insert(
					$conn,
					"INSERT INTO CureCodeMaster(
						ID,Name,ItemID,GreentireID,Company
					) VALUES (
						?,?,?,?,?
					)",
					[$id_name,$des_name,$item_name,$gt_name,$_SESSION["user_company"]]
				);
			if (!$query) {
				return false;
			}
			return true;
		} else {
			return false;
		}
	}

	public function update($des_name,$item_name,$gt_name,$id_name)
	{
		$id_name = trim($id_name);

		$conn = Database::connect();
		
		if (self::checkWhExist($des_name,$item_name,$gt_name,$id_name) === false) {

			$query = Sqlsrv::update(
				$conn,
				"UPDATE CureCodeMaster 
			     SET  Name=?,
				      ItemID=?,
				      GreentireID=?,
				      Company=?
				WHERE ID =?",
				[$des_name,$item_name,$gt_name,$_SESSION["user_company"],$id_name]
			);

			if (!$query) {
				return false;
			}

			return true;

		} else {

			return false;

		}
	}

	public function checkWhExist($des_name,$item_name,$gt_name,$id_name)
	{
		$id_name = trim($id_name);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM CureCodeMaster 
				WHERE Name = ? AND ItemID =? AND GreentireID =? AND ID =?",
				[$des_name,$item_name,$gt_name,$id_name]
			);
	}

	public function isSetDontCheckSerial($curecode) {
		$conn = Database::connect();
		return sqlsrv_has_rows(sqlsrv_query(
			$conn,
			"SELECT CCM.ItemID FROM CureCodeMaster CCM
			LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
			WHERE CCM.ID = ? AND IM.CheckSerial = 1",
			[
				$curecode
			]
		));
	}
}