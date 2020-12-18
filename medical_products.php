<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Medical Products</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="head">
  	<h1>MyHealthPortal</h1>
  </div>
  <div class="header">
  	<h2>Medical Products</h2>
  </div>
  <form method="post">
  <?php include('errors.php'); ?>
  	<?php
		$PID = $_SESSION['pid'];
		
		$query = "SELECT *
				FROM MedicalProducts";
		$results = mysqli_query($db, $query);
		
		$ins_query = "SELECT DISTINCT InsPlans.*
				FROM InsPlans, Membership
				WHERE Membership.PID = $PID AND Membership.PlanID = InsPlans.PlanID";
		$ins_results = mysqli_query($db, $ins_query);
		$insurance = mysqli_fetch_assoc($ins_results);
		
		$prod_query = "SELECT *
				FROM MedicalProducts";
		$prod_results = mysqli_query($db, $prod_query);
		
		if (mysqli_num_rows($prod_results) > 0)
		{
			echo "<div class=input-group>Pharmaceuticals <select name=prod_id>";
			if (isset($_SESSION['p_prod_id']))
			{
				$p_id = $_SESSION['p_prod_id'];
				$p_query = "SELECT *
					FROM MedicalProducts
					WHERE ProdID = $p_id";
				$p_results = mysqli_query($db, $p_query);
				$p = mysqli_fetch_assoc($p_results);
				
				echo "<option value=$p[ProdID]>$p[Description] [$p[ProdID]]</option>";
				echo "<option name=select>Select</option>";
			}
			else
			{
				echo "<option name=select>Select</option>";
			}
			while($prod = mysqli_fetch_assoc($prod_results))
			{
				$available_query = "SELECT *
					FROM ProductCosts
					WHERE ProductCosts.ProdID = $prod[ProdID];";
				$available_results = mysqli_query($db, $available_query);
				$available = mysqli_fetch_assoc($available_results);
				
				$availability = $available['Amount'];
				
				
				if (mysqli_num_rows($ins_results) > 0)
				{
					$product_query = "SELECT DISTINCT *
										FROM IncludedProducts
										WHERE IncludedProducts.PlanID = $insurance[PlanID]
										AND IncludedProducts.ProdID = $prod[ProdID]";
					$product_results = mysqli_query($db, $product_query);
					
					
				}
				else
				{
					echo "<b>$prod[Description]</b><br>";
				}
			
				if ($availability > 0)
				{
					if (isset($_SESSION['p_prod_id']))
					{
						$pr_id = $_SESSION['p_prod_id'];
						if ($pr_id != $prod['ProdID'])
						{
							if (mysqli_num_rows($product_results) > 0)
							{
								echo "<option value=$prod[ProdID]>$prod[Description] [$prod[ProdID]] <b>[Covered]</b></option>";
							}
							else
							{
								echo "<option value=$prod[ProdID]>$prod[Description] [$prod[ProdID]]</option>";
							}
						}
					}
					else
					{
						echo "<option value=$prod[ProdID]>$prod[Description] [$prod[ProdID]]</option>";
					}
				}
			}
			echo "</select></div>";
		}
		
		echo "
		<div class=input-group>
			<button type=submit class=btn name=show_details>Show Details</button>
		</div><br>";
		
		if (isset($_SESSION['p_prod_id']))
		{
			$prodid = $_SESSION['p_prod_id'];
			$prod_query = "SELECT *
				FROM MedicalProducts
				WHERE ProdID = $prodid";
			$prod_results = mysqli_query($db, $prod_query);
			$prod = mysqli_fetch_assoc($prod_results);
			
			$pharm_query = "SELECT Pharmacies.*
				FROM Pharmacies, ProductCosts
				WHERE ProductCosts.ProdID = $prod[ProdID]
				AND Pharmacies.PharmID = ProductCosts.PharmID;";
			$pharm_results = mysqli_query($db, $pharm_query);
			$pharmacy = mysqli_fetch_assoc($pharm_results);
			if (mysqli_num_rows($ins_results) > 0)
			{
				$product_query = "SELECT DISTINCT *
									FROM IncludedProducts
									WHERE IncludedProducts.PlanID = $insurance[PlanID]
									AND IncludedProducts.ProdID = $prod[ProdID]";
				$product_results = mysqli_query($db, $product_query);
				
				if (mysqli_num_rows($product_results) > 0)
				{
					echo "<b>$prod[Description] [Covered by insurance]</b><br>";
				}
				else
				{
					echo "<b>$prod[Description]</b><br>";
				}
			}
			else
			{
				echo "<b>$prod[Description]</b><br>";
			}
			
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
			echo "<p style='text-align:left;'>
					[Product ID]
				<span style='float:right;'>
					<b>$prod[ProdID]</b>
				</span>
			</p>";
			$cost_query = "SELECT *
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
			
			$total_cost = $cost['Cost'];
			
			if (mysqli_num_rows($ins_results) > 0)
			{
				$product_query = "SELECT DISTINCT *
									FROM IncludedProducts
									WHERE IncludedProducts.PlanID = $insurance[PlanID]
									AND IncludedProducts.ProdID = $product[ProdID]";
				$product_results = mysqli_query($db, $product_query);
				$product_row = mysqli_fetch_assoc($product_results);
				
				if (mysqli_num_rows($product_results) > 0)
				{
					echo "<p style='text-align:left;'>
							[Discount]
						<span style='float:right;'>
							$product_row[Discount]%
						</span>
					</p>";
					
					$discount_percent = (1 - ($product_row['Discount']/100));
					
					$total_cost *= $discount_percent;
					
					$total_cost = round($total_cost, 2);
					
					echo "<p style='text-align:left;'>
							[Total Cost]
						<span style='float:right;'>
							$$total_cost
						</span>
					</p>";
				}
				echo "</br>";
			}
		}
		if (mysqli_num_rows($results) > 0)
		{
			echo "<div class=input-group>
				<button type=submit class=btn name=purchase_product value=$prodid>Purchase</button>
			</div><br>";
		
		}
	?>
  	<div class="input-group">
  		<button formaction="/MyHealthPortal/index.php"  type="submit" class="btn" name="return">Return</button>
  	</div>

  </form>
</body>
</html>