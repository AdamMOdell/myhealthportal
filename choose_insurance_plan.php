<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Choose an insurance plan</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="head">
		<h1>MyHealthPortal</h1>
	</div>
	<div class="header">
		<h2>Health Insurance Plans</h2>
	</div>
	<form method="post">
		<?php include('errors.php'); ?>
			<?php
			$pid = $_SESSION['pid'];
			$memberquery = "SELECT * FROM Membership WHERE PID='$pid';";
			$memberresults = mysqli_query($db, $memberquery);
			$row = mysqli_fetch_assoc($memberresults);
			if (mysqli_num_rows($memberresults) > 0)
			{
				$query = "SELECT * FROM InsPlans WHERE PlanID = $row[PlanID];";
				$results = mysqli_query($db, $query);
				$plan = mysqli_fetch_assoc($results);
				
				echo "<p>Your current insurance policy is</p><br>";
				$compquery = "SELECT * FROM InsCompany WHERE CompID = $plan[CompID];";
				$compresults = mysqli_query($db, $compquery);
				$compname = mysqli_fetch_assoc($compresults);
				echo "<p><b>$compname[Name]</b></p>";
				echo "<p style='text-align:left;'>
						[Plan ID]
					<span style='float:right;'>
						$plan[PlanID]
					</span>
				</p><br>";
				echo "<p style='text-align:left;'>
						[Annual Premium]
					<span style='float:right;'>
						$$plan[AnnualPremium]
					</span>
				</p>";
				echo "<p style='text-align:left;'>
						[Annual Deductible]
					<span style='float:right;'>
						$$plan[AnnualDeductible]
					</span>
				</p>";
				echo "<p style='text-align:left;'>
						[Plan Contribution]
					<span style='float:right;'>
						$$plan[PlanContribution]
					</span>
				</p>";
				echo "<p style='text-align:left;'>
						[Max Coverage]
					<span style='float:right;'>
						$$plan[MaxCoverage]
					</span>
				</p>";
				echo "<br><u>Service Coverage</u>" . "<br>";
				
				$servicequery = "SELECT DISTINCT Services.*
								FROM Services, Coverages, InsPlans
								WHERE Services.ServiceCode = Coverages.ServiceCode AND Coverages.PlanID = $row[PlanID]";
				$serviceresults = mysqli_query($db, $servicequery);
				$servicerow = '';
				
				while($servicerow = mysqli_fetch_assoc($serviceresults)) {
					echo "&nbsp;&nbsp;" . $servicerow['Description'] . "<br>";
				}
				echo "</br>";
				
				echo "<u>Product Coverage</u>" . "<br>";
				
				$product_query = "SELECT DISTINCT MedicalProducts.*
								FROM MedicalProducts, IncludedProducts
								WHERE IncludedProducts.PlanID = $plan[PlanID]
								AND MedicalProducts.ProdID = IncludedProducts.ProdID";
				$product_results = mysqli_query($db, $product_query);
				$product_row = '';
				
				while($product_row = mysqli_fetch_assoc($product_results)) {
					echo "<p style='text-align:left;'>
								&nbsp;&nbsp;$product_row[Description]
							<span style='float:right;'>
								[$product_row[ProdID]]
							</span>
						</p>";
				}
				echo "</br>";
			}
			else
			{
				echo "<p><b>Insurance Policies</b></p><br></br>";
				
				$plan_query = "SELECT * FROM InsPlans;";
				$plan_results = mysqli_query($db, $plan_query);
				$plan_row = '';
				
				while($plan_row = mysqli_fetch_assoc($plan_results)) {
					$compquery = "SELECT * FROM InsCompany WHERE CompID = $plan_row[CompID];";
					$compresults = mysqli_query($db, $compquery);
					$compname = mysqli_fetch_assoc($compresults);
					echo "<input type='radio' name='plan' id='$plan_row[PlanID]' value='$plan_row[PlanID]' >  <b><label for='$plan_row[PlanID]'>$compname[Name]</label></b><br><br>\n";
					
					echo "<p style='text-align:left;'>
							[Plan ID]
						<span style='float:right;'>
							$plan_row[PlanID]
						</span>
					</p>";
					echo "<p style='text-align:left;'>
							[Annual Premium]
						<span style='float:right;'>
							$$plan_row[AnnualPremium]
						</span>
					</p>";
					echo "<p style='text-align:left;'>
							[Annual Deductible]
						<span style='float:right;'>
							$$plan_row[AnnualDeductible]
						</span>
					</p>";
					echo "<p style='text-align:left;'>
							[Plan Contribution]
						<span style='float:right;'>
							$$plan_row[PlanContribution]
						</span>
					</p>";
					echo "<p style='text-align:left;'>
							[Max Coverage]
						<span style='float:right;'>
							$$plan_row[MaxCoverage]
						</span>
					</p>";
					echo "<br><u>Service Coverage</u>" . "<br>";
					
					$servicequery = "SELECT DISTINCT Services.*
									FROM Services, Coverages, InsPlans
									WHERE Services.ServiceCode = Coverages.ServiceCode AND Coverages.PlanID = $plan_row[PlanID]";
					$serviceresults = mysqli_query($db, $servicequery);
					$servicerow = '';
					
					while($servicerow = mysqli_fetch_assoc($serviceresults)) {
						echo "&nbsp;&nbsp;" . $servicerow['Description'] . "<br>";
					}
					echo "</br>";
					echo "<u>Product Coverage</u>" . "<br>";
				
					$product_query = "SELECT DISTINCT MedicalProducts.*
									FROM MedicalProducts, IncludedProducts
									WHERE IncludedProducts.PlanID = $plan_row[PlanID]
									AND MedicalProducts.ProdID = IncludedProducts.ProdID";
					$product_results = mysqli_query($db, $product_query);
					$product_row = '';
					
					while($product_row = mysqli_fetch_assoc($product_results)) {
						echo "<p style='text-align:left;'>
								&nbsp;&nbsp;$product_row[Description]
							<span style='float:right;'>
								[$product_row[ProdID]]
							</span>
						</p>";
					}
					echo "</br>";
				}
			}
		?>
		<?php if (mysqli_num_rows($memberresults) == 0) : ?>
			<div class="input-group">
				<button formaction="/MyHealthPortal/choose_insurance_plan.php" type="submit" class="btn" name="select_insurance_plan">Select Plan</button>
			</div>
			<div class="input-group">
				<button formaction="/MyHealthPortal/index.php" type="submit" class="btn">No Plan</button>
			</div>
		<?php endif ?>
		
		<?php if (mysqli_num_rows($memberresults) > 0) : ?>
			<div class="input-group">
				<button formaction="/MyHealthPortal/choose_insurance_plan.php" type="submit" class="btn" name="remove_insurance_plan">Change Plan</button>
			</div>
			<div class="input-group">
				<button formaction="/MyHealthPortal/index.php" type="submit" class="btn">Return</button>
			</div>
		<?php endif ?>
	</form>
</body>
</html>