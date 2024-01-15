<?php

namespace App\V2\TypeTireMaster;

use App\V2\TypeTireMaster\TypeTireAPI;
use App\V2\Helper\Helper;
use App\Components\Utils;
use App\Components\Security;
use App\Components\Authentication;

class TypeTireController
{
	public function __construct()
	{
		$this->auth = new Authentication;
		$this->secure = new Security;
	}


	public function master()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('page/typetire_master');
	}

	public function bindGridMain()
	{
		echo (new TypeTireAPI)->bindGridMain();
	}

	public function bindGridLine($groupid)
	{
		echo (new TypeTireAPI)->bindGridLine($groupid);
	}

	public function insertGroup()
	{
		$groupdesc = filter_input(INPUT_POST, "txt_new_groupdesc");
		$insert = (new TypeTireAPI)->insertGroup($groupdesc);
		if ($insert === true) {
			return json_encode([
				'result' => true,
				'message' => 'Insert successful!'
			]);
		} else {
			return json_encode([
				'result' => false,
				'message' => $insert
			]);
		}
	}

	public function updateGroup()
	{
		$grpid = filter_input(INPUT_POST, "txt_edit_groupid");
		$grpdesc = filter_input(INPUT_POST, "txt_edit_groupdesc");
		$grpsort = filter_input(INPUT_POST, "txt_edit_sortby");

		$update = (new TypeTireAPI)->updateGroup($grpid, $grpdesc, $grpsort);
		if ($update === true) {
			return json_encode([
				'result' => true,
				'message' => 'Update successful!'
			]);
		} else {
			return json_encode([
				'result' => false,
				'message' => $update
			]);
		}
	}

	public function insertDetail()
	{
		$dDesc = $_POST['dDesc'];
		$dSize = $_POST['dSize'];
		$dGrpId = $_POST['gId'];

		$insertDetail = (new TypeTireAPI)->insertDetail($dDesc, $dSize, $dGrpId);
		if ($insertDetail === true) {
			return json_encode([
				'result' => true,
				'message' => 'Insert successful!'
			]);
		} else {
			return json_encode([
				'result' => false,
				'message' => $insertDetail
			]);
		}
	}

	public function updateDetail()
	{
		//$dDesc,$dSize,$dSort,$dIdAuto
		$edDesc = $_POST['edDesc'];
		$edSize = $_POST['edSize'];
		$edSort = $_POST['edSort'];
		$edIdAuto = $_POST['edIdAuto'];

		$updateDetail = (new TypeTireAPI)->updateDetail($edDesc, $edSize, $edSort, $edIdAuto);
		if ($updateDetail === true) {
			return json_encode([
				'result' => true,
				'message' => 'Insert successful!'
			]);
		} else {
			return json_encode([
				'result' => false,
				'message' => $updateDetail
			]);
		}
	}

	public function curemaster()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('page/cure_master');
	}

	public function cureGridMain()
	{
		echo (new TypeTireAPI)->cureGridMain();
	}

	public function updatecure()
	{
		$grpid = filter_input(INPUT_POST, "txt_edit_groupid");
		$grpdesc = filter_input(INPUT_POST, "txt_edit_groupdesc");
		$grpsort = filter_input(INPUT_POST, "txt_edit_sortby");
		$active = $_POST['txt_active'];
		$update = (new TypeTireAPI)->updatecure($grpid, $grpdesc, $grpsort, $active);
		if ($update === true) {
			return json_encode([
				'result' => true,
				'message' => 'Update successful!'
			]);
		} else {
			return json_encode([
				'result' => false,
				'message' => $update
			]);
		}
	}

	public function insertCure()
	{
		$CurID = filter_input(INPUT_POST, "txt_CurID");
		$CureSize = filter_input(INPUT_POST, "txt_CureSize");
		$insert = (new TypeTireAPI)->insertCure($CurID, $CureSize);
		if ($insert === true) {
			return json_encode([
				'result' => true,
				'message' => 'Insert successful!'
			]);
		} else {
			return json_encode([
				'result' => false,
				'message' => $insert
			]);
		}
	}
}
