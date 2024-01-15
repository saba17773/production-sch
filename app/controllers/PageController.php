<?php

namespace App\Controllers;

use App\Components\Security;
use App\Components\Authentication;
use App\Components\Utils;
use App\Controllers\UserController;

class PageController
{
	public function __construct()
	{
		$this->auth = new Authentication;
		$this->secure = new Security;
		$this->utils = new Utils;
		$this->user = new UserController;

		if ($this->auth->isLogin() === false) {
			renderView('page/login');
			exit;
		}

		$this->errorText = $this->user->getDefaultPage();
	}

	public function index()
	{
		if ($this->auth->isLogin() === false) {
			renderView("page/login");
		} else {
			if ($this->secure->isAccess() === false) {
				exit(renderView('page/404'));
			} else {
				renderView("page/home");
			}
		}
	}

	public function welcome()
	{
		$detect = new \Mobile_Detect;

		if ($detect->isMobile()) {
			header("Location:" . $this->user->getDefaultLink());
		} else {
			header("Location:" . root . '/home');
		}

	}

	public function desktopLogin()
	{
		renderView("page/login");
	}

	public function tracking()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/tracking");
	}

	public function masterWarehouse()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/warehouse");
	}

	public function masterCureTireCode()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagemaster/curetire");
	}

	public function masterLocation()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagemaster/location");
	}

	public function masterDisposal()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/disposal");
	}

	public function masterMenu()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/menu");
	}

	public function masterPermission()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/permission");
	}

	public function masterDepartment()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/department");
	}

	public function barcodePrinting()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/barcode_printing");
	}

	public function barcodeCuring()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/barcode_curing");
	}

	public function templateRegister()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/template_register");
	}

	public function masterCompany()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/company");
	}

	public function user()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/user");
	}

	public function greentireIncoming()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/greentire_incoming");
	}

	public function xray()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/xray");
	}

	public function loadingDesktop()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/loading_desktop");
	}

	public function loadingMobile()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/loading_mobile");
	}

	public function stockTaking()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/stocktaking");
	}

	public function masterDefect()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/defect");
	}

	public function masterGreentireCode()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/greentire_code");
	}

	public function masterBuilding()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/building");
	}

	public function masterPress()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/press");
	}

	public function hold()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/hold");
	}

	public function repair()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/repair");
	}

	public function scarp()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/scarp");
	}

	public function masterMold()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/mold");
	}

	public function ReportOnhand()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("pagemaster/report_onhand");
	}

	public function xrayIssueWH()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/xray_issue_wh");
	}

	public function warehouseIncoming()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/warehouse_incoming");
	}

	public function unhold()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/unhold");
	}

	public function unrepair()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("page/unrepair");
	}

	public function movementIssue()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/movement_issue");
	}

	public function landing()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		$detect = new \Mobile_Detect;
		if ($detect->isMobile()) {
			renderView("page/landing");
		} else {
			renderView("page/home");
		}
	}

	public function warehouseType()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/warehouse_type");
	}

	public function authorize()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/authorize");
	}

	public function employee()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/employee");
	}

	public function finalIncoming()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/final_incoming");
	}

	public function movementType()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/movement_type");
	}

	public function requsitionNote()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/requsition_note");
	}

	public function movementIssueNew()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/movement_issue_new");
	}

	public function movementReverse()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/movement_reverse");
	}

	public function finalReturn()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/final_return");
	}

	public function actions()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/actions");
	}

	public function reportGreentireHold()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/report_greentire_hold");
	}

	public function reportFinalHold()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/report_final_hold");
	}

	public function adjust()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('page/adjust');
	}

		public function ReportBuilding()
	{
		renderView("pagemaster/building");
	}

	public function ReportInternal()
	{
		renderView("pagemaster/internal");
	}

	public function ReportCuring()
	{
		renderView("pagemaster/curing");
	}

	public function ReportGreentireInventory()
	{
		renderView("pagemaster/greentireinventory");
	}

	public function ReportWarehousesent()
	{
		renderView("pagemaster/warehousesent");
	}

	public function ReportWarehouserecive()
	{
		renderView("pagemaster/warehouserecive");
	}

	public function ReportCuringPress()
	{
		renderView("pagemaster/curingpress");
	}
	// J Report
	public function ReportCureInventory()
	{
		renderView("pagemaster/cureinventory");
	}

	public function ReportWIPFinalFG()
	{
		renderView("pagemaster/wipfinal");
	}

	public function tracking_v2()
	{
		renderView("page/tracking_v2");
	}

	public function tracking_v3()
	{
		renderView("page/tracking_v3");
	}

	public function pressitem()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/pressitem_master_v2");
	}

}
