<?php
	require("template/header.php");
	
	if(isset($_POST['submitted']))
	{
	   if($functions->AddClass())
	   {
		  header("Refresh:0");
	   }
	}
	
	if(isset($_POST['submitted2']))
	{
	   if($functions->AddMultipleClass())
	   {
		  header("Refresh:0");
	   }
	}
	
	if(isset($_POST['deleteclass']))
	{
	   if($functions->DeleteAllClass())
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
	$total_pages = $functions->GetPageCount($classes);
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
			  <td width="200"><h3 class="box-title">Classes</h3></td>
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
					<th>Names</th>
					<th>Delete</th>
				</tr>
				</thead>
				<tbody>
					<?php
						echo $functions->GetClasses($offset);
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
              
				<form id='classes' class="form-horizontal" action='<?php echo $functions->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' role="form">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Classes</legend>
		 
				  <div class="form-group">
					<label class="col-md-3 control-label">Name:<span class="req">*</span></label>
					<div class="col-md-7">
					  <input class="form-control" name="name" type="text" required>
					</div>
				  </div>
	  
				  <div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:110px" name="submitted" id="submitted">Register Class</button>
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
              
				<form id='classes' class="form-horizontal" action='<?php echo $functions->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' role="form">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Multiple Classes</legend>
		 
				  <div class="form-group">
					<label class="col-md-3 control-label">Level:<span class="req">*</span></label>
					<div class="col-md-7">
					<div class = "select-style" >
					  <select id="level" name="level">
						<?php 
							$content = '<option value=""></option>';
							
							$content .= '<option value="1A">1A</option>';
							$content .= '<option value="1B">1B</option>';
							$content .= '<option value="2A">2A</option>';
							$content .= '<option value="2B">2B</option>';
							$content .= '<option value="3A">3A</option>';
							$content .= '<option value="3B">3B</option>';
									
							echo $content;
						?>	
						</select>
					</div>
					</div>
				  </div>
				  
				  <div class="form-group">
					<label class="col-md-3 control-label">Start:<span class="req">*</span></label>
					<div class="col-md-7">
					<div class = "select-style" >
					  <select id="start" name="start">
						<?php 
							$content = '<option value=""></option>';
							
							$range = range(01, 99);
							foreach($range as $level){
								$content .= '<option value="'.$level.'">'.$level.'</option>';
							}
							
							echo $content;
						?>	
						</select>
					</div>
					</div>
				  </div>
				  
				  <div class="form-group">
					<label class="col-md-3 control-label">End:<span class="req">*</span></label>
					<div class="col-md-7">
					<div class = "select-style" >
					  <select id="end" name="end">
						<?php 
							$content = '<option value=""></option>';
							
							$range = range(01, 99);
							foreach($range as $level){
								$content .= '<option value="'.$level.'">'.$level.'</option>';
							}
							
							echo $content;
						?>	
						</select>
					</div>
					</div>
				  </div>
	  
				  <div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:130px" name="submitted2" id="submitted2">Register Multiple</button>
					  <span></span>
					</div>
					<input type="reset" style="margin-left:30px" class="btn btn-default" value="Clear">
				  </div>
		  
				</form>

				</div>
				</div>
			</div>	
			
			<div class="col-md-4 col-xs-12">
				<div class="box box-primary">
				<div class="box-header with-border">
              
				<form id='classes' class="form-horizontal" action='<?php echo $functions->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' role="form">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Delete All Classes</legend>
		 
				  <div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:110px" name="deleteclass" id="deleteclass">Delete All Class</button>
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
              
				<form id='uploadclass' class="form-horizontal" action='upload-sql.php' method='post' accept-charset='UTF-8' role="form" enctype="multipart/form-data">
				<div><span class='error'><?php echo $functions->GetErrorMessage(); ?></span></div>
		  
				<legend>Upload</legend>
				
				<div class="form-group">
					<label class="col-md-3 control-label">File:<span class="req">*</span></label>
					<div class="col-md-7">
					  <input type="file" name="class" />
					</div>
				</div>
				  
				<div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4 col-xs-4">
					  <button type="submit" class="btn btn-primary" style="width:130px" name="uploadclass" id="uploadclass">Upload to SQL</button>
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
  function delete_class(id){

	$.ajax
	 ({
	  type:'post',
	  url:'delete-ajax.php',
	  data:{
	   classes:id
	  },
	  success:function(response) {
		location.reload();
	  }
	 });
  }
 </script>