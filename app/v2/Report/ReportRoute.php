<?php


$app->get('/report', 'App\V2\Report\ReportController::all');

$app->get('/production/sch/report', 'App\V2\Report\ReportController::reportSch');
$app->get('/production/sch/report/curing', 'App\V2\Report\ReportController::reportSchCuring');
$app->get('/production/sch/report/curingpress', 'App\V2\Report\ReportController::reportSchCuringPress');
$app->get('/production/sch/report/summary', 'App\V2\Report\ReportController::reportSchSummary');
$app->get('/production/sch/report/weight', 'App\V2\Report\ReportController::reportWeight');
$app->get('/production/sch/report/greentire', 'App\V2\Report\ReportController::reportSchGreentire');
$app->get('/production/sch/report/greentire/withdraw', 'App\V2\Report\ReportController::reportSchGreentireWithdraw');
$app->get('/production/sch/report/greentirerecive', 'App\V2\Report\ReportController::reportSchrecive');
$app->get('/production/sch/report/greentirereciveprint', 'App\V2\Report\ReportController::reportSchreciveprint');
$app->get('/production/sch/report/splittire', 'App\V2\Report\ReportController::reportSplittire');
$app->get('/production/sch/report/disbursementtire', 'App\V2\Report\ReportController::reportdisbursementtire');
$app->get('/production/sch/report/facetire', 'App\V2\Report\ReportController::reportFacetire');
$app->get('/production/sch/report/schreportplan', 'App\V2\Report\ReportController::schreportplan');
//saba
$app->get('/production/sch/report/billbuy', 'App\V2\Report\ReportController::reportSchbillbuy');
$app->get('/production/sch/report/OrderReport', 'App\V2\Report\ReportController::reportSchOrder');
$app->get('/production/sch/report/summaryorder', 'App\V2\Report\ReportController::reportsummaryorder');
$app->get('/production/sch/reportDraw', 'App\V2\Report\ReportController::reportSchDraw');
$app->get('/production/sch/reportall', 'App\V2\Report\ReportController::reportSchall');

// $app->post('/production/sch/pdf/report', 'App\V2\Report\ReportController::reportSchPdf');
$app->get('/production/sch/pdf/report/([^/]+)/([1-9])/([^/]+)/view', 'App\V2\Report\ReportController::reportSchPdf');
$app->post('/production/sch/pdf/report/greentire/withdraw', 'App\V2\Report\ReportController::reportSchGreentireWithdrawPdf');
$app->get('/production/sch/pdf/report/curing/([^/]+)/([1-9])/([1-9])/view', 'App\V2\Report\ReportController::reportSchCuringPdf');
$app->get('/production/sch/pdf/report/summary/([^/]+)/([^/]+)/view', 'App\V2\Report\ReportController::reportSchSummaryPdf');
$app->get('/production/sch/pdf/report/weight/([^/]+)/([1-9])/view', 'App\V2\Report\ReportController::reportSchWeightPdf');
$app->get('/production/sch/pdf/report/curingpress/([^/]+)/([1-9])/view', 'App\V2\Report\ReportController::reportSchCuringpressPdf');
$app->get('/production/sch/pdf/report/greentire/receive/([^/]+)/([1-9])/view', 'App\V2\Report\ReportController::reportSchReceiveGreentirePdf');
$app->get('/production/sch/pdf/report/greentire/receive_report/([^/]+)/([1-9])/([1-9])/view', 'App\V2\Report\ReportController::reportgatgetReceiveGreentirePdf');
$app->get('/production/sch/pdf/report/greentire/receiveprint_report/([^/]+)/([1-9])/([1-9])/view', 'App\V2\Report\ReportController::reportgreentireprintPdf');
$app->get('/production/sch/pdf/report/greentire/splittire_report/([^/]+)/([1-9])/([1-9])/view', 'App\V2\Report\ReportController::reportgreentiresplittirePdf');
$app->get('/production/sch/pdf/report/greentire/disbursementtire_report/([^/]+)/([1-9])/([1-9])/view', 'App\V2\Report\ReportController::reportdisbursementtirePdf');
$app->get('/production/sch/pdf/report/greentire/facetire_report/([^/]+)/([1-9])/([1-9])/view', 'App\V2\Report\ReportController::reportgreentirefacetirePdf');
$app->get('/production/sch/pdf/report/greentire/plantire_report/([^/]+)/([1-9])/([1-9])/view', 'App\V2\Report\ReportController::reportgreentireplantirePdf');
$app->get('/production/sch/pdf/report/billbuy/([^/]+)/([^/]+)/view', 'App\V2\Report\ReportController::reportSchBillbuyPdf');
$app->get('/production/sch/pdf/report/greentire/order_report/([^/]+)/([1-9])/([1-9])/view', 'App\V2\Report\ReportController::reportSchOrderPdf');
$app->get('/production/sch/pdf/report/summarymonth/([^/]+)/([1-9])/view', 'App\V2\Report\ReportController::reportSchSummaryMonthtPdf');
$app->get('/production/sch/pdf/reportDraw/([^/]+)/([1-9])/([^/]+)/view', 'App\V2\Report\ReportController::reportSchDrawPdf');
$app->get('/production/sch/pdf/reportall/([^/]+)/([^/]+)/view', 'App\V2\Report\ReportController::reportSchPdfall');
