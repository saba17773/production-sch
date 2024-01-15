<?php

$app->get("/pd/greentire", "App\V2\Greentire\GreentireController::index");
$app->get("/pd/greentire/export", "App\V2\Greentire\GreentireController::export");