<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class MenuService
{
	public function all()
	{
		$conn = Database::connect();

		if ($_SESSION["user_login"] === 1) {
			$sql = "SELECT * FROM MenuMaster";
		} else {
			$sql = "SELECT * FROM MenuMaster WHERE Status = 1";
		}

		$query = Sqlsrv::queryJson(
				$conn,
				$sql 
			);
		return $query;
	}

	public function create($description, $link, $sort)
	{
		$conn = Database::connect();

		if (self::checkExist($description) === true) {
			return false;
		}

		$query = Sqlsrv::insert(
				$conn,
				"INSERT INTO MenuMaster(
					Link, Description, Status, Sort
				) VALUES (?, ?, ?, ?)",
				[$link, trim($description), 1, $sort]
			);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function update($id, $description, $link, $sort)
	{
		$conn = Database::connect();
		$query = Sqlsrv::update(
				$conn,
				"UPDATE MenuMaster
				SET Description = ?,
				Link = ?,
				Sort = ?
				WHERE ID = ?",
				[$description, $link, $sort, $id]
			);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function checkExist($description)
	{
		$conn = Database::connect();
		$query = Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM MenuMaster
				WHERE Description = ?",
				[trim($description)]
			);
		return $query;
	}

	public function getMenu($menu_id)
	{
		$conn = Database::connect();
		
		$q = Sqlsrv::queryJson(
			$conn,
			"SELECT Description, Link FROM MenuMaster
			WHERE ID IN ($menu_id) AND Status = 1
			ORDER BY Sort ASC"
		);

		if ($q) {
			return $q;
		} else {
			return false;
		}
		
	}
}