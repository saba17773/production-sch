<?php
namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;
// use App\Components\Security;

class PressitemService{


	public function all($id)
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
				$conn,
				"SELECT I.ID [ITEMID]
				,I.ItemName [NAME]
				FROM ProductionSchItemMaster I
				JOIN PRESSITEMMASTER P
				ON I.ID = P.Itemid
				WHERE P.Pressid = ?",
				array($id)
			);
		return $query;
	}

	public function getitem($press)
	{
			$conn = Database::connect();
			if (sqlsrv_begin_transaction($conn) === false) {
				return 'can\'t connect database!';
			}
			$isrow = Sqlsrv::queryArray($conn,"SELECT Itemid
															 FROM PressItemMaster WHERE Pressid = ?
															 AND companyid=?",
															 [$press,'dsl']);
			return $isrow;
	}

	// public function create($press,$item_create,$item_delete)
	// {
	// 		$conn = Database::connect();
	// 		if (sqlsrv_begin_transaction($conn) === false) {
	//     	return 'can\'t connect database!';
	//     }

	// 		$check = true;

	// 		foreach ($item_create as $key)
	// 		{
	// 				if(Sqlsrv::hasRows($conn,"SELECT *
	// 																 FROM PressItemMaster WHERE Pressid = ?
	// 																 AND Itemid = ?
	// 																 AND companyid=?",
	// 																 [$press,$key,'dsl'])===false)
	// 				{
	// 						$query = Sqlsrv::insert(
	// 															$conn,
	// 															"INSERT INTO PressItemMaster(Itemid,Pressid,companyid) VALUES (?, ?, ?)",
	// 															[$key,$press,'dsl']);
	// 						if(!$query)
	// 						{
	// 								$check = false;
	// 						}
	// 						else {

	// 						}

	// 				}

	// 		}

	// 		if(isset($item_delete))
	// 		{
	// 				foreach ($item_delete as $d)
	// 				{
	// 						$query = Sqlsrv::delete(
	// 															$conn,
	// 															"DELETE FROM  PressItemMaster
	// 															WHERE Pressid = ?
	// 															AND Itemid = ?
	// 															AND companyid=? ",
	// 															[$press,$d,'dsl']);
	// 						if(!$query)
	// 						{
	// 								$check =false;
	// 						}
	// 				}
	// 		}

	// 		if($check === false)
	// 		{
	// 				sqlsrv_rollback($conn);
	// 				return "Create failed.";
	// 		}
	// 		else
	// 		{
	// 				sqlsrv_commit($conn);
	// 				return 200;
	// 		}


	// }

	public function create($press,$process,$arr)
	{
		$conn = Database::connect();
			if (sqlsrv_begin_transaction($conn) === false) {
	    	return 'can\'t connect database!';
	    }


	    $check = true;

	    if($process == "create")
	    {

	    	foreach ($arr as $a) {

	    		if(Sqlsrv::hasRows($conn,"SELECT *
	    			 					 FROM PressItemMaster
	    			 					 WHERE Pressid = ?
										 AND Itemid = ?
										 AND companyid=?",[$press,$a,'dsi'])===false)
	    		{
	    			$query = Sqlsrv::insert(
											$conn,
											"INSERT INTO PressItemMaster(Pressid,Itemid,companyid) 
											VALUES (?, ?, ?)",
											[$press,$a,'dsi']);

					if(!$query)
					{
							$check = false;
					}


	    		}
	    	}

	    }else{

	    	foreach ($arr as $a) {
	    		if(Sqlsrv::hasRows($conn,"SELECT *
	    			 					 FROM PressItemMaster
	    			 					 WHERE Pressid = ?
										 AND Itemid = ?
										 AND companyid=?",[$press,$a,'dsi']))
	    		{
	    			$query = Sqlsrv::delete(
											$conn,
											"DELETE FROM  PressItemMaster
											WHERE Pressid = ?
											AND Itemid = ?
											AND companyid=? ",
											[$press,$a,'dsi']);
						if(!$query)
						{
								$check = false;
						}


	    		}
	    	}
	    }

    	if($check === false)
		{
				sqlsrv_rollback($conn);
				return "Create failed.";
		}
		else
		{
				sqlsrv_commit($conn);
				return 200;
		}



	}



}

?>
