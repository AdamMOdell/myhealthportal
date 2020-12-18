<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Schedule appointment</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="head">
		<h1>MyHealthPortal</h1>
	</div>
	<div class="header">
		<h2>Appointments</h2>
	</div>
	<form method="post">
		<?php include('errors.php'); ?>
			<?php
			$pid = $_SESSION['pid'];
			$memberquery = "SELECT * FROM Membership WHERE PID='$pid';";
			$memberresults = mysqli_query($db, $memberquery);
			$row = mysqli_fetch_assoc($memberresults);
			if (mysqli_num_rows($memberresults) > 0)
			{
				$query = "SELECT * FROM InsPlans WHERE PlanID = $row[PlanID];";
				$results = mysqli_query($db, $query);
				$plan = mysqli_fetch_assoc($results);
				
				echo "<p><b>Insurance Service Coverage</b></p><br></br>";
				
				echo "<br><u>Service Coverage</u>" . "<br>";
				
				$servicequery = "SELECT DISTINCT Services.*
								FROM Services, Coverages, InsPlans
								WHERE Services.ServiceCode = Coverages.ServiceCode AND Coverages.PlanID = $row[PlanID]";
				$serviceresults = mysqli_query($db, $servicequery);
				$servicerow = '';
				
				while($servicerow = mysqli_fetch_assoc($serviceresults)) {
					echo "&nbsp;&nbsp;" . $servicerow['Description'] . "<br>\n";
				}
				echo "<br></br>";
				
				echo "<p><b>Service Providers</b></p><br><br></br>\n";
				
				$servicequery = "SELECT DISTINCT Services.*
								FROM Services, Coverages, InsPlans
								WHERE Services.ServiceCode = Coverages.ServiceCode AND Coverages.PlanID = $row[PlanID]";
				$serviceresults = mysqli_query($db, $servicequery);
				$servicerow = '';
				
				while($servicerow = mysqli_fetch_assoc($serviceresults)) {
					echo "[" . $servicerow['Description'] . "]<br>";
					
					$provquery = "SELECT DISTINCT ServiceProviders.*
								FROM ServiceProviders, IncludedService
								WHERE ServiceProviders.Specialty = IncludedService.Specialty AND IncludedService.ServiceCode = $servicerow[ServiceCode]";
					$provresults = mysqli_query($db, $provquery);
					$provrow = '';
					
					while($provrow = mysqli_fetch_assoc($provresults)) {
						//var_dump($provrow);
						echo "<input type='radio' name='provider' id='" . $provrow['ProvID'] . "' value='" . $provrow['ProvID'] . "' >  <label for='" . $provrow['ProvID'] . "'>  &nbsp;&nbsp;<b>Dr. " . $provrow['Name'] . "</b></label><br>\n";
						
					}
					echo "</br>";
				}
			}
			else
			{
				echo "<p>You require an <b>Insurance Plan</b> to set up appointments</p><br></br>";
			}
		?>
		<div class="input-group">
			<button formaction="/MyHealthPortal/makeappointment.php" type="submit" class="btn">Make Appointment</button>
		</div>
		<div class="input-group">
			<button formaction="/MyHealthPortal/index.php" type="submit" class="btn">Return</button>
		</div>
	</form>
</body>
</html>