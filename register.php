<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Patient Registration</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="header">
  	<h2>Register</h2>
  </div>
	
  <form method="post" action="register.php">
  	<?php include('errors.php'); ?>
	<div class="input-group">
  	  <label>First Name</label>
  	  <input type="text" name="firstname" value="<?php echo $firstname; ?>">
  	</div>
	<div class="input-group">
  	  <label>Last Name</label>
  	  <input type="text" name="lastname" value="<?php echo $lastname; ?>">
  	</div>
	<div class="input-group">
  	  <label>Social Security Number</label>
  	  <input type="number" name="ssn" value="<?php echo $ssn; ?>" min="100000000" max="999999999">
  	</div>
	<div class="input-group">
  	  <label>Phone Number</label>
  	  <input type="number" name="phone" value="<?php echo $phone; ?>" min="1000000000" max="9999999999">
  	</div>
	<div class="input-group">
  	  <label>Date of Birth<label>
	  <input type="date" name="DoB" value="<?php echo $dob; ?>">
  	</div>
	<div class="input-group">
  	  <label>Address</label>
  	  <input type="text" name="address" value="<?php echo $address; ?>">
  	</div>
	<br></br>
	<div class="input-group">
  	  <label>Email</label>
  	  <input type="email" name="email" value="<?php echo $email; ?>">
  	</div>
  	<div class="input-group">
  	  <label>Password</label>
  	  <input type="password" name="password_1">
  	</div>
  	<div class="input-group">
  	  <label>Confirm password</label>
  	  <input type="password" name="password_2">
  	</div>
	<br></br>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="reg_user">Register</button>
  	</div>
  	<p>
  		Already a member? <a href="login.php">Sign in</a>
  	</p>
  </form>
</body>
</html>