<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<style>
.mb-5{
	margin-bottom:5px;
}
.alert {
    margin-bottom: 0px !important;
}
</style>
<script>
$(document).ready(function(){
	var date_input=$('input[name="sdate"] , input[name="edate"]'); //our date input has the name "date"
	var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
	var options={
		format: 'mm/dd/yyyy',
		container: container,
		todayHighlight: true,
		autoclose: true,
	};
	date_input.datepicker(options);
})
</script>

<link rel="stylesheet" href="//formden.com/static/cdn/bootstrap-iso.css" />
<!-- Bootstrap Date-Picker Plugin -->
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="span12">
				  <h1>Transfer Your Staging Version Files to Production Version:</h1> 
			  <p>
			  <form class="form-inline" action="" method="POST">
			  <label for="sdate">Start Date <span class="label label-info">(Month/Date/Year):</span></label>
			  <input class="form-control" id="sdate" name="sdate" placeholder="MM/DD/YYY" type="text" value="<?php echo $_POST["sdate"]; ?>" required 
style="min-height: 30px;"	autocomplete="off"/>
			  <label for="edate">End Date <span class="label label-info">(Month/Date/Year):</span></label>
			  <input class="form-control" id="edate" name="edate" placeholder="MM/DD/YYY" type="text" value="<?php echo $_POST["edate"]; ?>" required style="min-height: 30px;" autocomplete="off"/>
			 
			  <button name="submit" type="submit" class="btn btn-primary">Sync</button>
			</form>
 
			  </p>
			</div>
		</div>
		<div class="col-md-12">
			<table class="table table-bordered span12">
			  <thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">File Path</th>
					<th scope="col">File Size</th>
					<th scope="col">Last Modify</th>
				</tr>
			  </thead>
			  <tbody id="tbody">
			  <?php 
				if(isset($_POST["submit"]))
				{
					require_once 'AbstractSync.php';
					require_once 'Production.php';
					/*
						IMPORTANT NOTE - 
						$SECRET = Specific string which should need to be same on both servers (Staging and Production)
						$PATH = This is a absolute path of Production server directory where you want to sync with staging server. (/home/######/public_html/production)
						$URL  = 'https://domain-name.com/staging/stagingsyncer.php' (URL path of staging server)
					*/
					$SECRET = '5ecR3t'; //make this long and complicated
					$PATH = '/home/######/public_html/production';
					$URL = 'https://domain-name.com/staging/stagingsyncer.php';
					$client = new \Outlandish\Sync\Client($SECRET, $PATH);
					$client->run($URL); 
				}
				?>
			  </tbody>
			</table>
		</div>
	</div>
</div>