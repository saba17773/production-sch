<?php
set_time_limit(60);
session_start();
error_reporting(0);
ini_set('memory_limit', '512M');

define("root", '');
define("app_name", "Production-Scheduler");
define("barcode_prefix", "5");

require_once "./vendor/autoload.php";
// require_once "./libs/simple-php-captcha/simple-php-captcha.php";
require_once "./libs/excel-reader/SpreadsheetReader.php";
require_once "./functions.php";

// Capcha ...
// $_SESSION['captcha'] = simple_php_captcha();

$app = new \Wattanar\Router;

// Test
// $app->get("/test/building/barcode", "App\Controllers\BarcodeController::build_fix");
// $app->get("/test_whrec", "App\Controllers\TestController::whrec");
$app->get("/test_dev", "App\V2\Pallet\PalletAPI::getSeqNumberLPN");

// Page
$app->get("/", "App\Controllers\PageController::welcome");
$app->get("/home", "App\Controllers\PageController::index");
$app->get("/tracking", "App\Controllers\PageController::tracking_v2");
$app->get("/tracking_v3", "App\Controllers\PageController::tracking_v3");
$app->get("/final_login", "\App\Controllers\LoginController::finalLogin");
$app->get('/qa_reverse', '\App\Controllers\MovementController::qaReverse');

// $app->get("/tracking_v2", "App\Controllers\PageController::tracking_v2");
$app->get("/barcode/printing", "App\Controllers\PageController::barcodePrinting");
$app->get("/barcode/curing", "App\Controllers\PageController::barcodeCuring");
$app->get("/curing", "App\Controllers\PageHandheldController::curingHandheld");
$app->get("/curing_no_serial", "App\Controllers\PageHandheldController::curingHandheldWithoutSerial");
$app->get("/loading/desktop", "App\Controllers\PageController::loadingDesktop");
$app->get('/loading/mobile', 'App\Controllers\PageController::loadingMobile');
$app->get("/stocktaking", "App\Controllers\PageController::stockTaking");
$app->get("/xray/issue/wh", "App\Controllers\PageController::xrayIssueWH");
$app->get("/warehouse/incoming", "App\Controllers\PageController::warehouseIncoming");
$app->get("/template/register", "App\Controllers\PageController::templateRegister");
$app->get("/greentire/incoming", "App\Controllers\PageController::greentireIncoming");
$app->get("/xray/issue", "App\Controllers\PageController::xray");
$app->get("/movement/issue", "App\Controllers\PageController::movementIssue");
$app->get("/landing", "App\Controllers\PageController::landing");
$app->get("/warehouse_type", "App\Controllers\PageController::warehouseType");
$app->get("/final/incoming", "App\Controllers\PageController::finalIncoming");
$app->get("/movement/issue/new", "App\Controllers\PageController::movementIssueNew");
$app->get('/movement/reverse', "App\Controllers\PageController::movementReverse");
$app->get('/final_return', 'App\Controllers\PageController::finalReturn');
$app->get('/adjust', 'App\Controllers\PageController::adjust');
$app->get('/master/actions', 'App\Controllers\PageController::actions');
$app->get('/ismobile', "App\Components\Utils::isMobile");

// Barcode Generator
$app->get("/generator/([^/]+)", "App\Controllers\BarcodeController::generator");
$app->get("/generator/user/([^/]+)/([^/]+)/([^/]+)/([^/]+)", "App\Controllers\UserController::genUserBarcode");

// not A5
$app->get("/generator/greentire/([^/]+)", "App\Controllers\BarcodeController::genGreentireCode");
$app->get("/generator/building/([^/]+)", "App\Controllers\BuildingController::genBuildingCode");
// ======= //

// A5
$app->get("/generator/greentire/a5/([^/]+)", "App\Controllers\BarcodeController::genGreentireCodeA5");
$app->get("/generator/building/a5/([^/]+)", "App\Controllers\BuildingController::genBuildingCodeA5");
$app->get("/generator/curetire/a5/([^/]+)/([^/]+)", "App\Controllers\BarcodeController::genCuretireA5");
// ======= //

$app->get("/generator/curing/([^/]+)", "App\Controllers\CuringController::genCuringCode");
$app->get("/template/generator/([^/]+)/([^/]+)", "App\Controllers\TemplateController::generate");
$app->get("/serial/print/([^/]+)/([^/]+)", "App\Controllers\TemplateController::printSerial");
// Print Movement Issue
$app->get("/movement_issue/print/([^/]+)", "App\Controllers\MovementController::printIssueByJournalID");

// hold & repair
$app->get("/hold", "App\Controllers\PageController::hold");
$app->get("/unhold", "App\Controllers\PageController::unhold");
$app->get("/repair", "App\Controllers\PageController::repair");
$app->get("/unrepair", "App\Controllers\PageController::unrepair");
$app->get("/greentire/scrap", "App\Controllers\PageController::scarp");

// Master
$app->get("/master/greentirecode", "App\Controllers\PageController::masterGreentireCode");
$app->get("/master/building", "App\Controllers\PageController::masterBuilding");
$app->get("/master/press", "App\Controllers\PageController::masterPress");
$app->get("/master/mold", "App\Controllers\PageController::masterMold");
$app->get("/master/curetirecode", "App\Controllers\PageController::masterCureTireCode");
$app->get("/master/warehouse", "App\Controllers\PageController::masterWarehouse");
$app->get("/master/location", "App\Controllers\PageController::masterLocation");
$app->get("/master/disposal", "App\Controllers\PageController::masterDisposal");
$app->get("/master/company", "App\Controllers\PageController::masterCompany");
$app->get("/master/department", "App\Controllers\PageController::masterDepartment");
$app->get("/master/user", "App\Controllers\PageController::user");
$app->get("/master/menu", "App\Controllers\PageController::masterMenu");
$app->get("/master/permission", "App\Controllers\PageController::masterPermission");
$app->get("/master/defect", "App\Controllers\PageController::masterDefect");
$app->get("/master/authorize", "App\Controllers\PageController::authorize");
$app->get("/master/employee", "App\Controllers\PageController::employee");
// $app->get("/master/gate", "App\Controllers\PageController::gate");
$app->get("/master/movement_type", "App\Controllers\PageController::movementType");
$app->get("/master/requsition_note", "App\Controllers\PageController::requsitionNote");

$app->get("/master/pressitem", "App\Controllers\PageController::pressitem");

// Report
$app->get("/report/onhand", "App\Controllers\PageController::ReportOnhand");
$app->get("/report/greentire/hold", "App\Controllers\PageController::reportGreentireHold");
$app->get("/report/final/hold", "App\Controllers\PageController::reportFinalHold");
$app->get("/report/greentire/scrap", "App\Controllers\ReportController::greentireScrap");
$app->get("/report/curetire/scrap", "App\Controllers\ReportController::curetireScrap");
$app->get("/report/curetire/master", "App\Controllers\ReportController::curetireMaster");
$app->get("/report/curetire/master/pdf", "App\Controllers\ReportController::curetireMasterPdf");
$app->get("/report/curetire/master/excel", "App\Controllers\ReportController::curetireMasterExcel");
$app->get("/report/greentire/scrap/([^/]+)/([^/]+)", "App\Controllers\ReportController::greentireScrapPdf");
$app->get("/report/curetire/scrap/([^/]+)/([^/]+)", "App\Controllers\ReportController::curetireScrapPdf");
$app->get("/report/building_ax", "App\Controllers\ReportController::buildingAx");
$app->post("/report/building_ax/pdf", "App\Controllers\ReportController::buildingAxPdf");
$app->get("/report/curing_ax", "App\Controllers\ReportController::curingAx");
$app->post("/report/curing_ax/pdf", "App\Controllers\ReportController::curingAxPdf");
// $app->get("/report/curing", "App\Controllers\ReportController::curingReport");
// $app->post("/report/curing/pdf", "App\Controllers\ReportController::curingReportPdf");
$app->get('/report/greentire/hold_unhold_repair', 'App\Controllers\ReportController::renderGreentireHoldUnholdAndRepair');
$app->post('/report/greentire/hold_unhold_repair/pdf', 'App\Controllers\ReportController::GreentireHoldUnholdAndRepair');
$app->get('/report/final/hold_unhold_repair', 'App\Controllers\ReportController::renderFinalHoldUnholdAndRepair');
$app->post('/report/final/hold_unhold_repair/pdf', 'App\Controllers\ReportController::FinalHoldUnholdAndRepair');
$app->get("/report/building_acc", "App\Controllers\ReportController::buildingAcc");

############### IMPORT #####################
$app->get("/import/topturn", "App\Controllers\ImportController::importTopTurn");
$app->get("/import/curecode", "App\Controllers\ImportController::importCureCode");

// ### J Report #####
$app->get("/report/building", "App\Controllers\PageController::ReportBuilding");
$app->post("/api/pdf/building", "App\Controllers\ReportController::genbuildingPDF");
$app->get("/report/internal", "App\Controllers\PageController::ReportInternal");
$app->post("/api/pdf/internal", "App\Controllers\ReportController::geninternalPDF");
$app->get("/report/curing", "App\Controllers\PageController::ReportCuring");
$app->get("/api/press/allBDF", "App\Controllers\PressController::allBDF");
$app->get("/api/press/allBDFA", "App\Controllers\PressController::allBDFA");
$app->post("/api/pdf/curing", "App\Controllers\ReportController::gencuringPDF");
$app->get("/report/greentire/inventory", "App\Controllers\PageController::ReportGreentireInventory");
$app->post("/api/pdf/inventory", "App\Controllers\ReportController::geninventoryPDF");
$app->get("/report/warehouse/sent", "App\Controllers\PageController::ReportWarehousesent");
$app->get("/api/press/allday", "App\Controllers\PressController::allday");
$app->get("/api/press/allnight", "App\Controllers\PressController::allnight");
$app->post("/api/pdf/warehouse", "App\Controllers\ReportController::genwarehousePDF");
$app->get("/report/warehouse/recive", "App\Controllers\PageController::ReportWarehouserecive");
$app->get("/api/brand/allbrand", "App\Controllers\ItemController::allbrand");
$app->get("/report/building_machine", "App\Controllers\ReportController::buildingMachine");
$app->post("/report/pdf/building-report-by-machine", "App\Controllers\ReportController::buildingMachinePdf");
$app->get("/report/curingpress", "App\Controllers\PageController::ReportCuringPress");

$app->post("/api/pdf/curingpress", "App\Controllers\ReportController::curingPress");
// J
$app->get("/report/wipfinalfg", "App\Controllers\PageController::ReportWIPFinalFG");
$app->post("/api/pdf/wipfinalfg", "App\Controllers\ReportController::genwipfinalfgPDF");
$app->get("/report/cure/inventory", "App\Controllers\PageController::ReportCureInventory");
$app->post("/api/pdf/cureinventory", "App\Controllers\ReportController::gencureinventoryPDF");
$app->get("/api/loading/report/([^/]+)/([^/]+)/([^/]+)/([^/]+)", "App\Controllers\ReportController::LoadingPDF");
// ######################################################

// Auth
$app->get("/d/auth", "App\Controllers\PageController::desktopLogin");
$app->get("/hh/auth", "App\Controllers\PageHandheldController::handheldLogin");
$app->get("/user/logout", "App\Controllers\UserController::logout");
$app->post("/clearsession", "App\Controllers\UserController::clearSession");

// Service API
/* GET */
$app->get("/api/barcode/printing/last", "App\Controllers\BarcodeController::getLastNumber");
$app->get("/api/department/all", "App\Controllers\DepartmentController::all");
$app->get("/api/warehouse/all", "App\Controllers\WarehouseController::all");
$app->get("/api/location/all", "App\Controllers\LocationController::all");
$app->get("/api/company/all", "App\Controllers\CompanyController::all");
$app->get("/api/item/all", "App\Controllers\ItemController::all");
$app->get("/api/permission/all", "App\Controllers\PermissionController::all");



// Employee
$app->get("/api/employee/all", "App\Controllers\EmployeeController::all");
$app->get("/api/employee/all/department/([^/]+)", "App\Controllers\EmployeeController::allByDepartmentName");
$app->get("/api/employee/all/by_status", "App\Controllers\EmployeeController::allByStatus");
$app->get("/api/employee/([^/]+)/division", "App\Controllers\EmployeeController::getDivisionByEmpCode");

$app->get("/api/press/all", "App\Controllers\PressController::all");
$app->get("/api/press_arm/all", "App\Controllers\PressArmController::all");
$app->get("/api/user/all", "App\Controllers\UserController::all");
$app->get("/api/template/all", "App\Controllers\TemplateController::all");
$app->get("/api/template/last", "App\Controllers\TemplateController::getLastRec");
$app->get("/api/template/generate/([^/]+)/([^/]+)", "App\Controllers\TemplateController::generate");
$app->get("/api/menu/all", "App\Controllers\MenuController::all");
$app->get("/api/defect/all", "App\Controllers\DefectController::all");
$app->get("/api/defect/reverse", "App\Controllers\DefectController::reverse");
$app->get("/api/defect/master/all", "App\Controllers\DefectController::masterAll");
$app->get("/api/greentire/all", "App\Controllers\GreentireController::all");
$app->get("/api/building/all", "App\Controllers\BuildingController::all");
$app->get("/api/curetire/all", "App\Controllers\CureTireController::all");
$app->get("/api/disposal/all", "App\Controllers\DisposalController::all");
$app->get("/api/disposal/action/all", "App\Controllers\DisposalController::actionAll");
$app->get("/api/disposal/company/all", "App\Controllers\DisposalController::companyAll");
$app->get("/api/mold/all", "App\Controllers\MoldController::all");
$app->get("/api/onhand/all", "App\Controllers\OnhandController::all");
$app->get("/api/invent/table/all", "App\Controllers\InventController::allInventTable");
$app->get("/api/invent/trans/([^/]+)", "App\Controllers\InventController::transDetail");
$app->get("/api/menu/generate", "App\Controllers\MenuController::generate");
$app->get("/api/location/by_warehouse/([^/]+)", "App\Controllers\LocationController::getLocationByWarehouse");
$app->get("/api/shift/all", "App\Controllers\ShiftController::getAll");
$app->get("/api/warehouse_type/all", "App\Controllers\WarehouseController::getAllWarehouseType");
$app->get("/api/authorize/all", "App\Controllers\AuthorizeController::all");
$app->get("/api/gate/all", "App\Controllers\GateController::all");
$app->get("/api/movement_type/all", "App\Controllers\MovementController::allMovementType");
$app->get("/api/movement_issue/all", "App\Controllers\MovementController::allMovementIssue");
$app->get("/api/movement_issue/([^/]+)/latest", "App\Controllers\MovementController::getLatestJournalTransByJournalId");
$app->get("/api/requsition_note/all", "App\Controllers\RequsitionController::all");
$app->get("/api/report/greentire/hold", "App\Controllers\OnhandController::getGreentireHold");
$app->get("/api/report/final/hold", "App\Controllers\OnhandController::getFinalHold");
$app->get('/api/loading/table/all_status', 'App\Controllers\LoadingController::getLoadingTableAllStatus');
$app->get('/api/authorize/field', 'App\Controllers\AuthorizeController::getPermissionField');
$app->get('/api/loading/line/([^/]+)', 'App\Controllers\LoadingController::getLoadingLine');
// $app->get('/api/loading/table/([^/]+)', 'App\Controllers\LoadingController::getLoadingTable');
$app->get('/api/loading/table/all', 'App\Controllers\LoadingController::getLoadingTableAll');
$app->get('/api/loading/table/([^/]+)/create', 'App\Controllers\LoadingController::createLoadingTable');

$app->get("/api/pressitem/all", "App\Controllers\PressitemController::all");


// Loading on mobile
$app->get('/api/barcode/([^/]+)', 'App\Controllers\BarcodeController::getBarcodeInfo');
$app->get("/api/loading/trans/([^/]+)/([^/]+)", 'App\Controllers\LoadingController::loadingTrans');
$app->get('/api/loading/pickinglist_by_orderid/([^/]+)', 'App\Controllers\LoadingController::getPickingListByOrderId');
$app->get('/api/actions', 'App\Controllers\PermissionController::actionsAll');
$app->get('/api/actions/user', 'App\Controllers\PermissionController::actionsUser');
$app->get('/api/actions/user/menu/([^/]+)', 'App\Controllers\PermissionController::actionsUserByPermissionDesktop');
$app->get('/api/actions/user/active/([^/]+)', 'App\Controllers\PermissionController::userActionActive');
$app->get("/api/invent/warehouse/total_receive", "App\Controllers\InventController::countReceiveToWarehouseFromFinal");
$app->get("/api/user/warehouse", "App\Controllers\WarehouseController::getUserWarehouse");

/* POST */
$app->post("/api/barcode/printing", "App\Controllers\BarcodeController::printing");
$app->post("/api/building/check", "App\Controllers\BuildingController::check");
$app->post("/api/greentire/barcode/check", "App\Controllers\GreentireController::checkBarcode");
$app->post("/api/user/create", "App\Controllers\UserController::create");
$app->post("/api/user/handheld/auth", "App\Controllers\UserController::handheldAuth");
$app->post("/api/curing/save", "App\Controllers\CuringController::curing");
$app->post("/api/xray/issue/wh", "App\Controllers\XrayController::issueToWH");
$app->post("/api/warehouse/create", "App\Controllers\WarehouseController::create");
$app->post("/api/menu/create", "App\Controllers\MenuController::create");
$app->post("/api/company/create", "App\Controllers\CompanyController::create");
$app->post("/api/defect/create", "App\Controllers\DefectController::create");
$app->post("/api/defect/update", "App\Controllers\DefectController::update");
$app->post("/api/department/create", "App\Controllers\DepartmentController::create");
$app->post("/api/greentire/create", "App\Controllers\GreentireController::create");
$app->post("/api/building/create", "App\Controllers\BuildingController::create");
$app->post("/api/press/create", "App\Controllers\PressController::create");
$app->post("/api/permission/create", "App\Controllers\PermissionController::create");
$app->post("/api/search/barcode", "App\Controllers\TrackingController::searchByBarcode");
$app->post("/api/search/barcode2", "App\Controllers\TrackingController::searchByBarcode2");
$app->post("/api/search/barcode/line", "App\Controllers\TrackingController::searchByBarcodeLine");
$app->post("/api/search/hold", "App\Controllers\HoldController::checkHold");
$app->post("/api/search/repair", "App\Controllers\RepairController::checkRepair");
$app->post("/api/location/create", "App\Controllers\LocationController::create");
$app->post("/api/curetire/create", "App\Controllers\CureTireController::create");
$app->post("/api/disposal/create", "App\Controllers\DisposalController::create");
$app->post("/api/user/desktop/auth", "App\Controllers\UserController::desktopAuth");
$app->post("/api/mold/create", "App\Controllers\MoldController::create");
$app->post("/api/greentire/receive", "App\Controllers\GreentireController::receive");
$app->post("/api/greentire/delete", "App\Controllers\GreentireController::delete");
$app->post("/api/building/delete", "App\Controllers\BuildingController::delete");
$app->post("/api/press/delete", "App\Controllers\PressController::delete");
$app->post("/api/warehouse/incoming", "App\Controllers\WarehouseController::incoming");
$app->post("/api/hold", "App\Controllers\HoldController::hold");
$app->post("/api/repair", "App\Controllers\RepairController::repair");
$app->post("/api/scrap", "App\Controllers\ScrapController::scrap");
$app->post("/api/unhold/authorize", "App\Controllers\HoldController::authorize");
$app->post("/api/unrepair/authorize", "App\Controllers\RepairController::authorize");
$app->post("/api/unhold", "App\Controllers\HoldController::unhold");
$app->post("/api/unrepair", "App\Controllers\RepairController::unrepair");
$app->post("/api/warehouse_type/create", "App\Controllers\WarehouseController::createType");
$app->post("/api/warehouse_type/delete", "App\Controllers\WarehouseController::deleteType");
$app->post("/api/authorize/create", "App\Controllers\AuthorizeController::create");
$app->post("/api/authorize/([0-9]+)/edit", "App\Controllers\AuthorizeController::edit");
$app->post("/api/employee/status/save", "App\Controllers\EmployeeController::setStatus");
$app->post("/api/gate/save", "App\Controllers\GateController::save");
$app->post("/api/final/save", "App\Controllers\FinalController::save");
$app->post("/api/movement_type/save", "App\Controllers\MovementController::save");
$app->post("/api/journal/table/save", "App\Controllers\MovementController::saveJournalTable");
$app->post("/api/movement/issue/save", "App\Controllers\MovementController::saveMovementIssue");
$app->post("/api/requsition_note/save", "App\Controllers\RequsitionController::saveRequsitionNote");
$app->post("/api/user/authorize", "App\Controllers\UserController::authorize");
$app->post('/api/movement/reverse/ok/save', "App\Controllers\MovementController::saveReverseOK");
$app->post('/api/movement/reverse/scrap/save', "App\Controllers\MovementController::saveReverseScrap");
$app->post("/api/movement_issue/complete", "App\Controllers\MovementController::completeIssue");
$app->post('/api/final/return/save', 'App\Controllers\FinalController::saveReturn');
$app->post('/apt/authorize/type', 'App\Controllers\UserController::getAuthorizeType');
$app->post('/api/user/location', 'App\Controllers\UserController::getUserLocation');
$app->post('/api/location/([0-9]+)/edit', 'App\Controllers\LocationController::setLocation');
$app->post('/api/loading/pick/save', 'App\Controllers\LoadingController::savePick');
$app->post('/api/loading/unpick/save', 'App\Controllers\LoadingController::saveUnpick');
$app->post('/api/loading/is_custome_remainder', 'App\Controllers\LoadingController::isCustomRemainder');
$app->post('/api/loading/confirm', 'App\Controllers\LoadingController::confirm');
$app->post('/api/loading/cancel', 'App\Controllers\LoadingController::cancel');
$app->post('/api/loading/add_remainder', 'App\Controllers\LoadingController::addRemainder');
$app->post('/api/loading/force_confirm', 'App\Controllers\LoadingController::forceConfirm');
$app->post('/api/loading/pickinglist_ref', 'App\Controllers\LoadingController::savePickingListRef');
$app->post('/api/actions/edit', 'App\Controllers\PermissionController::actionsEdit');
$app->post('/api/actions/create', 'App\Controllers\PermissionController::actionsCraete');
$app->post('/api/v1/adjust', 'App\Controllers\AdjustController::store');

$app->post("/api/pressitem/create", "App\Controllers\PressitemController::create");

// Import
$app->post("/api/import/topturn", "App\Controllers\ImportController::saveImportTopturn");
$app->post("/api/import/curecode", "App\Controllers\ImportController::saveImportCureCode");

// import route
require_once "./routes/profile.php";
require_once "./routes/itemset.php";
require_once "./routes/bom.php";
require_once "./routes/unbom.php";
require_once "./routes/item.php";
require_once "./routes/authorize.php";
require_once "./routes/foil.php";
require_once './routes/check.php';
require_once './routes/build.php';
require_once './routes/barcode.php';
require_once './routes/serial.php';
require_once './routes/finish_good.php';
require_once './routes/batch.php';
require_once './routes/employee.php';
require_once './routes/defect.php';
require_once './routes/phase2.php';
require_once './routes/component.php';

// App V2
require_once './app/v2/Pallet/PalletRoute.php';
require_once './app/v2/Location/LocationRoute.php';
require_once './app/v2/Item/ItemRoute.php';
require_once './app/v2/Batch/BatchRoute.php';
require_once './app/v2/PDF/PDFRoute.php';
require_once './app/v2/Component/ComponentRoute.php';
require_once './app/v2/Report/ReportRoute.php';
require_once './app/v2/ProductionSCH/ProductionSCHRoute.php';

// Phase 2
require_once './app/v2/TargetGreentire/TargetGreentireRoute.php';
require_once './app/v2/Shift/ShiftRoute.php';
require_once './app/v2/Module/ModuleRoute.php';

// Phase 3
require_once './app/v2/BuildSch/BuildSchRoute.php';
// Phase 4
require_once './app/v2/Greentire/GreentireRoute.php';

require_once './app/v2/TypeTireMaster/TypeTireRoute.php';
$app->run();
