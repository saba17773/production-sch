<?php

$app->get("/sch2", "App\V2\TargetGreentire\TargetGreentireController::index");
$app->get("/sch3", "App\V2\TargetGreentire\TargetGreentireController::receivesch");
$app->get("/sch4", "App\V2\TargetGreentire\TargetGreentireController::tirebill");
$app->get("/sch5", "App\V2\TargetGreentire\TargetGreentireController::facetireproduct");
$app->get("/sch6", "App\V2\TargetGreentire\TargetGreentireController::plantireproduct");
$app->get("/sch7", "App\V2\TargetGreentire\TargetGreentireController::ordersumaryOfmount");
$app->get("/insertcar", "App\V2\TargetGreentire\TargetGreentireController::insertcar");
$app->get("/greentrieprim", "App\V2\TargetGreentire\TargetGreentireController::greentrieprim");


$app->post("/sch/v2/get_greentire_list", "App\V2\TargetGreentire\TargetGreentireController::getGreentireLists");

$app->post("/sch2/api/shift_trans/add", "App\V2\TargetGreentire\TargetGreentireController::addShiftTrans");
$app->post("/sch2/api/shift_trans/get_all", "App\V2\TargetGreentire\TargetGreentireController::getShiftTrans");
$app->post("/sch2/api/shift_trans/get_by_id", "App\V2\TargetGreentire\TargetGreentireController::getShiftTransById");
$app->post("/sch2/module/1", "App\V2\TargetGreentire\TargetGreentireController::loadModule");
$app->post("/sch2/api/shift_trans/cancel", "App\V2\TargetGreentire\TargetGreentireController::cancel");
$app->get('/productionRecive/sch2/load', 'App\V2\TargetGreentire\TargetGreentireController::load');
$app->post('/productionRecive/sch2/gen/sch', 'App\V2\TargetGreentire\TargetGreentireController::gen');
$app->post('/sch2/sch/update/sch2', 'App\V2\TargetGreentire\TargetGreentireController::updateschrecive');
$app->post('/sch2/sch/add/item', 'App\V2\TargetGreentire\TargetGreentireController::additem');
$app->post('/sch2/sch/delete/item', 'App\V2\TargetGreentire\TargetGreentireController::deleterow');
$app->get('/sch2/sch/data/check', 'App\V2\TargetGreentire\TargetGreentireController::checkdata');
$app->post("/sch2/module/checkbill", "App\V2\TargetGreentire\TargetGreentireController::loadModulebillbuy");
//print
$app->get('/productionPrint/sch2/load', 'App\V2\TargetGreentire\TargetGreentireController::loadprint');
$app->post('/productionRecive/sch2/gen/schprint', 'App\V2\TargetGreentire\TargetGreentireController::genprint');
$app->post('/sch2/sch/add/itemprint', 'App\V2\TargetGreentire\TargetGreentireController::additemprint');
$app->post('/sch2/sch/update/schprintTable', 'App\V2\TargetGreentire\TargetGreentireController::updateschprint');
$app->post('/sch2/sch/delete/rowprint', 'App\V2\TargetGreentire\TargetGreentireController::deleterowprint');
$app->get('/sch2/sch/data/checkprint', 'App\V2\TargetGreentire\TargetGreentireController::checkdataprint');
// tirebill
$app->post('/productionRecive/sch2/gen/schbilltire', 'App\V2\TargetGreentire\TargetGreentireController::gentire');
$app->get('/ProductionGreentireDisburs/sch2/loadtire', 'App\V2\TargetGreentire\TargetGreentireController::loadtire');
$app->get('/sch2/sch/data/checkdisbursement', 'App\V2\TargetGreentire\TargetGreentireController::checkdatadisbursement');
$app->post('/sch2/sch/delete/itemdisbursement', 'App\V2\TargetGreentire\TargetGreentireController::deleterowdisbursement');
$app->post('/sch2/sch/update/UpdateSchDisburTable', 'App\V2\TargetGreentire\TargetGreentireController::UpdateSchDisburTable');
$app->post('/sch2/sch/update/UpdateSchDisburTableCar', 'App\V2\TargetGreentire\TargetGreentireController::UpdateSchDisburTableCar');
$app->post('/sch2/sch/add/itemDisburs', 'App\V2\TargetGreentire\TargetGreentireController::additemDisburs');
$app->post('/productionRecive/sch2/gen/updatebilltire', 'App\V2\TargetGreentire\TargetGreentireController::updateSchtireTable');
$app->post('/productionRecive/sch2/gen/updatebilltireStock', 'App\V2\TargetGreentire\TargetGreentireController::updateSchtireTableStock');
// facetireproduct
$app->post('/productionfacetire/sch2/gen/schfacetire', 'App\V2\TargetGreentire\TargetGreentireController::genfacetire');
$app->get('/productionfacetire/sch2/loadfacetire', 'App\V2\TargetGreentire\TargetGreentireController::loadfacetire');
$app->get('/sch2/sch/data/checkgridschfacetire', 'App\V2\TargetGreentire\TargetGreentireController::checkgridschfacetire');
$app->post('/sch2/sch/add/itemfacetire', 'App\V2\TargetGreentire\TargetGreentireController::additemfacetire');
$app->post('/sch2/sch/delete/itemfacetire', 'App\V2\TargetGreentire\TargetGreentireController::deleterowfacetire');
$app->post('/sch2/sch/update/UpdateSchFacetireTable', 'App\V2\TargetGreentire\TargetGreentireController::UpdateSchFacetireTable');
$app->get("/insertfacetirecar", "App\V2\TargetGreentire\TargetGreentireController::insertcfacetirecar");
$app->post('/sch2/sch/update/UpdateSchFacetireTableCar', 'App\V2\TargetGreentire\TargetGreentireController::UpdateSchFacetireTableCar');
$app->post('/productionface/sch2/gen/updateSchFacetireStock', 'App\V2\TargetGreentire\TargetGreentireController::updateSchfacetireTableStock');
// plantireproduct
$app->get('/productionfacetire/sch2/loadplantire', 'App\V2\TargetGreentire\TargetGreentireController::loadplantire');
$app->get('/productionfacetire/sch2/loadplantiregroup1', 'App\V2\TargetGreentire\TargetGreentireController::loadplantiregroup1');
$app->get('/sch2/sch/data/checkgridschplantire', 'App\V2\TargetGreentire\TargetGreentireController::checkgridschplantire');
$app->get('/sch2/sch/data/checkgriddateplantire', 'App\V2\TargetGreentire\TargetGreentireController::checkgriddateplantire');

// orderreport
$app->get('/productionfacetire/sch2/loadordersummary', 'App\V2\TargetGreentire\TargetGreentireController::loadplaloadordersummaryntire');
$app->post('/sch2/sch/update/schordersummary', 'App\V2\TargetGreentire\TargetGreentireController::updateschordersumary');

$app->post("/sch2/api/target_greentire/([^/]+)", "App\V2\TargetGreentire\TargetGreentireController::loadTargetGreentire");
$app->post("/sch2/api/target_greentire/update", "App\V2\TargetGreentire\TargetGreentireController::update");
$app->post("/sch2/api/target_greentire/delete", "App\V2\TargetGreentire\TargetGreentireController::delete");
$app->post("/sch2/api/target_greentire/add", "App\V2\TargetGreentire\TargetGreentireController::add");
$app->post("/sch2/api/target_greentire/master", "App\V2\TargetGreentire\TargetGreentireController::getGreentireMaster");
$app->get("/sch2/api/target_greentire/report/([0-9]+)/([^/]+)", "App\V2\TargetGreentire\TargetGreentireController::report");
$app->get("/sch2/api/target_greentire/report_excel/([0-9]+)/([^/]+)", "App\V2\TargetGreentire\TargetGreentireController::reportExcel");
$app->get('/sch2/sch2/load/item', 'App\V2\TargetGreentire\TargetGreentireController::loaditem');
$app->get('/sch2/sch2/load/itemEXT', 'App\V2\TargetGreentire\TargetGreentireController::loaditemEXT');
$app->post("/sch2/api/target_billbuy/([^/]+)", "App\V2\TargetGreentire\TargetGreentireController::loadTargetbillbuy");
$app->get("/sch2/api/target_greentireCure/report/([0-9]+)/([^/]+)", "App\V2\TargetGreentire\TargetGreentireController::reportCure");
$app->get("/sch2/api/target_greentire/reportCure_excel/([0-9]+)/([^/]+)", "App\V2\TargetGreentire\TargetGreentireController::reportCureExcel");
