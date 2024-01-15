<?php $PermissionService = new App\Services\PermissionService; ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0">
	<meta charset="UTF-8">
	<link rel="shortcut icon" type="image/png" href="/logo.png" />
	<title><?php echo $this->e($title); ?> - <?php echo app_name; ?></title>
	<link rel="manifest" href="/manifest.json">
	<!-- CSS -->
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/assets/jqwidgets/styles/jqx.base.css" />
	<link rel="stylesheet" href="/assets/jqwidgets/styles/theme.css" />
	<link rel="stylesheet" href="/assets/css/multiple-select.css" />
	<link rel="stylesheet" href="/assets/css/jquery-ui.min.css" />
	<link rel="stylesheet" href="/assets/css/jquery-ui-timepicker-addon.css">
	<!-- <link rel="stylesheet" href="/assets/datatables/datatables.min.css"> -->
	<link rel="stylesheet" href="/assets/datatables/Datatables-1.10.20/css/jquery.dataTables.min.css" />
	<link rel="stylesheet" href="/assets/datatables/Select-1.3.1/css/select.dataTables.min.css" />
	<link rel="stylesheet" href="/assets/datatables/Responsive-2.2.3/css/responsive.dataTables.min.css" />
	<link rel="stylesheet" href="/assets/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="/assets/css/yamm.css" />
	<link rel="stylesheet" href="/assets/css/app.css" />
	<link rel="stylesheet" href="/assets/css/bootstrap-datepicker1.5.0.css" />
	<link rel="stylesheet" href="/assets/bootstrap3-editable-1.5.1/bootstrap3-editable/css/bootstrap-editable.css" />
	<link rel="stylesheet" href="/assets/datatables-ext/datatables-ext.css">
	<!-- JS -->
	<script>
		var base_url = '';
		var api_url = '';
		var uuid = '<?php if (isset($_SESSION['user_login'])) echo $_SESSION['user_login']; ?>';
		var onFocus = '';
	</script>
	<script src="/assets/js/jquery-1.12.0.min.js"></script>
	<script src="/assets/js/jquery-ui.min.js"></script>
	<script src="/assets/js/jquery-ui-timepicker-addon.js"></script>
	<script src="/assets/js/bootstrap.min.js"></script>
	<script src="/assets/jqwidgets/jqxcore.js"></script>
	<script src="/assets/jqwidgets/jqxinput.js"></script>
	<script src="/assets/jqwidgets/jqxdata.js"></script>
	<script src="/assets/jqwidgets/jqxbuttons.js"></script>
	<script src="/assets/jqwidgets/jqxbuttongroup.js"></script>
	<script src="/assets/jqwidgets/jqxscrollbar.js"></script>
	<script src="/assets/jqwidgets/jqxmenu.js"></script>
	<script src="/assets/jqwidgets/jqxlistbox.js"></script>
	<script src="/assets/jqwidgets/jqxdropdownlist.js"></script>
	<script src="/assets/jqwidgets/jqxgrid.js"></script>
	<script src="/assets/jqwidgets/jqxgrid.selection.js"></script>
	<script src="/assets/jqwidgets/jqxgrid.columnsresize.js"></script>
	<script src="/assets/jqwidgets/jqxgrid.filter.js"></script>
	<script src="/assets/jqwidgets/jqxgrid.sort.js"></script>
	<script src="/assets/jqwidgets/jqxgrid.pager.js"></script>
	<script src="/assets/jqwidgets/jqxgrid.edit.js"></script>
	<script src="/assets/jqwidgets/jqxdatetimeinput.js"></script>
	<script src="/assets/jqwidgets/jqxcalendar.js"></script>
	<script src="/assets/jqwidgets/jqxgrid.grouping.js"></script>
	<script src="/assets/jqwidgets/jqxwindow.js"></script>
	<script src="/assets/jqwidgets/jqxinput.js"></script>
	<script src="/assets/jqwidgets/jqxcheckbox.js"></script>
	<script src="/assets/jqwidgets/jqxpanel.js"></script>
	<script src="/assets/jqwidgets/jqxcombobox.js"></script>
	<script src="/assets/jqwidgets/jqxdropdownbutton.js"></script>
	<script src="/assets/jqwidgets/globalization/globalize.js"></script>
	<script src="/assets/jqwidgets/jqxdatatable.js"></script>

	<script src="/assets/js/fastclick.js"></script>
	<script src="/assets/js/jquery.maskMoney.min.js"></script>
	<script src="/assets/js/jquery.mask.min.js"></script>
	<script src="/assets/js/gojax.min.js"></script>
	<script src="/assets/js/qs.min.js"></script>
	<script src="/assets/dayjs/dayjs.min.js"></script>
	<script src="/assets/js/multiple-select.js"></script>
	<script src="/assets/blockui/jquery.blockUI.js"></script>
	<script src="/assets/js/jquery.base64.min.js"></script>
	<script src="/assets/js/jquery.form-validator.min.js"></script>
	<!-- <script src="/assets/datatables/datatables.min.js"></script> -->
	<script src="/assets/datatables/Datatables-1.10.20/js/jquery.dataTables.min.js"></script>
	<script src="/assets/datatables/Select-1.3.1/js/dataTables.select.min.js"></script>
	<script src="/assets/datatables/Responsive-2.2.3/js/dataTables.responsive.min.js"></script>
	<script src="/assets/datatables-ext/datatables-ext.js"></script>
	<script src="/assets/bootstrap3-editable-1.5.1/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
	<script src="/assets/js/moment.js"></script>
	<script src="/assets/js/jqx_mod.js"></script>
	<script src="/assets/js/app.js"></script>
	<script src="/assets/js/shared.js"></script>
	<script src="/assets/js/bootstrap-datepicker1.5.0.js"></script>

	<!--[if lt IE 9]>
      <script src="/assets/js/html5shiv.js"></script>
      <script src="/assets/js/respond.js"></script>
    <![endif]-->
</head>

<body>
	<div id="http-loading"></div>
	<!-- <div id="http-loading"><img src="/assets/images/ajax-loader.gif" />	 กำลังประมวณผล... กรุณารอสักครู่...</div> -->

	<!-- Modal Alert-->
	<div class="modal" id="modal_alert" tabindex="-1" role="dialog" style="top: 80px; z-index: 9999999;">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content" style="background-color: red;">
				<div class="modal-header" style="border-bottom: 0px;">
				</div>
				<div class="modal-body">
					<!-- Content -->
					<table border="0" width="100%">
						<tr>
							<td valign="top" align="center">
								<img data-dismiss="modal" width="200" height="200" src="/assets/images/error01.png" alt="">
							</td>
						</tr>
						<tr>
							<td valign="top" align="center" style="color: white;">
								<h1 id="modal_alert_message"></h1>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Warning-->
	<div class="modal" id="modal_warning" tabindex="-1" role="dialog" style="top: 80px; z-index: 9999999;">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content" style="background-color: orange;">
				<div class="modal-header" style="border-bottom: 0px;">
				</div>
				<div class="modal-body">
					<!-- Content -->
					<table border="0" width="100%">
						<tr>
							<td valign="top" align="center">
								<img data-dismiss="modal" width="200" height="200" src="/assets/images/error01.png" alt="">
							</td>
						</tr>
						<tr>
							<td valign="top" align="center" style="color: black;">
								<h1 id="modal_warning_message"></h1>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Loading-->
	<div class="modal" id="modal_loading" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">กำลังประมวลผล</h4>
				</div>
				<div class="modal-body">
					<!-- Content -->
					<div class="text-center" style="margin-bottom: 20px;">กรุณารอสักครู่...</div>
					<div class="progress">
						<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger" onClick="window.location.reload()">ปิดหน้าต่างนี้</button>
				</div>
			</div>
		</div>
	</div>

	<div id="dialog-confirm" title="Are you sure ?" style="display: none; font-size: 2em;">
		<span>คุณยืนยันจะดำเนินการต่อไปหรือไม่ ?</span>
	</div>

	<nav class="navbar navbar-default navbar-static-top">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<?php
				echo '<a class="navbar-brand" href="/">' . app_name . '</a>';
				// $detect = new \Mobile_Detect;
				// if ($detect->isMobile()) {
				// 	echo '<a class="navbar-brand" href="#">' . app_name . '</a>';
				// } else {
				// 	echo '<a class="navbar-brand" href="/">' . app_name . '</a>';
				// }
				?>

			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-navbar-collapse">
				<!-- Menu -->
				<ul class="nav navbar-nav navbar-left">
					<?php if (isset($_SESSION["user_login"])) : ?>
						<?php if ($_SESSION["user_name"] == "admin") { ?>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="glyphicon glyphicon-wrench"></i> ตั้งค่าระบบ <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li class="dropdown dropdown-submenu">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown">
											<i class="glyphicon glyphicon-cog"></i> Green Tire
										</a>
										<ul class="dropdown-menu">
											<li>
												<a href="/master/greentirecode">
													<i class="glyphicon glyphicon-circle-arrow-right"></i> Greentire Code
												</a>
											</li>
											<li>
												<a href="/template/register">
													<i class="glyphicon glyphicon-circle-arrow-right"></i> Serial Register
												</a>
											</li>
											<li><a href="/master/building"><i class="glyphicon glyphicon-circle-arrow-right"></i> Building MC.</a></li>
											<li><a href="/master/press"><i class="glyphicon glyphicon-circle-arrow-right"></i> Press</a></li>
											<li><a href="/master/mold"><i class="glyphicon glyphicon-circle-arrow-right"></i> Mold</a></li>
											<li><a href="/master/curetirecode"><i class="glyphicon glyphicon-circle-arrow-right"></i> Curetire Code</a></li>
										</ul>
									</li>

									<li class="dropdown dropdown-submenu">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-cog"></i> Repair/Scrap</a>
										<ul class="dropdown-menu">
											<li><a href="/master/defect"><i class="glyphicon glyphicon-circle-arrow-right"></i> Defect</a></li>
										</ul>
									</li>
									<li class="dropdown dropdown-submenu">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-cog"></i> System Management</a>
										<ul class="dropdown-menu">
											<li><a href="/master/warehouse"><i class="glyphicon glyphicon-circle-arrow-right"></i> Warehouse</a></li>
											<li><a href="/master/location"><i class="glyphicon glyphicon-circle-arrow-right"></i> Location</a></li>
											<li><a href="/master/disposal"><i class="glyphicon glyphicon-circle-arrow-right"></i> Disposal to Use In</a></li>
											<li><a href="/master/company"><i class="glyphicon glyphicon-circle-arrow-right"></i> Company</a></li>
											<li><a href="/master/gate"><i class="glyphicon glyphicon-circle-arrow-right"></i> Gate</a></li>
										</ul>
									</li>
									<li class="dropdown dropdown-submenu">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-cog"></i> User Management</a>
										<ul class="dropdown-menu">
											<li><a href="/master/user"><i class="glyphicon glyphicon-circle-arrow-right"></i> User</a></li>
											<li><a href="/master/department"><i class="glyphicon glyphicon-circle-arrow-right"></i> Department</a></li>
											<li><a href="/master/menu"><i class="glyphicon glyphicon-circle-arrow-right"></i> Menu</a></li>
											<li><a href="/master/permission"><i class="glyphicon glyphicon-circle-arrow-right"></i> Permission</a></li>
											<li><a href="/master/authorize"><i class="glyphicon glyphicon-circle-arrow-right"></i> Authorize</a></li>
										</ul>
									</li>
								</ul>
							</li>
						<?php } ?>
					<?php endif ?>

					<?php if (isset($_SESSION["user_login"])) : ?>
						<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'menu_report') === true) : ?>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="glyphicon glyphicon-list"></i> รายงาน <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'onhand_report') === true) : ?>
										<li><a href="/report/onhand"><i class="glyphicon glyphicon-list-alt"></i> Onhand report</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_hold_report') === true) : ?>
										<li><a href="/report/greentire/hold"><i class="glyphicon glyphicon-list-alt"></i> Greentire Hold report</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'final_hold_report') === true) : ?>
										<li><a href="/report/final/hold"><i class="glyphicon glyphicon-list-alt"></i> Final Hold report</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'building_report') === true) : ?>
										<li><a href="/report/building"><i class="glyphicon glyphicon-list-alt"></i> Building report</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'internal_withdrawal_report') === true) : ?>
										<li><a href="/report/internal"><i class="glyphicon glyphicon-list-alt"></i> Final Withdraw</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'finish_good_withdraw_report') === true) : ?>
										<li><a href="/report/finish_good/withdraw"><i class="glyphicon glyphicon-list-alt"></i> Finish Good Withdraw</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'curing_report') === true) : ?>
										<li><a href="/report/curing"><i class="glyphicon glyphicon-list-alt"></i> Curing report</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_inventory_report') === true) : ?>
										<li><a href="/report/greentire/inventory"><i class="glyphicon glyphicon-list-alt"></i> Greentire Inventory</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'final_send_to_warehouse_report') === true) : ?>
										<li><a href="/report/warehouse/sent"><i class="glyphicon glyphicon-list-alt"></i> รายงานส่งยางเข้าคลังสินค้า</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'warehouse_receive_from_final_report') === true) : ?>
										<li><a href="/report/warehouse/recive"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงาน รับสินค้าเข้าคลังสินค้า</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'cured_report') === true) : ?>
										<li><a href="/report/curingpress"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงานจำนวนยางที่อบ</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_scrap_report') === true) : ?>
										<li><a href="/report/greentire/scrap"><i class="glyphicon glyphicon-list-alt"></i> Greentire Scrap</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'curetire_scrap_report') === true) : ?>
										<li><a href="/report/curetire/scrap"><i class="glyphicon glyphicon-list-alt"></i> Curetire Scrap</a></li>
									<?php endif ?>
									<!-- AX REPORT -->
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'building_ax_report') === true) : ?>
										<li><a href="/report/building_ax"><i class="glyphicon glyphicon-list-alt"></i> Building AX</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'curing_ax_report') === true) : ?>
										<li><a href="/report/curing_ax"><i class="glyphicon glyphicon-list-alt"></i> Curing AX</a></li>
									<?php endif ?>
									<!-- J Report -->
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'cure_inventory_report') === true) : ?>
										<li><a href="/report/cure/inventory"><i class="glyphicon glyphicon-list-alt"></i> Cured Inventory</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'wip_final_fg_report') === true) : ?>
										<li><a href="/report/wipfinalfg"><i class="glyphicon glyphicon-list-alt"></i> WIP Final FG.</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'cure_tire_code_master_report') === true) : ?>
										<li><a href="/report/curetire/master"><i class="glyphicon glyphicon-list-alt"></i> Cure Tire Code Master</a></li>
									<?php endif ?>

									<!-- 20/4/2560 -->
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_hold_unhold_repair_report') === true) : ?>
										<li><a href="/report/greentire/hold_unhold_repair"><i class="glyphicon glyphicon-list-alt"></i> Greentire Unhold/Unrepair</a></li>
									<?php endif ?>

									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'final_hold_unhold_repair_report') === true) : ?>
										<li><a href="/report/final/hold_unhold_repair"><i class="glyphicon glyphicon-list-alt"></i> Final Unhold/Unrepair</a></li>
									<?php endif ?>

									<!-- 2016.06.15 -->
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_bom') === true) : ?>
										<li><a href="/report_bom"><i class="glyphicon glyphicon-list-alt"></i> Bom</a></li>
									<?php endif ?>

									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_foil') === true) : ?>
										<li><a href="/report_foil"><i class="glyphicon glyphicon-list-alt"></i> Foil</a></li>
									<?php endif ?>

									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_building_machine') === true) : ?>
										<li><a href="/report/building_machine"><i class="glyphicon glyphicon-list-alt"></i> Building Machine</a></li>
									<?php endif ?>

									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch') === true) : ?>
										<li><a href="/production/sch/report"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงานการผลิต</a></li>
									<?php endif ?>

									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_summary') === true) : ?>
										<li><a href="/production/sch/report/summary"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงานสรุปการผลิตประจำวัน</a></li>
									<?php endif ?>

									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_curing') === true) : ?>
										<li><a href="/production/sch/report/curing"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงานจำนวนเตาทั้งหมด</a></li>
									<?php endif ?>

									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_weight') === true) : ?>
										<li><a href="/production/sch/report/weight"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงานสรุปน้ำนักผลิตประจำเดือน</a></li>
									<?php endif ?>

									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_curingpress') === true) : ?>
										<li><a href="/production/sch/report/curingpress"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงานสรุปการอบยางประจำเดือน</a></li>
									<?php endif ?>

									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_greentirereceive') === true) : ?>
										<li><a href="/production/sch/report/greentire"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงานรับเข้า-เบิก-จ่ายยางกรีนไทร์</a></li>
									<?php endif ?>

									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_greentirewithdraw') === true) : ?>
										<li><a href="/production/sch/report/greentire/withdraw"><i class="glyphicon glyphicon-list-alt"></i> ใบเบิกยางกรีนไทร์</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_greentirerecive') === true) : ?>
										<li><a href="/production/sch/report/greentirerecive"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงานรับ-เข้า-เบิก ยางกรีนไทร์</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_greentireprint') === true) : ?>
										<li><a href="/production/sch/report/greentirereciveprint"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงาน จำนวนพิมพ์</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_greentiresplittire') === true) : ?>
										<li><a href="/production/sch/report/splittire"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงาน แยกชนิดยาง</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_disbursementtire') === true) : ?>
										<li><a href="/production/sch/report/disbursementtire"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงาน เบิกจ่ายหน้ายาง</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_facetire') === true) : ?>
										<li><a href="/production/sch/report/facetire"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงาน หน้ายางผลิตได้</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_plantire') === true) : ?>
										<li><a href="/production/sch/report/schreportplan"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงาน Stock แผนสั่งออกหน้ายาง</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_billbuy') === true) : ?>
										<li><a href="/production/sch/report/billbuy"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงาน เบิกอบยาง หน้ายาง</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_orderreport') === true) : ?>
										<li><a href="/production/sch/report/OrderReport"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงาน Order</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_summaryreport') === true) : ?>
										<li><a href="/production/sch/report/summaryorder"><i class="glyphicon glyphicon-list-alt"></i> Summary Order Of Month</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_drowsreport2') === true) : ?>
										<li><a href="/production/sch/reportDraw"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงานการเบิกใช้ เบิกให้ หน้าเตา</a></li>
									<?php endif ?>
									<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_production_sch_all') === true) : ?>
										<li><a href="/production/sch/reportall"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงานแผนการผลิตรายวัน BIAS</a></li>
									<?php endif ?>




								</ul>
							</li>
						<?php endif ?>

						<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'tracking_so') === true) : ?>
							<li><a href="/tracking_v3"> <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Tracking</a></li>
						<?php endif ?>
					<?php endif; ?>
				</ul>

				<?php if (isset($_SESSION["user_login"])) : ?>
					<ul class="nav navbar-nav navbar-right">
						<li>
							<a href="#">
								<i class="glyphicon glyphicon-user"></i>
								<?php echo $_SESSION["user_name"]; ?>
							</a>
						</li>
						<li>
							<?php
							$detect = new \Mobile_Detect;
							if (!$detect->isMobile()) { ?>
								<a href="/change_password">
									<span class="glyphicon glyphicon-refresh"></span>
									เปลี่ยนรหัสผ่าน
								</a>
							<?php } ?>
						</li>
						<li>
							<a id="link_logout" href="/user/logout">
								<i class="glyphicon glyphicon-log-out"></i>
								ออกจากระบบ
							</a>
						</li>
					</ul>
				<?php endif; ?>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>

	<?php if (isset($_SESSION["user_login"])) : ?>

		<div class="panel panel-default" style="border-radius: 0px; margin-bottom: 0px;">
			<div class="panel-body" style="padding:0px;">
				<ul class="nav nav-pills container-fluid" id="menu-loader" style="overflow-x: auto; white-space: nowrap;">
					<li><a href="#">ไม่พบเมนูที่สามารถใช้งานได้</a></li>
				</ul>
			</div>
		</div>

		<div class="alert alert-success" id="top_alert" style="background: green;
			color:white;
			border: 0;
			font-weight: bold;
			font-size: 1.2em;
			text-align: center;
			display: none;" role="alert">
			<div id="top_alert_message"></div>
		</div>

	<?php endif; ?>

	<div style="padding: 10px; background: #eeeeee; color: #000000; border-bottom: 1px #cccccc solid; margin-bottom: 10px; font-weight: bold; display: none;">
		<?php echo $this->e($title); ?>
	</div>
	<div class="container-fluid">
		<?php echo $this->section("content"); ?>
	</div>

</body>

</html>