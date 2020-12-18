<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Prescriptions</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="head">
  	<h1>MyHealthPortal</h1>
  </div>
  <div class="header">
  	<h2>Prescriptions</h2>
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
		$date = date('m/d/Y h:i:s a', time());
		
		if (mysqli_num_rows($results) > 0)
		{
			while($record = mysqli_fetch_assoc($results))
			{
				if ($record['Date'] < $date_value)
				{
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
							[ServiceID]
						<span style='float:right;'>
							<b>$record[ServiceID]</b>
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
					
					if ($hour > 6)
					{
						$time = 'AM';
					}
					else
					{
						$time = 'PM';
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
					
					echo "<br>";
				}
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
			
			$prod_query = "SELECT *
					FROM MedicalProducts";
			$prod_results = mysqli_query($db, $prod_query);
			
			if (mysqli_num_rows($prod_results) > 0)
			{
				echo "<div class=input-group>Pharmaceutical Prescription <select name=prod_id>";
				echo "<option name=select>Select</option>";
				while($product = mysqli_fetch_assoc($prod_results))
				{
					echo "<option value=$product[ProdID]>$product[Description] [$product[ProdID]]</option>";
				}
				echo "</select></div>";
			}
			
			echo "
			<div class=input-group>
				<button type=submit class=btn name=drug_referral>Submit Referral</button>
			</div><br>";
			
			echo "<br></br>";
		}
	?>
	
	</br>
  	<div class="input-group">
  		<button formaction="/MyHealthPortal/index.php"  type="submit" class="btn" name="return">Return</button>
  	</div>

  </form>
</body>
</html>