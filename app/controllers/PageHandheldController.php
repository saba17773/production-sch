<?php 
namespace App\Controllers;

use App\Components\Security;
use App\Components\Authentication;
use App\Components\Utils;

class PageHandheldController
{

	private $auth;
	private $secure;
	private $utils;

	public function __construct()
	{
		$this->auth = new Authentication;
		$this->secure = new Security;
		$this->utils = new Utils;

		if ($this->auth->isLogin() === false) {
			renderView("page/handheld_login");
			exit;
		}
	}

	public function handheldLogin()
	{
		renderView("page/handheld_login");
	}

	public function curingHandheld()
	{
		renderView("page/handheld_curing");
	}

	public function curingHandheldWithoutSerial()
	{
		renderView("page/handheld_curing_without_serial");
	}
}