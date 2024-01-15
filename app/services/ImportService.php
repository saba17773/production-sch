<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class ImportService
{
	public function isCureTireExist($curetire_code)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT ID 
			FROM CureCodeMaster 
			WHERE ID = ?",
			[
				$curetire_code
			]
		);
	}

	public function isTopTurnChange($curetire_code, $rate12, $rate24)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT ID 
			FROM CureCodeMaster 
			WHERE ID = ? 
			AND rate12 = ?
			AND rate24 = ?",
			[
				$curetire_code,
				$rate12,
				$rate24
			]
		);
	}

	public function updateTopTurn($curetire_code, $rate12, $rate24)
	{
		$conn = Database::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE CureCodeMaster 
			SET rate12 = ?,
			rate24 = ?
			WHERE ID = ? 
			",
			[
				$rate12,
				$rate24,
				$curetire_code
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function createNewCureCode($curetire_code, $description, $item, $greentire)
	{
		$conn = Database::connect();
		$query = Sqlsrv::insert(
			$conn,
			"INSERT INTO CureCodeMaster(ID,Name,ItemID,GreentireID,Company,rate12,rate24)
			VALUES(?, ?, ?, ?, ?, ?, ?)",
			[
				strtoupper($curetire_code),
				$description,
				$item,
				$greentire,
				$_SESSION["user_company"],
				0,
				0
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function updateCureCode($curetire_code, $description, $item, $greentire)
	{
		$conn = Database::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE CureCodeMaster 
			SET Name = ?,
			ItemID = ?,
			GreentireID = ?
			WHERE ID = ? 
			",
			[
				$description,
				strtoupper($item),
				strtoupper($greentire),
				strtoupper($curetire_code)
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}
}