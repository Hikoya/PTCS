<?php
	require("template/header.php");
	
	if(isset($_POST['submitted']))
	{
	   if($functions->AddParent())
	   {
		  header("Refresh:0");
	   }
	}
	
	if(isset($_POST['deleteparent']))
	{
	   if($functions->DeleteAllParent())
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
	$total_pages = $functions->GetPageCount($parents);
	
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
			  <td width="200"><h3 class="box-title">Parents</h3></td>
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
					<th>Admin No</th>
					<th>Course</th>
					<th>Class</th>
					<th>Name</th>
					<th>Slots</th>
					<th>Assigned Lecturer</th>
					<th>OS</th>
					<th>OS Lecturer</th>
					<th>Delete</th>
				</tr>
				</thead>
				<tbody>
					<?php
						echo $functions->GetParents($offset);
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
              
				<form id='parents' class="form-horizontal" action='<?php echo $functions->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' role="form">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Parents</legend>
						  

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
					<label class="col-md-3 control-label">Admission Number:<span class="req">*</span></label>
					<div class="col-md-7">
						<input class="form-control" name="adminno" type="text" required>
					</div>
				  </div>
					 
				   <div class="form-group">
					<label class="col-md-3 control-label">Name:<span class="req">*</span></label>
					<div class="col-md-7">
					  <input class="form-control" name="name" type="text" required>
					</div>
				  </div>
				  
				  <div class="form-group">
					<label class="col-md-3 control-label">OS:<span class="req">*</span></label>
					<div class="col-md-7">
			
					   <input type="checkbox" name="os">
	
					</div>
				  </div>
				  
				   <div class="form-group">
					<label class="col-md-3 control-label">OS Lecturer:</label>
					<div class="col-md-7">
			        <div class = "select-style" >
					<select id="os_lecturer" name="os_lecturer">
						<?php 
							echo $functions->SelectionBoxLecturer();
						?>	
					</select>
					</div>
					</div>
				  </div>
				  
				  
				  <div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:120px" name="submitted" id="submitted">Register Student</button>
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
              
				<form id='parents' class="form-horizontal" action='<?php echo $functions->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' role="form">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Delete All Student</legend>
						  
				  <div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:130px" name="deleteparent" id="deleteparent">Delete All Student</button>
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
              
				<form id='uploadparent' class="form-horizontal" action='upload-sql.php' method='post' accept-charset='UTF-8' role="form" enctype="multipart/form-data">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Upload</legend>
				
				<div class="form-group">
					<label class="col-md-3 control-label">File:<span class="req">*</span></label>
					<div class="col-md-7">
					  <input type="file" name="parent" />
					</div>
				</div>
				  
				<div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:130px" name="uploadparent" id="uploadparent">Upload to SQL</button>
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
  function delete_parent(id){

	$.ajax
	 ({
	  type:'post',
	  url:'delete-ajax.php',
	  data:{
	   parent:id
	  },
	  success:function(response) {
		location.reload();
	  }
	 });
  }
 </script>