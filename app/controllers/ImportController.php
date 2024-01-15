<?php

namespace App\Controllers;

use App\Services\ImportService;

class ImportController
{
	public function __construct()
	{
		$this->import = new ImportService;
	}

	public function importTopTurn()
	{
		renderView("page/import_topturn");
	}

	public function saveImportTopturn()
	{
		$fileExcel = str_replace(" ", "_", $_FILES["import_topturn"]["name"]);
		$type = pathinfo($fileExcel,PATHINFO_EXTENSION);
		$fileExcelRenamed = "topturn.". $type;
		$target_dir = "./resources/topturn/";
		$target_file = $target_dir . $fileExcelRenamed;
		$uploadOk = 1;

		if ($type !== "xlsx") {
			echo "File type incorrect! (Please upload only excel file) ";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    echo "<br>Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
		    if (move_uploaded_file($_FILES["import_topturn"]["tmp_name"], $target_file)) {
		        // echo "The file ". basename( $_FILES["import_topturn"]["name"]). " has been uploaded.";
		        if (!file_exists($target_file)) {
		        	echo "File not found.";
		        } else {
		        	$result = self::updateTopTurn(new \SpreadsheetReader($target_file));
		        	if ($result["result"] === true) {
		        		header("Location: " . root . "/import/topturn?r=success&total=" . $result["total"] .
		        			"&import=" . $result["import"] . "&not_import=" . $result["not_import"]);
		        	} else {
		        		header("Location: " . root . "/import/topturn?r=failed&total=" . $result["total"] .
		        			"&import=" . $result["import"] . "&not_import=" . $result["not_import"]);
		        	}
		        }
		       
		    } else {
		        echo "<br>Sorry, there was an error uploading your file.";
		    }
		}
	}

	public function updateTopTurn($rows)
	{
		$import = 0;
		$not_import = 0;
		$errors = 0;
		$skipHeader = 0;

		foreach ($rows as $row) {
			if ($skipHeader >= 1) {
			
				if ($this->import->isCureTireExist($row[0]) === false) {
					$errors += 1;
				} else if ($row[1] <= 0 || $row[2] <= 0) {
					$errors += 1;
				}
			}
			$skipHeader++;
		}

		// if ($errors > 0) {
		// 	// failed
		// 	return [
		// 		"result" => false,
		// 		"total" => count($rows) - 1,
		// 		"import" => $import,
		// 		"not_import" => $not_import
		// 	];
		// }

		foreach ($rows as $row) {
			if ($skipHeader >= 1) {

				if($row[1] === "" || $row[1] === null){ $row[1] = 0; }
				if($row[2] === "" || $row[2] === null){ $row[2] = 0; }

				$result = $this->import->updateTopTurn($row[0], $row[1], $row[2]);
				if ($result["result"]  === true) {
					$import += 1;
				} else {
					$not_import += 1;
				}

				// if ($this->import->isTopTurnChange($row[0], $row[1], $row[2]) === false) {
				// 	$result = $this->import->updateTopTurn($row[0], $row[1], $row[2]);
				// 	if ($result["result"]  === true) {
				// 		$import += 1;
				// 	}
				// } else {
				// 	$not_import += 1;
				// }
			}
			$skipHeader++;
		}

		return [
			"result" => true,
			"total" => count($rows) - 2,
			"import" => (count($rows) - 2) - $errors,
			"not_import" => (int)$errors
		];
		
	}

	public function importCureCode()
	{
		renderView("page/import_curecode");
	}

	public function saveImportCureCode()
	{
		$fileExcel = str_replace(" ", "_", $_FILES["import_curecode"]["name"]);
		$type = pathinfo($fileExcel,PATHINFO_EXTENSION);
		$fileExcelRenamed = "curecode.". $type;
		$target_dir = "./resources/curecode/";
		$target_file = $target_dir . $fileExcelRenamed;
		$uploadOk = 1;

		if ($type !== "xlsx") {
			echo "File type incorrect! (Please upload only excel file) ";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    echo "<br>Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
		    if (move_uploaded_file($_FILES["import_curecode"]["tmp_name"], $target_file)) {
		        // echo "The file ". basename( $_FILES["import_topturn"]["name"]). " has been uploaded.";
		        if (!file_exists($target_file)) {
		        	echo "File not found.";
		        } else {
		        	$result = self::updateCureCode(new \SpreadsheetReader($target_file));
		        	if ($result["result"] === true) {
		        		header("Location: " . root . "/import/curecode?r=success&total=" . $result["total"] .
		        			"&import=" . $result["import"] . "&not_import=" . $result["not_import"]);
		        	} else {
		        		header("Location: " . root . "/import/curecode?r=failed&total=" . $result["total"] .
		        			"&import=" . $result["import"] . "&not_import=" . $result["not_import"]);
		        	}
		        }
		       
		    } else {
		        echo "<br>Sorry, there was an error uploading your file.";
		    }
		}
	}

	public function updateCureCode($rows)
	{
		$import = 0;
		$not_import = 0;
		foreach ($rows as $row) {
			if ($this->import->isCureTireExist($row[0]) === true) {
				$result = $this->import->updateCureCode($row[0], $row[1], $row[2], $row[3]);
					if ($result === false) {
						break;
						return [
							"result" => false,
							"total" => count($rows) - 1,
							"import" => $import,
							"not_import" => $not_import
						];
					} else {
						$import += 1;
					}
			} else {
				$new = $this->import->createNewCureCode($row[0], $row[1], $row[2], $row[3]);
				$not_import += 1;
			}
		}
		return [
			"result" => true,
			"total" => count($rows) - 1,
			"import" => $import,
			"not_import" => $not_import
		];
	}
}