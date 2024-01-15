<?php

namespace App\Controllers;

use App\Services\InventService;

class InventController
{
	public function __construct()
	{
		$this->invent = new InventService;
	}

	public function allInventTable()
	{
		echo (new InventService)->allInventTable();
	}

	public function transDetail($barcode)
	{
		echo (new InventService)->transDetail($barcode);
	}

	public function countReceiveToWarehouseFromFinal()
	{
		echo json_encode([
			"count" => count(json_decode($this->invent->countReceiveToWarehouseFromFinal()))
		]);
	}
}