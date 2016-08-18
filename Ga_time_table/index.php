<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kwara State University|Time Table Generator</title>
<link href='css/bootstrap.min.css'rel="stylesheet">
</head>
<body>

<div class="container row">
	<div class="col-xs-2">
	</div>
	<div class="col-xs-3">
		<img src="imgs/kwasu_logo.jpg" alt="kwasu logo" height="200"/>
	</div>
	<div class="col-xs-6">
		<br/>	<br/> <br/>
		<h1>Kwara State University</h1>
		<h3>Time Table Generator</h3>
		<hr/>

	<br/><br/>
		
	<h3> Please Provide a .csv configuration file </h3> 	

	<form id="form1" name="form1" enctype="multipart/form-data" method="post" action="tTable_gen.php">

			<div class="form-group">
				<label>Semester</label>
				<select class="form-control" name="semester">
                    <option>Harmattan Semester</option>
                    <option>Rain Semester</option>                                                
                </select>
            </div>

			<div class="form-group">
				<label>Schedule Type</label>
                <select class="form-control" name="s_type">
                    <option>Class Schedule</option>
                    <option>Examination Schedule</option>                                                
                </select>
			</div>		 
			<div class="form-group">
				<label>Schedule Settings (.Csv File)</label>
		 		<input type="file" name="userfile" id="fileField" class="form-control"/>
		 	</div>
		 	<div class="form-group">
		   		<input name="generate" type="submit" value="Generate Time Table" class="btn btn-success"/>
		   	</div>
	</form>

	</div>
	<div class="col-xs-2">
	<hr/><hr/>
	</div>

</div>
<br/><br/><br/><br/>
<div class="row">
	<div class="col-xs-2">
	</div>
	
	<div class="col-xs-8">	
	
	</div>
</div>

</body>
</html>
