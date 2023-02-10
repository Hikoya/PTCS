<?php
require("class/class.main.php");
	
?>
    
<!DOCTYPE html>
<html>

    <head>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF-8">
        <title>
            <?php echo $sitename?>
        </title>
		
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,500,600" rel="stylesheet">
        <link rel="stylesheet" href="dist/bower_components/bootstrap/dist/css/bootstrap.min.css">

		<style>

		img {
		margin-left: auto;
		margin-right: auto;
		display: block;
		}
		
		.col-centered{
			float: none;
			margin: 0 auto;
		}
		
			
		</style>
		 
    </head>

    <body background='img\wood.jpg'>
		<br>
		  
		<div class="container-fluid">
			<div class="row">
				<div align="center" class="col-lg-6 col-xs-6 col-md-6 col-sm-6 col-centered">
					<a href='<?php echo 'https://'. $_SERVER['HTTP_HOST']; ?>'><img class="img-responsive" align='center' src="img\sp_logo.png"></img></a>
					<div align='center'>
						<p style="font-size:2vw"> <?php echo $sitewelcome; ?></p>
					</div>
				</div>
			</div>
			<div class="row">
				<div align='center' class="col-lg-12 col-xs-12 col-md-12 col-sm-12 col-centered">
				    <p style="font-size:1vw"><b>Scan your QR Code here</b></p>
                </div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-md-10 col-lg-8 col-sm-12 col-centered" align='center'>
					<div class="embed-responsive embed-responsive-4by3" align='center'>
						<video class="embed-responsive-item" width="1024" height="800" id="preview"></video>
					</div>
				</div>
			</div>
			<br>
			<div class="row">
				<div align='center' class="col-lg-8 col-xs-12 col-md-10 col-sm-12 col-centered" >
					<button type="button" class="btn btn-info btn-lg btn-block" style="white-space: normal;" data-toggle="modal" data-target="#modal-register-new" id="register-slot">Register if without QR Code (Student ID needed)</button> 
				</div>	
			</div>
			<br>
			<div class="row">
				<div align='center' class="col-lg-8 col-xs-12 col-md-10 col-sm-12 col-centered">
                    <form autocomplete="off">
						<input autocomplete="false" name="hidden" type="text" style="display:none;">			   					
						<input class="form-control" style="font-size: 20px; white-space: normal;" placeholder="Enter student's full name to search for admission number" type="text" id="search" size="30" autocomplete="false">	
					</form>
                </div>
			</div>
			<br>
			<div class="row">
				<div align='center' class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
					<div align='left' style="font-size: 18px; background-color: white; white-space: normal;" id="livesearch"></div>
				</div>
			</div>
			<br>
			<br>
			<div class="row">
				<div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
					<TABLE BORDER="0" align='center'>
					<TR>
						
					<TD>
						<button onclick="window.location.href='/queue.php'" type="button" class="btn btn-primary" >View assigned slots</button>
					</TD>
					<TD>
						<div style="padding:10px"></div>
					</TD>
					<TD> 
						<button onclick="window.location.href='/queue-all.php'" type="button" class="btn btn-primary" >View all slots</button>
					</TD>
					
					</TR>

					</TABLE>
				</div>
			</div>
		</div>
			
		<div class="modal fade" id="modal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Slot</h4>
              </div>
              <div class="modal-body" id="modalContent" style="font-size:20px">
                
				<p id="school" align="center"> School of EEE </p>
				<p id="event" align="center"> Parent-Tutor Communication Session </p>
				<p id="date" align="center"> 21 July 2018 </p>
				
				<p id="id"></p>
                <p id="message"></p>
				<p id="name"></p>
                <p id="course"></p>
					
				<p id="class"></p>
				<p id="slot"></p>
				<p id="venue"></p>
				<p id="lecturer"></p>

				<p id="reminder" align="center"> Please be on time! </p>				
	
              </div>
              <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" id="savechanges" class="btn btn-primary" data-dismiss="modal" ></button>
              </div>
            </div>
          </div>
        </div>
		
		<div class="modal fade" id="modal-slot">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirmation</h4>
              </div>
              <div class="modal-body" id="modalContent-slot" style="font-size:20px">
                	
				<p id="school-slot" align="center"> School of EEE </p>
				<p id="event-slot" align="center"> Parent-Tutor Communication Session </p>
				<p id="date-slot" align="center"> 21 July 2018 </p>
				
				<p id="id-slot"></p>
                <p id="message-slot"></p>
				<p id="name-slot"></p>
                <p id="course-slot"></p>
					
				<p id="class-slot"></p>
				<p id="slot-slot"></p>
				<p id="venue-slot"></p>
				<p id="lecturer-slot"></p>	
				
				<p id="reminder-slot" align="center"> Please be on time! </p>
	
              </div>
              <div class="modal-footer">
                <button type="button" id="close-slot" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" id="printslot" class="btn btn-primary" data-dismiss="modal" >Print Slot</button>
              </div>
            </div>
          </div>
        </div>
		
		<div class="modal fade" id="modal-register-new">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Register Slot</h4>
              </div>
              <div class="modal-body" id="modal-register-new-content">
                
				<form id='register-new-form' class="form-horizontal" accept-charset='UTF-8' role="form" autocomplete="off">
				
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  		
				   <input autocomplete="false" name="hidden" type="text" style="display:none;">
				   
				   <div class="form-group">
					<label class="col-md-3 control-label">Admission Number:<span class="req">*</span></label>
					<div class="col-md-7">
						<input class="form-control" name="adminno" id= "adminno" type="text" required >
					</div>
				  </div>
				  
				  <div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:130px" name="registernew" id="registernew">Register Student</button>
					  <span></span>
					</div>
					<input type="reset" style="margin-left:20px" class="btn btn-default" data-dismiss="modal" value="Clear">
				  </div>
		   
				</form>

              </div>
              
            </div>
          </div>
        </div>

		</body>

		<script src="dist/bower_components/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="reader/instascan.min.js"></script>
		<script src="dist/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
		<script src="dist/bower_components/jQueryPrint/printThis.min.js"></script>

		<script type="text/javascript">

	$(document).ready(function(){
  
	
      let scanner = new Instascan.Scanner({ video: document.getElementById('preview') , mirror: false });

	  scanner.addListener('scan', function (content) {
		console.log("SCAN");
		checkSlot(content);
      });

      Instascan.Camera.getCameras().then(function (cameras) {
		 
		 /*
		if (cameras.length > 0) {
			var selectedCam = cameras[0];
			$.each(cameras, (i, c) => {
				if (c.name != null && c.name.indexOf('back') != -1) {
					selectedCam = c;
					return false;
				}
			});

			scanner.start(selectedCam);
		} else {
			console.error('No cameras found.');
		}
		*/
		
        if (cameras.length > 0) {
			if (/Mobi|Android/i.test(navigator.userAgent) && cameras.length > 1) {
				scanner.start(cameras[1]);
			}
			else{
				scanner.start(cameras[0]);
			}
          
        } else {
		  alert("No cameras found. Try refreshing the page");
          console.error('No cameras found.');
        }
		
		
      }).catch(function (e) {
        console.error(e);
      });

  
	$("#register-slot").click(function(){
		$('#adminno').val("");
	});
	
	
	$('#register-new-form').submit(function(e){
    e.preventDefault();
		var parentID = $("#adminno").val();
		$('#modal-register-new').modal('hide');
		checkSlot(parentID);
	});


	function checkSlot(parentID) {

		console.log("CHECK SLOT");

		$.ajax({
		url: 'backend.php',
		type: 'POST',
		async: true,
		dataType: "json",
		data: {id:parentID},
		success: function (data) {
			
			$("#savechanges").show();
			
			if(data.status == 0)
			{
				$("#id").text("ID: " + parentID );
				$("#message").text("Message: " + data.message);
				$("#name").text(data.name);
				$("#course").text(data.course);
				$("#class").text(data.class);
				$("#slot").text(data.slot);
				$("#venue").text(data.venue);
				$("#lecturer").text(data.lecturer);
				
				$("#school").hide();
				$("#event").hide();
				$("#date").hide();
				$("#reminder").hide();
				
				$("#savechanges").hide();
			}
			else{
				
				$("#id").text(data.parent);
				$("#message").text(data.message);
				$("#name").text(data.name);
				$("#course").text(data.course);
				$("#class").text(data.class);
				$("#slot").text(data.slot);
				$("#venue").text(data.venue);
				$("#lecturer").text(data.lecturer);
				
				if(data.status == 1)
				{
					$("#school").hide();
					$("#event").hide();
					$("#date").hide();
					$("#reminder").hide();
					
					$("#savechanges").text("Confirm Slot");
					$("#savechanges").unbind('click');
					$("#savechanges").click(function(){
						confirmSlot(data);
					});
				}
					
				else if(data.status == 2)
				{
					$("#school").show();
					$("#event").show();
					$("#date").show();
					$("#reminder").show();
					
					$("#savechanges").text("Print Slot");
					$("#savechanges").unbind('click');
					$("#savechanges").click(function(){
						printSlot();
					});
				}
				
				else if(data.status == 3)
				{
					$("#school").hide();
					$("#event").hide();
					$("#date").hide();
					$("#reminder").hide();
					
					$("#savechanges").hide();
				}
					
				
			}
			
			$('#modal').modal('show');
			
		}
	  });

	}
	
	function confirmSlot(data)
	{
		console.log("CONFIRM SLOT");
		
		$.ajax({
		url: 'backend.php',
		type: 'POST',
		async: true,
		dataType: "json",
		data: {slot: data.slotID, parent: data.parentID},
		success: function (result) {
			
			$('#modal').modal('hide');
			
			if(result.status == 1)
			{
				$("#close-slot").show();
				$("#printslot").text("Print Slot");
				$("#printslot").unbind('click');
				$("#printslot").click(function(){
					printSlot2();
				});
				
				$("#id-slot").text(result.parent);
				$("#message-slot").text(result.message);
				
				//$("#name-slot").text(result.name);
				$("#course-slot").text(result.course);
				$("#class-slot").text(result.class);
				$("#slot-slot").text(result.slot);
				$("#venue-slot").text(result.venue);
				$("#lecturer-slot").text(result.lecturer);
				
				$("#school-slot").show();
				$("#event-slot").show();
				$("#date-slot").show();
				$("#reminder-slot").show();
				
			}
			else if(result.status == 2)
			{
				$("#printslot").text("Close");
				$("#printslot").unbind('click');
				$("#close-slot").hide();
				
				$("#id-slot").text(result.parent);
				$("#message-slot").text(result.message);
				
				$("#school-slot").hide();
				$("#event-slot").hide();
				$("#date-slot").hide();
				$("#reminder-slot").hide();
				
			}
			
			$('#modal-slot').modal('show');
		}
	  });
	}
	
	function printSlot()
	{
		console.log("PRINT SLOT ");
		$('#modalContent').printThis();
	}
	
	function printSlot2()
	{
		console.log("PRINT SLOT 2");
		$('#modalContent-slot').printThis();
	}
	
	var countdowntimer;
	
	function countDown() {
		countdowntimer = setTimeout(function(){ $('#search').val(""); showResult(""); }, 20000);
	}

	function debounce(fn, delay) {
	  var timer = null;
	  return function () {
		var context = this, args = arguments;
		clearTimeout(timer);
		timer = setTimeout(function () {
		  fn.apply(context, args);
		}, delay);
	  };
	}
	
	$('#search').keyup(debounce(function (event) {

		var text = $(this).val();
		showResult(text);
		
	}, 300));

	function showResult(str) {
		
	  if (str.length==0) { 
		document.getElementById("livesearch").innerHTML="";
		document.getElementById("livesearch").style.border="0px";
		return;
	  }
	 
	  $.ajax({
		url: 'search.php',
		type: 'POST',
		async: true,
		data: {value: str},
		success: function (data) {
			
			document.getElementById("livesearch").innerHTML= data;
			document.getElementById("livesearch").style.border="1px solid #A5ACB2";
	        clearTimeout(countdowntimer);
			countDown();

		}
	  });
	  
	}
	
	});

	</script>


</html>