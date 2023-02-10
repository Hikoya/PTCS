<?php
	require("template/header.php");
	
	if(isset($_POST['submitted']))
	{
	   if($functions->AddSlot())
	   {
		  header("Refresh:0");
	   }
	}
	
	if(isset($_POST['submitted2']))
	{
	   if($functions->AssignSlot())
	   {
		  header("Refresh:0");
	   }
	}
	
	if(isset($_POST['deleteslot']))
	{
	   if($functions->DeleteAllSlots())
	   {
		  header("Refresh:0");
	   }
	}
	
	if (isset($_GET['pageno'])) {
		$pageno = (int)$_GET['pageno'];
	} else {
		$pageno = 1;
	}
	
	$offset = ($pageno - 1) * $num_of_records;
	$total_pages = $functions->GetPageCount($slots);
	
?>

<style>
  .select-style {
    border: 1px solid #ccc;
    border-radius: 3px;
    overflow: hidden;
  
}

.select-style select {
    padding: 5px 8px;
    width: 100%;
    border: none;
    box-shadow: none;
    background: transparent;
    background-image: none;
    -webkit-appearance: none;
}

.select-style select:focus {
    outline: none;
}
 </style>
 
  <div class="content-wrapper">  
    <section class="content">      
      <div class="row">
	  <div class="col-xs-12 col-md-12">
          <div class="box">
            <div class="box-header">
              <table>
              <tr>
			  <td width="200"><h3 class="box-title">Slots</h3></td>
			  <td><ul class="pagination">
					<li><a href="?pageno=1">First</a></li>
					<li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
						<a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">Prev</a>
					</li>
					<li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
						<a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next</a>
					</li>
					<li><a href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
				</ul></td>
				</tr>
			</table>
            </div>
            <div class="box-body table-responsive no-padding">
			
			<div style="padding:10px">
			 
				<table id="example1" class="table table-hover" style="table-layout:fixed;">
				<thead>
				<tr>
					<th>ID</th>
					<th>Venue</th>
					<th>Lecturer</th>
					<th>Time</th>
					<th>Course</th>
					<th>Class</th>
					<th>Parent</th>
					<th>Delete</th>
				</tr>
				</thead>
				<tbody>
					<?php
						echo $functions->GetSlots($offset);
					?>
				</tbody>
				</table>
				
				
			</div>
			</div>
          </div>
		  </div>
	  </div>
	  <div class="row">      
        <section>
			<div class="col-md-4 col-xs-12">
				<div class="box box-primary">
				<div class="box-header with-border">
              
				<form id='slots' class="form-horizontal" action='<?php echo $functions->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' role="form">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Individual Slots</legend>
					  	  
				  <div class="form-group">
					<label class="col-md-3 control-label">Venue:<span class="req">*</span></label>
					<div class="col-md-7">
					<div class = "select-style" >
					<select id="venue" name="venue">
						<?php 
							echo $functions->SelectionBoxVenue();
						?>	
					</select>
					</div>
					</div>
				  </div>
				  
				  <div class="form-group">
					<label class="col-md-3 control-label">Lecturer:<span class="req">*</span></label>
					<div class="col-md-7">
					  <div class = "select-style" >
						<select id="lecturer" name="lecturer">
						<?php 
							echo $functions->SelectionBoxLecturer();
						?>	
						</select>
					  </div>
					</div>
				  </div>
					
				  <div class="form-group">
					<label class="col-md-3 control-label">Time:<span class="req">*</span></label>
					<div class="col-md-7">
					  <div class = "select-style" >
						<select id="timeslot" name="timeslot">
						<?php 
							echo $functions->SelectionBoxTimeSlot();
						?>	
						</select>
					  </div>
					</div>
				  </div>
				  
				  <div class="form-group">
					<label class="col-md-3 control-label">Course:<span class="req">*</span></label>
					<div class="col-md-7">
					
					  <div class = "select-style" >
						<select id="course" name="course">
						<?php 
							echo $functions->SelectionBoxCourse();
						?>	
						</select>
					  </div>
				
					</div>
				  </div>
				  
				   <div class="form-group">
					<label class="col-md-3 control-label">Class:<span class="req">*</span></label>
					<div class="col-md-7">
					
					  <div class = "select-style" >
						<select id="class" name="class">
						<?php 
							echo $functions->SelectionBoxClass();
						?>	
						</select>
					  </div>
		
					</div>
				  </div>
				 	  
			
				  <div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:120px" name="submitted" id="submitted">Register Slots</button>
					  <span></span>
					</div>
					<input type="reset" style="margin-left:20px" class="btn btn-default" value="Clear">
				  </div>
		  
				</form>

				</div>
				</div>
			</div>	
			
			<div class="col-md-4 col-xs-12">
				
				<div class="box box-primary">
				<div class="box-header with-border">
              
				<form id='slots2' class="form-horizontal" action='<?php echo $functions->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' role="form">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Assign Slots</legend>
					  	  
				  <div class="form-group">
					<label class="col-md-3 control-label">Slot:<span class="req">*</span></label>
					<div class="col-md-7">
					<div class = "select-style" >
					<select id="slots" name="slots">
						<?php 
							echo $functions->SelectionBoxSlot();
						?>	
					</select>
					</div>
					</div>
				  </div>
				  
				  <div class="form-group">
					<label class="col-md-3 control-label">Parent:<span class="req">*</span></label>
					<div class="col-md-7">
					<div class = "select-style" >
					<select id="parent" name="parent">
						<?php 
							echo $functions->SelectionBoxParent();
						?>	
					</select>
					</div>
					</div>
				  </div>
				
				  <div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:120px" name="submitted2" id="submitted2">Assign Slots</button>
					  <span></span>
					</div>
					<input type="reset" style="margin-left:20px" class="btn btn-default" value="Clear">
				  </div>
		  
				</form>

				</div>
				</div>
			
			</div>
		
        </section>
      </div>
	  
	  <div class="row">
		<section>
			<div class="col-md-4 col-xs-12">
				<div class="box box-primary">
				<div class="box-header with-border">
              
				<form id='deleteslot' class="form-horizontal" action='<?php echo $functions->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' role="form">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Delete All Slots</legend>
		 
				  <div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:110px" name="deleteslot" id="deleteslot">Delete All Slots</button>
					  <span></span>
					</div>
				  </div>
		  
				</form>

				</div>
				</div>
			</div>
			
			<div class="col-md-4 col-xs-12">
				<div class="box box-primary">
				<div class="box-header with-border">
              
				<form id='uploadslots' class="form-horizontal" action='upload-sql.php' method='post' accept-charset='UTF-8' role="form" enctype="multipart/form-data">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Upload</legend>
				
				<div class="form-group">
					<label class="col-md-3 control-label">File:<span class="req">*</span></label>
					<div class="col-md-7">
					  <input type="file" name="slots" />
					</div>
				</div>
				  
				<div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:130px" name="uploadslots" id="uploadslots">Upload to SQL</button>
					  <span></span>
					</div>
				</div>
		  
				</form>

				</div>
				</div>
			</div>
		</section>
	  </div>
	  
    </section>
    
  </div>

<?php
	require("template/footer.php");
?>

<!--
 <script>
  $(function () {
    $("#example1").DataTable();

  });
 </script>
 -->
 
 <script>
  function delete_slot(id){

	$.ajax
	 ({
	  type:'post',
	  url:'delete-ajax.php',
	  data:{
	   slot:id
	  },
	  success:function(response) {
		location.reload();
	  }
	 });
  }
 </script>