<?php include('server.php') ?>
<?php
	require('fpdf.php');
	
	$page_width = 95;
	$page_font = 'Arial';
	$font_height = 6;

	$pdf = new FPDF();
	$pdf->AddPage();
	
	$ProvID = $_SESSION['ProvID'];
	
	$prov_query = "SELECT DISTINCT *
			FROM ServiceProviders
			WHERE ProvID = $ProvID";
	$prov_results = mysqli_query($db, $prov_query);
	$prov = mysqli_fetch_assoc($prov_results);
	
	$pdf->SetFont($page_font,'B',16);
	$pdf->Cell($page_width,$font_height,"Billing History");
	$pdf->Ln();
	$pdf->SetFont($page_font,'',13);
	$pdf->Cell($page_width,$font_height,"Dr. " . $prov['Name']);
	$pdf->Ln();
	date_default_timezone_set('America/Los_Angeles');
	$date = date('m/d/Y h:i:s a', time());
	$pdf->Cell($page_width,$font_height,$date . " PST");
	$pdf->Ln();
	
	$query = "SELECT DISTINCT *
			FROM ServiceRecords
			WHERE ProvID = $ProvID
			ORDER BY Date DESC";
	$results = mysqli_query($db, $query);
	
	$total_payment = 0;
	
	if (mysqli_num_rows($results) > 0)
	{
		$pdf->AddPage();
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
				$pdf->Ln();
				$pdf->SetFont($page_font,'B',20);
				$pdf->Cell($page_width,$font_height,"$service[Description]");
				$pdf->Ln();
				$pdf->Ln();
				$pdf->SetFont($page_font,'',13);
			}
			
			$pdf->Cell($page_width,$font_height,"[Service ID]");
			$pdf->MultiCell($page_width,$font_height,"$record[ServiceID]",'','R');
			
			$pdf->Cell($page_width,$font_height,"[Service Code]");
			$pdf->MultiCell($page_width,$font_height,"$record[ServiceCode]",'','R');
			
			if (mysqli_num_rows($patient_results) > 0)
			{
				$pdf->Cell($page_width,$font_height,"[Patient]");
				$pdf->MultiCell($page_width,$font_height,"$patient[Name]",'','R');
				
				$pdf->Cell($page_width,$font_height,"[Patient ID]");
				$pdf->MultiCell($page_width,$font_height,"$patient[PID]",'','R');
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
			
			$pdf->Cell($page_width,$font_height,"[Date]");
			$pdf->MultiCell($page_width,$font_height,"$month/$day/$year $hour:$minute $time",'','R');
			
			if (mysqli_num_rows($service_results) > 0)
			{
				$pdf->Cell($page_width,$font_height,"[Cost]");
				$pdf->MultiCell($page_width,$font_height,"$$service[Cost]",'','R');
				
				$total_payment += $service['Cost'];
			}
			$pdf->AddPage();
		}
	}
	$pdf->SetFont($page_font,'B',20);
	$pdf->Cell($page_width,$font_height,"Total Payments");
	$pdf->SetFont($page_font,'',13);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Cell($page_width,$font_height,"$$total_payment");
	
	$pdf->Output();
?>