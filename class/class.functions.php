<?php

date_default_timezone_set('Asia/Singapore');
class Functions
{
	var $username;
    var $password;
    var $database;
    var $tablename;
	var $servername;
	
    var $connection;
	
	var $error_message;
	
	var $num_of_records;
	
	function InitNumRecords($num)
	{
		$this->num_of_records = $num;
	}
	
	function InitDB($database, $username, $pwd, $servername)
	{
		$this->username = $username;
		$this->password = $pwd;
		$this->database = $database;
		$this->servername = $servername;
	}
	
	function InitDBTable($parents, $slots, $lecturers, $courses, $classes, $venues, $timeslot, $timeinterval)
	{
		$this->parents = $parents;
		$this->slots = $slots;
		$this->lecturers = $lecturers;
		$this->courses = $courses;
		$this->classes = $classes;
		$this->venues = $venues;
		$this->timeslot = $timeslot;
		$this->timeinterval = $timeinterval;
	}
	
	function InitPath($pdf_path,$excel_path, $composer_path)
	{
		$this->pdf_path = $pdf_path;
		$this->excel_path = $excel_path;
		$this->composer_path = $composer_path;
	}
	
	function DBLogin(){
		
		$this->connection = mysqli_connect($this->servername, $this->username, $this->password, $this->database);

        if(!$this->connection){     
            return false;
        }
		
        if(!mysqli_select_db($this->connection, $this->database)){
            return false;
        }
		
        if(!mysqli_query($this->connection,"SET NAMES 'UTF8'")){
            return false;
        }
		
        return true;
	}
	
	function AddLecturer()
	{
		$formvars = array();
		
		$formvars['name'] = $this->Sanitize($_POST['name']);
		
		if(!$this->SaveLecturer($formvars)){
            return false;
        }
		
		return true;
	}
	
	function SaveLecturer(&$formvars)
    {
        if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return false;
        }
       
        if(!$this->IsFieldUnique($this->lecturers , $formvars,'name')){
            $this->HandleError("This lecturer is already registered.");
            return false;
        }        
		
        if(!$this->InsertLecturer($formvars)){
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
		
		if(!$this->GenerateLecturerSlot($formvars)){
			$this->HandleError("Unable to generate lecturer slots");
			return false;
		}
		
        return true;
    }
	
	function InsertLecturer(&$formvars)
	{
		$insert_query = 'insert into '.$this->lecturers.'(
                name   
                )
                values
                (
				"' . strtoupper($this->SanitizeForSQL($formvars['name'])) . '"
                )';      
				
        if(!mysqli_query($this->connection,$insert_query)){
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }        
        return true;
	}
	
	function GenerateLecturerSlot(&$formvars)
	{
		$formvars['name'] = $this->SanitizeForSQL($formvars['name']);
		
		$sanity_pass = false;
		$sanity_check = 'select * from '.$this->slots.' where lecturer = "'. $this->FindLecturerByName($formvars['name']) . '" ';
		if($sanity_result = mysqli_query($this->connection, $sanity_check)){
			if(mysqli_num_rows($sanity_result) > 0){
				$sanity_pass = false;
			}
			else{
				$sanity_pass = true;
			}
		}
		
		if($sanity_pass){
			
			if($this->FindLecturerByName($formvars['name']) == 0 )
				return true;
			
			$is_os = false;
		
			$search_query = 'select * from '.$this->parents.' where os_lecturer = "'. $this->FindLecturerByName($formvars['name']) .'") ';
			if($search_result = mysqli_query($this->connection, $search_query)){
				if(mysqli_num_rows($search_result) > 0 ){
					$is_os = true;
				}
			}
		
			if($is_os){
				$select_os_query = 'select * from '. $this->timeslot . ' where OS = 1 order by id asc';
				if($os_result = mysqli_query($this->connection, $select_os_query)){
					if(mysqli_num_rows($os_result) > 0){
						while($os_row = $os_result->fetch_array(MYSQLI_BOTH)){
							$insert_os_query = 'insert into ' . $this->slots . ' (lecturer , timeslot ) values ("'. $this->FindLecturerByName($formvars['name']) .'" , "'. $os_row['id'] .'") ';
							mysqli_query($this->connection, $insert_os_query);
						}
					}
				}
			}
				
			$normal_slot_query = 'select * from '. $this->timeslot . ' where OS = 0 order by id asc';
			
			if($normal_result = mysqli_query($this->connection , $normal_slot_query)){
				if(mysqli_num_rows($normal_result) > 0){
					while($normal_row = $normal_result->fetch_array(MYSQLI_BOTH)){
						$insert_normal_query = 'insert into ' . $this->slots . ' (lecturer, timeslot) values ("' . $this->FindLecturerByName($formvars['name']) . '", "' . $normal_row['id'] . '") ';
						mysqli_query($this->connection, $insert_normal_query);
					}
				}
			}
		}
		
		return true;
	}
	
	function AddCourse()
	{
		$formvars = array();
		
		$formvars['name'] = $this->Sanitize($_POST['name']);
		$formvars['shortform'] = $this->Sanitize($_POST['shortform']);
		
		if(!$this->SaveCourse($formvars)){
            return false;
        }
		
		return true;
	}
	
	function SaveCourse(&$formvars)
    {
        if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return false;
        }
       
        if(!$this->IsFieldUnique($this->courses , $formvars, 'name')){
            $this->HandleError("This course is already registered.");
            return false;
        } 

		if(!$this->IsFieldUnique($this->courses , $formvars, 'shortform')){
            $this->HandleError("This course is already registered.");
            return false;
        }    		
		
        if(!$this->InsertCourse($formvars)){
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
		
        return true;
    }
	
	function InsertCourse(&$formvars)
	{
		$insert_query = 'insert into '.$this->courses.'(
                name,
				shortform				
                )
                values
                (
				"' . $this->SanitizeForSQL($formvars['name']) . '",
				"' . $this->SanitizeForSQL($formvars['shortform']) . '"
                )';      
				
        if(!mysqli_query($this->connection,$insert_query)){
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }        
        return true;
	}
	
	function AddVenue()
	{
		$formvars = array();
		
		$formvars['name'] = $this->Sanitize($_POST['name']);
		
		if(!$this->SaveVenue($formvars)){
            return false;
        }
		
		return true;
	}
	
	function SaveVenue(&$formvars)
    {
        if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return false;
        }
       
        if(!$this->IsFieldUnique($this->venues , $formvars,'name')){
            $this->HandleError("This venue is already registered.");
            return false;
        }        
		
        if(!$this->InsertVenue($formvars)){
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
		
        return true;
    }
	
	function InsertVenue(&$formvars)
	{
		$insert_query = 'insert into '.$this->venues.'(
                name   
                )
                values
                (
				"' . strtoupper($this->SanitizeForSQL($formvars['name'])) . '"
                )';      
				
        if(!mysqli_query($this->connection,$insert_query)){
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }        
        return true;
	}
	
	function AddClass()
	{
		$formvars = array();
		
		$formvars['name'] = $this->Sanitize($_POST['name']);
		
		if(!$this->SaveClass($formvars)){
            return false;
        }
		
		return true;
	}
	
	function SaveClass(&$formvars)
    {
        if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return false;
        }
       
        if(!$this->IsFieldUnique($this->classes , $formvars,'name')){
            $this->HandleError("This class is already registered.");
            return false;
        }        
		
        if(!$this->InsertClass($formvars)){
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
		
        return true;
    }
	
	function InsertClass(&$formvars)
	{
		$insert_query = 'insert into '.$this->classes.'(
                name   
                )
                values
                (
				"' . $this->SanitizeForSQL($formvars['name']) . '"
                )';      
				
        if(!mysqli_query($this->connection,$insert_query)){
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }        
        return true;
	}
	
	function AddMultipleClass()
	{
		$formvars = array();
		
		$formvars['level'] = $this->Sanitize($_POST['level']);
		$formvars['start'] = $this->Sanitize($_POST['start']);
		$formvars['end'] = $this->Sanitize($_POST['end']);
		
		if(!$this->SaveMultipleClass($formvars)){
            return false;
        }
		
		return true;
	}
	
	function SaveMultipleClass(&$formvars)
    {
        if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return false;
        }
    
        if(!$this->InsertMultipleClass($formvars)){
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
		
        return true;
    }
	
	function InsertMultipleClass(&$formvars)
	{
		$start = (int) $formvars['start'];
		$end = (int) $formvars['end'];
		
		while(($end - $start) > 0){
			if($start < 10){
				$value = "0".$start;
			}
			else
				$value = $start;
			
			$class = $formvars['level'] . $value;
			
			$sanity_check = true;
			$sanity_query = ' select * from ' . $this->classes . ' where name = "'.$this->SanitizeForSQL($class).'"';
			if($sanity_result = mysqli_query($this->connection, $sanity_query)){
				if(mysqli_num_rows($sanity_result) > 0){
					$sanity_check = false;
				}
			}
			
			if($sanity_check){
				$insert_query = 'insert into '.$this->classes.'(
					name   
					)
					values
					(
					"' . $this->SanitizeForSQL($class) . '"
					)';      
					
				if(!mysqli_query($this->connection,$insert_query)){
					$this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
					return false;
				}
			}
			
			$start ++;
		}
        return true;
	}
	
	function AddTime()
	{
		$formvars = array();
		
		$formvars['start'] = $this->Sanitize($_POST['start']);
		$formvars['end'] = $this->Sanitize($_POST['end']);
		
		if(isset($_POST['os']))
			$formvars['os'] = 1;
		else
			$formvars['os'] = 0;
		
		if(!$this->SaveTime($formvars)){
            return false;
        }
		
		return true;
	}
	
	function SaveTime(&$formvars)
    {
		if(strtotime($formvars['end']) - strtotime($formvars['start']) <= 0){
			$this->HandleError("End time cannot be earlier than start time!");
            return false;
		}
		
		$formvars['slots'] = $formvars['start'] . " - " . $formvars['end'];
		
        if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return false;
        }
      
		
        if(!$this->InsertTime($formvars)){
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
		
        return true;
    }
	
	function InsertTime(&$formvars)
	{
		$sanity_check = true;
		$sanity_query = 'select * from '.$this->timeslot.' where slots = "'. $formvars['slots']. '" and OS = "'. $formvars['os'] .'" ';
		if($sanity_result = mysqli_query($this->connection,$sanity_query)){
			if(mysqli_num_rows($sanity_result) > 0){
				$sanity_check = false;
			}
		}
		
		if($sanity_check){
			$insert_query = 'insert into '.$this->timeslot.'(
				start,
				end,
                slots,
				OS
                )
                values
                (
				"' . $this->SanitizeForSQL($formvars['start']) . '",
				"' . $this->SanitizeForSQL($formvars['end']) . '",
				"' . $this->SanitizeForSQL($formvars['slots']) . '",
				"' . $this->SanitizeForSQL($formvars['os']) . '"
                )';      
				
			if(!mysqli_query($this->connection,$insert_query)){
				$this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
				return false;
			}        
		}
		
        return true;
	}
	
	function AddMultipleTime()
	{
		$formvars = array();
		
		$formvars['start'] = $this->Sanitize($_POST['start']);
		$formvars['end'] = $this->Sanitize($_POST['end']);
		
		if(isset($_POST['os']))
			$formvars['os'] = 1;
		else
			$formvars['os'] = 0;
		
		if(!$this->SaveMultipleTime($formvars)){
            return false;
        }
		
		return true;
	}
	
	function SaveMultipleTime(&$formvars)
    {
		if(strtotime($formvars['end']) - strtotime($formvars['start']) <= 0){
			$this->HandleError("End time cannot be earlier than start time!");
            return false;
		}
	
        if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return false;
        }
       
        if(!$this->InsertMultipleTime($formvars)){
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
		
        return true;
    }
	
	function InsertMultipleTime(&$formvars)
	{
		while(strtotime($formvars['end']) - strtotime($formvars['start']) > 0){
			
			$formvars['slotend'] = date("H:i", strtotime($formvars['start']) + $this->timeinterval*60 );
			$slots = $formvars['start'] . " - " . date("H:i", strtotime($formvars['start']) + $this->timeinterval*60 );
			
			$sanity_check = true;
			$sanity_query = 'select * from '.$this->timeslot.' where slots = "' . $slots .'" ';
			if($sanity_result = mysqli_query($this->connection, $sanity_query)){
				if(mysqli_num_rows($sanity_result) > 0){
					$sanity_check = false;
				}
			}
			
			if($sanity_check){
				$insert_query = 'insert into '.$this->timeslot.'(
				start,
				end,
                slots,
				OS
                )
                values
                (
				"' . $this->SanitizeForSQL($formvars['start']) . '",
				"' . $this->SanitizeForSQL($formvars['slotend']) . '",
				"' . $this->SanitizeForSQL($slots) . '",
				"' . $this->SanitizeForSQL($formvars['os']) . '"
                )';      
				
				if(!mysqli_query($this->connection,$insert_query)){
					$this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
					return false;
				}
			}
			
			$formvars['start'] = date("H:i", strtotime($formvars['start']) + $this->timeinterval*60 );
		}
		
        return true;
	}
	
	function AddParent()
	{
		$formvars = array();
		
		$formvars['course'] = $this->Sanitize($_POST['course']);
		$formvars['class'] = $this->Sanitize($_POST['class']);
		$formvars['name'] = $this->Sanitize($_POST['name']);
		$formvars['id'] = $this->Sanitize($_POST['adminno']);
		
		if(isset($_POST['os'])){
			$formvars['os'] = 1;
			$formvars['os_lecturer'] = $this->Sanitize($_POST['os_lecturer']);
		}
		else{
			$formvars['os'] = 0;
			$formvars['os_lecturer'] = NULL;
		}
		
		if(!$this->SaveParent($formvars)){
            return false;
        }
		
		return true;
	}
	
	function SaveParent(&$formvars)
    {
        if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return false;
        }
		
		if(!$this->IsFieldUnique($this->parents , $formvars, 'id')){
            $this->HandleError("This parent is already registered.");
            return false;
        } 
       
        if(!$this->InsertParent($formvars)){
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
		
        return true;
    }
	
	function InsertParent(&$formvars)
	{
		
		if($formvars['os'] == 1 && isset($formvars['os_lecturer'])){
			$insert_query = 'insert into '.$this->parents.'(
				id,
                class,
				course,
				name,
				OS,
				OS_lecturer
                )
                values
                (
				"' . $this->SanitizeForSQL($formvars['id']) . '",
				"' . $this->SanitizeForSQL($formvars['class']) . '",
				"' . $this->SanitizeForSQL($formvars['course']) . '",
				"' . $this->SanitizeForSQL($formvars['name']) . '",
				"' . $this->SanitizeForSQL($formvars['os']) . '",
				"' . $this->SanitizeForSQL($formvars['os_lecturer']) . '"
                )';      
		}
		else{
			$insert_query = 'insert into '.$this->parents.'(
				id,
                class,
				course,
				name,
				OS
                )
                values
                (
				"' . $this->SanitizeForSQL($formvars['id']) . '",
				"' . $this->SanitizeForSQL($formvars['class']) . '",
				"' . $this->SanitizeForSQL($formvars['course']) . '",
				"' . $this->SanitizeForSQL($formvars['name']) . '",
				"' . $this->SanitizeForSQL($formvars['os']) . '"
                )';      
				
		}
		
        if(!mysqli_query($this->connection,$insert_query))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }     
		
        return true;
	}
	
	function AddSlot()
	{
		$formvars = array();
		
		$formvars['venue'] = $this->Sanitize($_POST['venue']);
		$formvars['lecturer'] = $this->Sanitize($_POST['lecturer']);
		$formvars['timeslot'] = $this->Sanitize($_POST['timeslot']);
		$formvars['course'] = $this->Sanitize($_POST['course']);
		$formvars['class'] = $this->Sanitize($_POST['class']);
		
		if(!$this->SaveSlot($formvars))
        {
            return false;
        }
		
		return true;
	}
	
	function SaveSlot(&$formvars)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
       
        if(!$this->InsertSlot($formvars))
        {
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
		
        return true;
    }
	
	function InsertSlot(&$formvars)
	{
		$sanity_check = true;
		$sanity_query = 'select * from '.$this->slots.' where lecturer = "' .  $this->SanitizeForSQL($formvars['lecturer']) . '" and timeslot = "' . $this->SanitizeForSQL($formvars['timeslot']) . '" ';
		if($sanity_result = mysqli_query($this->connection, $sanity_query)){
			if(mysqli_num_rows($sanity_result)){
				$sanity_check = false;
			}
		}
		
		if($sanity_check){
			$insert_query = 'insert into '.$this->slots.'(
				venue,
				lecturer,
                class,
				course,
				timeslot				
                )
                values
                (
				"' . $this->SanitizeForSQL($formvars['venue']) . '",
				"' . $this->SanitizeForSQL($formvars['lecturer']) . '",
				"' . $this->SanitizeForSQL($formvars['class']) . '",
				"' . $this->SanitizeForSQL($formvars['course']) . '",
				"' . $this->SanitizeForSQL($formvars['timeslot']) . '"
                )';      
				
			if(!mysqli_query($this->connection,$insert_query)){
				$this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
				return false;
			}      
		}
		
		  
        return true;
	}
	
	function GetCourses($offset)
	{
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
            return "";
        }
		
		$select_qry = "SELECT * FROM " . $this->courses . " ORDER BY ID  ASC LIMIT " . $offset . " , " . $this->num_of_records . "  ";
		$message = "";
		if($result = mysqli_query($this->connection, $select_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$message .= "<tr>";
					$message .= "<td>" . $row['id'] . "</td>";
					$message .= "<td>" . $row['name'] . "</td>";
					$message .= "<td>" . $row['shortform'] . "</td>";
					$message .= "<td>" . '<input type="button" class="edit_button" id="delete_course'.$row['id'].'" value="Delete" onclick="delete_course('.$row['id'].')">' . "</td>";
					$message .= "</tr>";
				}
			}
			else{
				$message .= "<tr>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "</tr>";
			}
		}
		
		return $message;
	}
	
	function GetClasses($offset)
	{
		if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return "";
        }
		
		$select_qry = "SELECT * FROM " . $this->classes . " ORDER BY ID  ASC LIMIT " . $offset . " , " . $this->num_of_records . " ";
		$message = "";
		if($result = mysqli_query($this->connection, $select_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$message .= "<tr>";
					$message .= "<td>" . $row['id'] . "</td>";
					$message .= "<td>" . $row['name'] . "</td>";
					$message .= "<td>" . '<input type="button" class="edit_button" id="delete_class'.$row['id'].'" value="Delete" onclick="delete_class('.$row['id'].')">' . "</td>";
					$message .= "</tr>";
				}
			}
			else{
				$message .= "<tr>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "</tr>";
			}
		}
		
		return $message;
	}
	
	function GetParents($offset)
	{
		if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return "";
        }
		
		$select_qry = "SELECT * FROM " . $this->parents . " ORDER BY AUTO_ID  ASC LIMIT " . $offset . " , " . $this->num_of_records . " ";
		$message = "";
		
		if($result = mysqli_query($this->connection, $select_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$message .= "<tr>";
					$message .= "<td>" . $row['id'] . "</td>";
					$message .= "<td>" . $this->ResolveCourse($row['course']) . "</td>";
					$message .= "<td>" . $this->ResolveClass($row['class']) . "</td>";				
					$message .= "<td>" . $row['name'] . "</td>";
					$message .= "<td>" . $this->ResolveSlot($row['slot']) . "</td>";
					$message .= "<td>" . $this->ResolveLecturer($row['lecturer']) . "</td>";
					$message .= "<td>" . $this->ResolveOS($row['OS']) . "</td>";
					$message .= "<td>" . $this->ResolveLecturer($row['OS_lecturer']) . "</td>";
					$message .= "<td>" . '<input type="button" class="edit_button" id="delete_parent'.$row['auto_id'].'" value="Delete" onclick="delete_parent('.$row['auto_id'].')">' . "</td>";
					$message .= "</tr>";
				}
			}
			else{
				$message .= "<tr>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "</tr>";
			}
		}
		
		return $message;
	}
	
	function GetVenues($offset)
	{
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
            return "";
        }
		
		$select_qry = "SELECT * FROM " . $this->venues . " ORDER BY ID  ASC LIMIT " . $offset . " , " . $this->num_of_records . " ";
		$message = "";
		if($result = mysqli_query($this->connection, $select_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$message .= "<tr>";
					$message .= "<td>" . $row['id'] . "</td>";
					$message .= "<td>" . $row['name'] . "</td>";
					$message .= "<td>" . '<input type="button" class="edit_button" id="delete_venue'.$row['id'].'" value="Delete" onclick="delete_venue('.$row['id'].')">' . "</td>";
					$message .= "</tr>";
				}
			}
			else{
				$message .= "<tr>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "</tr>";
			}
		}
		
		return $message;
	}
	
	
	
	function GetTimeSlots($offset)
	{
		if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return "";
        }
		
		$select_qry = "SELECT * FROM " . $this->timeslot . " ORDER BY ID  ASC LIMIT " . $offset . " , " . $this->num_of_records . " ";
		$message = "";
		if($result = mysqli_query($this->connection, $select_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$message .= "<tr>";
					$message .= "<td>" . $row['id'] . "</td>";
					$message .= "<td>" . $row['slots'] . "</td>";
					$message .= "<td>" . $this->ResolveOS($row['OS']) . "</td>";
					$message .= "<td>" . '<input type="button" class="edit_button" id="delete_timeslot'.$row['id'].'" value="Delete" onclick="delete_timeslot('.$row['id'].')">' . "</td>";
					$message .= "</tr>";
				}
			}
			else{
				$message .= "<tr>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "</tr>";
			}
		}
		
		return $message;
	}
	
	function GetLecturers($offset)
	{
		if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return "";
        }
		
		$select_qry = "SELECT * FROM " . $this->lecturers . " ORDER BY ID  ASC LIMIT " . $offset . " , " . $this->num_of_records . " ";
		$message = "";
		if($result = mysqli_query($this->connection, $select_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$message .= "<tr>";
					$message .= "<td>" . $row['id'] . "</td>";
					$message .= "<td>" . $row['name'] . "</td>";
					$message .= "<td>" . '<input type="button" class="edit_button" id="delete_lecturer'.$row['id'].'" value="Delete" onclick="delete_lecturer('.$row['id'].')">' . "</td>";
					$message .= "</tr>";
				}
			}
			else{
				$message .= "<tr>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "</tr>";
			}
		}
		
		return $message;
	}
	
	function GetSlots($offset)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return "";
        }
		
		$select_qry = "SELECT * FROM " . $this->slots . " ORDER BY ID  ASC LIMIT " . $offset . " , " . $this->num_of_records . " ";
		$message = "";
		if($result = mysqli_query($this->connection, $select_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$message .= "<tr>";
					$message .= "<td>" . $row['id'] . "</td>";
					$message .= "<td>" . $this->ResolveVenue($row['venue']) . "</td>";
					$message .= "<td>" . $this->ResolveLecturer($row['lecturer']) . "</td>";
					$message .= "<td>" . $this->ResolveTimeSlot($row['timeslot']) . "</td>";
					$message .= "<td>" . $this->ResolveCourse($row['course']) . "</td>";
					$message .= "<td>" . $this->ResolveClass($row['class']) . "</td>";
					
					if(!empty($row['parent']))
						$message .= "<td>" . $this->ResolveParent($row['parent']) . "</td>";
					else
						$message .= "<td>" . "No parent assigned" . "</td>";
					
					$message .= "<td>" . '<input type="button" class="edit_button" id="delete_slot'.$row['id'].'" value="Delete" onclick="delete_slot('.$row['id'].')">' . "</td>";
					
					$message .= "</tr>";
				}
			}
			else{
				$message .= "<tr>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "<td> No Data</td>";
				$message .= "</tr>";
			}
		}
		
		return $message;
	}
	
	function SelectionBoxVenue()
	{
		$this->DBLogin();
		
		$content = '<option value=""></option>';
		
		$venue_qry = "SELECT * FROM " . $this->venues . " ORDER BY ID ASC ";
		if($result = mysqli_query($this->connection, $venue_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$content .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
				}
			}
		}
			
		return $content;
	}

	function SelectionBoxSlot()
	{
		$this->DBLogin();
		
		$content = '<option value=""></option>';
		
		$venue_qry = "SELECT * FROM " . $this->slots . " ORDER BY ID ASC ";
		if($result = mysqli_query($this->connection, $venue_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$content .= '<option value="'.$row['id'].'">'.$row['id'].'</option>';
				}
			}
		}
			
		return $content;
	}
	
	function SelectionBoxCourse()
	{
		$this->DBLogin();
		
		$content = '<option value=""></option>';
		
		$course_qry = "SELECT * FROM " . $this->courses . " ORDER BY ID ASC ";
		if($result = mysqli_query($this->connection, $course_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$content .= '<option value="'.$row['id'].'">'.$row['shortform'].'</option>';
				}
			}
		}
			
		return $content;
	}
	
	function SelectionBoxTimeSlot()
	{
		$this->DBLogin();
		
		$content = '<option value=""></option>';
		
		$os_query = 'select * from '. $this->timeslot . 'where os = 1 order by id asc';
		if($os_result = mysqli_query($this->connection, $os_query)){
			if(mysqli_num_rows($os_result) > 0){
				while($os_row = $os_result->fetch_array(MYSQLI_BOTH)){
					$content .= '<option value="'.$os_row['id'].'">'.$os_row['slots'].' (OS)'.'</option>';
				}
			}
		}
		
		$times_qry = "SELECT * FROM " . $this->timeslot . " where os = 0 ORDER BY ID ASC ";
		if($result = mysqli_query($this->connection, $times_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$content .= '<option value="'.$row['id'].'">'.$row['slots'].'</option>';
				}
			}
		}
			
		return $content;
	}
	
	function SelectionBoxClass()
	{
		$this->DBLogin();
		
		$content = '<option value=""></option>';
		
		$class_qry = "SELECT * FROM " . $this->classes . " ORDER BY ID ASC ";
		if($result = mysqli_query($this->connection, $class_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$content .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
				}
			}
		}
			
		return $content;
	}
	
	function SelectionBoxLecturer()
	{
		$this->DBLogin();
		
		$content = '<option value=""></option>';
		
		$lect_qry = "SELECT * FROM " . $this->lecturers . " ORDER BY ID ASC ";
		if($result = mysqli_query($this->connection, $lect_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$content .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
				}
			}
		}
			
		return $content;
	}
	
	function SelectionBoxParent()
	{
		$this->DBLogin();
		
		$content = '<option value=""></option>';
		
		$lect_qry = "SELECT * FROM " . $this->parents . " ORDER BY ID ASC ";
		if($result = mysqli_query($this->connection, $lect_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$content .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
				}
			}
		}
			
		return $content;
	}
	
	function ResolveCourse($course_id)
	{
		$this->DBLogin();
		
		$content = "";
		
		if($course_id == 0)
		{
			$content = "Undefined";
			return $content;
		}
		
		$cr_qry = "SELECT * FROM " . $this->courses . " WHERE id = " . $this->SanitizeForSQL($course_id) . " ";
		if($result = mysqli_query($this->connection, $cr_qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH)){
					$content = $row['shortform'];
				}
			}
			else{
				$content = "Not registered in database";
			}
		}
		else{
			$content = "Undefined";
		}
			
		return $content;
	}
	
	function ResolveParent($parent_id)
	{
		$this->DBLogin();
		
		$content = "";
		
		if($parent_id == 0)
		{
			$content = "Undefined";
			return $content;
		}
		
		$cr_qry = "SELECT * FROM " . $this->parents . " WHERE id = " . $this->SanitizeForSQL($parent_id) . " ";
		if($result = mysqli_query($this->connection, $cr_qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH)){
					$content = $row['name'];
				}
			}
			else{
				$content = "Not registered in database";
			}
		}else{
			$content = "Undefined";
		}
			
		return $content;
	}
	
	function ResolveClass($class_id)
	{
		$this->DBLogin();
		
		$content = "";
		
		if($class_id == 0)
		{
			$content = "Undefined";
			return $content;
		}
		
		$cr_qry = "SELECT * FROM " . $this->classes . " WHERE id = " . $this->SanitizeForSQL($class_id) . " ";
		if($result = mysqli_query($this->connection, $cr_qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH)){
					$content = $row['name'];
				}
			}
			else{
				$content = "Not registered in database";
			}
		}else{
			$content = "Undefined";
		}
			
		return $content;
	}
	
	function ResolveVenue($ven_id)
	{
		$this->DBLogin();
		
		$content = "";
		
		if($ven_id == 0)
		{
			$content = "Undefined";
			return $content;
		}
		
		$cr_qry = "SELECT * FROM " . $this->venues . " WHERE id = '" . $this->SanitizeForSQL($ven_id) . "' ";
		if($result = mysqli_query($this->connection, $cr_qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH)){
					$content = $row['name'];
				}
			}
			else{
				$content = "Not Registered";
			}
		}else{
			$content = "Undefined";
		}
			
		return $content;
	}
	
	function ResolveTimeSlot($time_id)
	{
		$this->DBLogin();
		
		$content = "";
		
		if($time_id == 0)
		{
			$content = "Invalid Time Slot";
			return $content;
		}
		else
		{
			$cr_qry2 = "SELECT * FROM " . $this->timeslot . " WHERE id = " . $this->SanitizeForSQL($time_id) . " ";
			if($result2 = mysqli_query($this->connection, $cr_qry2)){
				if(mysqli_num_rows($result2) > 0){
					if($row2 = $result2->fetch_array(MYSQLI_BOTH)){
						$content = $row2['slots'];
					}
				}
				else{
					$content = "Invalid time slot";
				}
			}
		}
		
		return $content;
	}
	
	function ResolveSlot($time_id)
	{
		$this->DBLogin();
		
		$content = "";
		
		if($time_id == 0)
		{
			$content = "No Slot Allocated";
			return $content;
		}
		else
		{
			$cr_qry = "SELECT * FROM " . $this->slots . " WHERE id = " . $this->SanitizeForSQL($time_id) . " ";
			if($result = mysqli_query($this->connection, $cr_qry)){
				if(mysqli_num_rows($result) > 0){
					if($row = $result->fetch_array(MYSQLI_BOTH)){
						$content = $row['timeslot'];
					}
				}
				else{
					$content = "Invalid";
				}
			}
			else{
				$content = "Invalid";
			}
			
			if($content != "Invalid")
			{
				$cr_qry2 = "SELECT * FROM " . $this->timeslot . " WHERE id = " . $this->SanitizeForSQL($content) . " ";
				if($result2 = mysqli_query($this->connection, $cr_qry2)){
					if(mysqli_num_rows($result2) > 0){
						if($row2 = $result2->fetch_array(MYSQLI_BOTH)){
							$content = $row2['slots'];
						}
					}
					else{
						$content = "Invalid";
					}
				}
				else{
					$content = "Invalid";
				}
			}
		}
		
		return $content;
		
	}
	
	function ResolveLecturer($lect_id)
	{
		$this->DBLogin();
		
		$content = "";
		
		if($lect_id == 0){
			$content = "No Lecturer";
			return $content;
		}
		
		$cr_qry = "SELECT * FROM " . $this->lecturers . " WHERE id = " . $this->SanitizeForSQL($lect_id) . " ";
		if($result = mysqli_query($this->connection, $cr_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH)){
					$content = $row['name'];
				}
			}
			else{
				$content = "Not Registered";
			}	
		}else{
			$content = "Undefined";
		}
			
		return $content;
	}
	
	function ResolveLecturerID($slot_id)
	{
		$this->DBLogin();
		
		$content = "";
		
		if($slot_id == 0){
			$content = 0;
			return $content;
		}
		
		$qry = "SELECT * FROM " . $this->slots . " WHERE id = " . $this->SanitizeForSQL($slot_id) . " ";
		
		if($result = mysqli_query($this->connection, $qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH)){
					$content = $row['lecturer'];
				}
			}
		}
		else{
			$content = 0;
		}
			
		return $content;
	}
	
	function ResolveOS($os_id)
	{
		$content = "";
		
		if($os_id == 1)
		{
			$content = "YES";
		}
		else if($os_id == 0)
		{
			$content = "NO";
		}
		else
			$content = "Undefined";
			
		return $content;
	}
	
	function AssignSlot()
	{
		$formvars = array();
		
		$formvars['slots'] = $this->Sanitize($_POST['slots']);
		$formvars['parent'] = $this->Sanitize($_POST['parent']);
		
		if(!$this->SaveAssignSlot($formvars)){
            return false;
        }
		
		return true;
	}
		
	function SaveAssignSlot(&$formvars)
    {
        if(!$this->DBLogin()){
            $this->HandleError("Database login failed!");
            return false;
        }
		
		if(!$this->CheckSlotAssigned($formvars))
		{
			$this->HandleError("Slot is already assigned to someone else!");
            return false;
		}
       
        if(!$this->UpdateSlot($formvars)){
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
		
		if(!$this->UpdateParent($formvars)){
			$this->HandleError("Inserting to Database failed!");
            return false;
		}
		
        return true;
    }
	
	function UpdateSlot(&$formvars)
	{
		$update_query = 'update '.$this->slots.'
				set parent = 
				"' . $this->SanitizeForSQL($formvars['parent']) . '"
                where id =
				"' . $this->SanitizeForSQL($formvars['slots']) . '"				
				';      
				
        if(!mysqli_query($this->connection,$update_query)){
            $this->HandleDBError("Error inserting data to the table\nquery:$update_query");
            return false;
        }        
        return true;
	}
	
	function UpdateParent(&$formvars)
	{
		$update_query = 'update '.$this->parents.'
				set slot = 
				"' . $this->SanitizeForSQL($formvars['slots']) . '"	,
				lecturer = 
				"' . $this->SanitizeForSQL($this->ResolveLecturerID($formvars['slots'])) . '"	
                where id =
				"' . $this->SanitizeForSQL($formvars['parent']) . '"		
				';      
				
        if(!mysqli_query($this->connection,$update_query)){
            $this->HandleDBError("Error inserting data to the table\nquery:$update_query");
            return false;
        }        
        return true;
	}

	function CheckSlotAssigned(&$formvars)
	{
		$check_qry = "SELECT * FROM " . $this->slots . " WHERE slot = '" . $formvars['slots'] . "'  AND parent is null ";
		if($result = mysqli_query($this->connection, $check_qry)){
			if(mysqli_num_rows($result) > 0){
				return true;			
			}
			else
				return false;
		}
		
	}
	
	function FindLecturerByName($name)
	{
		$name = $this->SanitizeForSQL($name);
		$lecturer_id = 0;
		
		$search_query = 'select * from '. $this->lecturers . 'where lower(name) like lower("%' . $name. '%")';
		if($search_result = mysqli_query($this->connection , $search_query)){
			if(mysqli_num_rows($search_result) > 1){
				$rigid_search = 'select * from '. $this->lecturers . 'where lower(name) = lower("'. $name .'") ';
				if($rigid_result = mysqli_query($this->connection , $rigid_search)){
					if(mysqli_num_rows($rigid_result) == 1){
						if($rigid_row = $rigid_result->fetch_array(MYSQLI_BOTH)){
							$lecturer_id = $rigid_row['id'];
						}
					}
				}
			}
			else if(mysqli_num_rows($search_result) == 1){
				if($search_row = $search_result->fetch_array(MYSQLI_BOTH)){
					$lecturer_id = $search_row['id'];
				}
			}
		}
		
		return $lecturer_id;
	}
	
	/* Private Functions*/
	function SanitizeForSQL($str)
    {
        if( function_exists( "mysqli_real_escape_string" ) )
        {
              $ret_str = mysqli_real_escape_string( $this->connection , $str );
        }
        else
        {
              $ret_str = addslashes( $str );
        }
		
        return $ret_str;
    }
    
	function SanitizeForSQLExt($str , $connection)
    {
        if( function_exists( "mysqli_real_escape_string" ) )
        {
              $ret_str = mysqli_real_escape_string( $connection , $str );
        }
        else
        {
              $ret_str = addslashes( $str );
        }
		
        return $ret_str;
    }

    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }    
	
    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    }

	function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
		
        $errormsg = nl2br(htmlentities($this->error_message));
        return $errormsg;
    }    
    
    function HandleError($err)
    {
        $this->error_message .= $err."\r\n";
    }
	
	function HandleDBError($err)
    {
        $this->HandleError($err."\r\n mysqlerror:".mysqli_error($this->connection));
    }
	
	function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }  
	
	function IsFieldUnique($table, $formvars, $fieldname)
    {
        $field_val = $this->SanitizeForSQL($formvars[$fieldname]);
        $qry = "select * from ". $table ." where $fieldname = '".$field_val."'";
        $result = mysqli_query($this->connection,$qry);   
        if($result && mysqli_num_rows($result) > 0)
        {
            return false;
        }
        return true;
    }
	
	/*Private Functions */
	
	/* Delete Functions */
	
	function DeleteAllClass()
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$class_query = 'select * from '. $this->classes . ' ';
		if($class_result = mysqli_query($this->connection, $class_query)){
			if(mysqli_num_rows($class_result) > 0){
				while($class_row = $class_result->fetch_array(MYSQLI_BOTH)){
					$delete_query = 'delete from ' . $this->slots . ' where class = "' . $class_row['id'] . '"';
					mysqli_query($this->connection, $delete_query);
					
					$delete_query_2 = 'delete from ' . $this->parents . ' where class = "' . $class_row['id'] . '"';
					mysqli_query($this->connection, $delete_query_2);
				}
			}
		}
		
		$sql_query = "TRUNCATE TABLE ". $this->classes . " ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteAllParent()
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$parents_query = 'select * from '. $this->parents . '';
		if($parents_result = mysqli_query($this->connection, $parents_query)){
			if(mysqli_num_rows($parents_result) > 0){
				while($parents_row = $parents_result->fetch_array(MYSQLI_BOTH)){
					$delete_query = 'delete from ' . $this->slots . ' where parent = "' . $parents_row['id'] . '"';
					mysqli_query($this->connection, $delete_query);
				}
			}
		}
		
		$sql_query = "TRUNCATE TABLE ". $this->parents . " ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteAllCourse()
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$courses_query = 'select * from '. $this->courses . '';
		if($courses_result = mysqli_query($this->connection, $courses_query)){
			if(mysqli_num_rows($courses_result) > 0){
				while($courses_row = $courses_result->fetch_array(MYSQLI_BOTH)){
					$delete_query = 'delete from ' . $this->slots . ' where course = "' . $courses_row['id'] . '"';
					mysqli_query($this->connection, $delete_query);
					
					$delete_query_2 = 'delete from ' . $this->parents . ' where course = "' . $courses_row['id'] . '"';
					mysqli_query($this->connection, $delete_query_2);
				}
			}
		}
		
		$sql_query = "TRUNCATE TABLE ". $this->courses . " ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteAllSlots()
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$slot_query = 'select * from ' . $this->slots. '';
		if($slot_result = mysqli_query($this->connection, $slot_query)){
			if(mysqli_num_rows($slot_result) > 0){
				while($slot_row = $slot_result->fetch_array(MYSQLI_BOTH)){
					$parent_query = 'update '.$this->parents.' set slot = 0 where slot = "' . $slot_row['id'] . '" ';
					mysqli_query($this->connection, $parent_query);
				}
			}
		}
		
		$sql_query = "TRUNCATE TABLE ". $this->slots . " ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteAllLecturer()
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$lect_query = 'select * from '. $this->lecturers. '';
		if($lect_result = mysqli_query($this->connection, $lect_query)){
			if(mysqli_num_rows($lect_result) > 0){
				while($lect_row = $lect_result->fetch_array(MYSQLI_BOTH)){
					$delete_query = 'delete from '. $this->slots .' where lecturer = "'.$lect_row['id'].'"';
					mysqli_query($this->connection, $delete_query);
					
					$update_query = 'update '.$this->parents.' set lecturer = 0 where lecturer = "'.$lect_row['id'].'"';
					mysqli_query($this->connection, $update_query);
				}
			}
		}
		
		$sql_query = "TRUNCATE TABLE ". $this->lecturers . " ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteAllVenue()
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$venue_query = 'select * from '. $this->venues. '';
		if($venue_result = mysqli_query($this->connection, $venue_query)){
			if(mysqli_num_rows($venue_result) > 0){
				while($venue_row = $venue_result->fetch_array(MYSQLI_BOTH)){
					$delete_query = 'delete from '. $this->slots .' where venue = "'.$venue_row['id'].'"';
					mysqli_query($this->connection, $delete_query);
			
				}
			}
		}
		
		$sql_query = "TRUNCATE TABLE ". $this->venues . " ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteAllTimeSlot()
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$timeslot_query = 'select * from '. $this->timeslot. '';
		if($timeslot_result = mysqli_query($this->connection, $timeslot_query)){
			if(mysqli_num_rows($timeslot_result) > 0){
				while($timeslot_row = $timeslot_result->fetch_array(MYSQLI_BOTH)){
					$delete_query = 'delete from '. $this->slots .' where timeslot = "'.$timeslot_row['id'].'"';
					mysqli_query($this->connection, $delete_query);		
				}
			}
		}
		
		
		$sql_query = "TRUNCATE TABLE ". $this->timeslot . " ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteClass($id)
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$delete_query = 'delete from ' . $this->slots . ' where class = "' . $this->SanitizeForSQL($id) . '"';
		mysqli_query($this->connection, $delete_query);
					
		$delete_query_2 = 'delete from ' . $this->parents . ' where class = "' . $this->SanitizeForSQL($id) . '"';
		mysqli_query($this->connection, $delete_query_2);
			
		$sql_query = "DELETE FROM ". $this->classes . " WHERE id = '". $this->SanitizeForSQL($id) . "' ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteCourse($id)
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$delete_query = 'delete from ' . $this->slots . ' where course = "' . $this->SanitizeForSQL($id) . '"';
		mysqli_query($this->connection, $delete_query);
					
		$delete_query_2 = 'delete from ' . $this->parents . ' where course = "' . $this->SanitizeForSQL($id) . '"';
		mysqli_query($this->connection, $delete_query_2);
				
		
		$sql_query = "DELETE FROM ". $this->courses . " WHERE id = '". $this->SanitizeForSQL($id) . "' ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteLecturer($id)
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$delete_query = 'delete from '. $this->slots .' where lecturer = "'. $this->SanitizeForSQL($id) .'"';
		mysqli_query($this->connection, $delete_query);
					
		$update_query = 'update '.$this->parents.' set lecturer = 0 where lecturer = "'. $this->SanitizeForSQL($id) .'"';
		mysqli_query($this->connection, $update_query);
				
		
		$sql_query = "DELETE FROM ". $this->lecturers . " WHERE id = '". $this->SanitizeForSQL($id) . "' ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteVenue($id)
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$delete_query = 'delete from '. $this->slots .' where venue = "'. $this->SanitizeForSQL($id) .'"';
		mysqli_query($this->connection, $delete_query);
			
		$sql_query = "DELETE FROM ". $this->venues . " WHERE id = '". $this->SanitizeForSQL($id) . "' ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteTimeSlot($id)
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$delete_query = 'delete from '. $this->slots .' where timeslot = "'. $this->SanitizeForSQL($id) .'"';
		mysqli_query($this->connection, $delete_query);		
			
		
		$sql_query = "DELETE FROM ". $this->timeslot . " WHERE id = '". $this->SanitizeForSQL($id) . "' ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteParent($id)
	{
		if(!$this->DBLogin()){
			return false;
		}
		
		$delete_query = 'delete from ' . $this->slots . ' where parent = "' . $this->SanitizeForSQL($id) . '"';
		mysqli_query($this->connection, $delete_query);
				
		
		$sql_query = "DELETE FROM ". $this->parents . " WHERE auto_id = '". $this->SanitizeForSQL($id) . "' ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	
	function DeleteSlot($id)
	{
		if(!$this->DBLogin())
		{
			return false;
		}
		
		$parent_query = 'update '.$this->parents.' set slot = 0 where slot = "' . $this->SanitizeForSQL($id) . '" ';
		mysqli_query($this->connection, $parent_query);
				
		$sql_query = "DELETE FROM ". $this->slots . " WHERE id = '". $this->SanitizeForSQL($id) . "' ";
		$result = mysqli_query($this->connection, $sql_query);
		
		if($result){
			return true;
		}
		else{
			return false;
		}
	}	
	
	/* Delete Functions */
	
	
	/* Paging Functions */
	function GetPageCount($table_name)
	{
		$count = 0;
		
		if(!$this->DBLogin())
		{
			return $count;
		}
		
		$total_page_query = "SELECT COUNT(*) FROM " . $table_name . " ";
		if($total_page_result = mysqli_query($this->connection , $total_page_query)){
			if($total_page_row = $total_page_result->fetch_array(MYSQLI_BOTH)){
				$count = $total_page_row['COUNT(*)'];
				
				$total_pages = ceil($count / $this->num_of_records);
				
				return $total_pages;
			}
		}
	}

	/* Paging Functions */
}
?>