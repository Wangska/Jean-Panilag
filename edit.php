<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-top: 0;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        button[type="submit"], a {
            background-color: #4caf50;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }
        button[type="submit"]:hover, a:hover {
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
        <h2>Edit Account</h2>
        <?php
        $id = "";
        $var_AccountNo = "";
        $var_AccName = "";
        $var_balance = "";
        $var_status = "";

        $errors = ""; //variable to hold all errors

        if(isset($_GET["record"]))
        {
            $id = $_GET["record"]; //get the value of the record from the URL parameter

            //query the record again, only 1 record
            $db = mysqli_connect("localhost", "root", "", "bank");
            $sql = "SELECT * FROM account WHERE id = $id";
            $record = mysqli_query($db, $sql);

            //if the record exists, assign all its values to PHP variables...
            if(mysqli_num_rows($record) > 0)
            {
                //get the 1 record only
                $rec = mysqli_fetch_array($record);

                //assign to PHP variables
                $var_AccountNo = $rec["account_no"];
                $var_AccName = $rec["account_name"];
                $var_balance = $rec["balance"];
                $var_status = $rec["status"];
            }
            else
            {
                echo "<p style='color:red;'>Record is no longer existing...</p>";
            }
        }

        if(isset($_POST["BtnSaveAccount"]))
        {
            //get all inputs including the ID
            $id = $_POST["TxtID"];
            $var_AccountNo = trim($_POST["TxtAccountNo"]);
            $var_AccName = trim($_POST["TxtAccName"]);
            $var_balance = trim($_POST["TxtBalance"]);
            $var_status = $_POST["CboStatus"];

            //error trappings
            if(! is_numeric($var_AccountNo))
            {
                $errors = $errors . "<p style='color:red;'>Account number must be a number.</p>";
                $var_AccountNo = "";
            }

            if (
                strlen($var_AccountNo) !== 10 ||
                substr($var_AccountNo, 4, 2) !== '08' ||
                !is_numeric(substr($var_AccountNo, 6)) ||
                (intval(substr($var_AccountNo, 0, 4)) < 1900) ||
                (intval(substr($var_AccountNo, 0, 4)) > 2023)
            ) {
                $errors .= "<p style='color:red;'>Account number must be in the format YYYY08NNNN, the year must start from 1900, and it should not be above the current year (2023).</p>";
                $var_AccountNo = "";
            }

            if (!empty($rec) && is_array($rec)) {
                // Check if the user is editing the account number
                if ($var_AccountNo !== $rec["account_no"]) {
                    // Account number has been modified, perform validation
                    $db = mysqli_connect("localhost", "root", "", "bank");

                    // Check if the account number is already taken by another account
                    $sql = "SELECT id FROM account WHERE account_no = '$var_AccountNo'";
                    $result = mysqli_query($db, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        if ($row['id'] != $id) {
                            $errors .= "<p style='color:red;'>Account number '$var_AccountNo' is already taken.</p>";
                            $var_AccountNo = "";
                        }
                    }
                }
            }

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
                //if there are no errors, proceed
                //open a connection
                $db = mysqli_connect("localhost", "root", "", "bank");
                if($db)
                {
                    //create the update sql statement
                    $sql = "UPDATE account
                            SET
                            account_no = '".$var_AccountNo."', 
                            account_name = '".$var_AccName."', 
                            balance =
                            ".$var_balance.",
                            status = '".$var_status."'
                            WHERE 
                            id = $id ";
                                        
                    //execute the update sql statement
                    $query = mysqli_query($db, $sql);
                    if($query)
                    {
                        echo "<p style='color:blue;'><b>Record was updated successfully...</b></p>";
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
                
                //always close the connection
                mysqli_close($db);                    
            }
        }
        ?>
        <form method="POST" action="edit.php" class="edit-form">
            <input type="hidden" name="TxtID" value="<?php echo $id; ?>">
            <label for="TxtAccountNo">Account No.:</label>
            <input type="text" name="TxtAccountNo" maxlength="10" required placeholder="Ex: 2023083452" value="<?php echo $var_AccountNo; ?>">
            
            <label for="TxtAccName">Account Name:</label>
            <input type="text" name="TxtAccName" required value="<?php echo $var_AccName; ?>">
            
            <label for="TxtBalance">Balance:</label>
            <input type="text" name="TxtBalance" required value="<?php echo $var_balance; ?>">
            
            <label for="CboStatus">Status:</label>
            <select name="CboStatus">
                <option value="A" <?php if($var_status == "A") echo "selected"; ?>>Active</option>
                <option value="D" <?php if($var_status == "D") echo "selected"; ?>>Dormant</option>
            </select>
            
            <div class="button-group">
                <button type="submit" name="BtnSaveAccount">Save Updates</button>
                <a href="listall.php">Back</a>
            </div>
        </form>
    </div>
</body>
</html>
