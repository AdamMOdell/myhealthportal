<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Appointments</title>
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
		$ProvID = $_SESSION['ProvID'];
		
		$query = "SELECT DISTINCT *
				FROM ServiceRecords
				WHERE ProvID = $ProvID
				ORDER BY Date DESC";
		$results = mysqli_query($db, $query);
		
		date_default_timezone_set('America/Los_Angeles');
		$date_value = date('Ymd', time());
		
		$past = 0;
		$current = 0;
	?>
	
	<?php 
		if (mysqli_num_rows($results) > 0)
		{
			while($record = mysqli_fetch_assoc($results))
			{
				if ($record['Date'] < $date_value and $past == 0)
				{
					$past = 1;
					
					if ($current == 0)
					echo "<p style='font-size:20px'><b>No Upcoming Appointments</b></p><br></br>";
					echo "<p style='font-size:20px'><b>Past Appointments</b></p><br>";
				}
				else if ($record['Date'] >= $date_value and $current == 0)
				{
					$current = 1;
					echo "<p style='font-size:20px'><b>Upcoming Appointments</b></p><br>";
				}
				
				$service_code = $record['ServiceCode'];
				
				$pid = $record['PID'];
				
				$patient_query = "SELECT DISTINCT *
					FROM Patients
					WHERE PID = $pid
					LIMIT 1";
				$patient_results = mysqli_query($db, $patient_query);
				$patient = mysqli_fetch_assoc($patient_results);
				
				$service_query = "SELECT DISTINCT *
					FROM Services
					WHERE ServiceCode = $service_code
					LIMIT 1";
				$service_results = mysqli_query($db, $service_query);
				$service = mysqli_fetch_assoc($service_results);
				
				if (mysqli_num_rows($service_results) > 0)
				{
					echo "<p style='font-size:20px'><b>$service[Description]</b></p>";
				}
				
				echo "<p style='text-align:left;'>
						[Service ID]
					<span style='float:right;'>
						<b>$record[ServiceID]</b>
					</span>
				</p>";
				
				echo "<p style='text-align:left;'>
						[Service Code]
					<span style='float:right;'>
						$record[ServiceCode]
					</span>
				</p>";
				
				if (mysqli_num_rows($patient_results) > 0)
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
							$patient[PID]
						</span>
					</p>";
				}
				
				$date = $record['Date'];
				
				$year = substr($date, 0, 4);
				$month = substr($date, 4, 2);
				$day = substr($date, 6, 2);
				
				$hour = $record['Hour'];
				$minute = $record['Minute'];
				
				$time = '';
				
				if ($hour > 12)
				{
					$hour -= 12;
					$time = 'PM';
				}
				else
				{
					$time = 'AM';
				}
				
				if ($minute < 10)
				{
					$minute = "0" . $minute;
				}
				
				echo "<p style='text-align:left;'>
					[Date]
					<span style='float:right;'>
						$month/$day/$year $hour:$minute $time
					</span>
				</p>";
				
				if (mysqli_num_rows($service_results) > 0)
				{
					echo "<p style='text-align:left;'>
						[Cost]
						<span style='float:right;'>
							$$service[Cost]
						</span>
					</p>";
				}
				
				$product_query = "SELECT DISTINCT *
					FROM ServiceProducts
					WHERE ServiceCode = $service_code";
				$product_results = mysqli_query($db, $product_query);
				
				if (mysqli_num_rows($product_results) > 0)
				{
					echo "<br>[Products Used]<br><br>";
					while($product = mysqli_fetch_assoc($product_results))
					{
						$prod_query = "SELECT DISTINCT *
							FROM MedicalProducts
							WHERE ProdID = $product[ProdID];";
						$prod_results = mysqli_query($db, $prod_query);
						$prod = mysqli_fetch_assoc($prod_results);
						
						echo "<b>$prod[Description]</b>";
						echo "<p style='text-align:left;'>
								[Product ID]
							<span style='float:right;'>
								$prod[ProdID]
							</span>
						</p>";
						$cost_query = "SELECT DISTINCT *
							FROM ProductCosts
							WHERE ProductCosts.ProdID = $product[ProdID];";
						$cost_results = mysqli_query($db, $cost_query);
						$cost = mysqli_fetch_assoc($cost_results);
						echo "<p style='text-align:left;'>
								[Cost]
							<span style='float:right;'>
								$$cost[Cost]
							</span>
						</p>";
						$totalcost += $cost['Cost'];
					}
				}
				
				$referral_query = "SELECT DISTINCT *
					FROM ReferralProducts
					WHERE ServiceID = $record[ServiceID]";
				$referral_results = mysqli_query($db, $referral_query);
				
				if (mysqli_num_rows($referral_results) > 0)
				{
					echo "<br>[Prescriptions]<br><br>";
					while($referral = mysqli_fetch_assoc($referral_results))
					{
						$prod_query = "SELECT DISTINCT *
							FROM MedicalProducts
							WHERE ProdID = $referral[ProdID];";
						$prod_results = mysqli_query($db, $prod_query);
						$prod = mysqli_fetch_assoc($prod_results);
						
						echo "<b>$prod[Description]</b>";
						
						$cost_query = "SELECT DISTINCT *
							FROM ProductCosts
							WHERE ProductCosts.ProdID = $prod[ProdID];";
						$cost_results = mysqli_query($db, $cost_query);
						$cost = mysqli_fetch_assoc($cost_results);
						echo "<p style='text-align:left;'>
								[Cost]
							<span style='float:right;'>
								$$cost[Cost]
							</span>
						</p>";
						$totalcost += $cost['Cost'];
					}
				}
			echo "<br></br>";
			}
			
			$serv_query = "SELECT DISTINCT *
							FROM ServiceRecords
							WHERE ProvID = $ProvID
							ORDER BY Date DESC";
			$serv_results = mysqli_query($db, $serv_query);
			
			if (mysqli_num_rows($serv_results) > 0)
			{
				echo "<div class=input-group>Choose Service ID <select name=service_id>";
				echo "<option name=select>Select</option>}";
				while($serv_row = mysqli_fetch_assoc($serv_results))
				{
					if ($serv_row['Date'] < $date_value)
					{
						echo "<option value=$serv_row[ServiceID]>$serv_row[ServiceID]</option>}";
					}
				}
				echo "</select></div>";
			}
			
			echo "<label>Diagnosis</label><br>";
			
			echo "<textarea name=Diagnosis rows=4 cols=50></textarea>";
			
			echo "
			<div class=input-group>
				<button type=submit class=btn name=service_diagnosis>Submit Referral</button>
			</div><br>";
		}
	?>
	
	<?php
		if ($current == 0 and $past == 0)
		{
			echo "<b>You have no upcoming appointments</b><br></br>";
		}
		else if ($past == 0)
		{
			echo "<b>You have no past appointments</b><br></br>";
		}
	?>

	</br>
  	<div class="input-group">
  		<button formaction="/MyHealthPortal/index.php" type="submit" class="btn" name="return">Return</button>
  	</div>

  </form>
</body>
</html>