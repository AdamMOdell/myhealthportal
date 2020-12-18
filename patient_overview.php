<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Patients</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="head">
  	<h1>MyHealthPortal</h1>
  </div>
  <div class="header">
  	<h2>Patient Overview</h2>
  </div>
  <form method="post">
  
  <p><b>Patients</b></p><br>
  
  	<?php include('errors.php');
		
		$ProvID = $_SESSION['ProvID'];
		
		$patient_query = "SELECT Patients.*
			FROM Patients, Membership, IncludedService, Coverages, ServiceProviders
			WHERE ServiceProviders.ProvID = $ProvID
			AND ServiceProviders.Specialty = IncludedService.Specialty
			AND IncludedService.ServiceCode = Coverages.ServiceCode
			AND Coverages.PlanID = Membership.PlanID
			AND Membership.PID = Patients.PID";
		$patient_results = mysqli_query($db, $patient_query);
		
		if (mysqli_num_rows($patient_results) > 0)
		{
			while($patient = mysqli_fetch_assoc($patient_results))
			{
				echo "<p style='text-align:left;'>
						[Patient]
					<span style='float:right;'>
						$patient[Name]
					</span>
				</p>";
				echo "<p style='text-align:left;'>
						[Patient ID]
					<span style='float:right;'>
						<b>$patient[PID]</b>
					</span>
				</p>";
			
				echo "<br>";
			}
		}
		
		$patient_query = "SELECT Patients.*
			FROM Patients, Membership, IncludedService, Coverages, ServiceProviders
			WHERE ServiceProviders.ProvID = $ProvID
			AND ServiceProviders.Specialty = IncludedService.Specialty
			AND IncludedService.ServiceCode = Coverages.ServiceCode
			AND Coverages.PlanID = Membership.PlanID
			AND Membership.PID = Patients.PID";
		$patient_results = mysqli_query($db, $patient_query);
		
		if (mysqli_num_rows($patient_results) > 0)
		{
			echo "<div class=input-group>Choose Patient ID <select name=pid>";
			echo "<option name=select>Select</option>}";
			while($patient = mysqli_fetch_assoc($patient_results))
			{
				echo "<option value=$patient[PID]>$patient[Name] [$patient[PID]]</option>}";
			}
			echo "</select></div>";
		}
		
		echo "<div class=input-group>
		  <label>Subject</label>
		  <input type=text name=subject>
		</div>";
		
		echo "<label>Content</label><br>";
		
		echo "<div class=input-group><textarea name=email rows=4 cols=89></textarea></div>";
		
		echo "
		<div class=input-group>
			<button type=submit class=btn name=patient_email>Email</button>
		</div><br>";
		
	?>

  	<div class="input-group">
  		<button formaction="/MyHealthPortal/index.php" type="submit" class="btn" name="return">Return</button>
  	</div>

  </form>
</body>
</html>