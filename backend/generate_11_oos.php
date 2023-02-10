<?php

require("../class/class.main.php");
require("../vendor/autoload.php");

if(isset($_POST['upload_oos']))
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
					<span><font size="3">We would like to highlight that your child failed one or more modules in the previous semester and will likely be taking more than three years to complete his/her diploma. We believe that with strong parental support coupled with our student care and support system will help your child make studies a priority.  </font></span>
					<br>
					<br>
					<span><font size="3">At this session, we will give you a better understanding of the course your child is studying, especially on the condition(s) in which students would likely fail a module and/or receive only PASS/FAIL grade, course progression criteria, academic appeal process, Peer Tutoring scheme, etc. </font></span>
					<br>
					<br>
					<span><font size="3">Your child&rsquo;s Personal Tutor will discuss with you his/her performance for the recent Mid-Semester Tests if applicable and address any concerns or questions which you might have.  Each parent will be allocated 15 minutes of communication time with the Personal Tutor. If you require additional time, please consult your child&rsquo;s Personal Tutor to schedule another follow-up meeting. </font></span>
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
				$mpdf->Output($file_path.$course."_".$student_id."_edited_".strtoupper($student_name).".pdf", 'F');
				
				}
			}
			
			
			$file_path = $_SERVER["DOCUMENT_ROOT"].$student_oos_letter_path;
			$full_mpdf->Output($file_path."students-letters-11-oos".".pdf", 'F');
						
			$folder = $_SERVER["DOCUMENT_ROOT"].$excel_path;
			
			$path = $_FILES['student_oos']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_file_name = "student_info_oos_11_excel".".".$ext;
			
			move_uploaded_file($_FILES['student_oos']['tmp_name'], $folder.$new_file_name);	

			header("Location: classes.php");			
			
}
?>




<?php
	require("template/header.php");
	
	
?>


  <div class="content-wrapper">  
    <section class="content">      
	
	   <div class="row">
		
		<div class="col-md-4 col-xs-12">
				<div class="box box-primary">
				<div class="box-header with-border">
              
				<form id='uploadoos' class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post' accept-charset='UTF-8' role="form" enctype="multipart/form-data">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Upload OOS</legend>
				
				<div class="form-group">
					<label class="col-md-3 control-label">File:<span class="req">*</span></label>
					<div class="col-md-7">
					  <input type="file" name="student_oos" />
					</div>
				</div>
				  
				<div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:130px" name="upload_oos" id="upload_oos">Create letters</button>
					  <span></span>
					</div>
				</div>
		  
				</form>

				</div>
				</div>
			</div>
	  
	  </div>
	  
    </section>
    
  </div>

<?php
	require("template/footer.php");
?>

 