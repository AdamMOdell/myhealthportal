<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Drug Use</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="head">
  	<h1>MyHealthPortal</h1>
  </div>
  <div class="header">
  	<h2>Drug Use</h2>
  </div>
  <form method="post">
  <?php include('errors.php'); ?>
  	<?php
		$PharmID = $_SESSION['PharmID'];
		
		$query = "SELECT DISTINCT *
				FROM ProductCosts
				WHERE PharmID = $PharmID;";
		$results = mysqli_query($db, $query);
		
		$total_payment = 0;
		
		if (mysqli_num_rows($results) > 0)
		{
			while($record = mysqli_fetch_assoc($results))
			{
				$usage = 0;
				
				$ProdID = $record['ProdID'];
			
				$product_query = "SELECT DISTINCT *
					FROM MedicalProducts
					WHERE ProdID = $ProdID";
				$product_results = mysqli_query($db, $product_query);
				$product = mysqli_fetch_assoc($product_results);
				
				$service_query = "SELECT DISTINCT *
					FROM ServiceProducts
					WHERE ProdID = $ProdID";
				$service_results = mysqli_query($db, $service_query);
				$service = mysqli_fetch_assoc($service_results);
				
				$serv_query = "SELECT DISTINCT *
					FROM ServiceRecords
					WHERE ServiceCode = $service[ServiceCode]
					ORDER BY Date";
				$serv_results = mysqli_query($db, $serv_query);
				
				echo "<p style='font-size:20px'><b>$product[Description]</b></p><br>";
				
				if (mysqli_num_rows($serv_results) > 0)
				{
					while($serv = mysqli_fetch_assoc($serv_results))
					{
						$patient_query = "SELECT *
										FROM Patients
										WHERE Patients.PID = $serv[PID]";
						$patient_results = mysqli_query($db, $patient_query);
						$patient = mysqli_fetch_assoc($patient_results);
						
						echo "<p style='text-align:left;'>
								[Service ID]
							<span style='float:right;'>
								$serv[ServiceID]
							</span>
						</p>";
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
						echo "<p style='text-align:left;'>
								[Cost]
							<span style='float:right;'>
								$$record[Cost]
							</span>
						</p>";
						
						$total_payment += $record['Cost'];
						
						$date = $serv['Date'];
				
						$year = substr($date, 0, 4);
						$month = substr($date, 4, 2);
						$day = substr($date, 6, 2);
						
						echo "<p style='text-align:left;'>
								[Date]
							<span style='float:right;'>
								$month/$day/$year
							</span>
						</p><br>";
						$usage += 1;
					}
				}
				
				$transaction_query = "SELECT DISTINCT *
									FROM ProductTransactions
									WHERE ProdID = $ProdID
									ORDER BY Date";
				$transaction_results = mysqli_query($db, $transaction_query);
				
				if (mysqli_num_rows($transaction_results) > 0)
				{
					while($transaction = mysqli_fetch_assoc($transaction_results))
					{
						$tran_query = "SELECT Patients.*
							FROM Patients, ProductTransactions
							WHERE Patients.PID = $transaction[PID]";
						$tran_results = mysqli_query($db, $tran_query);
						$patient = mysqli_fetch_assoc($tran_results);
						
						$ins_query = "SELECT DISTINCT InsPlans.*
								FROM InsPlans, Membership
								WHERE Membership.PID = $patient[PID] AND Membership.PlanID = InsPlans.PlanID";
						$ins_results = mysqli_query($db, $ins_query);
						$insurance = mysqli_fetch_assoc($ins_results);
						
						echo "<p style='text-align:left;'>
								[Transaction ID]
							<span style='float:right;'>
								$transaction[TranID]
							</span>
						</p>";
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
						
						$total_cost = $record[Cost];
						
						if (mysqli_num_rows($ins_results) > 0)
						{
							$discount_query = "SELECT DISTINCT *
											FROM IncludedProducts
											WHERE PlanID = $insurance[PlanID]
											AND ProdID = $ProdID";
							$discount_results = mysqli_query($db, $discount_query);
							$discount = mysqli_fetch_assoc($discount_results);
							
							if (mysqli_num_rows($discount_results) > 0)
							{
								echo "<p style='text-align:left;'>
									[Cost]
								<span style='float:right;'>
									$$record[Cost]
								</span>
								</p>";
								echo "<p style='text-align:left;'>
										[Insurance Discount]
									<span style='float:right;'>
										$discount[Discount]%
									</span>
								</p>";
							
								$discount_percent = (1 - ($discount['Discount']/100));
								
								$total_cost *= $discount_percent;
								
								$total_cost = round($total_cost, 2);
								
								echo "<p style='text-align:left;'>
										[Patient Paid]
									<span style='float:right;'>
										$$total_cost
									</span>
								</p>";
							}
							else
							{
								echo "<p style='text-align:left;'>
										[Cost]
									<span style='float:right;'>
										$$record[Cost]
									</span>
								</p>";
							}
						}
						else
						{
							echo "<p style='text-align:left;'>
									[Cost]
								<span style='float:right;'>
									$$record[Cost]
								</span>
							</p>";
						}
						
						$total_payment += $record['Cost'];
						
						$date = $transaction['Date'];
				
						$year = substr($date, 0, 4);
						$month = substr($date, 4, 2);
						$day = substr($date, 6, 2);
						
						echo "<p style='text-align:left;'>
								[Date]
							<span style='float:right;'>
								$month/$day/$year
							</span>
						</p><br>";
						
						$usage += 1;
					}
				}
				
				if ($usage == 0)
				{
					echo "<p>No $product[Description] has been used.</p><br>";
				}
			}
			
			echo "<br><p style='text-align:left;'>
					<b style='font-size:20px'>Total Payments</b>
				<span style='float:right;'>
					$$total_payment
				</span>
			</p><br>";
		}
		else
		{
			echo "<p>No drugs have been used.</p><br>";
		}
	?>

	<br></br>

	<div class="input-group">
  		<button onclick="window.open('/MyHealthPortal/drug_use_pdf.php')" type="submit" class="btn" name="pdf">Print</button>
  	</div>

  	<div class="input-group">
  		<button formaction="/MyHealthPortal/index.php" type="submit" class="btn" name="return">Return</button>
  	</div>

  </form>
</body>
</html>