<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-top: 0;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create Account</h2>
        <?php
			$var_AccountNo = "";
			$var_AccName = "";
			$var_balance = "";
			$var_status = "";
			
			$errors = ""; 
			
			if(isset($_POST["BtnSaveAccount"]))
			{
				
				$var_AccountNo = trim($_POST["TxtAccountNo"]);
				$var_AccName = trim($_POST["TxtAccName"]);
				$var_balance = trim($_POST["TxtBalance"]);
				$var_status = $_POST["CboStatus"];				
			
				
			
				if(! is_numeric($var_AccountNo))
				{
					$errors = $errors . "<p style='color:red;'>Account number must be a number.</p>";

					$var_AccountNo = "";
				}
				


				if (
					strlen($var_AccountNo) !== 10
					
					
				) {
					$errors .= "<p style='color:red;'>Account number must be 10 numbers.</p>";
					$var_AccountNo = "";
				}

					
				$db = mysqli_connect("localhost", "root", "", "bank");
				$sql = "SELECT account_no FROM account WHERE account_no = '$var_AccountNo'";
				$result = mysqli_query($db, $sql);
				
				if (mysqli_num_rows($result) > 0) {
					$errors .= "<p style='color:red;'>Account number '$var_AccountNo' is already taken.</p>";
					$var_AccountNo = "";
				}
				
				if(is_numeric($var_AccName))
				{
					$errors = $errors . "<p style='color:red;'>Account name must not include numbers.</p>";
					$var_AccName = "";
				}
				
				/*if (!ctype_alpha(str_replace(' ',' ', $var_AccName))) {
					$errors .= "<p style='color:red;'>Account name must only contain letters.</p>";
					$var_AccName = "";
				}*/
				if (!ctype_alpha(str_replace(' ', '', $var_AccName)) || !ctype_alnum(str_replace(' ', '', $var_AccName))) {
					$errors .= "<p style='color:red;'>Invalid input. Account name must be a string value.</p>";
					$var_AccName = "";
				}
				if(! is_numeric($var_balance))
				{
					$errors = $errors . "<p style='color:red;'>Balance must be a number.</p>";
					$var_balance = "";
				}
				
				if(doubleval($var_balance) < 0 || doubleval($var_balance) > 999999999)	
				{
					$errors = $errors . "<p style='color:red;'>Price value must be between 1.00 and 999,999.00.</p>";
					$var_balance = "";
				}
				if($var_status === "D" && $var_balance != 0) {
					$errors .= "<p style='color:red;'>Dormants should not have any balance.</p>";
					$var_balance = "";
				}
				 if($var_status === "A" && $var_balance == 0) {
					$errors .= "<p style='color:red;'>Active accounts cannot have a balance of 0.</p>";
					$var_balance = "";
				}
					
				
				if($errors != "") 
				{
					echo $errors;
				} 
				else 
				{
				
					$db = mysqli_connect("localhost", "root", "", "bank");
					if($db)
					{
						
						$sql = "insert into account (account_no, account_name, balance, status) values 
								('$var_AccountNo', '$var_AccName', ".$var_balance.", '".$var_status."') ";
								
						
						$query = mysqli_query($db, $sql);
						if($query)
						{
							echo "<p style='color:blue;'><b>Record was saved successfully...</b></p>";
							
							$var_AccountNo = "";
							$var_AccName = "";
							$var_balance = "";
							$var_status = "";							
						}
						else 
						{
							echo "<p style='color:red;'>Something went wrong in your query...</p>";
						}
					}
					else 
					{
						echo "<p style='color:red;'>Error connecting to database sales...</p>";
					}
					
					
					mysqli_close($db);					
				}
			}
		
		
		?>
        <form method="POST" action="create.php">
            <label for="TxtAccountNo">Account No.:</label>
            <input type="text" id="TxtAccountNo" name="TxtAccountNo" maxlength="10" required placeholder="Ex: 2023083452" value="<?php echo $var_AccountNo; ?>">

            <label for="TxtAccName">Account Name:</label>
            <input type="text" id="TxtAccName" name="TxtAccName" required value="<?php echo $var_AccName; ?>">

            <label for="TxtBalance">Balance:</label>
            <input type="text" id="TxtBalance" name="TxtBalance" required value="<?php echo $var_balance; ?>">

            <label for="CboStatus">Status:</label>
            <select id="CboStatus" name="CboStatus">
                <option value="Y" <?php if($var_status == "Y") echo "selected"; ?>>Active</option>
                <option value="N" <?php if($var_status == "N") echo "selected"; ?>>Dormant</option>
            </select>

            <button type="submit" name="BtnSaveAccount">Save Account</button>
            <button type="reset">Clear</button>
            <a href="listall.php">Account List</a>
        </form>
    </div>
</body>
</html>
