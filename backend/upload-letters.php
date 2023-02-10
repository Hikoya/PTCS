<?php

error_reporting(E_ALL);

require("../class/class.main.php");
require("../vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

if(isset($_POST["upload_student"]))
{
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
	
	if(!empty($_FILES['student']['name']))
	{
		if(is_uploaded_file($_FILES['student']['tmp_name']))
		{	
			
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['student']['tmp_name']);
			$spreadsheet = $reader->load($_FILES['student']['tmp_name']);
			
			$sheet = $spreadsheet->getActiveSheet();
	
			$i = 0;
			
			$full_mpdf = new Mpdf\mPDF();
			
			$highestRow = $sheet->getHighestRow(); // e.g. 10
			$highestColumn = $sheet->getHighestColumn(); // e.g 'F'
		
			foreach ($sheet->getRowIterator() as $key => $row) {
				
				if($i === 0){
					$i++;
					continue;
				}
				
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(FALSE); 
				
				$course = NULL;
				$student_name = NULL;
				$student_id = NULL;
				$class = NULL;
				$tutor = NULL;
				$did = NULL;
				$email = NULL;
				$block = NULL;
				$street = NULL;
				$level = NULL;
				$unit = NULL;
				$building = NULL;
				$country = NULL;
				$postal = NULL;
				
				foreach ($cellIterator as $cell) {
					$value = $cell->getValue();
					
					if(!isset($course))
						$course = $value;
					else if(!isset($student_name))
						$student_name = $value;
					else if(!isset($student_id))
						$student_id = $value;
					else if(!isset($class))
						$class = $value;
					else if(!isset($tutor))
						$tutor = $value;
					else if(!isset($did))
						$did = $value;
					else if(!isset($email))
						$email = $value;
					else if(!isset($block))
						$block = $value;
					else if(!isset($street))
						$street = $value;
					else if(!isset($level))
						$level = $value;
					else if(!isset($unit))
						$unit = $value;
					else if(!isset($building))
						$building = $value;
					else if(!isset($country))
						$country = $value;
					else if(!isset($postal))
						$postal = $value;
				
				}
				
				if($course == "DASE"){
					$venue = "LT18A";
					$full_course = "Diploma in Aerospace Electronics, DASE";
				}
				else if($course == "DCPE"){
					$venue = "LT12B";
					$full_course = "Diploma in Computer Engineering, DCPE";
				}
				else if($course == "DEEE"){
					$venue = "LT14B";
					$full_course = "Diploma in Electrical and Electronic Engineering, DEEE";
				}
				else if($course == "DES"){
					$venue = "T1431";
					$full_course = "Diploma in Engineering Systems, DES";
				}
				else if($course == "DESM"){
					$venue = "T1432";
					$full_course = "Diploma in Energy Systems and Management, DESM";
				}
				else if($course == "DEB"){
					$venue = "LT12C";
					$full_course = "Diploma in Engineering with Business, DEB";
				}
				else{
					$venue = "";
					$full_course = "";
				}
				
		
				if(isset($course) && isset($student_name) && isset($student_id) && isset($class) && isset($tutor) && isset($did) && isset($email) && isset($block) && isset($street) && isset($level) && isset($unit) && isset($building) && isset($country) && isset($postal)){
					
				$mpdf = new Mpdf\mPDF();
				
				if((int)$level < 10 && $level != "NIL"){
					$level = "0" . (int)$level;
				}
				
				if((int)$unit < 10 && $level != "NIL"){
					$unit = "0" . (int)$unit;
				}
									
				$html = '
				<html>
					<body>
					<span><font size="3"><b>Please bring this letter along on the day of the event.</b></font></span>		
					<br>
					<br>
					<table width="220px" style="border: 1px solid black; border-collapse: collapse;">
					  <tr>
						 <td style="border: 1px solid black; width=40px;"><barcode code="'. ($student_id != "NIL" ? $student_id : "") .'" type="QR" class="barcode" size="1" error="M" disableborder="1" /></td>
						 <td style="border: 1px solid black; width=auto;" align="center" ><span style="text-align:centre;"><font size="2">Use your unique QR <br> code to book a time-<br>slot for meeting your <br>child&rsquo;s personal tutor. <br><font size ="4"><b>' . ($course != "NIL" ? $course : "") .'</b></font> <br><font size = "3"><b>'. $venue.'</b></font></font></span>
						 </td>
					  </tr>
					</table>
					<br>
					<span><font size="3">8th June 2018</font></span>	
					<br>
					<br>
					<table width="100%" style="border: none; table-layout: fixed;">
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">Parents of</font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">Student ID: '.($student_id != "NIL" ? $student_id : "" ).'</font></td> 
					  </tr>
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">'.($student_name != "NIL" ? strtoupper($student_name) : "" ).'</font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">Class: '.($class != "NIL" ? $class : "").'</font></td> 
					  </tr>
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">'.($block != "NIL" ? $block : "" ).' '. ($street != "NIL" ? $street : "") .' </font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">Tutor: '. ($tutor != "NIL" ? strtoupper($tutor) : "") .'</font></td> 
					  </tr>
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">'. ($level != "NIL" && $unit != "NIL" ? "#" : "") . ($level != "NIL" ? $level : "" ). ($level != "NIL" && $unit != "NIL" ? "-" : "").($unit != "NIL" ? $unit : "" ).' '. ($building != "NIL" ? $building : "" ).'</font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">DID: '.($did != "NIL" ? $did : "" ).'</font></td> 
					  </tr>
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">SINGAPORE '.($postal != "NIL" ? $postal : "").'</font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">Email: '.($email != "NIL" ? $email : "").'</font></td> 
					  </tr>
					</table>
					
					<br>
					<span><font size="3">Dear Parents,</font></span>
					<br>
					<br>
					<span><font size="4"><b><u>EEE Parent-Tutor Communication Session 2018 - 21 July 2018</u></b></font></span>
					<br>
					<br>
					<span><font size="3">We would like to invite you to the annual EEE Parent-Tutor Communication Session. This event is an opportunity for you to meet with your child&rsquo;s personal tutor to discuss how your child has settled in since the start of the academic year, review his/her progress, and consider next steps in his/her learning. </font></span>
					<br>
					<br>
					<span><font size="3">At the session, the Course Management Team will give you an understanding of the course that your child is studying, especially about course progression criteria, course specializations as well as the different teaching pedagogies adopted by EEE.</font></span>
					<br>
					<br>
					<span><font size="3">You will also be able to find out more about the opportunities available for your child to do Enhanced Internships, go on overseas learning programmes as well as educational and career guidance information through the exhibits and sharing by students and/or alumni.</font></span>
					<br>
					<br>
					<span><font size="3">Your child&rsquo;s Personal Tutor will be present to discuss with you his/her performance for the recent Mid-Semester Tests and address any concerns or questions which you may have.  Parent(s) of each student will be allocated 15 minutes of communication time with the Personal Tutor. If you require additional time, please consult your child&rsquo;s Personal Tutor to schedule another follow-up meeting. </font></span>
					<br>
					<br>
					<span><font size="3">The details for this event are as follows:</font><span>
					<br>
					<span><font size="3"><b>Date:</b>    Saturday, 21 July 2018</font> </span>
					<br>
					<span><font size="3"><b>Time:</b>    9 am - 12.30 pm </font></span>
					<br>
					<span><font size="3"><b>Registration Venue:    '. $venue .' </b> (For '. $full_course .')</font></span>
					<br>
					<span><font size="3"><b>Programme Schedule/Venue: </b> Please turn over for the details.</font></span>
					<br>
					<br>
					<span><font size="3">For logistics purpose, <b>please register your attendance by 29 June </b> via this link, <b><u>https://bit.ly/2siq3rn</u></b></font></span>
					<br>
					<br>
					<span><font size="3">We look forward to your active partnership and hope to see you and your child at the event.</font></span>
					<br>
					<br>
					<span><font size="2">Yours sincerely,</font></span>
					<br>
					<img src="img/signature.png" alt="signature">
					<br>
					<span><font size="2">Mr. Toh Ser Khoon</font></span>
					<br>
					<span><font size="2">Acting Director, School of Electrical & Electronic Engineering </font></span>
					<br>
					<br>
					<table width="700px" style="border: none;">
					  <tr>
						<td style="width: 10px; border: none;"><font size="1">P/S:</font></td>
						<td style="width: auto; border: none;"><font size="1">1. Please contact your child&rsquo;s personal tutor (contact number is given above) if you have any queries.</font> </td> 
					  </tr>
					  <tr>
						<td style="width: 10px; border: none;"></td>
						<td style="width: auto; border: none;"> <font size="1"> 2. Parking rate on campus: $1.20 per hour (differential charging on a per-minute basis) for Cars.  $0.65 per day for Motorcycles. </font></td> 
					  </tr>
					</table>
					
					
					</body>
				</html>	
				';
								
				$mpdf->shrink_tables_to_fit=1;
				$mpdf->keep_table_proportions = true;
				$mpdf->ignore_table_percents = true;
				$mpdf->WriteHTML($html);
				
				$mpdf->AddPage();
				
				$full_mpdf->shrink_tables_to_fit=1;
				$full_mpdf->keep_table_proportions = true;
				$full_mpdf->ignore_table_percents = true;
				$full_mpdf->WriteHTML($html);
				
				$full_mpdf->AddPage();

				$html_back = '
				<html>
					<body>
					<style>
					table, th, td {
						border: 1px solid black;
						border-collapse: collapse;
					}
					</style>
					<table width="900px">
						<tr>
							<td colspan="2"><b>EEE Parent-Tutor Communication Session 2018,  Saturday, 21 July 2018 </b><br> Programme Schedule For Parents of Freshmen</td>
						</tr>
						
						<tr>
							<td width="150px">9am - 9.15am</td>
							<td>Registration at <b>the respective venue</b> based on Course of Study as follows:
								<br>
								<br><b>LT18A</b> - Diploma in Aerospace Electronics (DASE)    
								<br><b>LT12B</b> - Diploma in Computer Engineering (DCPE)   
								<br><b>LT12C</b> - Diploma in Engineering with Business (DEB) 
								<br><b>LT14B</b> - Diploma in Electrical & Electronic Engineering (DEEE)
								<br><b>T1431</b> - Diploma in Engineering Systems (DES)
								<br><b>T1432</b> - Diploma in Energy Systems and Management (DESM) 
								<br>
								<br> 
								<i>(Please use your unique QR code printed on the top of this letter to book a time-slot for meeting your child&rsquo;s personal tutor. The meeting time-slot is 15 min for parent(s) of each student. The time-slot will be allocated on 1st-come-1st-served basis.)</i>
							</td>
						</tr>
						
						<tr>
							<td>9.20am</td>
							<td>Parents to be seated at the respective LT/Classroom</td>
						</tr>
						
						<tr>
							<td>9.20am - 9.45am</td>
							<td>Course Briefing</td>
						</tr>
						
						<tr>
							<td>9.45am - 9.55am</td>
							<td>Student helpers to usher Parents to "Meet Tutor" or to "EEE Experience Exhibition/Talk"</td>
						</tr>
						
						<tr>
							<td>10am - 12.30pm</td>
							<td>Concurrent Sessions: 
							<br>
							<ul>
							<li>Parent-Tutor Communication Sessions at the respective venues (refer to the table below)</li>
							<li>EEE Experience Exhibition and Talk</li>
							</ul>
							</td>
						</tr>
						
					</table>
					
					<br>
					<br>
					<table width="900px">
						<tr>
							<td><b>Course</b></td>
							<td><b>Registration</b></td>
							<td><b>Course Briefing</b></td>
							<td><b>Parent-Meet-Tutor<br>(15min per session)</b></td>
							<td><b>EEE Experience Exhibition and Talk</b></td>
						</tr>
						
						<tr>
							<td>DASE</td>
							<td>Outside LT18A</td>
							<td>LT18A</td>
							<td>AE308/09</td>
							<td rowspan="6" > 
							<ul>
							<li>Exhibition Tour Guided by Student Leaders followed by the Talk or vice versa </li>
							<li>Exhibition Venue: T12 Level 3 Labs </li>
							<li>Talk Venue: LT12A</li>
							</ul>
							</td>
						</tr>
						
						<tr>
							<td>DCPE</td>
							<td>Outside LT12B</td>
							<td>LT12B</td>
							<td>T12A401-7</td>
						</tr>
						
						<tr>
							<td>DEB</td>
							<td>Outside LT12C</td>
							<td>LT12C</td>
							<td>LT12D</td>
						</tr>
						
						<tr>
							<td>DEEE</td>
							<td>Outside LT14B</td>
							<td>LT14B</td>
							<td>T1541-5 T1551-5</td>
						</tr>
						
						<tr>
							<td>DES</td>
							<td>Outside T1431/2</td>
							<td>T1431</td>
							<td>T1434</td>
						</tr>
						
						<tr>
							<td>DESM</td>
							<td>Outside T1431/2</td>
							<td>T1432</td>
							<td>T1434</td>
						</tr>
					</table>
					
					<br>
					<br>
					<table width="600px"> 
						<tr>
							<td><img src="img/'.$venue.'.png" alt="Venue">
							
							<br>
							<br>
							<font size = "2"><b>You may visit the SP Online Campus Map at <u>https://www.sp.edu.sg/map/</u></b></font>
							</td>
						</tr>
					</table>
					</body>
				</html>
				';

				$mpdf->WriteHTML($html_back);
				$full_mpdf->WriteHTML($html_back);
								
				if($key != $highestRow)
					$full_mpdf->AddPage();
				
				$student_name = str_replace("/","",$student_name);
				
				$file_path = $_SERVER["DOCUMENT_ROOT"].$student_letter_path;
				$mpdf->Output($file_path.$course."_".$student_id."_".$student_name.".pdf", 'F');
				
				}
			}
			
			
			$file_path = $_SERVER["DOCUMENT_ROOT"].$student_letter_path;
			$full_mpdf->Output($file_path."students-letters".".pdf", 'F');
						
			$folder = $_SERVER["DOCUMENT_ROOT"].$excel_path;
			
			$path = $_FILES['student']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_file_name = "student_info_excel".".".$ext;
			
			move_uploaded_file($_FILES['student']['tmp_name'], $folder.$new_file_name);	

			header("Location: classes.php");			
		}
	}
}

if(isset($_POST['upload_reinstated']))
{
	if(!empty($_FILES['student_reinstated']['name']))
	{
		if(is_uploaded_file($_FILES['student_reinstated']['tmp_name']))
		{	
			
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['student_reinstated']['tmp_name']);
			$spreadsheet = $reader->load($_FILES['student_reinstated']['tmp_name']);
			
			$sheet = $spreadsheet->getActiveSheet();
	
			$i = 0;
			
			$full_mpdf = new Mpdf\mPDF();
			
			$highestRow = $sheet->getHighestRow(); // e.g. 10
			$highestColumn = $sheet->getHighestColumn(); // e.g 'F'
		
			foreach ($sheet->getRowIterator() as $key => $row) {
				
				if($i === 0){
					$i++;
					continue;
				}
				
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(FALSE); 
				
				$student_name = NULL;
				$student_id = NULL;
				$class = NULL;
				$tutor = NULL;
				$did = NULL;
				$email = NULL;
				$block = NULL;
				$street = NULL;
				$level = NULL;
				$unit = NULL;
				$building = NULL;
				$postal = NULL;
				
				foreach ($cellIterator as $cell) {
					$value = $cell->getValue();
					
					if(!isset($student_name))
						$student_name = $value;
					else if(!isset($student_id))
						$student_id = $value;
					else if(!isset($class))
						$class = $value;
					else if(!isset($tutor))
						$tutor = $value;
					else if(!isset($did))
						$did = $value;
					else if(!isset($email))
						$email = $value;
					else if(!isset($block))
						$block = $value;
					else if(!isset($street))
						$street = $value;
					else if(!isset($level))
						$level = $value;
					else if(!isset($unit))
						$unit = $value;
					else if(!isset($building))
						$building = $value;
					else if(!isset($postal))
						$postal = $value;
				
				}
				
				
				
		
				if(isset($student_name) && isset($student_id) && isset($class) && isset($tutor) && isset($did) && isset($email) && isset($block) && isset($street) && isset($level) && isset($unit) && isset($building) && isset($postal)){
					
				$mpdf = new Mpdf\mPDF();
				
				if((int)$level < 10 && $level != "NIL"){
					$level = "0" . (int)$level;
				}
				
				if((int)$unit < 10 && $level != "NIL"){
					$unit = "0" . (int)$unit;
				}
									
				$html = '
				<html>
					<body>
					<span><font size="3"><b>Please bring this letter along on the day of the event.</b></font></span>		
					<br>
					<br>
					<table width="220px" style="border: 1px solid black; border-collapse: collapse;">
					  <tr>
						 <td style="border: 1px solid black; width=40px;"><barcode code="'. ($student_id != "NIL" ? $student_id : "") .'" type="QR" class="barcode" size="1" error="M" disableborder="1" /></td>
						 <td style="border: 1px solid black; width=auto;" align="center" ><span style="text-align:centre;"><font size="2">Use your unique QR <br> code to book a time-<br>slot for meeting your <br>child&rsquo;s personal tutor.</font></span>
						 </td>
					  </tr>
					</table>
					<br>
					<span><font size="3">8th June 2018</font></span>	
					<br>
					<br>
					<table width="100%" style="border: none; table-layout: fixed;">
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">Parents of</font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">Student ID: '.($student_id != "NIL" ? $student_id : "" ).'</font></td> 
					  </tr>
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">'.($student_name != "NIL" ? strtoupper($student_name) : "" ).'</font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">Class: '.($class != "NIL" ? $class : "").'</font></td> 
					  </tr>
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">'.($block != "NIL" ? $block : "" ).' '. ($street != "NIL" ? $street : "") .' </font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">Tutor: '. ($tutor != "NIL" ? strtoupper($tutor) : "") .'</font></td> 
					  </tr>
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">'. ($level != "NIL" && $unit != "NIL" ? "#" : "") . ($level != "NIL" ? $level : "" ). ($level != "NIL" && $unit != "NIL" ? "-" : "").($unit != "NIL" ? $unit : "" ).' '. ($building != "NIL" ? $building : "" ).'</font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">DID: '.($did != "NIL" ? $did : "" ).'</font></td> 
					  </tr>
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">SINGAPORE '.($postal != "NIL" ? $postal : "").'</font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">Email: '.($email != "NIL" ? $email : "").'</font></td> 
					  </tr>
					</table>
					
					<br>
					<span><font size="3">Dear Parents,</font></span>
					<br>
					<br>
					<span><font size="4"><b><u>EEE Parent-Tutor Communication Session 2018 - 21 July 2018</u></b></font></span>
					<br>
					<br>
					<span><font size="3">We would like to invite you to the EEE Parent-Tutor Communication Session 2018.  This event is an opportunity for parents to meet with their child&rsquo;s personal tutor to discuss and review the progress in their course. </font></span>
					<br>
					<br>
					<span><font size="3">Your child was given another chance to continue with the course by being re-instated. This decision was based on the commitment made by him/her that he/she will put in extra effort to improve on his/her studies in order to be able to progress to the next stage (by passing all repeated modules). We seek your kind co-operation to work together with your child to follow through with this. We would like to emphasize that another removal will mean that your child will not be able to continue with the course. </font></span>
					<br>
					<br>
					<span><font size="3">To help you better understand the support needed from parents, please attend the Parent-Tutor Communication Session with your child.  The School will explain to you in detail the different help schemes available such as Peer Tutoring, Financial Assistance Scheme, Counselling services, as well as other important information relating to course removal, disciplinary issues, etc.</font></span>
					<br>
					<br>
					<span><font size="3">Your child&rsquo;s Personal Tutor will also discuss with you his/her performance for the recent Mid-Semester Tests and address any concerns or questions which you might have.  Each parent will be allocated 15 minutes of communication time with the Personal Tutor. If you require additional time, please consult your child’s Personal Tutor to schedule another follow-up meeting.</font></span>
					<br>
					<br>
					<span><font size="3">The details for this event are as follows:</font><span>
					<br>
					<span><font size="3"><b>Date:</b>    Saturday, 21 July 2018</font> </span>
					<br>
					<span><font size="3"><b>Time:</b>    8.30 am – 11.30am </font></span>
					<br>
					<span><font size="3"><b>Venue: LT17A </b>, Singapore Polytechnic </font></span>
					<br>
					<span><font size="3">(Please turn over for Programme Details and Campus Map.) </font></span>
					<br>
					<br>
					<span><font size="3">For logistics purpose, <b>please register your attendance by 29 June </b> via this link, <b><u>https://bit.ly/2spl6fC</u></b></font></span>
					<br>
					<br>
					<span><font size="3">We look forward to your active partnership and hope to see you and your child at the event.</font></span>
					<br>
					<br>
					<span><font size="2">Yours sincerely,</font></span>
					<br>
					<img src="img/signature.png" alt="signature">
					<br>
					<span><font size="2">Mr. Toh Ser Khoon</font></span>
					<br>
					<span><font size="2">Acting Director, School of Electrical & Electronic Engineering </font></span>
					<br>
					<br>
					<table width="700px" style="border: none;">
					  <tr>
						<td style="width: 10px; border: none;"><font size="1">P/S:</font></td>
						<td style="width: auto; border: none;"><font size="1">1. Please contact your child&rsquo;s personal tutor (contact number is given above) if you have any queries.</font> </td> 
					  </tr>
					  <tr>
						<td style="width: 10px; border: none;"></td>
						<td style="width: auto; border: none;"> <font size="1"> 2. Parking rate on campus: $1.20 per hour (differential charging on a per-minute basis) for Cars.  $0.65 per day for Motorcycles. </font></td> 
					  </tr>
					</table>
					
					
					</body>
				</html>	
				';
								
				$mpdf->shrink_tables_to_fit=1;
				$mpdf->keep_table_proportions = true;
				$mpdf->ignore_table_percents = true;
				$mpdf->WriteHTML($html);
				
				$mpdf->AddPage();
				
				$full_mpdf->shrink_tables_to_fit=1;
				$full_mpdf->keep_table_proportions = true;
				$full_mpdf->ignore_table_percents = true;
				$full_mpdf->WriteHTML($html);
				
				$full_mpdf->AddPage();

				$html_back = '
					<html>
					<body>
					<style>
					table, th, td {
						border: 1px solid black;
						border-collapse: collapse;
					}
					</style>
					<table width="900px">
						<tr>
							<td colspan="2"><b>EEE Parent-Tutor Communication Session 2018,  Saturday, 21 July 2018 </b></td>
						</tr>
						
						<tr>
							<td width="150px">8.30am</td>
							<td>Registration at <b>LT17A</b> 
								<br>
								<br> 
								<i>(Please use your unique QR code printed on the top of this letter to book a time-slot for meeting your child&rsquo;s personal tutor. The meeting time-slot is 15 min for parent(s) of each student. The time-slot will be allocated on 1st-come-1st-served basis.)</i>
							</td>
						</tr>
						
		
						<tr>
							<td>8.45am - 8.55am</td>
							<td>Student helpers to usher Parents to respective venue to meet Tutor </td>
						</tr>
						
						<tr>
							<td>9am to 11.30am </td>
							<td>Concurrent Sessions: 
							<br>
							<ul>
							<li>Parent-Tutor Communication Session at T16/T17 classroom</li>
							<li>Briefing on Support for Students-at-Risk at LT17A</li>
							</ul>
							</td>
						</tr>
						
					</table>
			
					<br>
					<br>
					<table width="600px"> 
						<tr>
							<td><img src="img/LT17A.png" alt="Venue">
							<br>
							<br>
							<font size = "2"><b>You may visit the SP Online Campus Map at <u>https://www.sp.edu.sg/map/</u></b></font>
							</td>
							
						</tr>
					</table>
					</body>
				</html>
				';

				$mpdf->WriteHTML($html_back);
				$full_mpdf->WriteHTML($html_back);
								
				if($key != $highestRow)
					$full_mpdf->AddPage();
				
				$student_name = str_replace("/","",$student_name);
				
				$file_path = $_SERVER["DOCUMENT_ROOT"].$student_reinstated_letter_path;
				$mpdf->Output($file_path.$student_id."_".strtoupper($student_name).".pdf", 'F');
				
				}
			}
			
			
			$file_path = $_SERVER["DOCUMENT_ROOT"].$student_reinstated_letter_path;
			$full_mpdf->Output($file_path."students-letters-reinstated".".pdf", 'F');
						
			$folder = $_SERVER["DOCUMENT_ROOT"].$excel_path;
			
			$path = $_FILES['student_reinstated']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_file_name = "student_info_reinstated_excel".".".$ext;
			
			move_uploaded_file($_FILES['student_reinstated']['tmp_name'], $folder.$new_file_name);	

			header("Location: classes.php");			
		}
	}
	
}



if(isset($_POST['upload_oos']))
{
	if(!empty($_FILES['student_oos']['name']))
	{
		if(is_uploaded_file($_FILES['student_oos']['tmp_name']))
		{	
			
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['student_oos']['tmp_name']);
			$spreadsheet = $reader->load($_FILES['student_oos']['tmp_name']);
			
			$sheet = $spreadsheet->getActiveSheet();
	
			$i = 0;
			
			$full_mpdf = new Mpdf\mPDF();
			
			$highestRow = $sheet->getHighestRow(); // e.g. 10
			$highestColumn = $sheet->getHighestColumn(); // e.g 'F'
		
			foreach ($sheet->getRowIterator() as $key => $row) {
				
				if($i === 0){
					$i++;
					continue;
				}
				
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(FALSE); 
				
				$course = NULL;
				$class = NULL;
				$student_name = NULL;
				$student_id = NULL;
				$tutor = NULL;
				$did = NULL;
				$email = NULL;
				$block = NULL;
				$street = NULL;
				$level = NULL;
				$unit = NULL;
				$building = NULL;
				$postal = NULL;
				
				foreach ($cellIterator as $cell) {
					$value = $cell->getValue();
					
					if(!isset($course))
						$course = $value;
					else if(!isset($student_id))
						$student_id = $value;
					else if(!isset($student_name))
						$student_name = $value;
					else if(!isset($class))
						$class = $value;
					else if(!isset($tutor))
						$tutor = $value;
					else if(!isset($did))
						$did = $value;
					else if(!isset($email))
						$email = $value;
					else if(!isset($block))
						$block = $value;
					else if(!isset($street))
						$street = $value;
					else if(!isset($level))
						$level = $value;
					else if(!isset($unit))
						$unit = $value;
					else if(!isset($building))
						$building = $value;
					else if(!isset($postal))
						$postal = $value;
				
				}
				
				
				
		
				if(isset($course) && isset($student_name) && isset($student_id) && isset($class) && isset($tutor) && isset($did) && isset($email) && isset($block) && isset($street) && isset($level) && isset($unit) && isset($building) && isset($postal)){
					
				$mpdf = new Mpdf\mPDF();
				
				if((int)$level < 10 && $level != "NIL"){
					$level = "0" . (int)$level;
				}
				
				if((int)$unit < 10 && $level != "NIL"){
					$unit = "0" . (int)$unit;
				}
									
				$html = '
					<html>
					<body>
					<span><font size="3"><b>Please bring this letter along on the day of the event.</b></font></span>		
					<br>
					<br>
					<table width="220px" style="border: 1px solid black; border-collapse: collapse;">
					  <tr>
						 <td style="border: 1px solid black; width=40px;"><barcode code="'. ($student_id != "NIL" ? $student_id : "") .'" type="QR" class="barcode" size="1" error="M" disableborder="1" /></td>
						 <td style="border: 1px solid black; width=auto;" align="center" ><span style="text-align:centre;"><font size="2">Use your unique QR <br> code to book a time-<br>slot for meeting your <br>child&rsquo;s personal tutor.</font></span>
						 </td>
					  </tr>
					</table>
					<br>
					<span><font size="3">8th June 2018</font></span>	
					<br>
					<br>
					<table width="100%" style="border: none; table-layout: fixed;">
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">Parents of</font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">Student ID: '.($student_id != "NIL" ? $student_id : "" ).'</font></td> 
					  </tr>
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">'.($student_name != "NIL" ? strtoupper($student_name) : "" ).'</font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">Class: '.($class != "NIL" ? $class : "").'</font></td> 
					  </tr>
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">'.($block != "NIL" ? $block : "" ).' '. ($street != "NIL" ? $street : "") .' </font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">Tutor: '. ($tutor != "NIL" ? strtoupper($tutor) : "") .'</font></td> 
					  </tr>
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">'. ($level != "NIL" && $unit != "NIL" ? "#" : "") . ($level != "NIL" ? $level : "" ). ($level != "NIL" && $unit != "NIL" ? "-" : "").($unit != "NIL" ? $unit : "" ).' '. ($building != "NIL" ? $building : "" ).'</font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">DID: '.($did != "NIL" ? $did : "" ).'</font></td> 
					  </tr>
					  <tr>
						<td style="width: 400px; border: none; overflow: hidden;"><font size="2">SINGAPORE '.($postal != "NIL" ? $postal : "").'</font></td>
						<td style="width: 300px; border: none; overflow: hidden;"><font size="2">Email: '.($email != "NIL" ? $email : "").'</font></td> 
					  </tr>
					</table>
					
					<br>
					<span><font size="3">Dear Parents,</font></span>
					<br>
					<br>
					<span><font size="4"><b><u>EEE Parent-Tutor Communication Session 2018 - 21 July 2018</u></b></font></span>
					<br>
					<br>
					<span><font size="3">We would like to invite you to the EEE Parent-Tutor Communication Session 2018.  This event is an opportunity for you to meet with your child&rsquo;s personal tutor to discuss and review your child&rsquo;s progress in their course of study at School of EEE.  </font></span>
					<br>
					<br>
					<span><font size="3">We would like to highlight that your child is repeating one or more modules in this current semester and will likely be taking more than three years to complete his/her diploma. We believe that with strong parental support coupled with our student care and support system will help your child make studies a priority.  </font></span>
					<br>
					<br>
					<span><font size="3">At this session, we will give you a better understanding of the course your child is studying, especially on the condition(s) in which students would likely fail a module and/or receive only PASS/FAIL grade, course progression criteria, academic appeal process, Peer Tutoring scheme, etc. </font></span>
					<br>
					<br>
					<span><font size="3">Your child&rsquo;s Personal Tutor will discuss with you his/her performance for the recent Mid-Semester Tests and address any concerns or questions which you might have.  Each parent will be allocated 15 minutes of communication time with the Personal Tutor. If you require additional time, please consult your child&rsquo;s Personal Tutor to schedule another follow-up meeting. </font></span>
					<br>
					<br>
					<span><font size="3">The details for this event are as follows:</font><span>
					<br>
					<span><font size="3"><b>Date:</b>    Saturday, 21 July 2018</font> </span>
					<br>
					<span><font size="3"><b>Time:</b>    8.30 am – 11.30am </font></span>
					<br>
					<span><font size="3"><b>Venue: LT17A </b>, Singapore Polytechnic </font></span>
					<br>
					<span><font size="3">(Please turn over for Programme Details and Campus Map.) </font></span>
					<br>
					<br>
					<span><font size="3">For logistics purpose, <b>please register your attendance by 29 June </b> via this link, <b><u>https://bit.ly/2spl6fC</u></b></font></span>
					<br>
					<br>
					<span><font size="3">We look forward to your active partnership and hope to see you and your child at the event.</font></span>
					<br>
					<br>
					<span><font size="2">Yours sincerely,</font></span>
					<br>
					<img src="img/signature.png" alt="signature">
					<br>
					<span><font size="2">Mr. Toh Ser Khoon</font></span>
					<br>
					<span><font size="2">Acting Director, School of Electrical & Electronic Engineering </font></span>
					<br>
					<br>
					<table width="700px" style="border: none;">
					  <tr>
						<td style="width: 10px; border: none;"><font size="1">P/S:</font></td>
						<td style="width: auto; border: none;"><font size="1">1. Please contact your child&rsquo;s personal tutor (contact number is given above) if you have any queries.</font> </td> 
					  </tr>
					  <tr>
						<td style="width: 10px; border: none;"></td>
						<td style="width: auto; border: none;"> <font size="1"> 2. Parking rate on campus: $1.20 per hour (differential charging on a per-minute basis) for Cars.  $0.65 per day for Motorcycles. </font></td> 
					  </tr>
					</table>
					
					
					</body>
				</html>	
				';
								
				$mpdf->shrink_tables_to_fit=1;
				$mpdf->keep_table_proportions = true;
				$mpdf->ignore_table_percents = true;
				$mpdf->WriteHTML($html);
				
				$mpdf->AddPage();
				
				$full_mpdf->shrink_tables_to_fit=1;
				$full_mpdf->keep_table_proportions = true;
				$full_mpdf->ignore_table_percents = true;
				$full_mpdf->WriteHTML($html);
				
				$full_mpdf->AddPage();

				$html_back = '
					<html>
					<body>
					<style>
					table, th, td {
						border: 1px solid black;
						border-collapse: collapse;
					}
					</style>
					<table width="900px">
						<tr>
							<td colspan="2"><b>EEE Parent-Tutor Communication Session 2018,  Saturday, 21 July 2018 </b></td>
						</tr>
						
						<tr>
							<td width="150px">8.30am</td>
							<td>Registration at <b>LT17A</b> 
								<br>
								<br> 
								<i>(Please use your unique QR code printed on the top of this letter to book a time-slot for meeting your child&rsquo;s personal tutor. The meeting time-slot is 15 min for parent(s) of each student. The time-slot will be allocated on 1st-come-1st-served basis.)</i>
							</td>
						</tr>
						
		
						<tr>
							<td>8.45am - 8.55am</td>
							<td>Student helpers to usher Parents to respective venue to meet Tutor </td>
						</tr>
						
						<tr>
							<td>9am to 11.30am </td>
							<td>Concurrent Sessions: 
							<br>
							<ul>
							<li>Parent-Tutor Communication Session at T16/T17 classroom</li>
							<li>Briefing on Support for Students-at-Risk at LT17A</li>
							</ul>
							</td>
						</tr>
						
					</table>
			
					<br>
					<br>
					<table width="600px"> 
						<tr>
							<td><img src="img/LT17A.png" alt="Venue">
							<br>
							<br>
							<font size = "2"><b>You may visit the SP Online Campus Map at <u>https://www.sp.edu.sg/map/</u></b></font>
							</td>
							
						</tr>
					</table>
					</body>
				</html>
				';

				$mpdf->WriteHTML($html_back);
				$full_mpdf->WriteHTML($html_back);
								
				if($key != $highestRow)
					$full_mpdf->AddPage();
				
				$student_name = str_replace("/","",$student_name);
				
				$file_path = $_SERVER["DOCUMENT_ROOT"].$student_oos_letter_path;
				$mpdf->Output($file_path.$course."_".$student_id."_".strtoupper($student_name).".pdf", 'F');
				
				}
			}
			
			
			$file_path = $_SERVER["DOCUMENT_ROOT"].$student_oos_letter_path;
			$full_mpdf->Output($file_path."students-letters-oos".".pdf", 'F');
						
			$folder = $_SERVER["DOCUMENT_ROOT"].$excel_path;
			
			$path = $_FILES['student_oos']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_file_name = "student_info_oos_excel".".".$ext;
			
			move_uploaded_file($_FILES['student_oos']['tmp_name'], $folder.$new_file_name);	

			header("Location: classes.php");			
		}
	}
	
}


?>