<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class PressService
{
	public function all()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM PressMaster"
		);
		return $query;
	}

	public function create($id, $desc)
	{
		if (self::checkExist($id) === true) {
			return false;
		}

		$conn = Database::connect();
		$query = Sqlsrv::insert(
			$conn,
			"INSERT INTO PressMaster(ID, Description, Company) VALUES (?, ?, ?)",
			[$id, $desc, $_SESSION["user_company"]]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function update($id, $desc)
	{

		$conn = Database::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE PressMaster
			SET	Description = ?
	        WHERE ID = ?",
			[
				$desc,
				$id
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}


	public function checkExist($id)
	{
		$id = trim($id);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM PressMaster
			WHERE ID = ?",
			[$id]
		);
	}

	public function delete($id)
	{
		$conn = Database::connect();
		$q = Sqlsrv::delete(
			$conn,
			"DELETE FROM PressMaster PM
			LEFT JOIN InventTable IT ON PM.ID = IT.PressNo
			WHERE PM.ID = ?
			AND IT.PressNo IS NULL",
			[$id]
		);

		return $q;
	}

	//j modify
	public function allBDF()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT 
					BDF
					,'01-12'[No]
			FROM(
			SELECT	P.ID
					,P.Description
					,LEFT(P.ID, 1)[BDF]
			FROM PressMaster P 
			WHERE LEFT(P.ID, 1) IN ('B','D','F','H','J','L')
			)Z
			GROUP BY
			Z.BDF"
		);
		return $query;
	}
	public function allBDFA()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT 
					BDF
					,'01-12'[No]
			FROM(
			SELECT	P.ID
					,P.Description
					,LEFT(P.ID, 1)[BDF]
			FROM PressMaster P 
			WHERE LEFT(P.ID, 1) IN ('A','C','E','G','I','K')
			)Z
			GROUP BY
			Z.BDF"
		);
		return $query;
	}
	public function allABCDEF()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT 
					BDF
					,'01-12'[No]
			FROM(
			SELECT	P.ID
					,P.Description
					,LEFT(P.ID, 1)[BDF]
			FROM PressMaster P 
			/*WHERE LEFT(P.ID, 1) IN ('A','C','E')*/
			)Z
			GROUP BY
			Z.BDF"
		);
		return $query;
	}
	public function allday()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT *
			FROM TimeMaster 
			WHERE TimeType=1"
		);
		return $query;
	}
	public function allnight()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT *
			FROM TimeMaster 
			WHERE TimeType=2"
		);
		return $query;
	}
}