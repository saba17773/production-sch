<?php  
namespace App\Controllers;

use App\Services\CureTireService;

class CureTireController
{
	public function all()
	{
		echo (new CureTireService)->all();
	}

	public function create()
	{
		$id_name = filter_input(INPUT_POST, "id_name");
		$des_name = filter_input(INPUT_POST, "des_name");
		$item_name = filter_input(INPUT_POST, "item_name");
		$gt_name = filter_input(INPUT_POST, "gt_name");	
		$form_type = filter_input(INPUT_POST, "form_type");

		if ($form_type == "create") {
			$c = (new CureTireService)->create($id_name,$des_name,$item_name,$gt_name);
			if($c=== false) {
				echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกได้"]);
				// var_dump(sqlsrv_errors());
				// exit;
			} else {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);	
			}
			
		}

		if ($form_type == "update") {
			if((new CureTireService)->update($des_name,$item_name,$gt_name,$id_name) === false) {
				echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกได้"]);
			} else {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			}
			
		}

	}
}