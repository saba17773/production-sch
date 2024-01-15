<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class UserService
{

	public function create(
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
	) {
		$conn = Database::connect();

		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT Username FROM UserMaster
				WHERE Username = ?",
			[$username]
		);

		if ($query === true) {
			return 'username นี้มีอยู่แล้วในระบบ';
		}

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'ไม่สามารถเชื่อมต่อได้';
		}

		$create_user = Sqlsrv::insert(
			$conn,
			"INSERT INTO UserMaster(
					Username,
					Password,
					Password2,
					Name,
					Department,
					Warehouse,
					Location,
					Authorize,
					EmployeeID,
					Barcode,
					Shift,
					Status,
					PermissionID,
					Company,
					SkipingDelay,
					UnitComponent,
					SectionComponent
					-- DirectTo,
					-- DirectToMobile
				)VALUES(
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, ?, ?,	?,
					?, ?
				)",
			[
				$username,
				$password,
				md5($password),
				$fullname,
				$department,
				(int) $warehouse,
				(int) $location,
				(int) $auth,
				$employee,
				$username,
				$shift,
				(int) $status,
				(int) $permission,
				$company,
				$time_check,
				$unit,
				$section
			]
		);

		if ($create_user) {
			$InsertLogUser = sqlsrv_query(
				$conn,
				"INSERT INTO [EA_APP].[dbo].[TB_USER_APP] (EMP_CODE,USER_NAME,HOST_NAME,PROJECT_NAME,CREATE_DATE)
	          VALUES (?,?,?,?,getdate())",
				[
					$employee,
					$username,
					gethostbyaddr($_SERVER['REMOTE_ADDR']),
					'Production Scheduler'
				]
			);

			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 400;
		}
	}

	public function update(
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
	) {
		$conn = Database::connect();
		$dateTime = date('Y-m-d H:i:s');
		$update_user = Sqlsrv::insert(
			$conn,
			"UPDATE UserMaster
				SET Username = ?,
				Password =?,
				Password2 = ?,
				Name = ?,
				Department = ?,
				Warehouse = ?,
				Location = ?,
				Authorize = ?,
				EmployeeID = ?,
				Barcode = ?,
				Shift = ?,
				Status = ?,
				PermissionID = ?,
				Company = ?,
				SkipingDelay = ?,
				UnitComponent = ?,
				SectionComponent = ?
				-- DirectTo = ?,
				-- DirectToMobile = ?
				WHERE ID = ?",
			[
				$username,
				$password,
				md5($password),
				$fullname,
				$department,
				(int) $warehouse,
				(int) $location,
				(int) $auth,
				$employee,
				$username,
				(int) $shift,
				(int) $status,
				(int) $permission,
				$company,
				$time_check,
				$unit,
				$section,
				// $default_page,
				// $default_page_mobile,
				$id
			]
		);

		if ($update_user) {

			if ($status === 1) {
				$InsertLogUser = sqlsrv_query(
					$conn,
					"INSERT INTO [EA_APP].[dbo].[TB_USER_APP] (EMP_CODE,USER_NAME,HOST_NAME,PROJECT_NAME,CREATE_DATE)
		          VALUES (?,?,?,?,getdate())",
					[
						$employee,
						$username,
						gethostbyaddr($_SERVER['REMOTE_ADDR']),
						'Production Scheduler'
					]
				);

				$InsertLogUserUSER_APP_CODE = sqlsrv_query(
					$conn,
					"INSERT INTO [192.168.90.30\DEVELOP].[EA_APP].[dbo].[USER_APP_CODE] (EMP_CODE,HOST_NAME,USER_NAME,PROJECT_NAME,CREATE_DATE,STATUS)
		          VALUES (?,?,?,?,getdate())",
					[
						$employee,
						gethostbyaddr($_SERVER['REMOTE_ADDR']),
						$username,
						'Production Scheduler',
						$dateTime,
						1

					]
				);
			}

			if ($status === 0) {
				// $DeleteLogUser = sqlsrv_query(
				// 	$conn,
				// 	"DELETE FROM [EA_APP].[dbo].[TB_USER_APP] WHERE EMP_CODE = ? AND  USER_NAME= ? AND PROJECT_NAME = ?",
				// 	[
				// 		$employee,
				// 		$username,
				// 		'Production Scheduler'
				// 	]
				// );


				$dateTime = date('Y-m-d H:i:s');

				$DeleteLogUser =  Sqlsrv::insert(
					$conn,
					"UPDATE [EA_APP].[dbo].[TB_USER_APP]
						SET UPDATE_DATE = ?, 
						 STATUS = ?
						WHERE USER_APP_CODE = (SELECT MAX(USER_APP_CODE) AS USER_APP_CODE FROM  [EA_APP].[dbo].[TB_USER_APP] WHERE  PROJECT_NAME = ? AND USER_NAME = ?)",
					[
						$dateTime,
						0,
						'Production Scheduler',
						$username


					]
				);
			}

			return 200;
		} else {
			return 400;
		}
	}

	public function isExist($username, $password)
	{
		$conn = Database::connect();
		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT Username, Password
				FROM UserMaster
				-- WHERE Username = ?
				-- AND Password = ?
				WHERE Username COLLATE Latin1_General_CS_AS = ?
				AND Password COLLATE Latin1_General_CS_AS = ?
				AND USERACTIVE = ?
				",
			[$username, $password, 1]
		);

		return $query;
	}

	public function getUserData($username)
	{
		if ($username === null) {
			return false;
		}

		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT U.*,
				P.DefaultDesktop,
				P.DefaultMobile,
				DPM.Description AS DPMDESC
				FROM UserMaster U
				LEFT JOIN PermissionMaster P ON U.PermissionID = P.ID
				LEFT JOIN DepartmentMaster DPM ON DPM.Code = U.Department
				WHERE Username = ?",
			[$username]
		);
		return $query;
	}

	public function all()
	{
		$conn = Database::connect();
		$department_desc = $_SESSION["user_department_desc"];
		$warehouse = $_SESSION["user_warehouse"];
		$location = $_SESSION["user_location"];

		if ($_SESSION['user_name'] === 'admin') {
			$sql = "SELECT
				U.Username,
				U.Password,
				U.ID,
				U.Name,
				D.Code [Department],
				D.Description [DepartmentDesc],
				W.ID [Warehouse],
				W.Description [WarehouseDesc],
				L.ID [Location],
				L.Description [LocationDesc],
				U.Authorize,
				AM.Description [AuthorizeDesc],
				U.EmployeeID,
				U.Barcode,
				U.Shift,
				S.Description [ShiftDesc],
				U.Status,
				U.PermissionID,
				P.Description [PermissionDesc],
				U.Company,
				U.DirectTo,
				U.DirectToMobile,
				U.SkipingDelay,
				U.UnitComponent,
				U.SectionComponent
				FROM UserMaster U
				LEFT JOIN DepartmentMaster D
					ON U.Department = D.Code
				LEFT JOIN WarehouseMaster W
					ON W.ID = U.Warehouse
				LEFT JOIN Location L
					ON L.ID = U.Location
				LEFT JOIN ShiftMaster S
					ON S.ID = U.Shift
				LEFT JOIN PermissionMaster P
					ON P.ID = U.PermissionID
				LEFT JOIN AuthorizeMaster AM
					ON AM.ID = U.Authorize
				ORDER BY U.ID DESC";
		} else {
			$sql = "SELECT
				U.Username,
				U.Password,
				U.ID,
				U.Name,
				D.Code [Department],
				D.Description [DepartmentDesc],
				W.ID [Warehouse],
				W.Description [WarehouseDesc],
				L.ID [Location],
				L.Description [LocationDesc],
				U.Authorize,
				AM.Description [AuthorizeDesc],
				U.EmployeeID,
				U.Barcode,
				U.Shift,
				S.Description [ShiftDesc],
				U.Status,
				U.PermissionID,
				P.Description [PermissionDesc],
				U.Company,
				U.DirectTo,
				U.DirectToMobile,
				U.SkipingDelay
				FROM UserMaster U
				LEFT JOIN DepartmentMaster D
					ON U.Department = D.Code
				LEFT JOIN WarehouseMaster W
					ON W.ID = U.Warehouse
				LEFT JOIN Location L
					ON L.ID = U.Location
				LEFT JOIN ShiftMaster S
					ON S.ID = U.Shift
				LEFT JOIN PermissionMaster P
					ON P.ID = U.PermissionID
				LEFT JOIN AuthorizeMaster AM
					ON AM.ID = U.Authorize
				WHERE D.Description = '$department_desc'
				AND U.Warehouse = '$warehouse'
				AND U.Location = '$location'
				ORDER BY U.ID DESC";
		}
		$query = Sqlsrv::queryJson(
			$conn,
			$sql
		);
		return $query;
	}

	public function isActive($username)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Status FROM UserMaster
				WHERE Username = ?
				AND Status = 1",
			[$username]
		);
	}

	public function isUserBarcodeExist($barcode_user)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Barcode FROM UserMaster
			WHERE Barcode = ?",
			[$barcode_user]
		);
	}

	public function isAuthorize($barcode_user, $password, $type)
	{
		// $type = Field Name in Table AuthorizeMaster
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT U.Barcode FROM UserMaster U
			LEFT JOIN AuthorizeMaster AM ON U.Authorize = AM.ID
			WHERE U.Barcode = ?
			AND AM.$type = 1
			AND U.Password = ?",
			[$barcode_user, $password]
		);
	}

	public function isDepartmentTrue($barcode_user, $type)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT U.Barcode FROM UserMaster U
			LEFT JOIN AuthorizeMaster AM ON U.Authorize = AM.ID
			WHERE U.Barcode COLLATE Latin1_General_CS_AS = ?
			AND AM.$type = 1
			AND U.Department = ?",
			[$barcode_user, $_SESSION["user_department"]]
		);
	}

	public function getDefaultPage($permission, $device)
	{
		if ($device === 'mobile') {
			$sql = 'SELECT M.Link FROM PermissionMaster P
						LEFT JOIN MenuMaster M ON P.DefaultMobile = M.ID AND M.Status = 1
						WHERE P.ID = ?';
		} else {
			$sql = 'SELECT M.Link FROM PermissionMaster P
						LEFT JOIN MenuMaster M ON P.DefaultDesktop = M.ID AND M.Status = 1
						WHERE P.ID = ?';
		}

		$conn = Database::connect();

		$defaultUrl = Sqlsrv::queryArray(
			$conn,
			$sql,
			[$permission]
		);

		if ($defaultUrl) {
			return root . $defaultUrl[0]['Link'];
		} else {
			return root;
		}
	}

	public function insertLog($username, $empid, $device_, $type)
	{
		$conn = Database::connect();
		$getdate = date("Y-m-d H:i:s");
		$computername = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$remark = $_SERVER['HTTP_USER_AGENT'];

		$InsertLog = sqlsrv_query(
			$conn,
			"INSERT INTO [WEB_CENTER].[dbo].[LoginLogs] (EmployeeID,ComputerName,Username,LoginDevice,LoginDate,ProjectID,Remark)
	      VALUES(?,?,?,?,?,?,?)",
			[$empid, $computername, $username, $device_, $getdate, 6, $remark]
		);

		$InsertlogApp = sqlsrv_query(
			$conn,
			"INSERT INTO [EA_APP].[dbo].[TB_LOG_APP] (EMP_CODE,USER_NAME,HOST_NAME," . $type . ",PROJECT_NAME)
	      VALUES (?,?,?,?,?)",
			array(
				$empid,
				$username,
				$computername,
				date('Y-m-d H:i:s'),
				'Production Scheduler'
			)
		);

		$update = Sqlsrv::insert(
			$conn,
			"UPDATE UserMaster SET LastLogin=?
			WHERE Username=?",
			[$getdate, $username]
		);
		if ($update) {
			return true;
		} else {
			return false;
		}
	}
}
