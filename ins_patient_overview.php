<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Patients</title>
  <link rel="stylesheet" type="text/css" href="style.css">
  <style>
td {
  border: 1px solid #ddd;
  padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2;}

tr:hover {background-color: #ddd;}

th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #4CAF50;
  color: white;
}
</style>
</head>
<body>
  <div class="head">
  	<h1>MyHealthPortal</h1>
  </div>
  <div class="header">
  	<h2>Patients</h2>
  </div>
  <form method="post">
  
  	<?php
		echo "<!-- ";
		echo "SESSION = ";
		var_dump($_SESSION);
		echo "\nPOST = ";
		var_dump($_POST);
		echo "\n-->\n";
		
		
		$CompID = $_SESSION['CompID'];
	
		$query = "SELECT PlanID 
					FROM InsPlans 
					WHERE CompID = $CompID;";
		//var_dump($query);
		$results = mysqli_query($db, $query);
		//var_dump($results);
		$planIDs = [];
		while ( $value = mysqli_fetch_assoc($results)){
			//var_dump($value['PlanID']);
			array_push($planIDs, $value['PlanID']);
		}
		//var_dump($PlanIDs);
		
		$company_total_payouts = 0;
		
		for( $m = 0; $m < count($planIDs); $m++)
		{
			$planID = $planIDs[$m];
			$query = "SELECT Patients.*
						FROM Membership, Patients
						WHERE Membership.PlanID = $planID
						AND Membership.PID = Patients.PID;";
			$results = mysqli_query($db, $query);
			$records = [];
			while ( $value = mysqli_fetch_assoc($results)){
				array_push($records, $value);
			}
			
			echo "<!-- ";
			echo "\nrecords = ";
			var_dump($records);
			echo "\n record count = ";
			echo count($records);
			echo "\n-->\n";
		
			echo "<h3>Insurance plan $planID</h3>";
			
			
			for ($n = 0; $n < count($records); $n++)
			{
				$Name = $records[$n]['Name'];
				echo '<p>' . $Name . "</p>\n";
				
				echo "<table style='border: 1px solid black;'>
						<tr>
							<td>Patient ID</td>
							<td>Social Security Number</td>
							<td>Address</td>
							<td>Date of Birth</td>
							<td>Phone number</td>
							<td>Email Address</td>
						</tr>
						<tr>
							<td>" . $records[$n]["PID"] . "</td>
							<td>" . $records[$n]["SSN"] . "</td>
							<td>" . $records[$n]["Address"] . "</td>
							<td>" . $records[$n]["DoB"] . "</td>
							<td>" . $records[$n]["Phone"] . "</td>
							<td>" . $records[$n]["Email"] . "</td>
						</tr>
					</table><br><br>";

				$PID = $records[$n]['PID'];			
			}
			echo "<br><br>";

		}
	?>
	<br></br>
  	<div class="input-group">
  		<button formaction="/MyHealthPortal/index.php"  type="submit" class="btn" name="return">Return</button>
  	</div>

  </form>
</body>
</html>