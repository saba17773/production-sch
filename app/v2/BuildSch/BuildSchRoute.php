<?php

$app->get("/sch/build", "App\V2\BuildSch\BuildSchController::index");
$app->get("/sch/build/export", "App\V2\BuildSch\BuildSchController::export");
$app->post("/sch/api/buildsch/get_all", "App\V2\BuildSch\BuildSchController::getBuildLists");
$app->get("/sch/api/buildsch/get_greentire", "App\V2\BuildSch\BuildSchController::getGreentireList");
$app->post("/sch/api/import", "App\V2\BuildSch\BuildSchController::import");
$app->post("/sch/api/build/clear", "App\V2\BuildSch\BuildSchController::clear");
$app->post("/sch/api/buildsch/get_all_group", "App\V2\BuildSch\BuildSchController::getBuildGroup");
$app->get("/sch/build/list/([1-9])/([^/]+)/view", "App\V2\BuildSch\BuildSchController::buildsch_list");
$app->post("/sch/api/build/import/ckeck", "App\V2\BuildSch\BuildSchController::importCheck");