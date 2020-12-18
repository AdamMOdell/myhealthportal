<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Inventory</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="head">
  	<h1>MyHealthPortal</h1>
  </div>
  <div class="header">
  	<h2>Inventory</h2>
  </div>
  <form method="post">
  <?php include('errors.php'); ?>
  	<?php
		$PharmID = $_SESSION['PharmID'];
		
		$pharm_query = "SELECT *
				FROM Pharmacies
				WHERE Pharmacies.PharmID = $PharmID;";
		$pharm_results = mysqli_query($db, $pharm_query);
		$pharmacy = mysqli_fetch_assoc($pharm_results);
		
		$query = "SELECT *
				FROM ProductCosts
				WHERE PharmID = $PharmID";
		$results = mysqli_query($db, $query);
	
	if (mysqli_num_rows($results) > 0)
	{
		while($product = mysqli_fetch_assoc($results))
		{
			$prod_query = "SELECT *
				FROM MedicalProducts
				WHERE ProdID = $product[ProdID]";
			$prod_results = mysqli_query($db, $prod_query);
			$prod = mysqli_fetch_assoc($prod_results);
			
			echo "<b>$prod[Description]</b><br>";
			
			echo "<p style='text-align:left;'>
					[Product ID]
				<span style='float:right;'>
					$prod[ProdID]
				</span>
			</p>";
			
			echo "<p style='text-align:left;'>
					[Manufacturer]
				<span style='float:right;'>
					$prod[Manufacturer]
				</span>
			</p>";
			
			echo "<p style='text-align:left;'>
					[Stock]
				<span style='float:right;'>
					$product[Amount]
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
			</p><br>";
		}
	}
	else
	{
		echo "No products currently available";
	}
	?>
	
  	<div class="input-group">
  		<button formaction="/MyHealthPortal/index.php"  type="submit" class="btn" name="return">Return</button>
  	</div>

  </form>
</body>
</html>