<?php

date_default_timezone_set('Asia/Singapore');

class FrontEnd
{
	var $username;
    var $password;
    var $database;
    var $tablename;
	var $servername;
	
    var $connection;
	
	var $error_message;
	
	var $real_time;
	
	function InitDB($database, $username, $pwd, $servername)
	{
		$this->username = $username;
		$this->password = $pwd;
		$this->database = $database;
		$this->servername = $servername;
	}
	
	function InitDBTable($parents, $slots, $lecturers, $courses, $classes, $venues, $timeslot)
	{
		$this->parents = $parents;
		$this->slots = $slots;
		$this->lecturers = $lecturers;
		$this->courses = $courses;
		$this->classes = $classes;
		$this->venues = $venues;
		$this->timeslot = $timeslot;
	}
	
	function InitRealTime($real)
	{
		$this->real_time = $real;
	}
	
	function DBLogin(){
		
		$this->connection = mysqli_connect($this->servername, $this->username, $this->password, $this->database);

        if(!$this->connection)
        {     
            return false;
        }
		
        if(!mysqli_select_db($this->connection, $this->database))
		{
            return false;
        }
		
        if(!mysqli_query($this->connection,"SET NAMES 'UTF8'"))
		{
            return false;
        }
		
        return true;
	}
	
	function CheckSlot($id)
	{
		if(!$this->DBLogin())
        {      
            return $this->HandleError("Database login failed!");;
        }
		
		$id = $this->Sanitize($id);
		
		$results = array();
		$echo_results = array();
		
		$select_qry = "SELECT * FROM ". $this->parents . " WHERE id = '".$this->SanitizeForSQL($id)."'";
		if($sql_result = mysqli_query($this->connection, $select_qry)){
			if(mysqli_num_rows($sql_result) > 0){
				if($row = $sql_result->fetch_array(MYSQLI_BOTH))
				{
					$results['parentID'] = $id;
					$results['id'] = $row['id'];
					$results['class'] = $row['class'];
					$results['course'] = $row['course'];
					$results['slot'] = $row['slot'];
					$results['name'] = $row['name'];
					$results['OS'] = $row['OS'];
					$results['OS_lecturer'] = $row['OS_lecturer'];
					
					if($results['slot'] == 0)
					{	
						$echo_results = $this->GetAvailableSlot($results);
						$echo_results['parentID'] = $id;
						$echo_results['parent'] = "Name: " . $row['name'];
					}
					else
					{
						$echo_results['status'] = 2;
						$echo_results['parentID'] = $id;
						$echo_results['parent'] = "Name: " . $this->ResolveParent($id);
						$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
						$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
						
						$echo_results['venue'] = "Venue: " . $this->ResolveVenue($results['slot']);
						$echo_results['lecturer'] = "Lecturer: " . $this->ResolveLecturer($results['slot']);
						
						$echo_results['slot'] = "Slot: " . $this->ResolveSlot($results['slot']);
						
						$echo_results['message'] = "Here is the slot that is assigned to you!";			
					}
				}
			}
			else
			{
				$echo_results['status'] = 0;
				$echo_results['message'] = "Sorry, this is an invalid ID";
				$echo_results['slot'] = '';
							
				$echo_results['venue'] = '';
				$echo_results['lecturer'] = '';
						
				$echo_results['class'] = '';
				$echo_results['course'] = '';
						
			}
		}
		
		return $echo_results;
		
	}
	
	function GetAvailableSlot(&$results)
	{
		
		$echo_results = array();
		
		if(!$this->DBLogin())
        {
            return $this->HandleError("Database login failed!");;
        }
		
		if($results['OS'] == 1 && $results['OS_lecturer'] == 0){
			
			$echo_results['message'] = "Error occurred. Please contact the help desk.";
				
			$echo_results['slot'] = '';
							
			$echo_results['venue'] = '';
			$echo_results['lecturer'] = '';
						
			$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
			$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
						
			$echo_results['status'] = 3;
		}
		else{
			
			if($results['OS'] == 1){
				
				//most troublesome part of the system.
				//zzzz
				$slot_qry = "SELECT * FROM " . $this->slots . " WHERE course = '". $results['course'] . "' AND class = '". $results['class'] . "' AND parent is NULL AND lecturer = '". $results['OS_lecturer'] ."' order by timeslot ASC";
				
				if($slot_result = mysqli_query($this->connection, $slot_qry)){
					if(mysqli_num_rows($slot_result) > 0){
						while($slot_row = $slot_result->fetch_array(MYSQLI_BOTH)){
							
							if($this->real_time == 1)
							{
								$start_time = $this->GetStartTime($slot_row['timeslot']);
								if($this->CheckTime($start_time) && $start_time != 'Invalid')
								{
									continue;
								}
							}
							
							//check against other OOS classes if they already occupy slot
							$double_check_query = "SELECT * FROM ". $this->slots . " WHERE lecturer = '". $results['OS_lecturer'] ."' AND timeslot = '". $slot_row['timeslot'] ."' AND id <> '". $slot_row['id'] ."' AND parent is NOT NULL";
							if($double_check_result = mysqli_query($this->connection , $double_check_query)){
								if(mysqli_num_rows($double_check_result) > 0){
									
									// got someone booked already
									continue;
								}
								else{
									
									//check against non-oos slot with the same time
									if($this->GetSameSlot($slot_row['timeslot']) != 0){
										$triple_check_query = "SELECT * FROM " . $this->slots. " WHERE lecturer = '". $results['OS_lecturer']."' AND timeslot = '". $this->GetSameSlot($slot_row['timeslot']) . "' AND id <> '". $slot_row['id']. "' AND parent is NOT NULL ";
										if($triple_check_result = mysqli_query($this->connection, $triple_check_query)){
											
											if(mysqli_num_rows($triple_check_result) > 0){
											
												// got someone booked already
												continue;
											}
											else{
												
												if($this->ResolveOS($slot_row['timeslot']) == 1){
										
													$echo_results['slotID'] = $slot_row['id'];
													$echo_results['slot'] = "Slot: " . $this->ResolveSlot($slot_row['id']);
													
													$echo_results['venue'] = "Venue: " . $this->ResolveVenue($slot_row['id']);
													$echo_results['lecturer'] = "Lecturer: " . $this->ResolveLecturer($slot_row['id']);
													
													$echo_results['class'] = "Class: " . $this->ResolveClass($slot_row['class']);
													$echo_results['course'] = "Course: " . $this->ResolveCourse($slot_row['course']);
														
													$echo_results['message'] = "Latest slot available";
													
													$echo_results['status'] = 1;
													
													break;				
												}else{
													continue;
												}
												
											}
										}
										else{
											
											$echo_results['message'] = "Error occurred. Please contact the help desk.";
				
											$echo_results['slot'] = '';
												
											$echo_results['venue'] = '';
											$echo_results['lecturer'] = '';
											
											$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
											$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
											
											$echo_results['status'] = 3;
											
										}
									}
									else{
										
										if($this->ResolveOS($slot_row['timeslot']) == 1){
										
											$echo_results['slotID'] = $slot_row['id'];
											$echo_results['slot'] = "Slot: " . $this->ResolveSlot($slot_row['id']);
											
											$echo_results['venue'] = "Venue: " . $this->ResolveVenue($slot_row['id']);
											$echo_results['lecturer'] = "Lecturer: " . $this->ResolveLecturer($slot_row['id']);
											
											$echo_results['class'] = "Class: " . $this->ResolveClass($slot_row['class']);
											$echo_results['course'] = "Course: " . $this->ResolveCourse($slot_row['course']);
												
											$echo_results['message'] = "Latest slot available";
											
											$echo_results['status'] = 1;
											
											break;				
										}else{
											continue;
										}
										
									}
									
								}
							}
							else{
								
								
								$echo_results['message'] = "Error occurred. Please contact the help desk.";
				
								$echo_results['slot'] = '';
									
								$echo_results['venue'] = '';
								$echo_results['lecturer'] = '';
								
								$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
								$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
								
								$echo_results['status'] = 3;
								
								
							}
						}
						
						if($echo_results['status'] != 1){
							$echo_results['message'] = "Sorry. No slots available. Please contact the help desk.";
				
							$echo_results['slot'] = '';
								
							$echo_results['venue'] = '';
							$echo_results['lecturer'] = '';
							
							$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
							$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
							
							$echo_results['status'] = 3;	
						}
					}
					else{
						
						$echo_results['message'] = "Error occurred. Please contact the help desk.";
				
						$echo_results['slot'] = '';
							
						$echo_results['venue'] = '';
						$echo_results['lecturer'] = '';
						
						$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
						$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
						
						$echo_results['status'] = 3;
						
					}
				}
				else{
						
						$echo_results['message'] = "Error occurred. Please contact the help desk.";
				
						$echo_results['slot'] = '';
							
						$echo_results['venue'] = '';
						$echo_results['lecturer'] = '';
						
						$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
						$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
						
						$echo_results['status'] = 3;
						
					}
				
				
				
			}  //non-oos student
			else{
			 
				$slot_qry = "SELECT * FROM " . $this->slots . " WHERE course = '". $results['course'] . "' AND class = '". $results['class'] . "' AND parent IS NULL ORDER BY timeslot ASC";
				
				if($slot_result = mysqli_query($this->connection, $slot_qry)){
					if(mysqli_num_rows($slot_result) > 0){
						while($slot_row = $slot_result->fetch_array(MYSQLI_BOTH))
						{
							if($this->real_time == 1)
							{
								$start_time = $this->GetStartTime($slot_row['timeslot']);
								if($this->CheckTime($start_time) && $start_time != 'Invalid')
								{
									continue;
								}
							}
							
							$double_check_query = "SELECT * FROM ". $this->slots . " WHERE lecturer = '" . $slot_row['lecturer'] . "' AND timeslot = '". $slot_row['timeslot'] ."' AND id <> '". $slot_row['id'] ."' AND parent is NOT NULL";
							if($double_check_result = mysqli_query($this->connection, $double_check_query)){
								if(mysqli_num_rows($double_check_result) > 0){
									// slot taken by other class
									// skip this timeslot
									continue;
								}
								else{
								
									if($this->GetSameSlot($slot_row['timeslot']) != 0){
										$triple_check_query = "SELECT * FROM ". $this->slots . " WHERE lecturer = '" . $slot_row['lecturer'] . "' AND timeslot = '". $this->GetSameSlot($slot_row['timeslot']) ."' AND id <> '".$slot_row['id']."' AND parent is NOT NULL";
										if($triple_check_result = mysqli_query($this->connection, $triple_check_query)){
											if(mysqli_num_rows($triple_check_result) > 0){
												//slot taken also
												continue;
											}
											else{
												//slot is free and non - os slot
												if($this->ResolveOS($slot_row['timeslot']) == 0){
												
													$echo_results['slotID'] = $slot_row['id'];
													$echo_results['slot'] = "Slot: " . $this->ResolveSlot($slot_row['id']);
													
													$echo_results['venue'] = "Venue: " . $this->ResolveVenue($slot_row['id']);
													$echo_results['lecturer'] = "Lecturer: " . $this->ResolveLecturer($slot_row['id']);
													
													$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
													$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
														
													$echo_results['message'] = "Latest slot available";
													
													$echo_results['status'] = 1;
													
													break;	
											
												}
												else{
													continue;
												}
												
											}
										}
										else{
											
											$echo_results['message'] = "Error occurred. Please contact the help desk.";
				
											$echo_results['slot'] = '';
												
											$echo_results['venue'] = '';
											$echo_results['lecturer'] = '';
											
											$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
											$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
											
											$echo_results['status'] = 3;
											
										}
									}
									else{
										if($this->ResolveOS($slot_row['timeslot']) == 0){
												
											$echo_results['slotID'] = $slot_row['id'];
											$echo_results['slot'] = "Slot: " . $this->ResolveSlot($slot_row['id']);
													
											$echo_results['venue'] = "Venue: " . $this->ResolveVenue($slot_row['id']);
											$echo_results['lecturer'] = "Lecturer: " . $this->ResolveLecturer($slot_row['id']);
													
											$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
											$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
														
											$echo_results['message'] = "Latest slot available";
												
											$echo_results['status'] = 1;
											
											break;	
										
										}
										else{
											continue;
										}
									}
									
								}							
							}
							else{
							
								$echo_results['message'] = "Error occurred. Please contact the help desk.";
					
								$echo_results['slot'] = '';
								
								$echo_results['venue'] = '';
								$echo_results['lecturer'] = '';
							
								$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
								$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
							
								$echo_results['status'] = 3;
					
							}
						}
						
						if(!isset($echo_results['status']) || $echo_results['status'] != 1){
							$echo_results['message'] = "Sorry. No slots available. Please contact the help desk.";
				
							$echo_results['slot'] = '';
								
							$echo_results['venue'] = '';
							$echo_results['lecturer'] = '';
							
							$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
							$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
							
							$echo_results['status'] = 3;	
						}
					}
					else{
						
						$echo_results['message'] = "Error occurred. Please contact the help desk.";
				
						$echo_results['slot'] = '';
							
						$echo_results['venue'] = '';
						$echo_results['lecturer'] = '';
						
						$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
						$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
						
						$echo_results['status'] = 3;
						
					}
				}
				else{
						
						$echo_results['message'] = "Error occurred. Please contact the help desk.";
				
						$echo_results['slot'] = '';
							
						$echo_results['venue'] = '';
						$echo_results['lecturer'] = '';
						
						$echo_results['class'] = "Class: " . $this->ResolveClass($results['class']);
						$echo_results['course'] = "Course: " . $this->ResolveCourse($results['course']);
						
						$echo_results['status'] = 3;
						
					}
			}
		}
		return $echo_results;
		
	}
	
	function AssignSlotToParent($slotID, $parentID)
	{
		$results = array();
		
		if(!$this->DBLogin())
        {
			$results['status'] = 2;
			$results['parent'] = "Name: " . $this->ResolveParent($parentID);
			$results['parentID'] = $parentID;
			$results['message'] = "Sorry, this slot has been taken by others. Please try again!";
			
            return $results;
        }
		
		
		$select_qry = "SELECT * from " . $this->slots . " WHERE id = '". $this->SanitizeForSQL($slotID) . "' ";
		
		if($result = mysqli_query($this->connection, $select_qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH))
				{
					if(empty($row['parent']))
					{
						$content = $this->UpdateSlot($slotID, $parentID);
						
						if($content != "")
						{
							$results['status'] = 1;
							$results['slotID'] = $slotID;
							$results['message'] = "Your slot has been assigned successfully!";
							$results['slot'] = "Slot: " . $this->ResolveSlot($slotID);
							$results['parentID'] = $parentID;
							$results['parent'] = "Name: " . $this->ResolveParent($parentID);
							$results['venue'] = "Venue: " . $this->ResolveVenue($slotID);
							$results['class'] = "Class: " . $this->ResolveClass($row['class']);
							$results['course'] = "Course: " . $this->ResolveCourse($row['course']);
							$results['lecturer'] = "Lecturer: " . $this->ResolveLecturer($slotID);
							
						}
						else{
							$results['status'] = 2;
							$results['parent'] = "Name: " . $this->ResolveParent($parentID);
							$results['parentID'] = $parentID;
							$results['message'] = "Sorry, an error occured. Please try again!";
								
						}
						
					}
					else
					{
						$results['status'] = 2;
						$results['parent'] = "Name: " . $this->ResolveParent($parentID);
						$results['parentID'] = $parentID;
						$results['message'] = "Sorry, this slot has been taken by others. Please try again!";
					}
				}
			}
			else
			{
				$results['status'] = 2;
				$results['parent'] = "Name: " . $this->ResolveParent($parentID);
				$results['parentID'] = $parentID;
				$results['message'] = "Sorry, invalid time slot. Please try again!";
			}
		}
		else{
			
			$results['status'] = 2;
			$results['parent'] = "Name: " . $this->ResolveParent($parentID);
			$results['parentID'] = $parentID;
			$results['message'] = "Sorry, this slot has been taken by others. Please try again!";
		}
		
		return $results;
	}
	
	function UpdateSlot($slotID, $parentID)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return "";
        }
		
		$update_qry = "UPDATE ".$this->slots." SET parent = '".$this->SanitizeForSQL($parentID)."' WHERE id = '".$this->SanitizeForSQL($slotID)."' ";
		mysqli_query($this->connection , $update_qry);
		
		$update_parent_qry = "UPDATE ".$this->parents." SET slot = '".$this->SanitizeForSQL($slotID)."' , lecturer = '".$this->SanitizeForSQL($this->ResolveLecturerID($slotID))."' WHERE id = '".$this->SanitizeForSQL($parentID)."' ";
		mysqli_query($this->connection , $update_parent_qry);
		
		return "SUCCESS";
	}
	
	function ResolveClass($class_id)
	{
		if(!$this->DBLogin())
        {
            $content = "Undefined";
			return $content;
        }
		
		$content = "";
		
		if($class_id == 0)
		{
			$content = "Undefined";
			return $content;
		}
		
		$cr_qry = "SELECT * FROM " . $this->classes . " WHERE id = " . $this->SanitizeForSQL($class_id) . " ";
		if($result = mysqli_query($this->connection, $cr_qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH))
				{
					$content = $row['name'];
				}
			}
		}
			
		return $content;
	}
	
	function ResolveCourse($course_id)
	{
		if(!$this->DBLogin())
        {
            $content = "Undefined";
			return $content;
        }
		
		$content = "";
		
		if($course_id == 0)
		{
			$content = "Undefined";
			return $content;
		}
		
		$cr_qry = "SELECT * FROM " . $this->courses . " WHERE id = " . $this->SanitizeForSQL($course_id) . " ";
		if($result = mysqli_query($this->connection, $cr_qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH))
				{
					$content = $row['name'];
				}
			}
		}
			
		return $content;
	}
	
	function ResolveVenue($slot_id)
	{
		if(!$this->DBLogin())
        {
            $content = "Undefined";
			return $content;
        }
		
		$content = "";
		$venue_content = "";
		
		if($slot_id == 0)
		{
			$content = "Undefined";
			return $content;
		}
		
		$qry = "SELECT * FROM " . $this->slots . " WHERE id = " . $this->SanitizeForSQL($slot_id) . " ";
		
		if($result = mysqli_query($this->connection, $qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH))
				{
					$venue_content = $row['venue'];
				}
			}
		}
			
		$ven_qry = "SELECT * FROM " . $this->venues . " WHERE id = " . $this->SanitizeForSQL($venue_content) . " ";
		
		if($result2 = mysqli_query($this->connection, $ven_qry)){
			if(mysqli_num_rows($result2) > 0){
				if($row2 = $result2->fetch_array(MYSQLI_BOTH))
				{
					$content = $row2['name'];
				}
			}
		}
		
		return $content;
	}
	
	function ResolveLecturer($slot_id)
	{
		if(!$this->DBLogin())
        {
            $content = "Undefined";
			return $content;
        }
		
		$content = "";
		$lect_content = "";
		
		if($slot_id == 0)
		{
			$content = "Undefined";
			return $content;
		}
		
		$qry = "SELECT * FROM " . $this->slots . " WHERE id = " . $this->SanitizeForSQL($slot_id) . " ";
		
		if($result = mysqli_query($this->connection, $qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH))
				{
					$lect_content = $row['lecturer'];
				}
			}
		}
			
		$ven_qry = "SELECT * FROM " . $this->lecturers . " WHERE id = " . $this->SanitizeForSQL($lect_content) . " ";
		
		if($result2 = mysqli_query($this->connection, $ven_qry)){
			if(mysqli_num_rows($result2) > 0){
				if($row2 = $result2->fetch_array(MYSQLI_BOTH))
				{
					$content = $row2['name'];
				}
			}
		}
		
		return $content;
	}
	
	function ResolveLecturerID($slot_id)
	{
		if(!$this->DBLogin())
        {
            $content = 0;
			return $content;
        }
		
		$content = "";
		
		if($slot_id == 0)
		{
			$content = 0;
			return $content;
		}
		
		$qry = "SELECT * FROM " . $this->slots . " WHERE id = " . $this->SanitizeForSQL($slot_id) . " ";
		
		if($result = mysqli_query($this->connection, $qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH))
				{
					$content = $row['lecturer'];
				}
			}
		}
			
		return $content;
	}
	
	function ResolveSlot($slot_id)
	{
		if(!$this->DBLogin())
        {
            $content = "Undefined";
			return $content;
        }
		
		$content = "";
		
		if($slot_id == 0)
		{
			$content = "Undefined";
			return $content;
		}
		
		$qry = "SELECT * FROM " . $this->slots . " WHERE id = " . $this->SanitizeForSQL($slot_id) . " ";
		
		if($result = mysqli_query($this->connection, $qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH))
				{
					$content = $row['timeslot'];
				}
			}
			else
			{
				$content = "Invalid";
				return $content;
			}
		}
			
		$ven_qry = "SELECT * FROM " . $this->timeslot . " WHERE id = " . $this->SanitizeForSQL($content) . " ";
		
		if($result2 = mysqli_query($this->connection, $ven_qry)){
			if(mysqli_num_rows($result2) > 0){
				if($row2 = $result2->fetch_array(MYSQLI_BOTH))
				{
					$content = $row2['slots'];
				}
			}
			else
			{
				$content = "Invalid";
				return $content;
			}
		}
		
		return $content;
	}
	
	function ResolveOS($timeslot_id)
	{
		if(!$this->DBLogin())
        {
            $content = "Invalid";
			return $content;
        }
		
		$content = "";
		
		if($timeslot_id == 0)
		{
			$content = "Invalid";
			return $content;
		}
		
	
		$qry = "SELECT * FROM " . $this->timeslot . " WHERE id = " . $this->SanitizeForSQL($timeslot_id) . " ";
		
		if($result = mysqli_query($this->connection, $qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH))
				{
					if($row['OS'] == 1)
						$content = 1;
					else if($row['OS'] == 0)
						$content = 0;
				}
			}
		}
			
		
		return $content;
	}
	
	function ResolveParent($parent_id)
	{
		if(!$this->DBLogin())
        {
            $content = "Invalid";
			return $content;
        }
		
		$content = "";
		
		if($parent_id == 0)
		{
			$content = "Invalid";
			return $content;
		}
		
		$qry = "SELECT * FROM " . $this->parents . " WHERE id = " . $this->SanitizeForSQL($parent_id) . " ";
		
		if($result = mysqli_query($this->connection, $qry)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH))
				{
					$content = $row['name'];
				}
			}
		}
			
		
		return $content;
	}
	
	function ResolveTimeSlot($time_slot_id)
	{
		if(!$this->DBLogin())
        {
            $content = "Invalid";
			return $content;
        }
		
		$content = "";
		
		if($time_slot_id == 0)
		{
			$content = "Invalid";
			return $content;
		}
		
		$query = "SELECT * FROM ". $this->timeslot . " WHERE id = " . $this->SanitizeForSQL($time_slot_id) . " ";
		
		if($result = mysqli_query($this->connection, $query)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH))
				{
					$content = $row['slots'];
				}
			}
		}
			
		
		return $content;
	}
	
	function GetSameSlot($time_slot_id)
	{
		if(!$this->DBLogin())
        {
            $content = 0;
			return $content;
        }
		
		$content = "";
		$return_content = "";
		
		if($time_slot_id == 0)
		{
			$content = 0;
			return $content;
		}
		
		$query = "SELECT * FROM ". $this->timeslot . " WHERE id = " . $this->SanitizeForSQL($time_slot_id) . " ";
		
		if($result = mysqli_query($this->connection, $query)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH))
				{
					$content = $row['slots'];
				}
			}
			else{
				$return_content = 0;
			}
		}
		
		if($content != ""){
			$second_query = "SELECT * FROM ". $this->timeslot . " WHERE slots = '". $this->SanitizeForSQL($content) ."' AND id <> '". $time_slot_id ."' ";
			if($second_result = mysqli_query($this->connection, $second_query)){
				if(mysqli_num_rows($second_result) > 0 ){
					if($second_row = $second_result->fetch_array(MYSQLI_BOTH)){
						$return_content = $second_row['id'];
					}
				}
				else{
					$return_content = 0;
				}
			}
		}
		else{
			$return_content = 0;
		}
		
		return $return_content;
		
	}
	
	function SearchResult($search_query)
	{
		if(!$this->DBLogin())
        {
            $content = "";
			return $content;
        }
		
		$content = "";
		
		$value = "%". $search_query . "%";
		
		$select_qry = "SELECT * FROM " . $this->parents . " WHERE id LIKE '" . $this->SanitizeForSQL($value) . "' OR lower(name) LIKE '". strtolower($this->SanitizeForSQL($value)) . "' ";
		
		if($result = mysqli_query($this->connection, $select_qry)){
			if(mysqli_num_rows($result) > 0){
				while($row = $result->fetch_array(MYSQLI_BOTH))
				{
					if($content == "")
					{
						$content = "Name: " . $row['name'] . " Course: " . $this->ResolveCourse($row['course']) . " Class: " . $this->ResolveClass($row['class']) . " Admin Number: " . $row['id'] ;
					}
					else
					{
						$content .= "<br><br>" . "Name: " . $row['name'] . " Course: " . $this->ResolveCourse($row['course']) . " Class: " . $this->ResolveClass($row['class']) . " Admin Number: " . $row['id'] ;
					}
				}
				
				return $content;
			}
			else
			{
				return "No student found";
			}
		}
		
		
	}
	
	function SearchSlots($search_query)
	{
		if(!$this->DBLogin())
        {
            $content = "";
			return $content;
        }
		
		$table_content = "<table class='table'>";
		$table_content .= "<tr><th>Results for " . $this->SanitizeForSQL($search_query) ."<br><br> </th></tr>";
		$content = "";
		
		$search_query = $this->SanitizeForSQL($search_query);
		$lect_content = array();
		$ven_content = array();
		
		$search_wild = "%". $search_query . "%";
		
		$lect_qry = "SELECT * FROM " . $this->lecturers . " WHERE lower(name) LIKE '" . strtolower($this->SanitizeForSQL($search_wild)) . "' ";
		if($lect_result = mysqli_query($this->connection, $lect_qry)){
			if(mysqli_num_rows($lect_result) > 0 ){
				while($lect_row = $lect_result->fetch_array(MYSQLI_BOTH))
				{
					$lect_content[] = $lect_row["id"];
				}
			}
		}
		
		$ven_qry = "SELECT * FROM " . $this->venues . " WHERE lower(name) LIKE '" . strtolower($this->SanitizeForSQL($search_wild)) . "' ";
		if($ven_result = mysqli_query($this->connection, $ven_qry)){
			if(mysqli_num_rows($ven_result) > 0 ){
				while($ven_row = $ven_result->fetch_array(MYSQLI_BOTH))
				{
					$ven_content[] = $ven_row["id"];
				}
			}
		}
		
		if(count($lect_content) > 0)
		{
			foreach($lect_content as $value)
			{
				$slot_qry = "SELECT * FROM " . $this->slots . " WHERE lecturer LIKE '" . $this->SanitizeForSQL($value) . "' and parent is not null order by timeslot asc ";
				if($slot_result = mysqli_query($this->connection, $slot_qry)){
					if(mysqli_num_rows($slot_result) > 0){
						while($slot_row = $slot_result->fetch_array(MYSQLI_BOTH)){
							$parent = $this->ResolveParent($slot_row['parent']);
							if($content == ""){
								$content .= "<tr><th>Tutor</th><th>Student</th><th>Course</th><th>Class</th><th>Time Slot</th></tr>";
								$content .= "<tr><td>Tutor: <br> ".  $this->ResolveLecturer($slot_row['id']). " </td><td>  Student: <br> " . $parent . " </td><td>  Course: <br>" . $this->ResolveCourse($slot_row['course']) . " </td><td>  Class: <br> " . $this->ResolveClass($slot_row['class']) . " </td><td> Timeslot: <br> " . $this->ResolveSlot($slot_row['id']) . "</td><td> Venue: <br> " . $this->ResolveVenue($slot_row['id']) . "</td></tr>";
							}
							else{
								$content .= "<tr><td>Tutor: <br> ".  $this->ResolveLecturer($slot_row['id']). " </td><td>  Student: <br> " . $parent . " </td><td>  Course: <br>" . $this->ResolveCourse($slot_row['course']) . " </td><td>  Class: <br> " . $this->ResolveClass($slot_row['class']) . " </td><td> Timeslot: <br> " . $this->ResolveSlot($slot_row['id']) . "</td><td> Venue: <br> " . $this->ResolveVenue($slot_row['id']) . "</td></tr>";
							}
						}
					}
				}
				
				
				$class_content = array();
				
				$class_qry = "SELECT DISTINCT class FROM " . $this->slots . " WHERE lecturer LIKE '" . $this->SanitizeForSQL($value) . "' order by class asc";
				if($class_result = mysqli_query($this->connection, $class_qry)){
					if(mysqli_num_rows($class_result) > 0){
						while($class_row = $class_result->fetch_array(MYSQLI_BOTH)){
							$class_content[] = $class_row['class'];
						}
					}
				}
				
				/*
				if(count($class_content) > 0){	
				    $count = 0;
					foreach($class_content as $class_value){
						if($count == 0){
							$content .= "<tr><th>Unassigned Slots<br><br> </th></tr>";
							$count ++;
						}
							$slot_qry = "SELECT * FROM " . $this->slots . " WHERE lecturer like '" . $this->SanitizeForSQL($value) ."' AND class = '" . $this->SanitizeForSQL($class_value) . "' AND parent is null order by timeslot ASC";
							if($slot_result = mysqli_query($this->connection, $slot_qry)){
								if(mysqli_num_rows($slot_result) > 0){
									while($slot_row = $slot_result->fetch_array(MYSQLI_BOTH)){
										
										if(!empty($slot_row['parent']))
											$parent = $this->ResolveParent($slot_row['parent']);
										else
											$parent = "Not assigned";
										
										$content .= "<tr><td>Tutor: <br> ".  $this->ResolveLecturer($slot_row['id']). " </td><td>  Student: <br> " . $parent . " </td><td>  Course: <br>" . $this->ResolveCourse($slot_row['course']) . " </td><td>  Class: <br> " . $this->ResolveClass($slot_row['class']) . " </td><td> Timeslot: <br> " . $this->ResolveSlot($slot_row['id']) . "</td><td> Venue: <br> " . $this->ResolveVenue($slot_row['id']) . "</td></tr>";
										
									}
								}
							}
						
					}
				}
				*/
				
			}
		
		}
		
		if(count($ven_content) > 0)
		{
			foreach($ven_content as $value)
			{
				$select_qry = "SELECT * FROM " . $this->slots . " WHERE venue LIKE '" . $this->SanitizeForSQL($value) . "' ORDER BY timeslot ASC ";
			
				if($result = mysqli_query($this->connection, $select_qry)){
					if(mysqli_num_rows($result) > 0){
						while($row = $result->fetch_array(MYSQLI_BOTH))
						{
							if(!empty($row['parent']))
								$parent = $this->ResolveParent($row['parent']);
							else
								$parent = "Not assigned";
							
							if($content == "")
							{
								$content .= "<tr><th>Tutor</th><th>Student</th><th>Course</th><th>Class</th><th>Time Slot</th></tr>";
								$content .= "<tr><td>Tutor: <br> ".  $this->ResolveLecturer($row['id']). " </td><td>  Student: <br> " . $parent . " </td><td>  Course: <br>" . $this->ResolveCourse($row['course']) . " </td><td>  Class: <br> " . $this->ResolveClass($row['class']) . " </td><td> Timeslot: <br> " . $this->ResolveSlot($row['id']) . "</td><td> Venue: <br> " . $this->ResolveVenue($row['id']) . "</td></tr>";
							}
							else
							{
								$content .= "<tr><td>Tutor: <br> ".  $this->ResolveLecturer($row['id']). " </td><td>  Student: <br> " . $parent . " </td><td>  Course: <br>" . $this->ResolveCourse($row['course']) . " </td><td>  Class: <br> " . $this->ResolveClass($row['class']) . " </td><td> Timeslot: <br> " . $this->ResolveSlot($row['id']) . "</td><td> Venue: <br> " . $this->ResolveVenue($row['id']) . "</td></tr>";
							}
						}
					}	
				}
			}
		}
		
		if($content != ""){
			$table_content .= $content;
		}
		else{
			$table_content .= "<tr><th>No students assigned yet.</th></tr>";
		}
		
		$table_content .= "</table>";
		
		return $table_content;
		
	}
	
	function SearchAllSlots($search_query)
	{
		if(!$this->DBLogin())
        {
            $content = "";
			return $content;
        }
		
		$table_content = "<table class='table'>";
		$table_content .= "<tr><th>Results for " . $this->SanitizeForSQL($search_query) ."<br><br> </th></tr>";
		$content = "";
		
		$lect_content = array();
		$ven_content = array();
		
		$search_query = $this->SanitizeForSQL($search_query);
		$search_wild = "%". $search_query . "%";
		
		$lect_qry = "SELECT * FROM " . $this->lecturers . " WHERE lower(name) LIKE '" . strtolower($this->SanitizeForSQL($search_wild)) . "' ";
		if($lect_result = mysqli_query($this->connection, $lect_qry)){
			if(mysqli_num_rows($lect_result) > 0 ){
				while($lect_row = $lect_result->fetch_array(MYSQLI_BOTH))
				{
					$lect_content[] = $lect_row["id"];
				}
			}
		}
		
		$ven_qry = "SELECT * FROM " . $this->venues . " WHERE lower(name) LIKE '" . strtolower($this->SanitizeForSQL($search_wild)) . "' ";
		if($ven_result = mysqli_query($this->connection, $ven_qry)){
			if(mysqli_num_rows($ven_result) > 0 ){
				while($ven_row = $ven_result->fetch_array(MYSQLI_BOTH))
				{
					$ven_content[] = $ven_row["id"];
				}
			}
		}
		
		if(count($lect_content) > 0)
		{
			foreach($lect_content as $value)
			{
				/*
				$slot_qry = "SELECT * FROM " . $this->slots . " WHERE lecturer LIKE '" . $this->SanitizeForSQL($value) . "' order by timeslot asc ";
				if($slot_result = mysqli_query($this->connection, $slot_qry)){
					if(mysqli_num_rows($slot_result) > 0){
						while($slot_row = $slot_result->fetch_array(MYSQLI_BOTH)){
							$parent = $this->ResolveParent($slot_row['parent']);
							if($content == ""){
								$content .= "<tr><th>Tutor</th><th>Student</th><th>Course</th><th>Class</th><th>Time Slot</th></tr>";
								$content .= "<tr><td>Tutor: <br> ".  $this->ResolveLecturer($slot_row['id']). " </td><td>  Student: <br> " . $parent . " </td><td>  Course: <br>" . $this->ResolveCourse($slot_row['course']) . " </td><td>  Class: <br> " . $this->ResolveClass($slot_row['class']) . " </td><td> Timeslot: <br> " . $this->ResolveSlot($slot_row['id']) . "</td><td> Venue: <br> " . $this->ResolveVenue($slot_row['id']) . "</td></tr>";
							}
							else{
								$content .= "<tr><td>Tutor: <br> ".  $this->ResolveLecturer($slot_row['id']). " </td><td>  Student: <br> " . $parent . " </td><td>  Course: <br>" . $this->ResolveCourse($slot_row['course']) . " </td><td>  Class: <br> " . $this->ResolveClass($slot_row['class']) . " </td><td> Timeslot: <br> " . $this->ResolveSlot($slot_row['id']) . "</td><td> Venue: <br> " . $this->ResolveVenue($slot_row['id']) . "</td></tr>";
							}
						}
					}
				}
				*/
				
				$class_content = array();
				
				$class_qry = "SELECT DISTINCT class FROM " . $this->slots . " WHERE lecturer LIKE '" . $this->SanitizeForSQL($value) . "' order by class asc";
				if($class_result = mysqli_query($this->connection, $class_qry)){
					if(mysqli_num_rows($class_result) > 0){
						while($class_row = $class_result->fetch_array(MYSQLI_BOTH)){
							$class_content[] = $class_row['class'];
						}
					}
				}
				
				
				if(count($class_content) > 0){	
				    $count = 0;
					foreach($class_content as $class_value){
						if($count == 0){
							//$content .= "<tr><th>Unassigned Slots<br><br> </th></tr>";
							$count ++;
						}
						
						$content .= "<tr><th><h1>Class: ". $this->ResolveClass($class_value) ."</h1><br> </th></tr>";
						
							$slot_qry = "SELECT * FROM " . $this->slots . " WHERE lecturer like '" . $this->SanitizeForSQL($value) ."' AND class = '" . $this->SanitizeForSQL($class_value) . "'  order by timeslot ASC";
							if($slot_result = mysqli_query($this->connection, $slot_qry)){
								if(mysqli_num_rows($slot_result) > 0){
									while($slot_row = $slot_result->fetch_array(MYSQLI_BOTH)){
										
										if(!empty($slot_row['parent']))
											$parent = $this->ResolveParent($slot_row['parent']);
										else
											$parent = "Not assigned";
										
										$content .= "<tr><td>Tutor: <br> ".  $this->ResolveLecturer($slot_row['id']). " </td><td>  Student: <br> " . $parent . " </td><td>  Course: <br>" . $this->ResolveCourse($slot_row['course']) . " </td><td>  Class: <br> " . $this->ResolveClass($slot_row['class']) . " </td><td> Timeslot: <br> " . $this->ResolveSlot($slot_row['id']) . "</td><td> Venue: <br> " . $this->ResolveVenue($slot_row['id']) . "</td></tr>";
										
									}
								}
							}
						
					}
				}
			
				
			}
		
		}
		
		if(count($ven_content) > 0)
		{
			foreach($ven_content as $value)
			{
				$select_qry = "SELECT * FROM " . $this->slots . " WHERE venue LIKE '" . $this->SanitizeForSQL($value) . "' ORDER BY timeslot ASC ";
			
				if($result = mysqli_query($this->connection, $select_qry)){
					if(mysqli_num_rows($result) > 0){
						while($row = $result->fetch_array(MYSQLI_BOTH))
						{
							if(!empty($row['parent']))
								$parent = $this->ResolveParent($row['parent']);
							else
								$parent = "Not assigned";
							
							if($content == "")
							{
								$content .= "<tr><th>Tutor</th><th>Student</th><th>Course</th><th>Class</th><th>Time Slot</th></tr>";
								$content .= "<tr><td>Tutor: <br> ".  $this->ResolveLecturer($row['id']). " </td><td>  Student: <br> " . $parent . " </td><td>  Course: <br>" . $this->ResolveCourse($row['course']) . " </td><td>  Class: <br> " . $this->ResolveClass($row['class']) . " </td><td> Timeslot: <br> " . $this->ResolveSlot($row['id']) . "</td><td> Venue: <br> " . $this->ResolveVenue($row['id']) . "</td></tr>";
							}
							else
							{
								$content .= "<tr><td>Tutor: <br> ".  $this->ResolveLecturer($row['id']). " </td><td>  Student: <br> " . $parent . " </td><td>  Course: <br>" . $this->ResolveCourse($row['course']) . " </td><td>  Class: <br> " . $this->ResolveClass($row['class']) . " </td><td> Timeslot: <br> " . $this->ResolveSlot($row['id']) . "</td><td> Venue: <br> " . $this->ResolveVenue($row['id']) . "</td></tr>";
							}
						}
					}	
				}
			}
		}
		
		if($content != ""){
			$table_content .= $content;
		}
		else{
			$table_content .= "<tr><th>No students assigned yet.</th></tr>";
		}
		
		$table_content .= "</table>";
		
		return $table_content;
		
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
	
	function GetStartTime($timeslot_id)
	{
		if($timeslot_id == 0)
		{
			$content = 'Invalid';
			return $content;
		}
		
		if(!$this->DBLogin())
		{
			$content = 'Invalid';
			return $content;
		}
		
		$query = "SELECT * FROM ". $this->timeslot . " WHERE id = " . $this->SanitizeForSQL($timeslot_id) . " ";
		
		if($result = mysqli_query($this->connection, $query)){
			if(mysqli_num_rows($result) > 0){
				if($row = $result->fetch_array(MYSQLI_BOTH))
				{
					$content = $row['start'];
				}
			}
			else{
				$content = 'Invalid';
			}
		}
		
		return $content;
	}
	
	function CheckTime($start_time)
	{
		//$date = date("Y-m-d");
		$date = "2018-07-21";
		
		$now =  time();
		
		$full_date = $date . " " . $start_time;
		$full_time = strtotime($full_date);
		
		//too late bro
		if( (int)$now >= (int)$full_time )
		{
			return true;
		}
		else{
			// not so late yet
			return false;
		}
		
	}
}

?>