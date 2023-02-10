<?php
	require("template/header.php");
	
	if(isset($_POST['submitted']))
	{
	   if($functions->AddCourse())
	   {
		  header("Refresh:0");
	   }
	}
	
	if(isset($_POST['deletecourse']))
	{
	   if($functions->DeleteAllCourse())
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
	$total_pages = $functions->GetPageCount($courses);
	
?>

  <div class="content-wrapper">  
    <section class="content">      
      <div class="row">
	  <div class="col-xs-12 col-md-12">
          <div class="box">
            <div class="box-header">
            <table>
              <tr>
			  <td width="200"><h3 class="box-title">Courses</h3></td>
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
				
				<table id="example1" class="table table-hover">
				<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Short Form</th>
					<th>Delete </th>
				</tr>
				</thead>
				<tbody>
					<?php
						echo $functions->GetCourses($offset);
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
              
				<form id='courses' class="form-horizontal" action='<?php echo $functions->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' role="form">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Courses</legend>
		 
				  <div class="form-group">
					<label class="col-md-3 control-label">Name:<span class="req">*</span></label>
					<div class="col-md-7">
					  <input class="form-control" name="name" type="text" required>
					</div>
				  </div>
	  
				  <div class="form-group">
					<label class="col-md-3 control-label">Short Form:<span class="req">*</span></label>
					<div class="col-md-7">
					  <input class="form-control" name="shortform" type="text" required>
					</div>
				  </div>
	  
				  <div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:110px" name="submitted" id="submitted">Register Course</button>
					  <span></span>
					</div>
					<input type="reset" style="margin-left:10px" class="btn btn-default" value="Clear">
				  </div>
		  
				</form>

				</div>
				</div>
			</div>	
			
			<div class="col-md-4 col-xs-12">
				<div class="box box-primary">
				<div class="box-header with-border">
              
				<form id='courses' class="form-horizontal" action='<?php echo $functions->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' role="form">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Delete All Courses</legend>
		 
				  <div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:120px" name="deletecourse" id="deletecourse">Delete All Course</button>
					  <span></span>
					</div>
					
				  </div>
		  
				</form>

				</div>
				</div>
			</div>	
        </section>
      </div>
	  
	  <div class="row">
		
		<div class="col-md-4 col-xs-12">
				<div class="box box-primary">
				<div class="box-header with-border">
              
				<form id='uploadcourse' class="form-horizontal" action='upload-sql.php' method='post' accept-charset='UTF-8' role="form" enctype="multipart/form-data">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Upload</legend>
				
				<div class="form-group">
					<label class="col-md-3 control-label">File:<span class="req">*</span></label>
					<div class="col-md-7">
					  <input type="file" name="course" />
					</div>
				</div>
				  
				<div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:130px" name="uploadcourse" id="uploadcourse">Upload to SQL</button>
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

<!--
 <script>
  $(function () {
    $("#example1").DataTable();

  });
 </script>
 -->
 
 <script>
  function delete_course(id){

	$.ajax
	 ({
	  type:'post',
	  url:'delete-ajax.php',
	  data:{
	   course:id
	  },
	  success:function(response) {
		location.reload();
	  }
	 });
  }
 </script>