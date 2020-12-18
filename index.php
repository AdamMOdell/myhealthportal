<?php 
  session_start(); 

  if (!isset($_SESSION['name'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['name']);
  	header("location: login.php");
  }
?>
<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="head">
		<h1>MyHealthPortal</h1>
	</div>
<div class="header">
	<h2>Home Page</h2>
</div>
<div class="content">
  	<!-- notification message -->
  	<?php if (isset($_SESSION['success'])) : ?>
      <div class="error success" >
      	<h3>
          <?php 
          	echo $_SESSION['success'];
          	unset($_SESSION['success']);
          ?>
      	</h3>
      </div>

  	<?php endif ?>
    <!-- logged in user information -->
	<?php  if ($_SESSION['type'] == "Patient") : ?>
		<?php  if (isset($_SESSION['name'])) : ?>
			<p><strong>Patient</strong></p><br>
			<p>Welcome <strong><?php echo $_SESSION['name']; ?></strong></p>
			<?php 
			$pid = $_SESSION['pid'];
			$memberquery = "SELECT * FROM Membership WHERE PID='$pid';";
			$memberresults = mysqli_query($db, $memberquery);
			$membership = mysqli_fetch_assoc($memberresults);
			$ins_query = "SELECT * FROM InsPlans WHERE PlanID = $membership[PlanID];";
			$ins_results = mysqli_query($db, $ins_query);
			$ins_plan = mysqli_fetch_assoc($ins_results);
			$comp_query = "SELECT * FROM InsCompany WHERE CompID = $ins_plan[CompID];";
			$comp_results = mysqli_query($db, $comp_query);
			$comp_name = mysqli_fetch_assoc($comp_results);
			if (mysqli_num_rows($comp_results) > 0)
			{
				echo "<p>Insurance <b>'$comp_name[Name]'</b></p>";
			}
			else
			{
				echo "<p>You currently have no insurance policy</p>";
			}
			unset($_SESSION['p_prod_id']);
			?>
			<!--homepage-->
			<!--<p>___________________________________________</p> -->
			<br></br><br></br>
			<p>Click below to manage appointments</p>
			<div class="input-group">
				<a href="/MyHealthPortal/appointments.php"><button type="submit" class="btn" name="reg_user">Appointments</button></a>
			</div>
			<br></br>
			<p>Click below to purchase pharmaceuticals</p>
			<div class="input-group">
				<a href="/MyHealthPortal/medical_products.php"><button type="submit" class="btn" name="reg_user">Pharmaceuticals</button></a>
			</div>
			<br></br>
			<p>Click below to manage insurance plans</p>
			<div class="input-group">
				<a href="/MyHealthPortal/choose_insurance_plan.php"><button type="submit" class="btn" name="reg_user">Insurance Plans</button></a>
			</div>
			<br></br>
			<p>Click below to view your medication and treatment history</p>
			<div class="input-group">
				<a href="/MyHealthPortal/medical_history.php"><button type="submit" class="btn" name="reg_user">Medical History</button></a>
			</div>
			<br></br>
			<p>Click below to view medical billings</p>
			<div class="input-group">
				<a href="/MyHealthPortal/billings.php"><button type="submit" class="btn" name="reg_user">Billings</button>
			</div>
			<br></br>
			
			<p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
		<?php endif ?>
	<?php endif ?>
	<?php  if ($_SESSION['type'] == "Provider") : ?>
		<?php  if (isset($_SESSION['name'])) : ?>
			<p><strong>Service Provider</strong></p><br>
			<p>Welcome <strong><?php echo $_SESSION['name']; ?></strong></p>
			
			<!--homepage-->
			<!--<p>___________________________________________</p> -->
			<br></br><br></br>
			<p>Click below to manage appointments</p>
			<div class="input-group">
				<a href="/MyHealthPortal/doctor_appointments.php"><button type="submit" class="btn" name="reg_user">Appointments</button></a>
			</div>
			<br></br>
			<p>Click below to manage patients and diagnosis</p>
			<div class="input-group">
				<a href="/MyHealthPortal/patient_overview.php"><button type="submit" class="btn" name="reg_user">Patients</button></a>
			</div>
			<br></br>
			<p>Click below to view medical billings</p>
			<div class="input-group">
				<a href="/MyHealthPortal/doctor_billings.php"><button type="submit" class="btn" name="reg_user">Billings</button></a>
			</div>
			<br></br>
			<p>Click below to view prescriptions</p>
			<div class="input-group">
				<a href="/MyHealthPortal/medications.php"><button type="submit" class="btn" name="reg_user">Prescriptions</button></a>
			</div>
			<br></br>
			
			<p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
		<?php endif ?>
	<?php endif ?>
	<?php  if ($_SESSION['type'] == "Insurance") : ?>
		<?php  if (isset($_SESSION['name'])) : ?>
			<p><strong>Insurance</strong></p><br>
			<p>Welcome <strong><?php echo $_SESSION['name']; ?></strong></p>
			
			<!--homepage-->
			<!--<p>___________________________________________</p> -->
			<br></br><br></br>
			<p>Click below to view patients</p>
			<div class="input-group">
				<a href="/MyHealthPortal/ins_patient_overview.php"><button type="submit" class="btn" name="reg_user">Patients</button></a>
			</div>

			<p>Click below to view medical billings</p>
			<div class="input-group">
				<a href="/MyHealthPortal/insbilling.php"><button type="submit" class="btn" name="reg_user">Billings</button></a>
			</div>
			<br></br>
			<br></br>
			
			<p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
		<?php endif ?>
	<?php endif ?>
	<?php  if ($_SESSION['type'] == "Pharmacy") : ?>
		<?php  if (isset($_SESSION['name'])) : ?>
			<p><strong>Pharmacy</strong></p><br>
			<p>Welcome <strong><?php echo $_SESSION['name']; ?></strong></p>
			
			<!--homepage-->
			<!--<p>___________________________________________</p> -->
			<br></br><br></br>
			<p>Click below to manage inventory</p>
			<div class="input-group">
				<a href="/MyHealthPortal/inventory.php"><button type="submit" class="btn" name="inventory">Inventory</button></a>
			</div>
			<br></br>
			<p>Click below to manage patient drug use</p>
			<div class="input-group">
				<a href="/MyHealthPortal/drug_use.php"><button type="submit" class="btn" name="prices">Drug Use</button></a>
			</div>
			<br></br>
			<p>Click below to contact patients</p>
			<div class="input-group">
				<a href="/MyHealthPortal/pharm_patients.php"><button type="submit" class="btn" name="prices">Patients</button></a>
			</div>
			<br></br>
			
			<p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
		<?php endif ?>
	<?php endif ?>
</div>
		
</body>
</html>