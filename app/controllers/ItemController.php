<?php

namespace App\Controllers;

use App\Services\ItemService;
use App\Components\Database;
use App\Models\Item;

class ItemController
{
	public function __construct() {
		$this->item = new Item;
	}

	public function all()
	{
		// var_dump((new ItemService)->all());
		echo (new ItemService)->all();
	}

	public function allBrand()
	{
		echo (new ItemService)->allBrand();
	}

	public function getItemSet() {
		echo $this->item->getItemSet();
	}

	public function getItemNormal() {
		echo $this->item->getItemNormal();
	}

	public function getItemGroupSM()
	{
		echo $this->item->getItemGroupSM();
	}
}