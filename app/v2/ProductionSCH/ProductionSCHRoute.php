<?php

$app->get('/production/sch/load/cure', 'App\V2\ProductionSCH\ProductionSCHController::load_cure');

// DEV
$app->get('/production_sch/sch', 'App\V2\ProductionSCH\ProductionSCHController::sch');
$app->get('/production_sch/sch/approve', 'App\V2\ProductionSCH\ProductionSCHController::approve');

// Master
$app->get('/production_sch/sch/map/employee', 'App\V2\ProductionSCH\ProductionSCHController::mapEmployee');
$app->get('/production_sch/sch/master/item', 'App\V2\ProductionSCH\ProductionSCHController::masterItem');
$app->get('/production_sch/sch/master/remark', 'App\V2\ProductionSCH\ProductionSCHController::masterRemark');
$app->get('/production_sch/sch/master/time', 'App\V2\ProductionSCH\ProductionSCHController::masterTime');
$app->get('/production_sch/sch/master/scheduler', 'App\V2\ProductionSCH\ProductionSCHController::masterScheduler');
$app->get('/production_sch/sch/master/itemGT', 'App\V2\ProductionSCH\ProductionSCHController::masterItemitemGT');
$app->get('/production_sch/sch/master/itemEXT', 'App\V2\ProductionSCH\ProductionSCHController::masterItemitemEXT');
$app->get('/production_sch/sch/master/itemCP', 'App\V2\ProductionSCH\ProductionSCHController::masterItemitemCP');

$app->get('/production/sch/load', 'App\V2\ProductionSCH\ProductionSCHController::load');
// $app->post('/production/sch/load2', 'App\V2\ProductionSCH\ProductionSCHController::load2');

$app->get('/production/sch/loadisexist', 'App\V2\ProductionSCH\ProductionSCHController::loadisExist');
$app->get('/production/sch/load/employee', 'App\V2\ProductionSCH\ProductionSCHController::loademployee');
$app->get('/production/sch/load/item', 'App\V2\ProductionSCH\ProductionSCHController::loaditem');
$app->get('/production/sch/load/itemGT', 'App\V2\ProductionSCH\ProductionSCHController::loaditemGT');
$app->get('/production/sch/load/arms', 'App\V2\ProductionSCH\ProductionSCHController::loadarms');
$app->get('/production/sch/load/remark', 'App\V2\ProductionSCH\ProductionSCHController::loadremark');
$app->get('/production/sch/get/remark', 'App\V2\ProductionSCH\ProductionSCHController::getremark');
$app->get('/production/sch/get/employee', 'App\V2\ProductionSCH\ProductionSCHController::getemployee');
$app->get('/production/sch/complete/check', 'App\V2\ProductionSCH\ProductionSCHController::checkcomplete');
$app->get('/production/sch/data/check', 'App\V2\ProductionSCH\ProductionSCHController::checkdata');
$app->get('/production/sch/load/date', 'App\V2\ProductionSCH\ProductionSCHController::loaddate');
$app->get('/production/sch/load/listboiler', 'App\V2\ProductionSCH\ProductionSCHController::listboiler');
$app->get('/production/sch/load/time', 'App\V2\ProductionSCH\ProductionSCHController::loadtime');
$app->get('/production/sch/master/reportsch', 'App\V2\ProductionSCH\ProductionSCHController::getmasterreportsch');
$app->get('/production/sch/load/itemEXT', 'App\V2\ProductionSCH\ProductionSCHController::loaditemEXT');
$app->get('/production/sch/load/itemCP', 'App\V2\ProductionSCH\ProductionSCHController::loaditemCP');

// saba
$app->get("/insertbuybill", "App\V2\ProductionSCH\ProductionSCHController::insertbuybill");
//$app->get('/productionfacetire/sch/loadbuybill', 'App\V2\ProductionSCH\ProductionSCHController::loadbuybill');

$app->post('/production/sch/gen/sch', 'App\V2\ProductionSCH\ProductionSCHController::gen');
$app->post('/production/sch/add/sch', 'App\V2\ProductionSCH\ProductionSCHController::addrow');
$app->post('/production/sch/add/employee', 'App\V2\ProductionSCH\ProductionSCHController::addemployee');
$app->post('/production/sch/delete/employee', 'App\V2\ProductionSCH\ProductionSCHController::deleteemployee');
$app->post('/production/sch/add/item', 'App\V2\ProductionSCH\ProductionSCHController::additem');
$app->post('/production/sch/update/sch', 'App\V2\ProductionSCH\ProductionSCHController::updatesch');
$app->post('/production/sch/update/sch2', 'App\V2\ProductionSCH\ProductionSCHController::updatesch2');
$app->post('/production/sch/delete/sch', 'App\V2\ProductionSCH\ProductionSCHController::deleterow');
$app->post('/production/sch/add/remark', 'App\V2\ProductionSCH\ProductionSCHController::addremark');
$app->post('/production/sch/complete/sch', 'App\V2\ProductionSCH\ProductionSCHController::complete');
$app->post('/production/sch/sendmail/sch', 'App\V2\ProductionSCH\ProductionSCHController::sendmail');
$app->post('/production/sch/update/list', 'App\V2\ProductionSCH\ProductionSCHController::updatelist');
$app->post('/production/sch/update/time', 'App\V2\ProductionSCH\ProductionSCHController::updatetime');
$app->post('/production/sch/update/item', 'App\V2\ProductionSCH\ProductionSCHController::updateitem');
$app->post('/production/sch/update/itemGT', 'App\V2\ProductionSCH\ProductionSCHController::updateitemGT');
$app->post('/production/sch/update/remark', 'App\V2\ProductionSCH\ProductionSCHController::updateremark');
$app->post('/production/sch/create/remark', 'App\V2\ProductionSCH\ProductionSCHController::createremark');
$app->post('/production/sch/delete/remark', 'App\V2\ProductionSCH\ProductionSCHController::deleteremark');
$app->post('/production/sch/delete/remark/id', 'App\V2\ProductionSCH\ProductionSCHController::deleteremarkById');
$app->post('/production/sch/master/sch', 'App\V2\ProductionSCH\ProductionSCHController::createmastersch');
$app->post('/production/sch/update/clear', 'App\V2\ProductionSCH\ProductionSCHController::clearList');
$app->post('/production/sch/delete/employee/id', 'App\V2\ProductionSCH\ProductionSCHController::deleteemployeeById');
$app->post('/production/sch/confirm', 'App\V2\ProductionSCH\ProductionSCHController::confirmSch');
$app->get('/production/sch/confirm/check', 'App\V2\ProductionSCH\ProductionSCHController::checkconfirmSch');
$app->post('/productionRecive/sch/gen/itemedit', 'App\V2\ProductionSCH\ProductionSCHController::edititem');
// CURL
$app->get('/production/sch/sync/employee', 'App\V2\ProductionSCH\ProductionSCHController::syncEmployee');
