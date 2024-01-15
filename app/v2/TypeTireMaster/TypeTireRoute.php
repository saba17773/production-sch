<?php
$app->get('/typetire/master', 'App\V2\TypeTireMaster\TypeTireController::master');
$app->get('/bindgrid/main', 'App\V2\TypeTireMaster\TypeTireController::bindGridMain');
$app->get('/bindgrid/line/([^/]+)', 'App\V2\TypeTireMaster\TypeTireController::bindGridLine');
$app->post('/insert/group', 'App\V2\TypeTireMaster\TypeTireController::insertGroup');
$app->post('/update/group', 'App\V2\TypeTireMaster\TypeTireController::updateGroup');
$app->post('/insert/detail', 'App\V2\TypeTireMaster\TypeTireController::insertDetail');
$app->post('/update/detail', 'App\V2\TypeTireMaster\TypeTireController::updateDetail');
// saba add master cure
$app->get('/cure/master', 'App\V2\TypeTireMaster\TypeTireController::curemaster');
$app->get('/cure/main', 'App\V2\TypeTireMaster\TypeTireController::cureGridMain');
$app->post('/update/cureschmater', 'App\V2\TypeTireMaster\TypeTireController::updatecure');
$app->post('/insert/cure', 'App\V2\TypeTireMaster\TypeTireController::insertCure');
