<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Billings</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="head">
  	<h1>MyHealthPortal</h1>
  </div>
  <div class="header">
  	<h2>Billings</h2>
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
		
		$total_payment = 0;
		
		if (mysqli_num_rows($results) > 0)
		{
			while($record = mysqli_fetch_assoc($results))
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
						[Service ID]
					<span style='float:right;'>
						$record[ServiceID]
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
							<b>$$service[Cost]</b>
						</span>
					</p>";
					
					$total_payment += $service['Cost'];
				}
				echo "<br></br>";
			}
		}
		
		echo "<p style='text-align:left;'>
				<b style='font-size:20px'>Total Payments</b>
			<span style='float:right;'>
				<b>$$total_payment</b>
			</span>
		</p>";
	?>
	</br>
	<div class="input-group">
  		<button onclick="window.open('/MyHealthPortal/doctor_billings_pdf.php')" type="submit" class="btn" name="pdf">Print</button>
  	</div>
  	<div class="input-group">
  		<button formaction="/MyHealthPortal/index.php" type="submit" class="btn" name="return">Return</button>
  	</div>

  </form>
</body>
</html>