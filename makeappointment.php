<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Schedule appointment</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php include('errors.php'); ?>
<!--
	<?php
	var_dump($_POST);
	
	$pid = $_SESSION['pid'];
	
	$provquery = "SELECT *
						FROM ServiceProviders
						WHERE ServiceProviders.ProvID = $_POST[provider]";
	$provresults = mysqli_query($db, $provquery);
	$provrow = mysqli_fetch_assoc($provresults);
	
	
	?>
-->
	<div class="head">
		<h1>MyHealthPortal</h1>
	</div>
	<div class="header">
		<h2><?php echo "Schedule Appointment with Dr. $provrow[Name]"; ?></h2>
	</div>
	<?php if( isset($_POST['provider'])) : ?>
	<form method="post">
		<p>Day of appointment:</p>
		<input type="date" id="appointment_day" name="appointment_day">
		
		<label for="ServiceCode">Choose a service:</label>
		<select name="ServiceCode" id="ServiceCode">
		<?php
			// get specialty from ServiceProviders with ProvID 
			$specialty_text = $provrow['Specialty'];
			
			// get every ServiceCode for specialty from IncludedService
			// get description for every ServiceCode from Services
			$query = 'SELECT IncludedService.ServiceCode, Services.Description
						FROM IncludedService, Services
						WHERE IncludedService.Specialty ="' . $specialty_text . '" ' .
						'AND IncludedService.ServiceCode = Services.ServiceCode;';
			$results = mysqli_query($db, $query);
			
			$row = '';
			// iterate over every ServiceCode 
			while (($row = mysqli_fetch_assoc($results))){
				// echo option with value=ServiceCode, text = Description
				echo "<!-- ";
				var_dump($row);
				echo " -->\n";
				echo '<option value="' . $row['ServiceCode'] . '">' . $row['Description'] . '</option>\n';
			}
		?>
		</select>
		
		
		<input type="hidden" id="provider" name="provider" value="<?php echo$_POST['provider']; ?>" > 
		
		<div class="input-group">
			<button formaction="/MyHealthPortal/choose_appointment_time.php" type="submit" class="btn" name="choose_appointment_time">View available times</button>
		</div>
		<div class="input-group">
			<button formaction="/MyHealthPortal/appointments.php" type="submit" class="btn">Return</button>
		</div>
	</form>
	<?php else : ?>
		<div class="content">
			<p> Error: You must select a service provider. </p>
			<div class="input-group">
						<a href="/MyHealthPortal/appointments.php"><button type="submit" class="btn" name="home">Return</button></a>
			</div>
		</div>
	<?php endif; ?>
</body>
</html>