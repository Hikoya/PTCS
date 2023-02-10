<?php
	require("template/header.php");
	
	
?>


  <div class="content-wrapper">  
    <section class="content">      
	  <div class="row">
		
		<div class="col-md-4 col-xs-12">
				<div class="box box-primary">
				<div class="box-header with-border">
              
				<form id='uploadstudent' class="form-horizontal" action="upload-letters.php" method='post' accept-charset='UTF-8' role="form" enctype="multipart/form-data">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Upload Freshmen</legend>
				
				<div class="form-group">
					<label class="col-md-3 control-label">File:<span class="req">*</span></label>
					<div class="col-md-7">
					  <input type="file" name="student" />
					</div>
				</div>
				  
				<div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:130px" name="upload_student" id="upload_student">Create letters</button>
					  <span></span>
					</div>
				</div>
		  
				</form>

				</div>
				</div>
			</div>
	  
	  </div>
	  
	  
	   <div class="row">
		
		<div class="col-md-4 col-xs-12">
				<div class="box box-primary">
				<div class="box-header with-border">
              
				<form id='uploadreinstated' class="form-horizontal" action="upload-letters.php" method='post' accept-charset='UTF-8' role="form" enctype="multipart/form-data">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Upload Reinstated</legend>
				
				<div class="form-group">
					<label class="col-md-3 control-label">File:<span class="req">*</span></label>
					<div class="col-md-7">
					  <input type="file" name="student_reinstated" />
					</div>
				</div>
				  
				<div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:130px" name="upload_reinstated" id="upload_reinstated">Create letters</button>
					  <span></span>
					</div>
				</div>
		  
				</form>

				</div>
				</div>
			</div>
	  
	  </div>
	  
	  
	  
	   <div class="row">
		
		<div class="col-md-4 col-xs-12">
				<div class="box box-primary">
				<div class="box-header with-border">
              
				<form id='uploadoos' class="form-horizontal" action="upload-letters.php" method='post' accept-charset='UTF-8' role="form" enctype="multipart/form-data">
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

 