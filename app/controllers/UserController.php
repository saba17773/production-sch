<?php

namespace App\Controllers;

use App\Services\UserService;
use App\Services\MenuService;
use App\Components\Utils;

class UserController
{
	public function create()
	{
		$username = trim(filter_input(INPUT_POST, "username"));
		$password = trim(filter_input(INPUT_POST, "password"));
		$fullname = trim(filter_input(INPUT_POST, "fullname"));
		$department = trim(filter_input(INPUT_POST, "department"));
		$warehouse = trim(filter_input(INPUT_POST, "warehouse"));
		$location = trim(filter_input(INPUT_POST, "location"));
		$status = trim(filter_input(INPUT_POST, "status")) ? 1 : 0;
		$time_check = trim(filter_input(INPUT_POST, "time_check")) ? 1 : 0;
		$auth = trim(filter_input(INPUT_POST, "auth"));
		$permission = trim(filter_input(INPUT_POST, "permission"));
		$formtype = trim(filter_input(INPUT_POST, "form_type"));
		$employee = trim(filter_input(INPUT_POST, "empid"));
		$company = trim(filter_input(INPUT_POST, "company"));
		$component = trim(filter_input(INPUT_POST, "component")) ? 1 : 0;

		if ($component === 1) {
			$unit = trim(filter_input(INPUT_POST, "unit"));
			$section = Utils::arr2str($_POST['section']);
		} else {
			$unit = null;
			$section = null;
		}

		// $default_page = trim(filter_input(INPUT_POST, "default_page"));
		// $default_page_mobile = trim(filter_input(INPUT_POST, "default_page_mobile"));
		$id = filter_input(INPUT_POST, "_id");
		$shift = filter_input(INPUT_POST, "shift");

		if (!$username) {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอก Username"]));
		}

		if (!$password) {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอก Password"]));
		}

		if (!$department) {
			exit(json_encode(["status" => 404, "message" => "กรุณาเลือก department"]));
		}

		if (!$warehouse) {
			exit(json_encode(["status" => 404, "message" => "กรุณาเลือก warehouse"]));
		}

		if (!$location) {
			exit(json_encode(["status" => 404, "message" => "กรุณาเลือก location"]));
		}

		if (!$permission) {
			exit(json_encode(["status" => 404, "message" => "กรุณาเลือก permission"]));
		}

		if (!$employee) {
			exit(json_encode(["status" => 404, "message" => "กรุณาเลือก employee"]));
		}

		if (!$company) {
			exit(json_encode(["status" => 404, "message" => "กรุณาเลือก company"]));
		}

		// if (!$default_page) {
		// 	exit(json_encode(["status" => 404, "message" => "กรุณาเลือก default page"]));
		// }

		// if (!$default_page_mobile) {
		// 	exit(json_encode(["status" => 404, "message" => "กรุณาเลือก default page for mobile device"]));
		// }

		if ($formtype == "create") {
			$create_user = (new UserService)->create(
				$username,
				$password,
				$fullname,
				$department,
				$warehouse,
				$location,
				$status,
				$auth,
				$permission,
				$employee,
				$company,
				// $default_page,
				// $default_page_mobile,
				$shift,
				$time_check,
				$unit,
				$section
			);

			if ($create_user === 200) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
				exit;
			} else {
				echo json_encode(["status" => 404, "message" => $create_user]);
				exit;
			}
		}

		if ($formtype == "update") {
			$edit_user = (new UserService)->update(
				$username,
				$password,
				$fullname,
				$department,
				$warehouse,
				$location,
				$status,
				$auth,
				$permission,
				$employee,
				$company,
				$id,
				// $default_page,
				// $default_page_mobile,
				$shift,
				$time_check,
				$unit,
				$section
			);

			if ($edit_user === 200) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
				exit;
			} else {
				echo json_encode(["status" => 404, "message" => $edit_user]);
				exit;
			}
		}

		echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
		exit;
	}

	public function handheldAuth()
	{
		$username = filter_input(INPUT_POST, "hh_username");
		$password = filter_input(INPUT_POST, "hh_password");

		if (trim($username) == "") {
			exit(json_encode(["status" => 404, "message" => "Please fill Username data"]));
		}

		if (trim($password) == "") {
			exit(json_encode(["status" => 404, "message" => "Please fill Password data"]));
		}

		$is_exist = (new UserService)->isExist($username, $password);

		if ($is_exist === false) {
			exit(json_encode(["status" => 404, "message" => "User not found"]));
		}

		$user_data = (new UserService)->getUserData($username);

		if (!$user_data) {
			exit(json_encode(["status" => 404, "message" => "Can't fetch user data."]));
		}

		if ($user_data[0]["Location"] !== 3) {
			exit(json_encode(["status" => 404, "message" => "You don't have permission to access this section."]));
		}

		$detect = new \Mobile_Detect;

		if ($detect->isMobile()) {
			$_SESSION["user_device"] = "mobile";
		} else {
			$_SESSION["user_device"] = "desktop";
		}

		$_SESSION["user_name"] = $user_data[0]["Username"];
		$_SESSION["user_login"] = $user_data[0]["ID"];
		$_SESSION["user_company"] = $user_data[0]["Company"];
		$_SESSION["user_warehouse"] = $user_data[0]["Warehouse"];
		$_SESSION["user_location"] = $user_data[0]["Location"];
		$_SESSION["Shift"] = $user_data[0]["Shift"];
		$_SESSION["user_permission"] = $user_data[0]["PermissionID"];
		$_SESSION["user_department"] = $user_data[0]["Department"];
		$_SESSION["user_department_desc"] = $user_data[0]["DPMDESC"];

		echo json_encode([
			"status" => 200,
			"message" => "ดำเนินการเสร็จสิ้น",
			"location" => $user_data[0]["Location"]
		]);
	}

	public function desktopAuth()
	{
		$username = filter_input(INPUT_POST, "username_login");
		$password = filter_input(INPUT_POST, "password_login");
		$shift = filter_input(INPUT_POST, "shift");

		// $username = str_replace("@", "_", $username);

		if (trim($username) == "") {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอก Username"]));
		}

		if (trim($password) == "") {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอก Password"]));
		}

		$is_exist = (new UserService)->isExist($username, $password);

		if ($is_exist === false) {
			exit(json_encode(["status" => 404, "message" => "ข้อมูลไม่ถูกต้อง "]));
		}

		if ((new UserService)->isActive($username) === false) {
			exit(json_encode(["status" => 404, "message" => "ผู้ใช้นี้ยังไม่ได้เปิดใช้งาน"]));
		}

		$user_data = (new UserService)->getUserData($username);

		if (!$user_data) {
			exit(json_encode(["status" => 404, "message" => "ไม่สามารถดึงข้อมูลผู้ใช้ได้"]));
		}

		$detect = new \Mobile_Detect;

		if ($detect->isMobile()) {
			$_SESSION["user_device"] = "mobile";
			$device = $user_data[0]["DefaultMobile"];
			$device_ = 2;
		} else {
			$_SESSION["user_device"] = "desktop";
			$device = $user_data[0]["DefaultDesktop"];
			$device_ = 1;
		}

		$insertLog = (new UserService)->insertLog($username, $user_data[0]["EmployeeID"], $device_, 'LOGIN_DATE');

		$_SESSION["user_name"] = $user_data[0]["Username"];
		$_SESSION["user_login"] = $user_data[0]["ID"];
		$_SESSION["user_company"] = $user_data[0]["Company"];
		$_SESSION["user_warehouse"] = $user_data[0]["Warehouse"];
		$_SESSION["user_location"] = $user_data[0]["Location"];
		$_SESSION["Shift"] = $shift;
		$_SESSION["user_permission"] = $user_data[0]["PermissionID"];
		$_SESSION["user_department"] = $user_data[0]["Department"];
		$_SESSION["user_department_desc"] = $user_data[0]["DPMDESC"];
		$_SESSION["user_component"] = $user_data[0]["UnitComponent"];
		$_SESSION["user_componentsection"] = $user_data[0]["SectionComponent"];
		$_SESSION["user_employee"] = $user_data[0]["EmployeeID"];

		if ((int) $device !== 0) {

			$get_menu = (new MenuService)->getMenu($device);

			if ($get_menu === false) {
				$defaultLink = '/landing';
			} else {
				$link_direct = json_decode($get_menu);
				$defaultLink = $link_direct[0]->Link;
			}
		} else {
			$defaultLink = '/landing';
		}

		// $link_direct = self::getDefaultPage();

		echo json_encode([
			"status" => 200,
			"message" => "ดำเนินการเสร็จสิ้น",
			"user_location" => $_SESSION["user_location"],
			"redirectTo" => $defaultLink
		]);
	}

	public function all()
	{
		echo (new UserService)->all();
	}

	public function clearSession()
	{
		session_destroy();
		echo json_encode(["status" => 200, "message" => "clear session successful!"]);
	}

	public function logout()
	{

		$detect = new \Mobile_Detect;

		if ((int) $_SESSION["user_location"] === 3 && $detect->isMobile() === true) { // curing
			$redirect_path = root . "/hh/auth";
		} else if ((int) $_SESSION["user_permission"] === 19 && $detect->isMobile() === true) { // final inspect
			$redirect_path = root;
		} else {
			$redirect_path = root;
		}

		$insertLog = (new UserService)->insertLog($_SESSION["user_name"], $_SESSION["user_employee"], 1, 'LOGOUT_DATE');

		session_destroy();

		if ($redirect_path !== '') {
			header("Location: " . $redirect_path);
		} else {
			header("Location: / ");
		}
	}

	/**
	 * @param  string
	 * @param  string
	 * @return template
	 */
	public function genUserBarcode($username, $empCode, $password, $name)
	{
		renderView('page/user_barcode', [
			"username" => $username,
			"empCode" => $empCode,
			"name" => $name,
			"password" => base64_decode($password)
		]);
	}

	public function authorize()
	{
		$code = filter_input(INPUT_POST, "code");
		$user_password = filter_input(INPUT_POST, "password");
		$type = filter_input(INPUT_POST, "type");

		if ((new UserService)->isUserBarcodeExist($code) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่มี User ในระบบ"]));
		}

		if ((new UserService)->isAuthorize($code, $user_password, $type) === false) {
			exit(json_encode(["status" => 404, "message" => "User ไม่มีสิทธิ์อนุมัติ"]));
		}

		// if ((new UserService)->isDepartmentTrue($code, $type) === false) {
		// 	exit(json_encode(["status" => 404, "message" => "Location incorrect."]));
		// }

		exit(json_encode(["status" => 200, "message" => "Authorize successful!"]));
	}

	public function getAuthorizeType()
	{
		$type = filter_input(INPUT_POST, "type");

		$u = ['Unhold_Unrepair_GT', 'Unhold_Unrepair_Final'];
		$a = ['Adjust_GT', 'Adjust_Final', 'Adjust_FG'];

		if ($_SESSION["user_warehouse"] === 1) {
			if ($type === 'unhold_unrepair') {
				echo json_encode(["type" => $u[0]]);
			}

			if ($type === 'adjust') {
				echo json_encode(["type" => $a[0]]);
			}
		}

		if ($_SESSION["user_warehouse"] === 2) {
			if ($type === 'unhold_unrepair') {
				echo json_encode(["type" => $u[1]]);
			}

			if ($type === 'adjust') {
				echo json_encode(["type" => $a[1]]);
			}
		}

		if ($_SESSION["user_warehouse"] === 3) {
			if ($type === 'adjust') {
				echo json_encode(["type" => $a[2]]);
			}
		}
		// End
	}

	public function getUserLocation()
	{
		if (isset($_SESSION['user_location']) && $_SESSION['user_location'] !== '') {
			echo json_encode(["location" => $_SESSION['user_location']]);
		} else {
			echo json_encode(["location" => '']);
		}
	}

	public function getDefaultPage()
	{
		$link =  (new UserService)->getDefaultPage(
			$_SESSION['user_permission'],
			$_SESSION["user_device"]
		);

		$wrap = '<h1>Permission Required!</h1>';
		$wrap .= '<p>You don\'t have permission to access this page!</p>';
		$wrap .= '<a class="btn btn-primary btn-lg" href="' . $link . '" role="button">Go to home page.</a>';
		return $wrap;
	}

	public function getDefaultLink()
	{
		$link =  (new UserService)->getDefaultPage(
			$_SESSION['user_permission'],
			$_SESSION["user_device"]
		);

		return $link;
	}
}
