<?php

namespace App\Controllers;

use App\Components\Database;
use App\Services\GreentireService;
use App\Services\BuildingService;
use App\Services\BarcodeService;

class GreentireController
{
	public function all()
	{
		echo (new GreentireService)->all();
	}

	public function create()
	{
		$id = trim(filter_input(INPUT_POST, "id"));
		$description = trim(filter_input(INPUT_POST, "description"));
		$form_type = trim(filter_input(INPUT_POST, "form_type"));
		$_id = trim(filter_input(INPUT_POST, "_id"));

		if ($form_type == "create") {
			if((new GreentireService)->create($id, $description) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}

		if ($form_type == "update") {
			if((new GreentireService)->update($id, $description, $_id) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}

		if ($form_type == "map_item") {
			$item = $_POST["item"];
			if((new GreentireService)->mapItem($_id, $item) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
			} else {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			}
		}
	}

	public function receive()
	{
		$building_code = trim(filter_input(INPUT_POST, "building_code"));
		$greentire_code = trim(filter_input(INPUT_POST, "greentire_code"));
		$barcode = trim(filter_input(INPUT_POST, "barcode"));
		// $weight = trim(filter_input(INPUT_POST, "weight"));
		$weight = 0;

		// Check Building Code
		if ((new BuildingService)->isExist($building_code) === false) {
			exit(json_encode(["status" => 404, "message" => "Building MC. ไม่มีอยู่ในระบบ"]));
		}

		// Check Green Tire Code
		if ((new GreentireService)->isExist($greentire_code) === false) {
			exit(json_encode(["status" => 404, "message" => "Greentire code ไม่มีอยู่ในระบบ"]));
		}

		// Check Barcode is in ranged
		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ไม่ถูกต้อง"]));
		}

		// Check barcode in invent table
		if ((new BarcodeService)->isExistInventTable($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode number มีอยู่แล้วในระบบ."]));
		}

		// Insert invent table
		$invent_table = (new GreentireService)->receive($barcode, $building_code, $greentire_code, $weight);

		if ($invent_table == 200) {
			exit(json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $invent_table]));
		}
	}

	public function delete()
	{
		$id = trim(filter_input(INPUT_POST, "id"));
		if ((new GreentireService)->delete($id)) {
			echo json_encode(["status" => 200, "message" => "ลบสำเร็จ"]);
		} else {
			echo json_encode(["status" => 404, "message" => "ลบไม่สำเร็จ"]);
		}
	}
}