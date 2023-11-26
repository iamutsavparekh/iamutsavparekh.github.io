<?php

error_reporting(E_ERROR | E_PARSE);

// Oracle database connection details
$tns = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = navydb.artg.arizona.edu)(PORT = 1521))(CONNECT_DATA = (SERVER = dedicated)(SERVICE_NAME = COMPDB)))";
$username = "mis531groupS1E";
$password = "qvQTYAS58$(H:qG";

// Function to establish a database connection
function connectDB() {
    global $tns, $username, $password;
    return oci_connect($username, $password, $tns);
}

// Function to fetch data from the 'employee' table
function fetchEmployeeData() {
    $conn = connectDB();
    if ($conn) {
        $query = "SELECT * FROM employee ORDER BY employeeID";
        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $employeeData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $employeeData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $employeeData;
    } else {
        return array();
    }
}

// Insert new employee data if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['insert'])) {
    $conn = connectDB();
    if ($conn) {
        // Retrieve form data
        $employeeID = $_POST["employeeID"];
        $email = $_POST["email"];
        $firstName = $_POST["firstName"];
        $lastName = $_POST["lastName"];
        $departmentID = $_POST["departmentID"];
        $employeeType = $_POST["employeeType"];

        // Prepare and execute the SQL query for inserting data into the employee table
        $query = "INSERT INTO employee (EMPLOYEEID, EMAIL, FIRSTNAME, LASTNAME, DEPARTMENTID, EMPLOYEETYPE) 
                  VALUES (:employeeID, :email, :firstName, :lastName, :departmentID, :employeeType)";

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':employeeID', $employeeID);
        oci_bind_by_name($stmt, ':email', $email);
        oci_bind_by_name($stmt, ':firstName', $firstName);
        oci_bind_by_name($stmt, ':lastName', $lastName);
        oci_bind_by_name($stmt, ':departmentID', $departmentID);
        oci_bind_by_name($stmt, ':employeeType', $employeeType);

        $success = oci_execute($stmt);

        if ($success) {
            echo '<script type="text/javascript">alert("Data inserted successfully!");</script>';
        } else {
            echo '<script type="text/javascript">alert("Error Inserting Data!");</script>';
        }        

        oci_free_statement($stmt);
        oci_close($conn);
    }
}
    
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete'])) {
        $conn = connectDB();
        if ($conn) {
            $employeeID = $_POST["employeeID"];
    
            $query = "DELETE FROM employee WHERE EmployeeID = :employeeID";
    
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':employeeID', $employeeID);
    
            $success = oci_execute($stmt);
    
            if ($success) {
                echo '<script type="text/javascript">alert("Data deleted successfully!");</script>';
            } else {
                echo '<script type="text/javascript">alert("Error deleting data.");</script>';
            }            
    
            oci_free_statement($stmt);
            oci_close($conn);
        }
    }
    


// Check if the form is submitted for update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    $conn = connectDB();
    if ($conn) {
        // Retrieve form data
        $employeeID = $_POST["employeeID"];

        // Construct the dynamic UPDATE query
        $updateValues = array();
        if (!empty($_POST["email"])) {
            $updateValues[] = "Email = :email";
            $email = $_POST["email"];
        }
        if (!empty($_POST["firstName"])) {
            $updateValues[] = "FirstName = :firstName";
            $firstName = $_POST["firstName"];
        }
        if (!empty($_POST["lastName"])) {
            $updateValues[] = "LastName = :lastName";
            $lastName = $_POST["lastName"];
        }
        if (!empty($_POST["departmentID"])) {
            $updateValues[] = "DepartmentID = :departmentID";
            $departmentID = $_POST["departmentID"];
        }
        if (!empty($_POST["employeeType"])) {
            $updateValues[] = "EmployeeType = :employeeType";
            $employeeType = $_POST["employeeType"];
        }

        // Prepare and execute the SQL query for updating specific columns in the employee table
        $query = "UPDATE employee SET " . implode(", ", $updateValues) . " WHERE EmployeeID = :employeeID";

        $stmt = oci_parse($conn, $query);
        if (!empty($email)) oci_bind_by_name($stmt, ':email', $email);
        if (!empty($firstName)) oci_bind_by_name($stmt, ':firstName', $firstName);
        if (!empty($lastName)) oci_bind_by_name($stmt, ':lastName', $lastName);
        if (!empty($departmentID)) oci_bind_by_name($stmt, ':departmentID', $departmentID);
        if (!empty($employeeType)) oci_bind_by_name($stmt, ':employeeType', $employeeType);
        oci_bind_by_name($stmt, ':employeeID', $employeeID);

        $success = oci_execute($stmt);

        if ($success) {
            echo '<script type="text/javascript">alert("Data updated successfully!");</script>';
        } else {
            echo '<script type="text/javascript">alert("Error updating data.");</script>';
        }        

        oci_free_statement($stmt);
        oci_close($conn);
    }
    
}

// Fetch employee data initially
$employees = fetchEmployeeData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Management </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
 
        table {
            border-collapse: collapse;
            width: 100%;
        }
 
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
 
        th {
            background-color: lightgray;
            color: black;
        }
 
        .flex-container {
            display: flex;
            justify-content: center;
            /* align-items: center; */
        }
 
        .flex-container > div {
            background-color: lightgray;
            margin: 10px;
            padding: 20px;
            font-size: 20px;
        }
        
    /* Existing styles... */

    /* Update form container */
    .flex-container > div {
        background-color: lightgray;
        margin: 10px;
        padding: 20px;
        font-size: 20px;
        width: 300px; /* Adjust the width as needed */
        box-sizing: border-box; /* Include padding and border in the width */
    }

    /* Style labels for both forms */
    label {
        display: block;
        margin-bottom: 5px;
    }

    /* Style input fields for both forms */
    input {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        box-sizing: border-box;
    }

    /* Style submit buttons for both forms */
    input[type="submit"] {
        background-color: #4CAF50;
        color: white;
        padding: 10px;
        border: none;
        cursor: pointer;
    }

    </style>
</head>
<body>
    <h2>Employee Information Dashboard</h2>

    <!-- Display employee data in a table -->
    <table>
        <!-- Table headers -->
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Email</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Department ID</th>
                <th>Employee Type</th>
            </tr>
        </thead>
        <!-- Table data -->
        <tbody>
            <?php foreach ($employees as $employee) : ?>
                <tr>
                    <td><?php echo $employee['EMPLOYEEID']; ?></td>
                    <td><?php echo $employee['EMAIL']; ?></td>
                    <td><?php echo $employee['FIRSTNAME']; ?></td>
                    <td><?php echo $employee['LASTNAME']; ?></td>
                    <td><?php echo $employee['DEPARTMENTID']; ?></td>
                    <td><?php echo $employee['EMPLOYEETYPE']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Insert Form -->
    <h2>Insert Employee Information</h2>
    <div class="flex-container">
        <!-- Insert Form -->
        <div>
            <h2>Insert Employee Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="insert" value="insert">
                <label for="employeeID">Employee ID:</label>
                <input type="text" id="employeeID" name="employeeID" required><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>

                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required><br>

                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required><br>

                <label for="departmentID">Department ID:</label>
                <input type="text" id="departmentID" name="departmentID" required><br>

                <label for="employeeType">Employee Type:</label>
                <input type="text" id="employeeType" name="employeeType" required><br><br>

                <input type="submit" value="Insert">
            </form>
        </div>

        <!-- Update Form -->
        <div>
            <h2>Update Employee Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="update" value="update">
                <label for="updateEmployeeID">Employee ID*:</label>
                <input type="text" id="updateEmployeeID" name="employeeID" required><br>

                <label for="updateEmail">Email:</label>
                <input type="email" id="updateEmail" name="email"><br>

                <label for="updateFirstName">First Name:</label>
                <input type="text" id="updateFirstName" name="firstName"><br>

                <label for="updateLastName">Last Name:</label>
                <input type="text" id="updateLastName" name="lastName"><br>

                <label for="updateDepartmentID">Department ID:</label>
                <input type="text" id="updateDepartmentID" name="departmentID"><br>

                <label for="updateEmployeeType">Employee Type:</label>
                <input type="text" id="updateEmployeeType" name="employeeType"><br><br>

                <input type="submit" value="Update">
            </form>
        </div>
        <div>
            <h2>Delete Employee Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="delete" value="delete">
                <label for="deleteEmployeeID">Employee ID:</label>
                <input type="text" id="deleteEmployeeID" name="employeeID" required><br>
                <input type="submit" value="Delete">
            </form>
        </div>
    </div>
</body>
</html>
