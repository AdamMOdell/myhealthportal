<?php include('server.php') ?>
<?php
	require('fpdf.php');

	$page_width = 95;
	$page_font = 'Arial';
	$font_height = 6;

	$pdf = new FPDF();
	$pdf->AddPage();
	$pdf->SetFont($page_font,'B',16);
	
	$PID = $_SESSION['pid'];
	
	$query = "SELECT *
			FROM Patients
			WHERE PID = $PID";
	$results = mysqli_query($db, $query);
	$patient = mysqli_fetch_assoc($results);
	
	$pdf->Cell($page_width,$font_height,"Medical History");
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
	
	$tran_query = "SELECT DISTINCT *
		FROM ProductTransactions
		WHERE PID = $PID
		ORDER BY Date";
	$tran_results = mysqli_query($db, $tran_query);
	
	$pdf->AddPage();
	
	if (mysqli_num_rows($results) > 0)
	{
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
			
			$pdf->Cell($page_width,$font_height,"[ServiceID]");
			$pdf->MultiCell($page_width,$font_height,"$record[ServiceID]",'','R');
			
			if (mysqli_num_rows($provider_results) > 0)
			{
				$pdf->Cell($page_width,$font_height,"[$provider[Specialty]]");
				$pdf->MultiCell($page_width,$font_height,"Dr. $provider[Name]",'','R');
			}
			
			$date = $record['Date'];
			
			$year = substr($date, 0, 4);
			$month = substr($date, 4, 2);
			$day = substr($date, 6, 2);
			
			$pdf->Cell($page_width,$font_height,"[Date]");
			$pdf->MultiCell($page_width,$font_height,"$month/$day/$year",'','R');
			
			if (mysqli_num_rows($service_results) > 0)
			{
				$pdf->Cell($page_width,$font_height,"[Cost]");
				$pdf->MultiCell($page_width,$font_height,"$$service[Cost]",'','R');
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
			
			$pdf->Ln();
			$pdf->SetFont($page_font,'B',13);
			$pdf->Cell($page_width,$font_height,"[Diagnosis]");
			$pdf->SetFont($page_font,'',13);
			$pdf->Ln();
			$pdf->Cell($page_width,$font_height,"$diagnosis");
			$pdf->AddPage();
		}
		$pdf->Ln();
	}	
	else
	{
		$pdf->Cell($page_width,$font_height,"You have currently not paid for any services");
		$pdf->Ln();
	}
	
	if (mysqli_num_rows($tran_results) > 0)
	{
		$pdf->SetFont($page_font,'B',16);
		$pdf->Cell($page_width,$font_height,"Medical Products");
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
			
			$pdf->Cell($page_width,$font_height,"[Cost]");
			$pdf->MultiCell($page_width,$font_height,"$$cost[Cost]",'','R');
			$pdf->Ln();
		}
	}
	
	$pdf->Output();
?>