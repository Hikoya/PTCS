<?php

require("../class/class.main.php");
require("../vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

if(isset($_POST["uploadlecturer"]))
{
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
	
	if(!empty($_FILES['lecturer']['name']))
	{
		if(is_uploaded_file($_FILES['lecturer']['tmp_name']))
		{	
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['lecturer']['tmp_name']);
			$spreadsheet = $reader->load($_FILES['lecturer']['tmp_name']);
			
			$sheet = $spreadsheet->getActiveSheet();
			
			$highestRow = $sheet->getHighestRow(); // e.g. 10
			$highestColumn = $sheet->getHighestColumn(); // e.g 'F'
			$highestColumn++; //skip first column
			
			$connection = mysqli_connect($servername, $username, $password, $dbname );
			
			if($append_mode == 1){
				
			}					
			else{
				$truncate_query = "TRUNCATE table " . $functions->SanitizeForSQLExt($lecturers, $connection) . " ";
				mysqli_query($connection, $truncate_query);
			}
			
			$i = 0;
			foreach ($sheet->getRowIterator() as $row) {
				
				if($i === 0){
					$i++;
					continue;
				}
				
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(FALSE); 
			
				$lecturer = NULL;
				
		
				foreach ($cellIterator as $cell) {
	
				    $value = $cell->getValue();
					if(!isset($lecturer))
						$lecturer = $value;
				}
				
				if(isset($lecturer)){
					$prevQuery = "SELECT id FROM ".$functions->SanitizeForSQLExt($lecturers, $connection)." WHERE lower(name) = '".strtolower($functions->SanitizeForSQLExt($lecturer, $connection))."'";
					$prevResult = mysqli_query($connection, $prevQuery);
						
					if($prevResult && mysqli_num_rows($prevResult) > 0){
						if($prevResult_row = $prevResult->fetch_array(MYSQLI_BOTH)){	
							$update_query = "UPDATE " . $functions->SanitizeForSQLExt($lecturers, $connection)." set name = '". strtoupper($functions->SanitizeForSQLExt($lecturer, $connection)) ."' WHERE name = '". $prevResult_row['name'] ."' ";
							mysqli_query($connection , $update_query);
						}	
					}else{
						$nowQuery = "INSERT INTO ".$functions->SanitizeForSQLExt($lecturers, $connection)." (name) VALUES ('". strtoupper($functions->SanitizeForSQLExt($lecturer, $connection))."')";
						mysqli_query($connection, $nowQuery);
					}
				}
					
			}
			
			$folder = $_SERVER["DOCUMENT_ROOT"].$excel_path;
			$path = $_FILES['lecturer']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_file_name = "lecturer".".".$ext;
			
			move_uploaded_file($_FILES['lecturer']['tmp_name'], $folder.$new_file_name);	

			header("Location: lecturers.php");			
		}
	}
}

if(isset($_POST["uploadvenue"]))
{
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'
	, 'application/vnd.ms-excel' , 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' , 'application/vnd.ms-excel.sheet.macroEnabled.12' , 'application/vnd.ms-excel.template.macroEnabled.12');
	
	if(!empty($_FILES['venue']['name']))
	{
		if(is_uploaded_file($_FILES['venue']['tmp_name']))
		{	
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['venue']['tmp_name']);
			$spreadsheet = $reader->load($_FILES['venue']['tmp_name']);
			
			$sheet = $spreadsheet->getActiveSheet();
			
			$highestRow = $sheet->getHighestRow(); // e.g. 10
			$highestColumn = $sheet->getHighestColumn(); // e.g 'F'
			$highestColumn++;
	
			$connection = mysqli_connect($servername, $username, $password, $dbname );
			
			if($append_mode == 1){
			}
			else{
				$truncate_query = "TRUNCATE table " . $functions->SanitizeForSQLExt($venues , $connection) . " ";
				mysqli_query($connection, $truncate_query);
			}
			
			$i = 0;
			foreach ($sheet->getRowIterator() as $row) {
				
				if($i === 0){
					$i++;
					continue;
				}
				
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(FALSE); 
			
				$value = NULL;
				
				foreach ($cellIterator as $cell) {
	
					if(!isset($value))
						$value = $cell->getValue();
				}
				
				$prevQuery = "SELECT id FROM ".$functions->SanitizeForSQLExt($venues , $connection)." WHERE lower(name) = '". strtolower($functions->SanitizeForSQLExt($value, $connection))."'";
				$prevResult = mysqli_query($connection, $prevQuery);
					
				if($prevResult && mysqli_num_rows($prevResult) > 0){	
					if($prevResult_row = $prevResult->fetch_array(MYSQLI_BOTH)){	
						$update_query = "UPDATE " . $functions->SanitizeForSQLExt($venues, $connection)." set name = '". $functions->SanitizeForSQLExt($value, $connection) ."' WHERE name = '". $prevResult_row['name'] ."' ";
						mysqli_query($connection , $update_query);
					}	
				}else{
						
					$nowQuery = "INSERT INTO ".$functions->SanitizeForSQLExt($venues, $connection)." (name) VALUES ('".$functions->SanitizeForSQLExt($value, $connection)."')";
					mysqli_query($connection, $nowQuery);
				}
					
			}
			
	
			$folder = $_SERVER["DOCUMENT_ROOT"].$excel_path;
			
			$path = $_FILES['venue']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_file_name = "venues".".".$ext;
			
			move_uploaded_file($_FILES['venue']['tmp_name'], $folder.$new_file_name);	

			header("Location: venues.php");	
			
		}
	}
}

if(isset($_POST["uploadparent"]))
{
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
	
	if(!empty($_FILES['parent']['name']))
	{
		if(is_uploaded_file($_FILES['parent']['tmp_name']))
		{	
			
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['parent']['tmp_name']);
			$spreadsheet = $reader->load($_FILES['parent']['tmp_name']);
			
			$sheet = $spreadsheet->getActiveSheet();
		
			$connection = mysqli_connect($servername, $username, $password, $dbname );
			$i = 0;
			
			//$full_mpdf = new Mpdf\mPDF();
			
			$highestRow = $sheet->getHighestRow(); // e.g. 10
			$highestColumn = $sheet->getHighestColumn(); // e.g 'F'
			
			if($append_mode == 1){
			}
			else{
				$truncate_query = "TRUNCATE table " . $functions->SanitizeForSQLExt($parents, $connection) . " ";
				mysqli_query($connection, $truncate_query);
			}
			
			foreach ($sheet->getRowIterator() as $key => $row) {
				
				if($i === 0){
					$i++;
					continue;
				}
				
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(FALSE); 
				
				$adm_no = NULL;
				$course = NULL;
				$class = NULL;
				$name = NULL;
				$OS = NULL;
				$OS_lecturer = NULL;
				
				foreach ($cellIterator as $cell) {
					$value = $cell->getValue();
					if(!isset($adm_no))
				        $adm_no = $value;
					else if(!isset($course))
						$course = $value;
					else if(!isset($class))
						$class = $value;
					else if(!isset($name))
						$name = $value;
					else if(!isset($OS))
						$OS = $value;
					else if(!isset($OS_lecturer))
						$OS_lecturer = $value;
				}
				
				//echo nl2br($adm_no . " " . $course . " " . $class . " " . $name . " " . $OS . " " . $OS_lecturer . " \n") ;
			
					
				if(isset($adm_no) && isset($course) && isset($class) && isset($name) && isset($OS) && isset($OS_lecturer))
				{
					$parent_exist = false;
					$parent_id = 0;
					
				    $prevQuery = "SELECT auto_id FROM ".$functions->SanitizeForSQLExt($parents, $connection)." WHERE id = '".$functions->SanitizeForSQLExt($adm_no, $connection)."'";
				
					$prevResult = mysqli_query($connection, $prevQuery);
					
					if($prevResult && mysqli_num_rows($prevResult) > 0){	
						if($prevResult_row = $prevResult->fetch_array(MYSQLI_BOTH)){
							$parent_exist = true;
							$parent_id = $prevResult_row['auto_id'];
						}
					}else{
						$parent_exist = false;
					}
					
						$courseQuery = "SELECT id FROM ".$functions->SanitizeForSQLExt($courses, $connection)." WHERE lower(shortform) = '".strtolower($functions->SanitizeForSQLExt($course, $connection))."'";
						
						$courseResult = mysqli_query($connection, $courseQuery);
						if($courseResult && mysqli_num_rows($courseResult) > 0){	
							if($row = $courseResult->fetch_array(MYSQLI_BOTH))
								$course_id = $row['id'];
						}
						else{
							
							if($create_if_notexist == 1)
							{
								if ($course == "DASE"){ $course_full = "Diploma in AerOSpace Engineering"; }
								else if ($course == "DEEE"){ $course_full = "Diploma in Electrical and Electronic Engineering"; }
								else if ($course == "DCPE"){ $course_full = "Diploma in Computer Engineering"; }
								else if ($course == "DES"){ $course_full = "Diploma in Engineering Systems"; }
								else if ($course == "DESM"){ $course_full = "Diploma in Energy System and Management"; }
								else if ($course == "DEB"){ $course_full = "Diploma in Engineering with Business"; }
								else if ($course == "DCEP"){ $course_full = "Diploma in Common Engineering"; }
								else {$course_full = $course;}
								
								$course_insert_query = "INSERT INTO ".$courses." (name,shortform) VALUES ('".$functions->SanitizeForSQLExt($course_full, $connection)."', '".$functions->SanitizeForSQLExt($course, $connection)."') ";
								$course_result = mysqli_query($connection,$course_insert_query);
								
								$courseQuery_again = "SELECT id FROM ".$functions->SanitizeForSQLExt($courses, $connection)." WHERE lower(shortform) = '". strtolower($functions->SanitizeForSQLExt($course, $connection)) ."'";
						
								$courseResult_again = mysqli_query($connection, $courseQuery_again);
								if($courseResult_again && mysqli_num_rows($courseResult_again) > 0){	
									if($row_again = $courseResult_again->fetch_array(MYSQLI_BOTH))
										$course_id = $row_again['id'];
								}
								else{
									$course_id = 0;
								}
							}
							else
								$course_id = 0;
				
						}
				
						$classQuery = "SELECT id FROM ".$functions->SanitizeForSQLExt($classes, $connection)." WHERE lower(name) = '".strtolower($functions->SanitizeForSQLExt($class, $connection))."'";
							
						$classResult = mysqli_query($connection, $classQuery);
						if($classResult && mysqli_num_rows($classResult) > 0){	
							if($row = $classResult->fetch_array(MYSQLI_BOTH))
								$class_id = $row['id'];
						}
						else{
							if($create_if_notexist == 1)
							{
								$class_insert_query = "INSERT INTO ".$functions->SanitizeForSQLExt($classes, $connection)." (name) VALUES ('".$functions->SanitizeForSQLExt($class, $connection)."') ";
								$class_result = mysqli_query($connection,$class_insert_query);
								
								$classQuery_again = "SELECT id FROM ".$functions->SanitizeForSQLExt($classes, $connection)." WHERE lower(name) = '". strtolower($functions->SanitizeForSQLExt($class, $connection)) ."'";
								
								$classResult_again = mysqli_query($connection, $classQuery_again);
								if($classResult_again && mysqli_num_rows($classResult_again) > 0){	
									if($row_again = $classResult_again->fetch_array(MYSQLI_BOTH))
										$class_id = $row_again['id'];
								}
								else{
									$class_id = 0;
								}
							
							}
							else
								$class_id = 0;
						}
						
                        if($OS == 1 && (strtolower($OS_lecturer) == strtolower("NIL"))){							
							$functions->HandleError("OS Lecturer not Assigned to :" . $name);
							continue;
						}		

						if($OS == 1 && (strtolower($OS_lecturer) != strtolower("NIL"))){
							
							$OS_lecturer_query = "SELECT id from " . $functions->SanitizeForSQLExt($lecturers, $connection) . " where lower(name) = '" . strtolower($functions->SanitizeForSQLExt($OS_lecturer, $connection)) . "' ";
							$OS_lecturer_result = mysqli_query($connection, $OS_lecturer_query);
							if($OS_lecturer_result && mysqli_num_rows($OS_lecturer_result) > 0){	
								if($row = $OS_lecturer_result->fetch_array(MYSQLI_BOTH))
									$OS_lecturer_id = $row['id'];
							}
							else{
								if($create_if_notexist == 1)
								{
									$OSlecturer_insert_query = "INSERT INTO ".$functions->SanitizeForSQLExt($lecturers, $connection)." (name) VALUES ('".$functions->SanitizeForSQLExt($OS_lecturer, $connection)."') ";
									$OSlecturer_insert_result = mysqli_query($connection , $OSlecturer_insert_query);
									
									$OSlecturer_insert_query_again = "SELECT id from " . $functions->SanitizeForSQLExt($lecturers, $connection) . " where lower(name) = '" . strtolower($functions->SanitizeForSQLExt($OS_lecturer, $connection)) . "' ";
									
									$OSlecturer_insert_result_again = mysqli_query($connection, $OSlecturer_insert_query_again);
									if($OSlecturer_insert_result_again && mysqli_num_rows($OSlecturer_insert_result_again) > 0){	
										if($row_again = $OSlecturer_insert_result_again->fetch_array(MYSQLI_BOTH))
											$OS_lecturer_id = $row_again['id'];
									}
									else{
										$OS_lecturer_id = 0;
									}
								
								}
								else
									$OS_lecturer_id = 0;
							}
						}
						else{
							$OS_lecturer_id = 0;
						}
						
						if(($OS == 0 && (strtolower($OS_lecturer) == strtolower("NIL"))) || ($OS == 1 && (strtolower($OS_lecturer) != strtolower("NIL")))){
							
							if(!$parent_exist){
								$nowQuery = "INSERT INTO ".$functions->SanitizeForSQLExt($parents, $connection)." (id,course,class,name,OS,OS_lecturer) VALUES ('".$functions->SanitizeForSQLExt($adm_no, $connection)."' , '".$functions->SanitizeForSQLExt($course_id, $connection)."' , '".$functions->SanitizeForSQLExt($class_id, $connection)."', '". strtoupper($functions->SanitizeForSQLExt($name, $connection)) ."' , '".$functions->SanitizeForSQLExt($OS, $connection)."', '" . $functions->SanitizeForSQLExt($OS_lecturer_id, $connection) . "')";
								
								mysqli_query($connection, $nowQuery);
							}
							else{
								
								if($parent_id != 0){	
									$update_query = "UPDATE ".$functions->SanitizeForSQLExt($parents, $connection)." SET id = '".$functions->SanitizeForSQLExt($adm_no, $connection)."' , course = '".$functions->SanitizeForSQLExt($course_id, $connection)."' , class = '".$functions->SanitizeForSQLExt($class_id, $connection)."' , name = '". strtoupper($functions->SanitizeForSQLExt($name, $connection)) ."' , OS = '".$functions->SanitizeForSQLExt($OS, $connection)."' , OS_lecturer = '" . $functions->SanitizeForSQLExt($OS_lecturer_id, $connection) . "' WHERE auto_id = '". $parent_id ."' ";
									
									mysqli_query($connection , $update_query);
								}
							}
						}
						
				}
				
			}
			
			$folder = $_SERVER["DOCUMENT_ROOT"].$excel_path;
			
			$path = $_FILES['parent']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_file_name = "parent".".".$ext;
			
			move_uploaded_file($_FILES['parent']['tmp_name'], $folder.$new_file_name);	

			header("Location: parents.php");			
		}
	}
}

if(isset($_POST["uploadtimeslot"]))
{
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'
	, 'application/vnd.ms-excel' , 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' , 'application/vnd.ms-excel.sheet.macroEnabled.12' , 'application/vnd.ms-excel.template.macroEnabled.12');
	
	if(!empty($_FILES['timeslot']['name']))
	{
		if(is_uploaded_file($_FILES['timeslot']['tmp_name']))
		{	
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['timeslot']['tmp_name']);
			$spreadsheet = $reader->load($_FILES['timeslot']['tmp_name']);
			
			$sheet = $spreadsheet->getActiveSheet();
			
			$connection = mysqli_connect($servername, $username, $password, $dbname );
			$i = 0;
			
			if($append_mode == 1){
			}
			else{
				$truncate_query = "TRUNCATE table " . $functions->SanitizeForSQLExt($time, $connection) . " ";
				mysqli_query($connection, $truncate_query);
			}
			
			foreach ($sheet->getRowIterator() as $row) {
				
				if($i === 0){
					$i++;
					continue;
				}
				
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(FALSE); 
				
				$start = NULL;
				$end = NULL;
				$OS = NULL;
				
				foreach ($cellIterator as $cell) {
				
				    $value = $cell->getValue();
					if(!isset($start))
				        $start = $value;
					else if(!isset($end))
						$end = $value;
					else if(!isset($OS))
						$OS = $value;
					
				}
				
				if(isset($start) && isset($end))
					$time_slot = $start . " - " . $end;
				else
					$time_slot = NULL;
				
				if(isset($time_slot))
				{ 
					
					$prevQuery = "SELECT id FROM ".$functions->SanitizeForSQLExt($time, $connection)." WHERE slots = '".$functions->SanitizeForSQLExt($time_slot, $connection)."' and OS = '" . $functions->SanitizeForSQLExt($OS, $connection) . "'";
					$prevResult = mysqli_query($connection, $prevQuery);
						
					if($prevResult && mysqli_num_rows($prevResult) > 0){	
						if($prevResult_row = $prevResult->fetch_array(MYSQLI_BOTH)){
							
							$update_query = "UPDATE ".$functions->SanitizeForSQLExt($time, $connection)." SET start = '".$functions->SanitizeForSQLExt($start, $connection)."' , end = '".$functions->SanitizeForSQLExt($end, $connection)."' , slots = '".$functions->SanitizeForSQLExt($time_slot, $connection)."' , OS = '".$functions->SanitizeForSQLExt($OS, $connection)."' where id = '". $prevResult_row['id'] ."' ";
							
							mysqli_query($connection, $update_query);
						}
					}else{
						
						$nowQuery = "INSERT INTO ".$functions->SanitizeForSQLExt($time, $connection)." (start,end,slots,OS) VALUES ('".$functions->SanitizeForSQLExt($start, $connection)."', '".$functions->SanitizeForSQLExt($end, $connection)."', '".$functions->SanitizeForSQLExt($time_slot, $connection)."' , '".$functions->SanitizeForSQLExt($OS, $connection)."')";
						
						mysqli_query($connection, $nowQuery);
						
			
					}	
				}
				
			}
					
			$folder = $_SERVER["DOCUMENT_ROOT"].$excel_path;
			
			$path = $_FILES['timeslot']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_file_name = "timeslot".".".$ext;
			
			move_uploaded_file($_FILES['timeslot']['tmp_name'], $folder.$new_file_name);	

			header("Location: timeslot.php");	
			
		}
	}
}

if(isset($_POST["uploadclass"]))
{
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'
	, 'application/vnd.ms-excel' , 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' , 'application/vnd.ms-excel.sheet.macroEnabled.12' , 'application/vnd.ms-excel.template.macroEnabled.12');
	
	if(!empty($_FILES['class']['name']))
	{
		if(is_uploaded_file($_FILES['class']['tmp_name']))
		{	
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['class']['tmp_name']);
			$spreadsheet = $reader->load($_FILES['class']['tmp_name']);
			
			$sheet = $spreadsheet->getActiveSheet();
			
			$connection = mysqli_connect($servername, $username, $password, $dbname );
			$i = 0;
			
			if($append_mode == 1){
			}
			else{
				$truncate_query = "TRUNCATE table " . $functions->SanitizeForSQLExt($classes, $connection) . " ";
				mysqli_query($connection, $truncate_query);
			}
			
			foreach ($sheet->getRowIterator() as $row) {
				
				if($i === 0){
					$i++;
					continue;
				}
				
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(FALSE); 
				
				$level = NULL;
				$class_name = NULL;
				
				foreach ($cellIterator as $cell) {
					
				    $value = $cell->getValue();
					if(!isset($level))
				        $level = $value;
					else if(!isset($class_name))
						$class_name = $value;
				}
						
				if(isset($level) && isset($class_name))
				{
					if((int)$class_name < 10)
					{
						$class_name = "0".(int)$class_name;
					}
					
					$full_class = $level.$class_name;
					
					$prevQuery = "SELECT id FROM ".$functions->SanitizeForSQLExt($classes, $connection)." WHERE lower(name) = '". strtolower($functions->SanitizeForSQLExt($full_class, $connection))."'";
					$prevResult = mysqli_query($connection, $prevQuery);
						
					if($prevResult && mysqli_num_rows($prevResult) > 0){	
						if($prevResult_row = $prevResult->fetch_array(MYSQLI_BOTH)){
							
							$update_query = "UPDATE ".$functions->SanitizeForSQLExt($classes, $connection)." SET name = '". $functions->SanitizeForSQLExt($full_class, $connection) ."' WHERE id = '". $prevResult_row['id'] ."' ";
							
							mysqli_query($connection, $update_query);
						}
					}else{
									
						$nowQuery = "INSERT INTO ".$functions->SanitizeForSQLExt($classes, $connection)." (name) VALUES ('".$functions->SanitizeForSQLExt($full_class, $connection)."' )";
						
						mysqli_query($connection, $nowQuery);
					}	
				}
				
			}
					
			$folder = $_SERVER["DOCUMENT_ROOT"].$excel_path;
			
			$path = $_FILES['class']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_file_name = "class".".".$ext;
			
			move_uploaded_file($_FILES['class']['tmp_name'], $folder.$new_file_name);	

			header("Location: classes.php");	
			
		}
	}
}

if(isset($_POST["uploadcourse"]))
{
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'
	, 'application/vnd.ms-excel' , 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' , 'application/vnd.ms-excel.sheet.macroEnabled.12' , 'application/vnd.ms-excel.template.macroEnabled.12');
	
	if(!empty($_FILES['course']['name']))
	{
		if(is_uploaded_file($_FILES['course']['tmp_name']))
		{	
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['course']['tmp_name']);
			$spreadsheet = $reader->load($_FILES['course']['tmp_name']);
			
			$sheet = $spreadsheet->getActiveSheet();
			
			$connection = mysqli_connect($servername, $username, $password, $dbname );
			$i = 0;
			
			if($append_mode == 1){
			}
			else{
				$truncate_query = "TRUNCATE table " . $functions->SanitizeForSQLExt($courses, $connection) . " ";
				mysqli_query($connection, $truncate_query);
			}
			
			foreach ($sheet->getRowIterator() as $row) {
				
				if($i === 0){
					$i++;
					continue;
				}
				
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(FALSE); 
				
				$name = NULL;
				$shortform = NULL;
				
				foreach ($cellIterator as $cell) {
					
				    $value = $cell->getValue();
					if(!isset($name))
				        $name = $value;
					else if(!isset($shortform))
						$shortform = $value;
				}
						
				if(isset($name) && isset($shortform))
				{
					//echo nl2br("" . $name . " " . $shortform . "\n");
					
					$prevQuery = "SELECT id FROM ".$functions->SanitizeForSQLExt($courses, $connection)." WHERE lower(shortform) = '".strtolower($functions->SanitizeForSQLExt($shortform, $connection))."'";
					$prevResult = mysqli_query($connection, $prevQuery);
						
					if($prevResult && mysqli_num_rows($prevResult) > 0){	
						if($prevResult_row = $prevResult->fetch_array(MYSQLI_BOTH)){
						
							$update_query = "UPDATE ".$functions->SanitizeForSQLExt($courses, $connection)." SET name = '".$functions->SanitizeForSQLExt($name, $connection)."' , shortform = '".$functions->SanitizeForSQLExt($shortform, $connection)."' WHERE id = '" . $prevResult_row['id'] . "' ";
							
							mysqli_query($connection, $update_query);
						}
						
					}else{
									
						$nowQuery = "INSERT INTO ".$functions->SanitizeForSQLExt($courses, $connection)." (name, shortform) VALUES ('".$functions->SanitizeForSQLExt($name, $connection)."' , '".$functions->SanitizeForSQLExt($shortform, $connection)."')";
						
						mysqli_query($connection, $nowQuery);
					}	
				}
			
			}
					
			$folder = $_SERVER["DOCUMENT_ROOT"].$excel_path;
			
			$path = $_FILES['course']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_file_name = "course".".".$ext;
			
			move_uploaded_file($_FILES['course']['tmp_name'], $folder.$new_file_name);	

			header("Location: courses.php");	
			
		}
	}
}



if(isset($_POST["uploadslots"]))
{
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'
	, 'application/vnd.ms-excel' , 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' , 'application/vnd.ms-excel.sheet.macroEnabled.12' , 'application/vnd.ms-excel.template.macroEnabled.12');
	
	if(!empty($_FILES['slots']['name']))
	{
		if(is_uploaded_file($_FILES['slots']['tmp_name']))
		{	
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['slots']['tmp_name']);
			$spreadsheet = $reader->load($_FILES['slots']['tmp_name']);
			
			$sheet = $spreadsheet->getActiveSheet();
			
			$connection = mysqli_connect($servername, $username, $password, $dbname );
			$i = 0;
			
			if($append_mode == 1){
			}
			else{
				$truncate_query = "TRUNCATE table " . $functions->SanitizeForSQLExt($slots, $connection) . " ";
				mysqli_query($connection, $truncate_query);
			}
			
			
			foreach ($sheet->getRowIterator() as $row) {
				
				if($i === 0){
					$i++;
					continue;
				}
				
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(FALSE); 
				
				$venue = NULL;
				$lecturer = NULL;
				$course = NULL;
				$class = NULL;
		
				foreach ($cellIterator as $cell) {
	
				    $value = $cell->getValue();
					if(!isset($venue))
				        $venue = $value;
					else if(!isset($lecturer))
						$lecturer = $value;
					else if(!isset($course))
						$course = $value;
					else if(!isset($class))
						$class = $value;
				}
				
				
				if(isset($venue) &&  isset($lecturer) && isset($course) && isset($class))
				{
					//echo nl2br("SOMETHING:  " . $venue . " " . $lecturer . " " . $course . " " . $class . " " . "\n");
				
					$courseQuery = "SELECT id FROM ".$functions->SanitizeForSQLExt($courses, $connection)." WHERE lower(shortform) = '".strtolower($functions->SanitizeForSQLExt($course, $connection))."'";
						
					$courseResult = mysqli_query($connection, $courseQuery);
					if($courseResult && mysqli_num_rows($courseResult) > 0){	
						if($row = $courseResult->fetch_array(MYSQLI_BOTH))
							$course_id = $row['id'];
					}
					else{
						
						if($create_if_notexist == 1)
						{
							
							if ($course == "DASE"){ $course_full = "Diploma in AerOSpace Engineering"; }
								else if ($course == "DEEE"){ $course_full = "Diploma in Electrical and Electronic Engineering"; }
								else if ($course == "DCPE"){ $course_full = "Diploma in Computer Engineering"; }
								else if ($course == "DES"){ $course_full = "Diploma in Engineering Systems"; }
								else if ($course == "DESM"){ $course_full = "Diploma in Energy System and Management"; }
								else if ($course == "DEB"){ $course_full = "Diploma in Engineering with Business"; }
								else if ($course == "DCEP"){ $course_full = "Diploma in Common Engineering"; }
								else {$course_full = $course;}
								
							$course_insert_query = "INSERT INTO ".$courses." (name,shortform) VALUES ('".$functions->SanitizeForSQLExt($course_full, $connection)."', '".$functions->SanitizeForSQLExt($course, $connection)."') ";
							$course_result = mysqli_query($connection,$course_insert_query);
							
							$courseQuery_again = "SELECT id FROM ".$functions->SanitizeForSQLExt($courses, $connection)." WHERE lower(shortform) = '". strtolower($functions->SanitizeForSQLExt($course, $connection)) ."'";
						
							$courseResult_again = mysqli_query($connection, $courseQuery_again);
							if($courseResult_again && mysqli_num_rows($courseResult_again) > 0){	
								if($row_again = $courseResult_again->fetch_array(MYSQLI_BOTH))
									$course_id = $row_again['id'];
							}
							else{
								$course_id = 0;
							}
						}
						else
						   $course_id = 0;
					}
					
					$classQuery = "SELECT id FROM ".$functions->SanitizeForSQLExt($classes, $connection)." WHERE lower(name) = '". strtolower($functions->SanitizeForSQLExt($class, $connection)) ."'";
							
					$classResult = mysqli_query($connection, $classQuery);
					if($classResult && mysqli_num_rows($classResult) > 0){	
						if($row = $classResult->fetch_array(MYSQLI_BOTH))
							$class_id = $row['id'];
					}
					else{
						
						if($create_if_notexist == 1)
						{
							$class_insert_query = "INSERT INTO ".$functions->SanitizeForSQLExt($classes, $connection)." (name) VALUES ('".$functions->SanitizeForSQLExt($class, $connection)."') ";
							$class_result = mysqli_query($connection,$class_insert_query);
							
							$classQuery_again = "SELECT id FROM ".$functions->SanitizeForSQLExt($classes, $connection)." WHERE lower(name) = '". strtolower($functions->SanitizeForSQLExt($class, $connection))."'";
								
							$classResult_again = mysqli_query($connection, $classQuery_again);
							if($classResult_again && mysqli_num_rows($classResult_again) > 0){	
								if($row_again = $classResult_again->fetch_array(MYSQLI_BOTH))
									$class_id = $row_again['id'];
							}
							else{
								$class_id = 0;
							}
						}
						else
							$class_id = 0;
					}
					
					$lecturerQuery = "SELECT id  FROM ".$functions->SanitizeForSQLExt($lecturers, $connection)." WHERE lower(name) = '".strtolower($functions->SanitizeForSQLExt($lecturer, $connection))."'";
					
					$lecturerResult = mysqli_query($connection, $lecturerQuery);
					if($lecturerResult && mysqli_num_rows($lecturerResult) > 0){
						if($row = $lecturerResult->fetch_array(MYSQLI_BOTH))
							$lecturer_id = $row['id'];
					}
					else{
						
						if($create_if_notexist == 1)
						{
							$lecturer_insert_query = "INSERT INTO ".$functions->SanitizeForSQLExt($lecturers, $connection)." (name) VALUES ('". strtoupper($functions->SanitizeForSQLExt($lecturer, $connection)) ."') ";
							$lecturer_result = mysqli_query($connection,$lecturer_insert_query);
							
							$lecturer_insert_query_again = "SELECT id from " . $functions->SanitizeForSQLExt($lecturers, $connection) . " where lower(name) = '" . strtolower($functions->SanitizeForSQLExt($lecturer, $connection)). "' ";
									
							$lecturer_insert_result_again = mysqli_query($connection, $lecturer_insert_query_again);
							if($lecturer_insert_result_again && mysqli_num_rows($lecturer_insert_result_again) > 0){	
								if($row_again = $lecturer_insert_result_again->fetch_array(MYSQLI_BOTH))
									$lecturer_id = $row_again['id'];
							}
							else{
								$lecturer_id = 0;
							}
						}
						else
						$lecturer_id = 0;
					}
					
					$venueQuery = "SELECT id  FROM ".$functions->SanitizeForSQLExt($venues, $connection)." WHERE lower(name) = '". strtolower($functions->SanitizeForSQLExt(
					$venue, $connection))."'";
					
					$venueResult = mysqli_query($connection, $venueQuery);
					if($venueResult && mysqli_num_rows($venueResult) > 0){
						if($row = $venueResult->fetch_array(MYSQLI_BOTH))
							$venue_id = $row['id'];
					}
					else{
						
						if($create_if_notexist == 1)
						{
							$venue_insert_query = "INSERT INTO ".$functions->SanitizeForSQLExt($venues, $connection)." (name) VALUES ('". strtoupper($functions->SanitizeForSQLExt($venue, $connection)) ."') ";
							$venue_result = mysqli_query($connection,$venue_insert_query);
							
							$venue_insert_query_again = "SELECT id from " . $functions->SanitizeForSQLExt($venues, $connection) . " where lower(name) = '" . strtolower($functions->SanitizeForSQLExt($venue, $connection)) . "' ";
									
							$venue_insert_result_again = mysqli_query($connection, $venue_insert_query_again);
							if($venue_insert_result_again && mysqli_num_rows($venue_insert_result_again) > 0){	
								if($row_again = $venue_insert_result_again->fetch_array(MYSQLI_BOTH))
									$venue_id = $row_again['id'];
							}
							else{
								$venue_id = 0;
							}
						}
						else
							$venue_id = 0;
					}
					
					$is_OS = false;
					$OS_class = substr($class, 2, 4);
					
					if( $OS_class == "99")
					{
						$is_OS = true;
						
						$OS_lecturerQuery = "SELECT id  FROM ".$functions->SanitizeForSQLExt($lecturers, $connection)." WHERE lower(name) = '". strtolower($functions->SanitizeForSQLExt($lecturer, $connection))."'";
						
						$OS_lecturerResult = mysqli_query($connection, $OS_lecturerQuery);
						if($OS_lecturerResult && mysqli_num_rows($OS_lecturerResult) > 0){
							if($row = $OS_lecturerResult->fetch_array(MYSQLI_BOTH))
								$OS_lecturer_id = $row['id'];
						}
						else{
							if($create_if_notexist == 1)
							{
								$OSlecturer_insert_query = "INSERT INTO ".$functions->SanitizeForSQLExt($lecturers, $connection)." (name) VALUES ('".strtoupper($functions->SanitizeForSQLExt($lecturer, $connection)) ."') ";
								$OS_lecturer_result = mysqli_query($connection,$OSlecturer_insert_query);
								
								$OSlecturer_insert_query_again = "SELECT id from " . $functions->SanitizeForSQLExt($lecturers, $connection) . " where lower(name) = '" . strtolower($functions->SanitizeForSQLExt($lecturer, $connection)) . "' ";
									
								$OSlecturer_insert_result_again = mysqli_query($connection, $OSlecturer_insert_query_again);
								if($OSlecturer_insert_result_again && mysqli_num_rows($OSlecturer_insert_result_again) > 0){	
									if($row_again = $OSlecturer_insert_result_again->fetch_array(MYSQLI_BOTH))
										$OS_lecturer_id = $row_again['id'];
								}
								else{
									$OS_lecturer_id = 0;
								}
							}
							else
							    $OS_lecturer_id = 0;
						}
					}
					
					$prevQuery = "SELECT id FROM ".$functions->SanitizeForSQLExt($slots, $connection)." WHERE venue = '".$functions->SanitizeForSQLExt($venue_id, $connection)."' AND lecturer = '". $functions->SanitizeForSQLExt($lecturer_id, $connection) ."' AND class = '". $functions->SanitizeForSQLExt($class_id, $connection) ."' AND course = '". $functions->SanitizeForSQLExt($course_id, $connection) ."' ";
					$prevResult = mysqli_query($connection, $prevQuery);
				
					if($prevResult && mysqli_num_rows($prevResult) > 0){	
						while($prevResult_row = $prevResult->fetch_array(MYSQLI_BOTH)){
							
							if($is_OS){
								$update_query = "UPDATE ".$functions->SanitizeForSQLExt($slots, $connection)." SET venue = '" . $venue_id . "' , lecturer = '" . $OS_lecturer_id . "' , class = '" . $class_id . "' , course = '" . $course_id . "' WHERE id = '" . $prevResult_row['id'] . "' ";

								mysqli_query($connection, $update_query);
							}
							else{
								
								$update_query = "UPDATE ".$functions->SanitizeForSQLExt($slots, $connection)." SET venue = '" . $venue_id . "' , lecturer = '" . $lecturer_id . "' , class = '" . $class_id . "' , course = '" . $course_id . "' WHERE id = '" . $prevResult_row['id'] . "' ";

								mysqli_query($connection, $update_query);
							}
						
						}
					}
					else{
						
						if($is_OS)
						{
							$select_OS_time_qry = "SELECT * FROM " . $functions->SanitizeForSQLExt($time, $connection) . " ORDER BY ID ASC ";
							if($select_OS_result = mysqli_query($connection, $select_OS_time_qry)){
								if(mysqli_num_rows($select_OS_result) > 0){
									while($row_OS = $select_OS_result->fetch_array(MYSQLI_BOTH))
									{
										if($row_OS['OS'] == 1 && $is_OS)
										{
											$insert_query = 'insert into '.$slots.'(
														venue,
														lecturer,
														class,
														course,
														timeslot				
														)
														values
														(
														"' . $venue_id . '",
														"' . $OS_lecturer_id . '",
														"' . $class_id . '",
														"' . $course_id . '",
														"' . $row_OS['id'] . '"
														)';      
													
													//echo $insert_query;
											mysqli_query($connection, $insert_query);
													
										}
									}
								}
							}
						}
						else{
							
							$select_time_qry = "SELECT * FROM " . $functions->SanitizeForSQLExt($time, $connection) . " ORDER BY ID ASC ";
							if($result = mysqli_query($connection, $select_time_qry)){
								if(mysqli_num_rows($result) > 0){
									while($row = $result->fetch_array(MYSQLI_BOTH))
									{
										if($row['OS'] == 0 && !$is_OS)
										{
											$insert_query = 'insert into '.$slots.'(
												venue,
												lecturer,
												class,
												course,
												timeslot				
												)
												values
												(
												"' . $venue_id . '",
												"' . $lecturer_id . '",
												"' . $class_id . '",
												"' . $course_id  . '",
												"' . $row['id'] . '"
												)';      
												
											mysqli_query($connection,$insert_query);			
										}					
									}
								}
							}
						}
	
					}
				}
			}
					
			$folder = $_SERVER["DOCUMENT_ROOT"].$excel_path;
			
			$path = $_FILES['slots']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_file_name = "slots".".".$ext;
			
			move_uploaded_file($_FILES['slots']['tmp_name'], $folder.$new_file_name);	

			header("Location: slots.php");	
			
		}
	}
}




?>