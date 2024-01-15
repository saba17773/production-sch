<?php

namespace App\Controllers;

use App\Components\Database;
use App\Services\PressitemService;

class PressitemController{

	public function all()
	{
		$id = filter_input(INPUT_GET, "id");
		echo (new PressitemService)->all($id);

	}

	public function create()
	{



		// method1
		// $press = $_POST["pressid"];//filter_input(INPUT_POST, 'pressid');

		// if (isset($_POST["select_item"])) {
		// 	$select_item = $_POST["select_item"];
		// } else {
		// 	$select_item = null;
		// }

		// $getitem = (new PressitemService)->getitem($press);
		// $count = count($getitem)-1;

		// for($i=0;$i<=$count;$i++)
		// {
		// 		$finditem = $getitem[$i][0];
		// 		if(!in_array($finditem,$select_item))
		// 		{
		// 			$item_delete = [$finditem];
		// 		}

		// }

	 // 	$create =(new PressitemService)->create($press,$select_item,$item_delete);
		
		// if($create == 200)
		// {
		// 		echo json_encode(["status" => 200, "message" => "บันทึกข้อมูล" . $press . "สำเร็จ"]);
		// }
		// else{
		// 		echo json_encode(["status" => 404, "message" => $create]);
		// }

		//end method1

		// $press = $_POST["pressid"];//filter_input(INPUT_POST, 'pressid');

		$press = filter_input(INPUT_POST, 'pressid');
		$process = filter_input(INPUT_POST, 'process');
		$arr = $_POST["item"];


		$create =(new PressitemService)->create($press,$process,$arr);
		
		if($create == 200)
		{
				echo json_encode(["status" => 200, "message" => "บันทึกข้อมูล" . $press . "สำเร็จ"]);
		}
		else{
				echo json_encode(["status" => 404, "message" => $create]);
		}

	}


}


?>
