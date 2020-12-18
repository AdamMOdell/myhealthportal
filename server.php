<?php
session_start();

// initializing variables
$firstname 	= "";
$lastname 	= "";
$ssn		= "";
$email 		= "";
$phone 		= "";
$age 		= "";
$dob 		= "";
$dob_del 	= "";
$address 	= "";
$email 		= "";
$errors 	= array(); 

$servername = "localhost";
$serv_username = "myhealth5";
$serv_password = "u5t2_9AW)Pnx";
$dbname = "myhealth5";

// connect to the database
$db = new mysqli($servername, $serv_username, $serv_password, $dbname);//mysqli_connect($servername, $serv_username, $serv_password, $dbname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  
  $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
  $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
  $ssn = mysqli_real_escape_string($db, $_POST['ssn']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $phone = mysqli_real_escape_string($db, $_POST['phone']);
  
  $dob_del = mysqli_real_escape_string($db, $_POST['DoB']);
  $dob = str_replace("-", "", $dob_del);
  
  $address 	= mysqli_real_escape_string($db, $_POST['address']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
  
  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($firstname)) { array_push($errors, "A First Name is required"); }
  if (empty($lastname)) { array_push($errors, "A Last Name is required"); }
  if (empty($ssn)) { array_push($errors, "A Social Security Number is required"); }
  if (empty($email)) { array_push($errors, "An Email is required"); }
  if (empty($phone)) { array_push($errors, "A Phone Number is required"); }
  if (empty($dob)) { array_push($errors, "A Date of Birth is required"); }
  if (empty($address)) { array_push($errors, "An Address is required"); }
  if (empty($password_1)) { array_push($errors, "A Password is required"); }
  
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM Patients WHERE Email='$email' LIMIT 1;";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['Email'] === $email) {
      array_push($errors, "An account with this email already exists");
    }
  }
  
  if ($user) { // if user exists
    if ($user['SSN'] === $ssn) {
      array_push($errors, "An account with this SSN already exists");
    }
  }
	
  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1); //encrypt the password before saving in the database
	$name = $firstname . " " . $lastname;
  	$query = "INSERT INTO Patients (SSN, Password, Name, Address, DoB, Phone, Email) 
  			  VALUES ('$ssn', '$password', '$name', '$address', '$dob', '$phone', '$email');";
  	mysqli_query($db, $query);
  	$query = "SELECT * FROM Patients WHERE Email='$email' AND Password='$password';";
	$results = mysqli_query($db, $query);
	$user = mysqli_fetch_assoc($results);
	$_SESSION['pid'] = $user['PID'];
	//list($firstname, $lastname) = explode(" ", $name);
	$_SESSION['type'] = "Patient";
  	$_SESSION['name'] = "$name";
  	$_SESSION['success'] = "You are now logged in";
	
  	header('location: index.php');
	$msg = "Thank you for registering with the MyHealthPortal. If you would like to schedule appointments or manage your insurance plans, please follow the link - https://myhealth5.kb-projects.com/MyHealthPortal/index.php";
	$msg = wordwrap($msg,70);
	mail("$email", "MyHealthPortal Patient Regstration", $msg);
  }
}

//LOGIN USER
if (isset($_POST['login_user'])) {
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($email)) {
  	array_push($errors, "An Email is required");
  }
  if (empty($password)) {
  	array_push($errors, "A Password is required");
  }

  if (count($errors) == 0) {
  	$password = md5($password);
  	$query = "SELECT * FROM Patients WHERE Email='$email' AND Password='$password';";
	$results = mysqli_query($db, $query);
	$user = mysqli_fetch_assoc($results);
	
	$docquery = "SELECT * FROM ServiceProviders WHERE Email='$email' AND Password='$password';";
	$docresults = mysqli_query($db, $docquery);
	$docuser = mysqli_fetch_assoc($docresults);

	$ins_query = "SELECT * FROM InsCompany WHERE Email='$email' AND Password='$password';";
	$ins_results = mysqli_query($db, $ins_query);
	$ins_user = mysqli_fetch_assoc($ins_results);
	
	$pharm_query = "SELECT * FROM Pharmacies WHERE Email='$email' AND Password='$password';";
	$pharm_results = mysqli_query($db, $pharm_query);
	$pharm_user = mysqli_fetch_assoc($pharm_results);
	
  	if (mysqli_num_rows($results) == 1) {
	  $_SESSION['type'] = "Patient";
  	  $_SESSION['name'] = $user['Name'];
	  $_SESSION['pid'] = $user['PID'];
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: index.php');
  	} else if (mysqli_num_rows($docresults) == 1) {
	  $_SESSION['type'] = "Provider";
  	  $_SESSION['name'] = $docuser['Name'];
	  $_SESSION['ProvID'] = $docuser['ProvID'];
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: index.php');
	} else if (mysqli_num_rows($ins_results) == 1) {
	  $_SESSION['type'] = "Insurance";
  	  $_SESSION['name'] = $ins_user['Name'];
	  $_SESSION['CompID'] = $ins_user['CompID'];
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: index.php');
	} else if (mysqli_num_rows($pharm_results) == 1) {
	  $_SESSION['type'] = "Pharmacy";
  	  $_SESSION['name'] = $pharm_user['Name'];
	  $_SESSION['PharmID'] = $pharm_user['PharmID'];
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: index.php');
	} else {
  		array_push($errors, "Wrong username/password combination");
  	}
  }
}

// SELECT HEALTH INSURANCE PLAN
if (isset($_POST['select_insurance_plan'])) {
	echo "<!-- Selected new health insurance plan: $_POST[plan] for patient $_SESSION[pid] -->\n";
	
	$planid = $_POST[plan];
	$pid = $_SESSION['pid'];
	$memberquery = "SELECT * FROM Membership WHERE PID='$pid';";
	$memberresults = mysqli_query($db, $memberquery);
	$record = mysqli_fetch_assoc($memberresults);
	
	if (empty($planid)) { array_push($errors, "Please choose a plan"); }
	
	if (mysqli_num_rows($memberresults) == 1) { // switching insurance
		//This shouldn't be necessary anymore
		$insert_query = "UPDATE Membership SET PlanID = '$planid' WHERE PID = '$pid';";
		mysqli_query($db, $insert_query);
		echo "<!-- switched insurance -->";
	}
	else { // enrolling in insurance for first time
		$insert_query = "INSERT INTO Membership(PID, PlanID) VALUES('$pid', '$planid');";
		mysqli_query($db, $insert_query);
		echo "<!-- enrolled in insurance -->";
	}
	
}

// REMOVE INSURANCE PLAN
if (isset($_POST['remove_insurance_plan'])) {
	echo "<!-- Selected new health insurance plan: $_POST[plan] for patient $_SESSION[pid] -->\n";
	
	$planid = $_POST[plan];
	$pid = $_SESSION['pid'];
	$memberquery = "SELECT * FROM Membership WHERE PID='$pid';";
	$memberresults = mysqli_query($db, $memberquery);
	
	if (mysqli_num_rows($memberresults) > 0) { // switching insurance
		$insert_query = "DELETE FROM Membership WHERE PID = '$pid';";
		mysqli_query($db, $insert_query);
		echo "<!-- removed insurance -->";
	}
}

// PURCHASE MEDICAL PRODUCT
if (isset($_POST['purchase_product'])) {
  $ProdID = mysqli_real_escape_string($db, $_POST['prod_id']);
  $PID = $_SESSION['pid'];

  if (empty($ProdID)) {
  	array_push($errors, "A Product ID is required");
  }
  
	$available_query = "SELECT *
				FROM ProductCosts
				WHERE ProductCosts.ProdID = $ProdID;";
	$available_results = mysqli_query($db, $available_query);
	$available = mysqli_fetch_assoc($available_results);

	$availability = $available['Amount'];
	  
  if ($availability == 0) {
  	array_push($errors, "Product is not available at this time");
  }

  if (count($errors) == 0) {
  	$query = "SELECT * FROM MedicalProducts WHERE ProdID='$ProdID';";
	$results = mysqli_query($db, $query);
	$product = mysqli_fetch_assoc($results);
  	if (mysqli_num_rows($results) == 1) {
		date_default_timezone_set('America/Los_Angeles');
		$date = date('Ymd', time());
		$query = "INSERT INTO ProductTransactions (PID, ProdID, Date) 
  			  VALUE ('$PID', $ProdID, $date);";
		$update_query = "UPDATE ProductCosts
				SET Amount = Amount - 1
				WHERE ProdID = $ProdID;";
		mysqli_query($db, $update_query);
		mysqli_query($db, $query);
		array_push($errors, "Item <b>$product[Description]</b> was Purchased");
	} else {
  		array_push($errors, "Product Not Found");
  	}
  }
}

// SHOW MEDICAL PRODUCT DETAILS
if (isset($_POST['show_details'])) {
  $ProdID = mysqli_real_escape_string($db, $_POST['prod_id']);

	if ($ProdID == "Select")
	{
		unset($_SESSION['p_prod_id']);
	}
	else
	{
		if (empty($ProdID)) {
		array_push($errors, "A Product ID is required");
		}

		$available_query = "SELECT *
					FROM ProductCosts
					WHERE ProductCosts.ProdID = $ProdID;";
		$available_results = mysqli_query($db, $available_query);
		$available = mysqli_fetch_assoc($available_results);

		$availability = $available['Amount'];
		  
		if ($availability == 0) {
		array_push($errors, "Product is not available at this time");
		}

		if (count($errors) == 0) {
		$_SESSION['p_prod_id'] = $ProdID;
		}
	}
}

// DRUG REFERRAL
if (isset($_POST['drug_referral'])) {
  $ServiceID = mysqli_real_escape_string($db, $_POST['service_id']);
  $ProdID = mysqli_real_escape_string($db, $_POST['prod_id']);
  $ProvID = $_SESSION['ProvID'];

  if (empty($ServiceID) or $ServiceID == "Select") {
  	array_push($errors, "A Service ID is required");
  }
  if (empty($ProdID) or $ProdID == "Select") {
  	array_push($errors, "A Product is required");
  }
  
	date_default_timezone_set('America/Los_Angeles');
	$date = date('Ymd', time());
  
	$patient_query = "SELECT DISTINCT Patients.*
			FROM Patients, ServiceRecords
			WHERE ServiceRecords.ServiceID = $ServiceID
			AND Patients.PID = ServiceRecords.PID;";
	$patient_results = mysqli_query($db, $patient_query);
	$patient = mysqli_fetch_assoc($patient_results);

  if (count($errors) == 0) {
  	$query = "SELECT DISTINCT * FROM MedicalProducts WHERE ProdID='$ProdID' LIMIT 1;";
	$results = mysqli_query($db, $query);
	$product = mysqli_fetch_assoc($results);
  	if (mysqli_num_rows($results) > 0) {
		date_default_timezone_set('America/Los_Angeles');
		$date = date('Ymd', time());
		$referral_query = "INSERT INTO ReferralProducts (ServiceID, ProdID)
  			  VALUE ($ServiceID, $product[ProdID]);";
		$update_query = "UPDATE ProductCosts
				SET Amount = Amount - 1
				WHERE ProdID = $product[ProdID];";
		mysqli_query($db, $referral_query);
		mysqli_query($db, $update_query);
		array_push($errors, "Prescription Sumbitted");
	} else {
  		array_push($errors, "Product $ProdName Not Found");
  	}
  }
}

// PATIENT EMAIL
if (isset($_POST['patient_email'])) {
	$PID = mysqli_real_escape_string($db, $_POST['pid']);
	$subject = mysqli_real_escape_string($db, $_POST['subject']);
	$content = mysqli_real_escape_string($db, $_POST['email']);
	$ProvID = $_SESSION['ProvID'];

	if (empty($PID) or $ServiceID == "Select") {
		array_push($errors, "A Patient ID is required");
	}
	if (empty($subject)) {
		array_push($errors, "A Subject is required");
	}
	if (empty($content)) {
		array_push($errors, "Content is required");
	}
	if (empty($ProvID)) {
		array_push($errors, "$ProvID is invalid");
	}
	
	if (count($errors) == 0) {
		$query = "SELECT * FROM Patients WHERE PID = $PID;";
		$results = mysqli_query($db, $query);
		$patient = mysqli_fetch_assoc($results);
		
		$docquery = "SELECT * FROM ServiceProviders WHERE ProvID = $ProvID;";
		$docresults = mysqli_query($db, $docquery);
		$docuser = mysqli_fetch_assoc($docresults);
		
		mail("$patient[Email]", "$subject", "Dr. $docuser[Name]: \n\n" . $content);
		array_push($errors, "Email sent.");
	}
}

// DRUG REFERRAL
if (isset($_POST['service_diagnosis'])) {
	$ServiceID = mysqli_real_escape_string($db, $_POST['service_id']);
	$Description = mysqli_real_escape_string($db, $_POST['Diagnosis']);
	$ProvID = $_SESSION['ProvID'];

	if (empty($ServiceID) or $ServiceID == "Select") {
		array_push($errors, "A Service ID is required");
	}
	if (empty($Description)) {
		array_push($errors, "A Diagnosis is required");
	}

  if (count($errors) == 0) {
  	$query = "SELECT DISTINCT * FROM Diagnosis WHERE ServiceID = $ServiceID LIMIT 1;";
	$results = mysqli_query($db, $query);
	$service = mysqli_fetch_assoc($results);
  	if (mysqli_num_rows($results) > 0) {
		$update_query = "UPDATE Diagnosis
				SET Description = '$Description'
				WHERE ServiceID = $ServiceID";
		mysqli_query($db, $update_query);
		array_push($errors, "Diagnosis Change Submitted");
	}
	else
	{
  		$insert_query = "INSERT INTO Diagnosis (ServiceID, Description)
				VALUES ($ServiceID, '$Description');";
		mysqli_query($db, $insert_query);
		array_push($errors, "New Diagnosis Submitted");
  	}
  }
}

?>