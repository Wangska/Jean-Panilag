<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        .add-btn {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .add-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Account List</h2>
        <?php
        $db = mysqli_connect("localhost", "root", "", "bank");
        if ($db) //if connection is ok
        {

            // Check if the form is submitted for multiple deletions
            if (isset($_POST["BtnDelete"])) {
                // Ensure at least one checkbox is selected
                if (!empty($_POST["delete_ids"])) {
                    $delete_ids = implode(",", $_POST["delete_ids"]);
                    $sql = "DELETE FROM account WHERE id IN ($delete_ids)";
                    $query = mysqli_query($db, $sql);

                    if ($query) {
                        echo "<p style='color:blue;'><b>Selected records were deleted successfully...</b></p>";
                    } else {
                        echo "<p style='color:red;'>Something went wrong in your query...</p>";
                    }
                } else {
                    echo "<p style='color:red;'>Please select at least one record to delete...</p>";
                }
            }

            $sql = "SELECT * FROM account ORDER BY account_no";

            $records = mysqli_query($db, $sql);

            if (mysqli_num_rows($records) > 0) {
                echo "<form method='POST' action='listall.php'>";
                echo "<table>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>Seq#</th>";
                echo "<th>Account Number</th>";
                echo "<th>Account Name</th>";
                echo "<th>Balance</th>";
                echo "<th>Status</th>";
                echo "<th>Delete</th>";
                echo "<th>Edit</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                $sequence = 1;
                while ($rec = mysqli_fetch_array($records)) {
                    echo "<tr>";
                    echo "<td>$sequence.</td>";
                    echo "<td>" . $rec["account_no"] . "</td>";
                    echo "<td>" . $rec["account_name"] . "</td>";
                    echo "<td>" . $rec["balance"] . "</td>";
                    echo "<td>" . $rec["status"] . "</td>";
                    echo "<td><input type='checkbox' name='delete_ids[]' value='" . $rec["id"] . "'></td>";
                    echo "<td><a href='edit.php?record=" . $rec["id"] . "'>Edit</a></td>";
                    echo "</tr>";

                    $sequence = $sequence + 1;
                }

                echo "</tbody>";
                echo "</table>";
                echo "<br>";
                echo "<button type='submit' name='BtnDelete' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete selected records?\");'>Delete Selected</button>";

                echo "</form>";
            } else {
                echo "<p style='color:red;'>No records found...</p>";
            }
            mysqli_close($db);
        } else {
            echo "<p style='color:red;'>Error connecting to the database...</p>";
        }
        ?>
        <form action="create.php">
           <br> <button type="submit" class="add-btn">Add Another Account</button>
        </form>
    </div>
</body>
</html>
