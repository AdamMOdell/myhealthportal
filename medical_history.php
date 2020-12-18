<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Medical History</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="head">
  	<h1>MyHealthPortal</h1>
  </div>
  <div class="header">
  	<h2>Medical History</h2>
  </div>
  <form method="post">
  <?php include('errors.php'); ?>
  	<?php
		$PID = $_SESSION['pid'];
		
		$query = "SELECT DISTINCT *
				FROM ServiceRecords
				WHERE PID = $PID
				ORDER BY Date";
		$results = mysqli_query($db, $query);
	?>
	
	<?php if (mysqli_num_rows($results) > 0) : ?>
		<?php while($record = mysqli_fetch_assoc($results)) : ?>
		
			<?php 
				$service_code = $record['ServiceCode'];
				
				$provid = $record['ProvID'];
			
				$provider_query = "SELECT DISTINCT *
					FROM ServiceProviders
					WHERE ProvID = $provid
					LIMIT 1";
				$provider_results = mysqli_query($db, $provider_query);
				$provider = mysqli_fetch_assoc($provider_results);
				
				$service_query = "SELECT DISTINCT *
					FROM Services
					WHERE ServiceCode = $service_code
					LIMIT 1";
				$service_results = mysqli_query($db, $service_query);
				$service = mysqli_fetch_assoc($service_results);
			?>
			
			<?php if (mysqli_num_rows($service_results) > 0) : ?>
				<?php echo "<p style='font-size:20px'><b>$service[Description]</b></p>";
				?>
			<?php endif ?>
			
			<?php echo "<p style='text-align:left;'>
						[ServiceID]
					<span style='float:right;'>
						$record[ServiceID]
					</span>
				</p>";
			?>
			
			<?php if (mysqli_num_rows($provider_results) > 0) : ?>
				<?php echo "<p style='text-align:left;'>
						[$provider[Specialty]]
					<span style='float:right;'>
						Dr. $provider[Name]
					</span>
				</p>";
				?>
			<?php endif ?>
			
			<?php 
				$date = $record['Date'];
				
				$year = substr($date, 0, 4);
				$month = substr($date, 4, 2);
				$day = substr($date, 6, 2);
			?>
			
			<?php echo "<p style='text-align:left;'>
					[Date]
					<span style='float:right;'>
						$month/$day/$year
					</span>
				</p>";
			?>
			
			<?php if (mysqli_num_rows($service_results) > 0) : ?>
				<?php echo "<p style='text-align:left;'>
					[Cost]
					<span style='float:right;'>
						$$service[Cost]
					</span>
				</p>";
			?>
			<?php endif ?>
			
			<?php
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
				
				$diagnosis = '';
				
				//Query diagnosis
				$diagnosis_query = "SELECT DISTINCT *
					FROM Diagnosis
					WHERE ServiceID = $record[ServiceID]";
				$diagnosis_results = mysqli_query($db, $diagnosis_query);
				
				if (mysqli_num_rows($diagnosis_results) > 0)
				{
					$diagnosis = mysqli_fetch_assoc($diagnosis_results)['Description'];
				}
				else
				{
					$diagnosis = "You have not been diagnosed for this procedure yet.";
				}
				
				echo "<br><p><b>[Diagnosis]</b></p>";
				
				echo "<p>$diagnosis</p>";
				
			?>
			
			<br></br>
			
		<?php endwhile ?>
	<?php else: ?>
	
		<p>You have currently not paid for any services.</p><br>
	
	<?php endif ?>
		
	<?php
		$tran_query = "SELECT DISTINCT *
				FROM ProductTransactions
				WHERE PID = $PID
				ORDER BY Date";
		$tran_results = mysqli_query($db, $tran_query);
	?>
	<?php if (mysqli_num_rows($tran_results) > 0) : ?>
		<?php while($transaction = mysqli_fetch_assoc($tran_results)) : ?>
			<?php
				$product_query = "SELECT DISTINCT *
					FROM MedicalProducts
					WHERE ProdID = $transaction[ProdID]
					LIMIT 1";
				$product_results = mysqli_query($db, $product_query);
				$product = mysqli_fetch_assoc($product_results);
			
				echo "<p style='font-size:20px'><b>$product[Description]</b></p>";
				$pharm_query = "SELECT Pharmacies.*
					FROM Pharmacies, ProductCosts
					WHERE ProductCosts.ProdID = $product[ProdID]
					AND Pharmacies.PharmID = ProductCosts.PharmID;";
				$pharm_results = mysqli_query($db, $pharm_query);
				$pharmacy = mysqli_fetch_assoc($pharm_results);
				echo "<p style='text-align:left;'>
						[Pharmacy]
					<span style='float:right;'>
						$pharmacy[Name]
					</span>
				</p>";
				echo "<p style='text-align:left;'>
						[Pharmacy ID]
					<span style='float:right;'>
						$pharmacy[PharmID]
					</span>
				</p>";
				echo "<p style='text-align:left;'>
						[Address]
					<span style='float:right;'>
						$pharmacy[Address]
					</span>
				</p>";
				
				$date = $transaction['Date'];
				
				$year = substr($date, 0, 4);
				$month = substr($date, 4, 2);
				$day = substr($date, 6, 2);
			
				echo "<p style='text-align:left;'>
						[Date]
					<span style='float:right;'>
						$month/$day/$year
					</span>
				</p>";
				echo "<p style='text-align:left;'>
						[Product ID]
					<span style='float:right;'>
						$product[ProdID]
					</span>
				</p>";
				$cost_query = "SELECT *
					FROM ProductCosts
					WHERE ProductCosts.ProdID = $product[ProdID];";
				$cost_results = mysqli_query($db, $cost_query);
				$cost = mysqli_fetch_assoc($cost_results);
				echo "<p style='text-align:left;'>
						[Cost]
					<span style='float:right;'>
						$$cost[Cost]
					</span>
				</p><br>";
			?>
		<?php endwhile ?>
	<?php endif ?>
	
	<br></br>

	<div class="input-group">
  		<button onclick="window.open('/MyHealthPortal/medical_history_pdf.php')" type="submit" class="btn" name="pdf">Print</button>
  	</div>
  	<div class="input-group">
  		<button formaction="/MyHealthPortal/index.php" type="submit" class="btn" name="return">Return</button>
  	</div>

  </form>
</body>
</html>