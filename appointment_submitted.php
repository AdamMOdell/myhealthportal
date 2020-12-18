<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Appointment Made</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<?php include('errors.php'); ?>
<?php			
	$pid = $_SESSION['pid'];
	echo "<!-- ";
	var_dump($_POST);
	echo "-->\n";
	
	if(isset($_POST['hour']) && $_POST['hour'] > 0){
		$query = "INSERT INTO ServiceRecords(PID, ProvID, ServiceCode, Date, Hour, Minute)
					VALUES (" 
					. $pid . ", " 
					. $_POST['ProvID'] . ", "
					. $_POST['servicecode'] . ", "
					. $_POST['appointment_day'] . ", "
					. $_POST['hour'] . ", 0);";
		//var_dump($query);
		$results = mysqli_query($db, $query);
		
		// get provider name
		$query = "SELECT Name, Address
					FROM ServiceProviders
					WHERE ProvID = " . $_POST['ProvID'] . ";";
		$result = mysqli_query($db, $query);
		$row = mysqli_fetch_assoc($result);
		$provaddress = $row['Address'];
		$provname = $row['Name'];
		
		$serv_query = "SELECT *
					FROM ServiceProducts
					WHERE ServiceCode = " . $_POST['servicecode'] . ";";
		$serv_results = mysqli_query($db, $serv_query);
		
		if (mysqli_num_rows($serv_results) > 0)
		{
			while($serv = mysqli_fetch_assoc($serv_results))
			{
				$ProdID = $serv['ProdID'];
				
				$update_query = "UPDATE ProductCosts
						SET Amount = Amount - 1
						WHERE ProdID = $ProdID;";
				mysqli_query($db, $update_query);
			}
		}
	}
?>

	<div class="head">
		<h1>MyHealthPortal</h1>
	</div>
	<div class="header">
		<h2><?php echo "Appointment made with Dr. $provname"; ?></h2>
	</div>
	<?php if(isset($_POST['hour']) && $_POST['hour'] > 0) : ?>
		<div class="content">
			<p><?php 
				echo "You have scheduled an apppointment with Dr. $provname on ";
				$date = $_POST['appointment_day'];
				echo substr($date, 0, 4) . "-" . substr($date, 4, 2) . "-" . substr($date, 6, 2);
				echo " at ";
				$hour = $_POST['hour'];
				if ($hour > 12)
					echo $hour - 12 . " PM.";
				else if ($hour == 12)
					echo "12 PM.";
				else 
					echo $hour . " AM.";
			?></p>
			<p><?php
				echo "This service provider may be reached at $provaddress";
			?></p>
			<!--<p><a href="/MyHealthPortal/index.php"> Home </a></p> <button type="submit" class="btn" name="reg_user">-->
			<div class="input-group">
					<a href="/MyHealthPortal/index.php"><button type="submit" class="btn" name="home">Home</button></a>
			</div>
		</div>
	<?php else : ?>
		<div class="content">
			<p> Error: You must select an appointment time.</p>
			<div class="input-group">
			<a href="/MyHealthPortal/appointments.php"><button type="submit" class="btn" name="home">Return</button></a>
			</div>
		</div>
	<?php endif; ?>
</body>
</html>