<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Billing</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="head">
  	<h1>MyHealthPortal</h1>
  </div>
  <div class="header">
  	<h2>Billing</h2>
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
			$query = "SELECT Membership.PID, Name
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
		
			echo "<h1>Insurance plan $planID</h1>";
			
			$totalplanpayout = 0;
			
			for ($n = 0; $n < count($records); $n++)
			{
				$totalpatientpayout = 0;
				$Name = $records[$n]['Name'];
				echo '<h2 style="text-align:center">' . $Name . "</h2>\n";
				$PID = $records[$n]['PID'];
				
				//Select All Service Records Patient has
				$query = "SELECT DISTINCT *
						FROM ServiceRecords
						WHERE PID = $PID
						ORDER BY Date";
				$results = mysqli_query($db, $query);
				
				//Select Insurance Plan that Patient has
				$ins_query = "SELECT DISTINCT InsPlans.*
						FROM InsPlans, Membership
						WHERE Membership.PID = $PID AND Membership.PlanID = InsPlans.PlanID";
				$ins_results = mysqli_query($db, $ins_query);
				$insurance = mysqli_fetch_assoc($ins_results);
				
				//Select Insurance Company that Insurance Plan has
				$comp_query = "SELECT DISTINCT InsCompany.*
						FROM InsCompany, InsPlans
						WHERE InsPlans.PlanID = $insurance[PlanID] AND InsCompany.CompID = InsPlans.CompID";
				$comp_results = mysqli_query($db, $comp_query);
				$company = mysqli_fetch_assoc($comp_results);
				
				$totaldeductible = $insurance['AnnualDeductible'];
			
				//SERVICE RECORDS QUERY
				if (mysqli_num_rows($results) > 0)
				{
					//Go through all Service Records Patient has
					while($record = mysqli_fetch_assoc($results))
					{
						//This will be reset everytime, adding up individual service records
						$totalcost = 0;
						
						//Retrieve service code
						$service_code = $record['ServiceCode'];
						
						//Find disinct service
						$service_query = "SELECT DISTINCT *
							FROM Services
							WHERE ServiceCode = $service_code
							LIMIT 1";
						$service_results = mysqli_query($db, $service_query);
						$service = mysqli_fetch_assoc($service_results);
						
						//Print out service
						if (mysqli_num_rows($service_results) > 0)
						{
							echo "<p style='font-size:16px'><b>$service[Description]</b></p>";
						}
						
						//Print out Service ID
						echo "<p style='text-align:left;'>
								[Service ID]
							<span style='float:right;'>
								$record[ServiceID]
							</span>
						</p>";
						
						//Explode date into year, month, day, then print it
						$date = $record['Date'];
						
						$year = substr($date, 0, 4);
						$month = substr($date, 4, 2);
						$day = substr($date, 6, 2);
						
						echo "<p style='text-align:left;'>
								[Date]
							<span style='float:right;'>
								$month/$day/$year
							</span>
						</p>";
						
						//Print out Service Cost
						if (mysqli_num_rows($service_results) > 0)
						{
							echo "<p style='text-align:left;'>
										[Service Cost]
									<span style='float:right;'>
										$$service[Cost]
									</span>
								</p>";
						}
						
						//Query for Products included in the Service (service code)
						$product_query = "SELECT DISTINCT *
							FROM ServiceProducts
							WHERE ServiceCode = $service_code";
						$product_results = mysqli_query($db, $product_query);
						
						//Add these products to the billing
						if (mysqli_num_rows($product_results) > 0)
						{
							echo "<br>Products Used<br><br>"; //products used in service
							while($product = mysqli_fetch_assoc($product_results))
							{
								//Select product using its ProdID
								$prod_query = "SELECT DISTINCT *
									FROM MedicalProducts
									WHERE ProdID = $product[ProdID];";
								$prod_results = mysqli_query($db, $prod_query);
								$prod = mysqli_fetch_assoc($prod_results);
								
								//Print Product
								echo "<b>$prod[Description]</b>";
								
								//Print Product Cost and which pharmacy sells it
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
								
								//This cost is added to the $totalcost
								$totalcost += $cost['Cost'];
							}
						}
						
						//This queries any prescriptions given by the service provider
						$referral_query = "SELECT DISTINCT *
							FROM ReferralProducts
							WHERE ServiceID = $record[ServiceID]";
						$referral_results = mysqli_query($db, $referral_query);
						
						//If there are prescriptions, then run this
						if (mysqli_num_rows($referral_results) > 0)
						{
							echo "<br>[Prescriptions]<br><br>";
							//Query out each prescription
							while($referral = mysqli_fetch_assoc($referral_results))
							{
								//Select actual product
								$prod_query = "SELECT DISTINCT *
									FROM MedicalProducts
									WHERE ProdID = $referral[ProdID];";
								$prod_results = mysqli_query($db, $prod_query);
								$prod = mysqli_fetch_assoc($prod_results);
								
								//Print product
								echo "<b>$prod[Description]</b>";
								
								//Query cost of product and print it
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
								
								//This cost is added to the $totalcost
								$totalcost += $cost['Cost'];
							}
						}
						
						//This service cost is finally added to the $totalcost
						$totalcost += $service['Cost'];
						
						//Print total cost (original cost) before any insurance if available
						echo "<br>";
						echo "<p style='text-align:left;'>
									[Original Cost]
								<span style='float:right;'>
									$$totalcost
								</span>
						</p>";
						
						//If the patient has insurance, run this
						if (mysqli_num_rows($ins_results) > 0)
						{
							//Print Annual Deductible of insurance plan patient has
							echo "<p style='text-align:left;'>
								[Current Deductible]
								<span style='float:right;'>
									$$totaldeductible
								</span>
							</p>";
							
							//Perform calculations to determine whether the cost is brought
							//down because of the deductible, or if it stays the same.
							$tempdeductible = $totaldeductible;
							
							//Either way, the totalcost is minused from the deductible.
							//This will eventually lead the deductible to be 0, where the patient
							//no longer has to pay
							$totaldeductible -= $totalcost;
							if ($totaldeductible < 0)
							{
								echo "<p style='text-align:left;'>
										[Insurance payout]
									<span style='float:right;'>
										<b>$" . -$totaldeductible . "</b>
									</span>
									</p>";
								$totalpatientpayout -= $totaldeductible; //totaldeductible is negative
								$totaldeductible = 0;
							}
							else
							{
								echo "<p style='text-align:left;'>
										[Insurance payout]
									<span style='float:right;'>
										<b>$0</b>
									</span>
									</p>";
							}
							
							if ($totalcost > $tempdeductible)
							{
								$totalcost = $tempdeductible;
							}
						}
							
						//Print actual cost (may be the same as original cost if no insurance was given
						echo "<p style='text-align:left;'>
									[Total Cost to Patient]
								<span style='float:right;'>
									<b>$$totalcost</b>
								</span>
						</p>";
						
						//Format date so date_create() works properly and time can be added 
						$date_format = $year . "-" . $month . "-" . $day;
						
						$due = date_create("$date_format");
						//Time is added to the date of service, meaning its payment due date
						date_add($due, date_interval_create_from_date_string('1 month'));
						$due_date = date_format($due,"m/d/Y");
						
						//Print due date for payment
						echo "<p style='text-align:left;'>
									[Due Date]
								<span style='float:right;'>
									$due_date
								</span>
						</p><br>";
					}
				}
				else
				{
					echo "<p>You have currently not paid for any services.</p>";
				}
				echo "<br>";
				
				//Query product transaction 
				$tran_query = "SELECT DISTINCT *
						FROM ProductTransactions
						WHERE PID = $PID
						ORDER BY Date";
				$tran_results = mysqli_query($db, $tran_query);
				
				//If the patient has any transactions, it will show here
				if (mysqli_num_rows($tran_results) > 0)
				{
					//Query through all product transactions
					while($transaction = mysqli_fetch_assoc($tran_results))
					{
						//Select actual product
						$product_query = "SELECT DISTINCT *
							FROM MedicalProducts
							WHERE ProdID = $transaction[ProdID]
							LIMIT 1";
						$product_results = mysqli_query($db, $product_query);
						$product = mysqli_fetch_assoc($product_results);
					
						//Print product
						echo "<p style='font-size:20px'><b>$product[Description]</b></p>";
						
						//Query pharmacy 
						$pharm_query = "SELECT Pharmacies.*
							FROM Pharmacies, ProductCosts
							WHERE ProductCosts.ProdID = $product[ProdID]
							AND Pharmacies.PharmID = ProductCosts.PharmID;";
						$pharm_results = mysqli_query($db, $pharm_query);
						$pharmacy = mysqli_fetch_assoc($pharm_results);
						
						//Print pharmacy details
						echo "$pharmacy[Name]<br>";
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
						
						//Same date calculation as before
						$date = $transaction['Date'];
						
						$year = substr($date, 0, 4);
						$month = substr($date, 4, 2);
						$day = substr($date, 6, 2);
					
						//Print date
						echo "<p style='text-align:left;'>
								[Date]
							<span style='float:right;'>
								$month/$day/$year
							</span>
						</p>";
						
						//Print Product ID
						echo "<p style='text-align:left;'>
								[Product ID]
							<span style='float:right;'>
								$product[ProdID]
							</span>
						</p><br>";
						
						//Query cost
						$cost_query = "SELECT *
							FROM ProductCosts
							WHERE ProductCosts.ProdID = $product[ProdID];";
						$cost_results = mysqli_query($db, $cost_query);
						$cost = mysqli_fetch_assoc($cost_results);
						
						//Print original cost (if there is a discount, we will add after)
						echo "<p style='text-align:left;'>
								[Original Cost]
							<span style='float:right;'>
								$$cost[Cost]
							</span>
						</p>";
						
						//This, becomes the new totalcost of this single product
						$total_cost = $cost['Cost'];
						
						//If the patient has insurance, we continue
						if (mysqli_num_rows($ins_results) > 0)
						{
							//Query discount
							$discount_query = "SELECT DISTINCT *
											FROM IncludedProducts
											WHERE PlanID = $insurance[PlanID]
											AND ProdID = $product[ProdID]";
							$discount_results = mysqli_query($db, $discount_query);
							$discount = mysqli_fetch_assoc($discount_results);
							
							//If there exists a discount for the given product, we add its discount
							if (mysqli_num_rows($discount_results) > 0)
							{
								//Print discount
								echo "<p style='text-align:left;'>
										[Discount]
									<span style='float:right;'>
										$discount[Discount]%
									</span>
								</p>";
							}
							echo "</br>";
							
							//Discount calculations (discount is a whole number from 0-50)
							//This is why we divide it by 100
							//We simply multiply the total cost by (1 - discount%)
							//Since its discount% off
							$discount_percent = (1 - ($discount['Discount']/100));
							
							$total_cost *= $discount_percent;
							
							$total_cost = round($total_cost, 2);
						}
						
						//Print total cost (after discount is any)
						echo "<p style='text-align:left;'>
								[Total Cost]
							<span style='float:right;'>
								<b>$$total_cost</b>
							</span>
						</p>";
						
						//Calculate due date
						$date_format = $year . "-" . $month . "-" . $day;
						
						$due = date_create("$date_format");
						date_add($due, date_interval_create_from_date_string('1 month'));
						$due_date = date_format($due,"m/d/Y");
						
						echo "<p style='text-align:left;'>
								[Due Date]
							<span style='float:right;'>
								$due_date
							</span>
						</p><br>";
					}
				}
				echo "<br>";
			
				//Query Insurance
				//This part of the program displays the patients insurance payment
				if (mysqli_num_rows($ins_results) > 0)
				{
					echo "<p style='font-size:20px'><b>Insurance</b></p>";
					//Display Insurance Company
					echo $company['Name'] . "<br></br>";
					
					//Print Annual Premium
					echo "<p style='text-align:left;'>
								[Annual Premium]
							<span style='float:right;'>
								$$insurance[AnnualPremium]
							</span>
					</p>";
					
					//Print Plan Contribution
					echo "<p style='text-align:left;'>
								[Employer Contribution]
							<span style='float:right;'>
								$$insurance[PlanContribution]
							</span>
					</p><br>";
					
					//Calculate new insurance based on the plan contribution
					$totalinsurance = $insurance['AnnualPremium'] - $insurance['PlanContribution'];
					
					//Make sure it does not go below 0
					if ($totalinsurance < 0) $totalinsurance = 0;
					
					//Print total cost of insurance
					echo "<p style='text-align:left;'>
								[Total Cost to Patient]
							<span style='float:right;'>
								<b>$$totalinsurance</b>
							</span>
					</p>";
					
					//Print total insurance payout
					echo "<p style='text-align:left;'>
								[Total Insurance Payout]
							<span style='float:right;'>
								<b>$$totalpatientpayout</b>
							</span>
					</p>";
					$totalplanpayout += $totalpatientpayout;
					
					//Calculate end of year date (12,31,year) and print it
					$yearEnd = date('m/d/Y', strtotime('12/31'));
					echo "<p style='text-align:left;'>
								[Due Date]
							<span style='float:right;'>
								$yearEnd
							</span>
					</p>";
				}	
				else
				{
					echo"<p>You do not have an insurance plan</p>";
				}
				echo "<br><br><br>\n";
			}
			echo "<p style='font-size:20px'><b>Total payout for insurance plan " . $planID . ": $" . $totalplanpayout . "</b></p>";
			$company_total_payouts += $totalplanpayout;
		}
		echo "<p style='font-size:20px'><b>Total payout for  all insurance plans: $" . $company_total_payouts . "</b></p>";
	?>
	<br></br>

	<div class="input-group">
  		<button onclick="window.open('/MyHealthPortal/insbilling_pdf.php')" type="submit" class="btn" name="pdf">Print</button>
  	</div>
  	<div class="input-group">
  		<button formaction="/MyHealthPortal/index.php"  type="submit" class="btn" name="return">Return</button>
  	</div>

  </form>
</body>
</html>