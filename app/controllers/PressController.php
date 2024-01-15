<?php

namespace App\Controllers;

use App\Services\PressService;

class PressController
{
	public function all()
	{
		echo (new PressService)->all();
	}

	public function create()
	{
		$id = filter_input(INPUT_POST, "id");
		$desc = filter_input(INPUT_POST, "desc");
		$form_type = filter_input(INPUT_POST, "form_type");
		

		if ($form_type == "create") {
			if((new PressService)->create($id, $desc) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}
			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}

		if ($form_type == "update") {
			if((new PressService)->update($id, $desc) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}
	}

	public function delete()
	{
		$id = filter_input(INPUT_POST, "id");
		if ((new PressService)->delete($id)) {
			echo json_encode(["status" => 200, "message" => "ลบสำเร็จ"]);
		} else {
			echo json_encode(["status" => 404, "message" => "ลบไม่สำเร็จ"]);
		}
	}

	//j modify
	public function allBDF()
	{
		echo (new PressService)->allBDF();
	}
	public function allBDFA()
	{
		echo (new PressService)->allBDFA();
	}
	public function allABCDEF()
	{
		echo (new PressService)->allABCDEF();
	}
	public function allday()
	{
		echo (new PressService)->allday();
	}
	public function allnight()
	{
		echo (new PressService)->allnight();
	}
}