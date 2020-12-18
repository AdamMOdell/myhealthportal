<?php include('server.php') ?>
<?php

	// init 
	require('fpdf.php');

	$page_width = 95;
	$page_font = 'Arial';
	$font_height = 6;

	$pdf = new FPDF();
	$pdf->AddPage();
	
	// get insurance plans 
	$CompID = $_SESSION['CompID'];	
	$query = "SELECT PlanID 
					FROM InsPlans 
					WHERE CompID = $CompID;";
	$results = mysqli_query($db, $query);
	$planIDs = [];
	
	$query = "Select Name
				FROM InsCompany 
				WHERE CompID = $CompID;";
	$result = mysqli_query($db, $query);
	$CompName = mysqli_fetch_assoc($result)['Name'];
	
	$pdf->SetFont($page_font,'B',16);
	$pdf->Cell($page_width,$font_height,"Billing History for $CompName");
	$pdf->Ln();
	date_default_timezone_set('America/Los_Angeles');
	$date = date('m/d/Y h:i:s a', time());
	$pdf->Cell($page_width,$font_height,$date . " PST");
	$pdf->Ln();
	$pdf->AddPage();
	
	while ( $value = mysqli_fetch_assoc($results)){
		//var_dump($value['PlanID']);
		array_push($planIDs, $value['PlanID']);
	}
	
	$company_total_payouts = 0;
	
	for( $m = 0; $m < count($planIDs); $m++)// patients for every insurance plan 
	{
		$planID = $planIDs[$m];
		$query = "SELECT PID
					FROM Membership
					WHERE Membership.PlanID = $planID";
		$results = mysqli_query($db, $query);
		$PIDs = [];
		while ( $value = mysqli_fetch_assoc($results)){
			array_push($PIDs, $value);
		}

		$totalplanpayout = 0;
	
		for ($n = 0; $n < count($PIDs); $n++)// billing info for every patient
		{
			$totalpatientpayout = 0;
			$PID = $PIDs[$n]['PID'];
			
			$query = "SELECT *
					FROM Patients
					WHERE PID = $PID";
			$results = mysqli_query($db, $query);
			$patient = mysqli_fetch_assoc($results);
			
			$pdf->SetFont($page_font,'B',16);
			$pdf->Cell($page_width,$font_height,"Billing History");
			$pdf->Ln();
			$pdf->SetFont($page_font,'',13);
			$pdf->Cell($page_width,$font_height,$patient['Name']);
			$pdf->Ln();
			date_default_timezone_set('America/Los_Angeles');
			$date = date('m/d/Y h:i:s a', time());
			$pdf->Cell($page_width,$font_height,$date . " PST");
			$pdf->Ln();
			$query = "SELECT DISTINCT *
					FROM ServiceRecords
					WHERE PID = $PID
					ORDER BY Date";
			$results = mysqli_query($db, $query);
			
			$ins_query = "SELECT DISTINCT InsPlans.*
					FROM InsPlans, Membership
					WHERE Membership.PID = $PID AND Membership.PlanID = InsPlans.PlanID";
			$ins_results = mysqli_query($db, $ins_query);
			$insurance = mysqli_fetch_assoc($ins_results);
			
			$tran_query = "SELECT DISTINCT *
					FROM ProductTransactions
					WHERE PID = $PID
					ORDER BY Date";
			$tran_results = mysqli_query($db, $tran_query);
			
			$pdf->AddPage();
			
			if (mysqli_num_rows($results) > 0) // service records per patient
			{
				$totalcost = 0;
				while($record = mysqli_fetch_assoc($results))
				{
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
					
					if (mysqli_num_rows($service_results) > 0)
					{
						$pdf->Ln();
						$pdf->SetFont($page_font,'B',20);
						$pdf->Cell($page_width,$font_height,"$service[Description]");
						$pdf->Ln();
						$pdf->Ln();
						$pdf->SetFont($page_font,'',13);
					}
					
					$pdf->Cell($page_width,$font_height,"[Service ID]");
					$pdf->MultiCell($page_width,$font_height,"$record[ServiceID]",'','R');
					
					$date = $record['Date'];
					
					$year = substr($date, 0, 4);
					$month = substr($date, 4, 2);
					$day = substr($date, 6, 2);
					
					$pdf->Cell($page_width,$font_height,"[Date]");
					$pdf->MultiCell($page_width,$font_height,"$month/$day/$year",'','R');
					
					if (mysqli_num_rows($service_results) > 0)
					{
						$pdf->Cell($page_width,$font_height,"[Service Cost]");
						$pdf->MultiCell($page_width,$font_height,"$$service[Cost]",'','R');
						$totalcost += $service['Cost'];
					}
					
					$product_query = "SELECT DISTINCT *
						FROM ServiceProducts
						WHERE ServiceCode = $service_code";
					$product_results = mysqli_query($db, $product_query);
					
					if (mysqli_num_rows($product_results) > 0)
					{
						$pdf->Ln();
						$pdf->Cell($page_width,$font_height,"[Products Used]");
						$pdf->Ln();
						$pdf->Ln();
						while($product = mysqli_fetch_assoc($product_results))
						{
							$prod_query = "SELECT DISTINCT *
								FROM MedicalProducts
								WHERE ProdID = $product[ProdID];";
							$prod_results = mysqli_query($db, $prod_query);
							$prod = mysqli_fetch_assoc($prod_results);
							
							$pdf->SetFont($page_font,'B',13);
							$pdf->Cell($page_width,$font_height,"$prod[Description]");
							$pdf->SetFont($page_font,'',13);
							$pdf->Ln();
							
							$pdf->Cell($page_width,$font_height,"[Product ID]");
							$pdf->MultiCell($page_width,$font_height,"$product[ProdID]",'','R');
							
							$cost_query = "SELECT DISTINCT *
								FROM ProductCosts
								WHERE ProductCosts.ProdID = $product[ProdID];";
							$cost_results = mysqli_query($db, $cost_query);
							$cost = mysqli_fetch_assoc($cost_results);
							$pdf->Cell($page_width,$font_height,"[Cost]");
							$pdf->MultiCell($page_width,$font_height,"$$cost[Cost]",'','R');
							$pdf->Ln();
							$totalcost += $cost['Cost'];
						}
					}
					
					$referral_query = "SELECT DISTINCT *
						FROM ReferralProducts
						WHERE ServiceID = $record[ServiceID]";
					$referral_results = mysqli_query($db, $referral_query);
					
					if (mysqli_num_rows($referral_results) > 0)
					{
						$pdf->Cell($page_width,$font_height,"[Prescriptions]");
						$pdf->Ln();
						$pdf->Ln();
						while($referral = mysqli_fetch_assoc($referral_results))
						{
							$prod_query = "SELECT DISTINCT *
								FROM MedicalProducts
								WHERE ProdID = $referral[ProdID];";
							$prod_results = mysqli_query($db, $prod_query);
							$prod = mysqli_fetch_assoc($prod_results);
							
							$pdf->SetFont($page_font,'B',13);
							$pdf->Cell($page_width,$font_height,"$prod[Description]");
							$pdf->SetFont($page_font,'',13);
							$pdf->Ln();
							
							$cost_query = "SELECT DISTINCT *
								FROM ProductCosts
								WHERE ProductCosts.ProdID = $prod[ProdID];";
							$cost_results = mysqli_query($db, $cost_query);
							$cost = mysqli_fetch_assoc($cost_results);
							$pdf->Cell($page_width,$font_height,"[Cost]");
							$pdf->MultiCell($page_width,$font_height,"$$cost[Cost]",'','R');
							$pdf->Ln();
							$totalcost += $cost['Cost'];
						}
					}
					
					$totaldeductible = $insurance['AnnualDeductible'];
					
					$pdf->Cell($page_width,$font_height,"[Original Cost]");
					$pdf->MultiCell($page_width,$font_height,"$$totalcost",'','R');
					
					if (mysqli_num_rows($ins_results) > 0)
					{
						$pdf->Cell($page_width,$font_height,"[Current Deductible]");
						$pdf->MultiCell($page_width,$font_height,"$$totaldeductible",'','R');
						
						$tempdeductible = $totaldeductible;
						
						$totaldeductible -= $totalcost;
						
						if ($totaldeductible < 0)
						{
							$pdf->Cell($page_width,$font_height,"[Insurance payout]");
							$pdf->MultiCell($page_width,$font_height,"$" . -$totaldeductible,'','R');
							$totalpatientpayout -= $totaldeductible;
							$totaldeductible = 0;
						}
						
						if ($totalcost > $tempdeductible)
						{
							$totalcost = $tempdeductible;
						}
					}
					
					$pdf->Cell($page_width,$font_height,"[Total Cost]");
					$pdf->SetFont($page_font,'B',13);
					$pdf->MultiCell($page_width,$font_height,"$$totalcost",'','R');
					$pdf->SetFont($page_font,'',13);
					
					$date_format = $year . "-" . $month . "-" . $day;
					
					$due = date_create("$date_format");
					date_add($due, date_interval_create_from_date_string('1 month'));
					$due_date = date_format($due,"m/d/Y");
					
					$pdf->Cell($page_width,$font_height,"[Due Date]");
					$pdf->MultiCell($page_width,$font_height,"$due_date",'','R');
				}
				$pdf->Ln();
			}	
			else
			{
				$pdf->Cell($page_width,$font_height,"You have currently not paid for any services");
				$pdf->Ln();
			}
			
			if (mysqli_num_rows($tran_results) > 0) // products per patient
			{
				$pdf->AddPage();
				$pdf->SetFont($page_font,'B',16);
				$pdf->Cell($page_width,$font_height,"Products Transactions");
				$pdf->Ln();
				$pdf->SetFont($page_font,'',13);
				$pdf->Cell($page_width,$font_height,$patient['Name']);
				$pdf->Ln();
				date_default_timezone_set('America/Los_Angeles');
				$date = date('m/d/Y h:i:s a', time());
				$pdf->Cell($page_width,$font_height,$date . " PST");
				$pdf->Ln();
				$pdf->Ln();
				while($transaction = mysqli_fetch_assoc($tran_results))
				{
					$pdf->AddPage();
					$product_query = "SELECT DISTINCT *
						FROM MedicalProducts
						WHERE ProdID = $transaction[ProdID]
						LIMIT 1";
					$product_results = mysqli_query($db, $product_query);
					$product = mysqli_fetch_assoc($product_results);
				
					$pdf->SetFont($page_font,'B',20);
					$pdf->Cell($page_width,$font_height,"$product[Description]");
					$pdf->SetFont($page_font,'',13);
					$pdf->Ln();
					$pdf->Ln();
					$pharm_query = "SELECT Pharmacies.*
						FROM Pharmacies, ProductCosts
						WHERE ProductCosts.ProdID = $product[ProdID]
						AND Pharmacies.PharmID = ProductCosts.PharmID;";
					$pharm_results = mysqli_query($db, $pharm_query);
					$pharmacy = mysqli_fetch_assoc($pharm_results);
					$pdf->Cell($page_width,$font_height,"[Pharmacy]");
					$pdf->MultiCell($page_width,$font_height,"$pharmacy[Name]",'','R');
					
					$pdf->Cell($page_width,$font_height,"[Pharmacy ID]");
					$pdf->MultiCell($page_width,$font_height,"$pharmacy[PharmID]",'','R');
					
					$pdf->Cell($page_width,$font_height,"[Address]");
					$pdf->MultiCell($page_width,$font_height,"$pharmacy[Address]",'','R');
					
					$date = $transaction['Date'];
					
					$year = substr($date, 0, 4);
					$month = substr($date, 4, 2);
					$day = substr($date, 6, 2);
				
					$pdf->Cell($page_width,$font_height,"[Date]");
					$pdf->MultiCell($page_width,$font_height,"$month/$day/$year",'','R');
					
					$pdf->Cell($page_width,$font_height,"[Product ID]");
					$pdf->MultiCell($page_width,$font_height,"$product[ProdID]",'','R');
					
					$cost_query = "SELECT *
						FROM ProductCosts
						WHERE ProductCosts.ProdID = $product[ProdID];";
					$cost_results = mysqli_query($db, $cost_query);
					$cost = mysqli_fetch_assoc($cost_results);
					$pdf->Ln();
					
					$pdf->Cell($page_width,$font_height,"[Original Cost]");
					$pdf->MultiCell($page_width,$font_height,"$$cost[Cost]",'','R');
					
					$total_cost = $cost['Cost'];
						
					if (mysqli_num_rows($ins_results) > 0)
					{
						$discount_query = "SELECT DISTINCT *
										FROM IncludedProducts
										WHERE PlanID = $insurance[PlanID]
										AND ProdID = $product[ProdID]";
						$discount_results = mysqli_query($db, $discount_query);
						$discount = mysqli_fetch_assoc($discount_results);
						
						if (mysqli_num_rows($discount_results) > 0)
						{
							$pdf->Cell($page_width,$font_height,"[Discount]");
							$pdf->MultiCell($page_width,$font_height,"$discount[Discount]%",'','R');
						}
						$pdf->Ln();
						
						$discount_percent = (1 - ($discount['Discount']/100));
						
						$total_cost *= $discount_percent;
						
						$total_cost = round($total_cost, 2);
					}
					
					$pdf->Cell($page_width,$font_height,"[Total Cost]");
					$pdf->SetFont($page_font,'B',13);
					$pdf->MultiCell($page_width,$font_height,"$$total_cost",'','R');
					$pdf->SetFont($page_font,'',13);
					
					$date_format = $year . "-" . $month . "-" . $day;
						
					$due = date_create("$date_format");
					date_add($due, date_interval_create_from_date_string('1 month'));
					$due_date = date_format($due,"m/d/Y");
					
					$pdf->Cell($page_width,$font_height,"[Due Date]");
					$pdf->MultiCell($page_width,$font_height,"$due_date",'','R');
					$pdf->Ln();
				}
			}
			if (mysqli_num_rows($ins_results) > 0) // insurance bills per patient
			{
				$comp_query = "SELECT DISTINCT InsCompany.*
				FROM InsCompany, InsPlans
				WHERE InsPlans.PlanID = $insurance[PlanID] AND InsCompany.CompID = InsPlans.CompID";
				$comp_results = mysqli_query($db, $comp_query);
				$company = mysqli_fetch_assoc($comp_results);
				
				$pdf->AddPage();
				$pdf->SetFont($page_font,'B',16);
				$pdf->Cell($page_width,$font_height,"Insurance");
				$pdf->SetFont($page_font,'',13);
				$pdf->Ln();
				$pdf->Cell($page_width,$font_height,$patient['Name']);
				$pdf->Ln();
				date_default_timezone_set('America/Los_Angeles');
				$date = date('m/d/Y h:i:s a', time());
				$pdf->Cell($page_width,$font_height,$date . " PST");
				$pdf->Ln();
				$pdf->Ln();
				$pdf->SetFont($page_font,'B',13);
				$pdf->Cell($page_width,$font_height,"$company[Name]");
				$pdf->SetFont($page_font,'',13);
				$pdf->Ln();
				
				$pdf->Cell($page_width,$font_height,"[Annual Premium]");
				$pdf->MultiCell($page_width,$font_height,"$$insurance[AnnualPremium]",'','R');
				
				$pdf->Cell($page_width,$font_height,"[Plan Contribution]");
				$pdf->MultiCell($page_width,$font_height,"$$insurance[PlanContribution]",'','R');
				
				$totalinsurance = $insurance['AnnualPremium'] - $insurance['PlanContribution'];
				
				if ($totalinsurance < 0) $totalinsurance = 0;
				
				$pdf->Cell($page_width,$font_height,"[Total Cost to Patient]");
				$pdf->SetFont($page_font,'B',13);
				$pdf->MultiCell($page_width,$font_height,"$$totalinsurance",'','R');
				$pdf->SetFont($page_font,'',13);
				
				$pdf->Cell($page_width,$font_height,"[Total Insurance Payout]");
				$pdf->SetFont($page_font,'B',13);
				$pdf->MultiCell($page_width,$font_height,"$$totalpatientpayout",'','R');
				$pdf->SetFont($page_font,'',13);
				$totalplanpayout += $totalpatientpayout;
				
				$yearEnd = date('m/d/Y', strtotime('12/31'));
				
				$pdf->Cell($page_width,$font_height,"[Due Date]");
				$pdf->MultiCell($page_width,$font_height,"$yearEnd",'','R');
			}
			else // no insurance 
			{
				$pdf->AddPage();
				$pdf->SetFont($page_font,'B',20);
				$pdf->Cell($page_width,$font_height,"Insurance");
				$pdf->SetFont($page_font,'',13);
				$pdf->Ln();
				$pdf->SetFont($page_font,'',13);
				$pdf->Cell($page_width,$font_height,$patient['Name']);
				$pdf->Ln();
				date_default_timezone_set('America/Los_Angeles');
				$date = date('m/d/Y h:i:s a', time());
				$pdf->Cell($page_width,$font_height,$date . " PST");
				$pdf->Ln();
				$pdf->Ln();
				$pdf->Cell($page_width,$font_height,"You do not have an insurance plan");
			}
		}
		// billing info for entire insurance plan 
		$pdf->AddPage();
		$pdf->SetFont($page_font,'B',20);
		$pdf->Cell($page_width,$font_height,"Insurance plan $planID");
		$pdf->SetFont($page_font,'',13);
		$pdf->Ln();
		$pdf->SetFont($page_font,'',13);
		date_default_timezone_set('America/Los_Angeles');
		$date = date('m/d/Y h:i:s a', time());
		$pdf->Cell($page_width,$font_height,$date . " PST");
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Cell($page_width,$font_height,"Total payouts: $$totalplanpayout");
		$pdf->AddPage();
		$company_total_payouts += $totalplanpayout;
	}
	// billing info for entire insurance company 
	$pdf->SetFont($page_font,'B',20);
	$pdf->Cell($page_width,$font_height,"Billing for all insurance plans");
	$pdf->SetFont($page_font,'',13);
	$pdf->Ln();
	$pdf->SetFont($page_font,'',13);
	date_default_timezone_set('America/Los_Angeles');
	$date = date('m/d/Y h:i:s a', time());
	$pdf->Cell($page_width,$font_height,$date . " PST");
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Cell($page_width,$font_height,"Total payouts for all insurance plans: $$company_total_payouts");
	$pdf->AddPage();
	// end 
	$pdf->Output();
?>