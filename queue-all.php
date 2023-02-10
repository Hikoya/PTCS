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
		
		table {
		  border-collapse: separate;
		  border-spacing: 50px 0;
		}

		td {
		  padding: 10px 0;
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
						<p style="font-size:2vw">Welcome to Parents-Tutor Meeting Day</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div align='center' class="col-lg-8 col-xs-8 col-md-8 col-sm-8 col-centered">
					<form autocomplete="off">	
						<input autocomplete="false" name="hidden" type="text" style="display:none;">					
						<input class="form-control" style="font-size: 20px;" placeholder="Search all slots (Enter full name of lecturer or venue here.)" type="text" id="search" size="30">	
					</form>
				</div>	
			</div>
			<br>
			<div class="row">
				<div align='center' class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
				<div class='table-responsive'>
					<div align='left' class='table-responsive' style="font-size: 18px; background-color: white; white-space: normal;" id="livesearch"></div>
				</div>
				</div>
			</div>
		</div>

		</body>

		<script src="dist/bower_components/jquery/dist/jquery.min.js"></script>
		<script src="dist/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	
	<script type="text/javascript">
	
	setInterval(function(){ 

		var text = $("#search").val();
		showResult(text);
		
	}, 60000);
	
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
		data: {search_all: str},
		success: function (data) {
			
			document.getElementById("livesearch").innerHTML= data;
			document.getElementById("livesearch").style.border="1px solid #A5ACB2";
	  
		}
	  });
	  
	}
	</script>


</html>