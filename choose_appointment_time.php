<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Schedule appointment time</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<?php include('errors.php'); ?>
<?php	
	echo "<!--";
	var_dump($_POST);
	echo "-->\n";
	$pid = $_SESSION['pid'];
	$datenum = str_replace("-", "", $_POST['appointment_day']);
	$query = "SELECT Hour
						FROM ServiceRecords
						WHERE ServiceRecords.ProvID = $_POST[provider]
						AND ServiceRecords.Date = $datenum";
	//var_dump($query);
	$results = mysqli_query($db, $query);

	//$num_rows = mysqli_stmt_num_rows($results);
	echo "\n";
	//var_dump($num_rows);
	$occupied_hours = [];
	/*
	for ( $n = 0; $n < $num_rows ; $n++){
		var_dump(mysqli_fetch_assoc($results));
	}
	*/
	while ($row = mysqli_fetch_assoc($results)){
		//var_dump($row);
		array_push($occupied_hours, (int)$row["Hour"]);
	}
	echo "\n";
	//var_dump($occupied_hours);
?>

	<div class="head">
		<h1>MyHealthPortal</h1>
	</div>
	<div class="header">
		<h2><?php echo "TESTING Schedule Appointment with Dr. $provrow[Name]"; ?></h2>
	</div>
	<?php if( isset($_POST['appointment_day']) && $_POST['appointment_day'] != '' ) : ?>
	<form method="post">
		<p>Appointment openings:</p>
		<?php 
			for ($n = 9; $n <= 16; $n++){
				if(!in_array($n, $occupied_hours)){
					echo "<input type='radio' name='hour' id='$n' value='$n'>&nbsp;&nbsp;";
					echo "<label for='$n'>";
					if ($n > 12){
						$pm = $n - 12;
						echo "$pm PM";
					}
					else if ($hour == 12){
						echo "12 PM";
					}
					else {
						echo "$n AM";
					}
					echo "</label><br>\n";
				}
			}
		?>
		<input type="hidden" name="ProvID" id="ProvID" value="<?php echo $_POST['provider']; ?>">
		<input type="hidden" name="appointment_day" id="appointment_day" value="<?php echo $datenum; ?>">
		<input type="hidden" name="servicecode" id="servicecode" value="<?php echo $_POST['ServiceCode']; ?>">
		<div class="input-group">
			<button formaction="/MyHealthPortal/appointment_submitted.php" type="submit" class="btn" name="appointment_submitted">Make Appointment</button>
		</div>
		<div class="input-group">
			<button formaction="/MyHealthPortal/appointments.php" type="submit" class="btn">Return</button>
		</div>
	</form>
	<?php else : ?>
		<div class="content">
			<p> Error: You must select an appointment date. </p>
			<div class="input-group">
						<a href="/MyHealthPortal/appointments.php"><button type="submit" class="btn" name="home">Return</button></a>
			</div>
		</div>
	<?php endif; ?>
</body>
</html>