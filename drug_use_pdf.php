<?php include('server.php') ?>
<?php
	require('fpdf.php');

	$page_width = 95;
	$page_font = 'Arial';
	$font_height = 6;

	$pdf = new FPDF();
	$pdf->AddPage();
	$pdf->SetFont($page_font,'B',16);
	
	$PharmID = $_SESSION['PharmID'];
	
	$pharm_query = "SELECT DISTINCT *
			FROM Pharmacies
			WHERE PharmID = $PharmID;";
	$pharm_results = mysqli_query($db, $pharm_query);
	$pharm = mysqli_fetch_assoc($pharm_results);
	
	$pdf->SetFont($page_font,'B',16);
	$pdf->Cell($page_width,$font_height,"Billing History");
	$pdf->Ln();
	$pdf->SetFont($page_font,'',13);
	$pdf->Cell($page_width,$font_height,$pharm['Name']);
	$pdf->Ln();
	date_default_timezone_set('America/Los_Angeles');
	$date = date('m/d/Y h:i:s a', time());
	$pdf->Cell($page_width,$font_height,$date . " PST");
	$pdf->Ln();
	$query = "SELECT DISTINCT *
			FROM ProductCosts
			WHERE PharmID = $PharmID;";
	$results = mysqli_query($db, $query);
	
	$total_payment = 0;
	
	if (mysqli_num_rows($results) > 0)
	{
		while($record = mysqli_fetch_assoc($results))
		{
			$pdf->AddPage();
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
			
			$pdf->Ln();
			$pdf->SetFont($page_font,'B',20);
			$pdf->Cell($page_width,$font_height,"$product[Description]");
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFont($page_font,'',13);
			
			if (mysqli_num_rows($serv_results) > 0)
			{
				while($serv = mysqli_fetch_assoc($serv_results))
				{
					$patient_query = "SELECT *
									FROM Patients
									WHERE Patients.PID = $serv[PID]";
					$patient_results = mysqli_query($db, $patient_query);
					$patient = mysqli_fetch_assoc($patient_results);
					
					$pdf->Cell($page_width,$font_height,"[Service ID]");
					$pdf->MultiCell($page_width,$font_height,"$serv[ServiceID]",'','R');
					
					$pdf->Cell($page_width,$font_height,"[Patient]");
					$pdf->MultiCell($page_width,$font_height,"$patient[Name]",'','R');
					
					$pdf->Cell($page_width,$font_height,"[Patient ID]");
					$pdf->MultiCell($page_width,$font_height,"$patient[PID]",'','R');
					
					$pdf->Cell($page_width,$font_height,"[Cost]");
					$pdf->MultiCell($page_width,$font_height,"$$record[Cost]",'','R');
					
					$total_payment += $record['Cost'];
					
					$date = $serv['Date'];
			
					$year = substr($date, 0, 4);
					$month = substr($date, 4, 2);
					$day = substr($date, 6, 2);
					
					$pdf->Cell($page_width,$font_height,"[Date]");
					$pdf->MultiCell($page_width,$font_height,"$month/$day/$year",'','R');
					
					$usage += 1;
					$pdf->Ln();
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
					
					$pdf->Cell($page_width,$font_height,"[Transaction ID]");
					$pdf->MultiCell($page_width,$font_height,"$transaction[TranID]",'','R');
					//$pdf->Ln();
					
					$pdf->Cell($page_width,$font_height,"[Patient]");
					$pdf->MultiCell($page_width,$font_height,"$patient[Name]",'','R');
					//$pdf->Ln();
					
					$pdf->Cell($page_width,$font_height,"[Patient ID]");
					$pdf->MultiCell($page_width,$font_height,"$patient[PID]",'','R');
					//$pdf->Ln();
					
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
							$pdf->Cell($page_width,$font_height,"[Cost]");
							$pdf->MultiCell($page_width,$font_height,"$$record[Cost]",'','R');
							//$pdf->Ln();
							
							$pdf->Cell($page_width,$font_height,"[Insurance Discount]");
							$pdf->MultiCell($page_width,$font_height,"$discount[Discount]%",'','R');
							//$pdf->Ln();
						
							$discount_percent = (1 - ($discount['Discount']/100));
							
							$total_cost *= $discount_percent;
							
							$total_cost = round($total_cost, 2);
							
							$pdf->Cell($page_width,$font_height,"[Patient Paid]");
							$pdf->MultiCell($page_width,$font_height,"$$total_cost",'','R');
							//$pdf->Ln();
						}
						else
						{
							$pdf->Cell($page_width,$font_height,"[Cost]");
							$pdf->MultiCell($page_width,$font_height,"$$record[Cost]",'','R');
							//$pdf->Ln();
						}
					}
					else
					{
						$pdf->Cell($page_width,$font_height,"[Cost]");
						$pdf->MultiCell($page_width,$font_height,"$$record[Cost]",'','R');
						//$pdf->Ln();
					}
					
					$total_payment += $record['Cost'];
					
					$date = $transaction['Date'];
			
					$year = substr($date, 0, 4);
					$month = substr($date, 4, 2);
					$day = substr($date, 6, 2);
					
					$pdf->Cell($page_width,$font_height,"[Date]");
					$pdf->MultiCell($page_width,$font_height,"$month/$day/$year",'','R');
					//$pdf->Ln();
					
					$usage += 1;
					$pdf->Ln();
				}
			}
			
			if ($usage == 0)
			{
				$pdf->Cell($page_width,$font_height,"No $product[Description] has been used.");
				$pdf->Ln();
				$pdf->Ln();
			}
		}
		
		$pdf->AddPage();
		$pdf->SetFont($page_font,'B',20);
		$pdf->Cell($page_width,$font_height,"Total Payments");
		$pdf->SetFont($page_font,'',13);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Cell($page_width,$font_height,"$$total_payment");
	}
	else
	{
		$pdf->Cell($page_width,$font_height,"No drugs have been used.");
		$pdf->Ln();
	}
	
	$pdf->Output();
?>