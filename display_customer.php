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

// Function to fetch data from the 'customer' table
function fetchCustomerData() {
    $conn = connectDB();
    if ($conn) {
        $query = "SELECT * FROM customer ORDER BY CustomerID";
        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $customerData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $customerData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $customerData;
    } else {
        return array();
    }
}

// Insert new customer data if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['insert'])) {
    $conn = connectDB();
    if ($conn) {
        // Retrieve form data
        $customerID = $_POST["customerID"];
        $customerName = $_POST["customerName"];
        $contactNumber = $_POST["contactNumber"];
        $emailID = $_POST["emailID"];
        $creditLimit = $_POST["creditLimit"];
        $shippingAddress = $_POST["shippingAddress"];
        $paymentID = $_POST["paymentID"];

        // Prepare and execute the SQL query for inserting data into the customer table
        $query = "INSERT INTO customer (CustomerID, CustomerName, ContactNumber, EmailID, CreditLimit, ShippingAddress, PaymentID) 
                  VALUES (:customerID, :customerName, :contactNumber, :emailID, :creditLimit, :shippingAddress, :paymentID)";

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':customerID', $customerID);
        oci_bind_by_name($stmt, ':customerName', $customerName);
        oci_bind_by_name($stmt, ':contactNumber', $contactNumber);
        oci_bind_by_name($stmt, ':emailID', $emailID);
        oci_bind_by_name($stmt, ':creditLimit', $creditLimit);
        oci_bind_by_name($stmt, ':shippingAddress', $shippingAddress);
        oci_bind_by_name($stmt, ':paymentID', $paymentID);

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

// Deletion Logic
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete'])) {
    $conn = connectDB();
    if ($conn) {
        $customerID = $_POST["customerID"];

        $query = "DELETE FROM customer WHERE CustomerID = :customerID";

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':customerID', $customerID);

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

// Update Logic
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    $conn = connectDB();
    if ($conn) {
        // Retrieve form data
        $customerID = $_POST["customerID"];

        // Construct the dynamic UPDATE query
        $updateValues = array();
        if (!empty($_POST["customerName"])) {
            $updateValues[] = "CustomerName = :customerName";
            $customerName = $_POST["customerName"];
        }
        if (!empty($_POST["contactNumber"])) {
            $updateValues[] = "ContactNumber = :contactNumber";
            $contactNumber = $_POST["contactNumber"];
        }
        if (!empty($_POST["emailID"])) {
            $updateValues[] = "EmailID = :emailID";
            $emailID = $_POST["emailID"];
        }
        if (!empty($_POST["creditLimit"])) {
            $updateValues[] = "CreditLimit = :creditLimit";
            $creditLimit = $_POST["creditLimit"];
        }
        if (!empty($_POST["shippingAddress"])) {
            $updateValues[] = "ShippingAddress = :shippingAddress";
            $shippingAddress = $_POST["shippingAddress"];
        }
        if (!empty($_POST["paymentID"])) {
            $updateValues[] = "PaymentID = :paymentID";
            $paymentID = $_POST["paymentID"];
        }

        // Prepare and execute the SQL query for updating specific columns in the customer table
        $query = "UPDATE customer SET " . implode(", ", $updateValues) . " WHERE CustomerID = :customerID";

        $stmt = oci_parse($conn, $query);
        if (!empty($customerName)) oci_bind_by_name($stmt, ':customerName', $customerName);
        if (!empty($contactNumber)) oci_bind_by_name($stmt, ':contactNumber', $contactNumber);
        if (!empty($emailID)) oci_bind_by_name($stmt, ':emailID', $emailID);
        if (!empty($creditLimit)) oci_bind_by_name($stmt, ':creditLimit', $creditLimit);
        if (!empty($shippingAddress)) oci_bind_by_name($stmt, ':shippingAddress', $shippingAddress);
        if (!empty($paymentID)) oci_bind_by_name($stmt, ':paymentID', $paymentID);
        oci_bind_by_name($stmt, ':customerID', $customerID);

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

// Fetch customer data initially
$customerData = fetchCustomerData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Management</title>
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
    <h2>Customer Information Dashboard</h2>

    <!-- Display customer data in a table -->
    <table>
        <!-- Table headers -->
        <thead>
            <tr>
                <th>CustomerID</th>
                <th>Customer Name</th>
                <th>Contact Number</th>
                <th>Email ID</th>
                <th>Credit Limit</th>
                <th>Shipping Address</th>
                <th>Payment ID</th>
            </tr>
        </thead>
        <!-- Table data -->
        <tbody>
            <?php foreach ($customerData as $customer) : ?>
                <tr>
                    <td><?php echo $customer['CUSTOMERID']; ?></td>
                    <td><?php echo $customer['CUSTOMERNAME']; ?></td>
                    <td><?php echo $customer['CONTACTNUMBER']; ?></td>
                    <td><?php echo $customer['EMAILID']; ?></td>
                    <td><?php echo $customer['CREDITLIMIT']; ?></td>
                    <td><?php echo $customer['SHIPPINGADDRESS']; ?></td>
                    <td><?php echo $customer['PAYMENTID']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Insert, Update, Delete Forms -->
    <h2>Manage Customers</h2>
    <div class="flex-container">
        <!-- Insert Form -->
        <div>
            <h2>Insert Customer Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="insert" value="insert">
                <label for="customerID">CustomerID:</label>
                <input type="text" id="customerID" name="customerID" required><br>

                <label for="customerName">Customer Name:</label>
                <input type="text" id="customerName" name="customerName" required><br>

                <label for="contactNumber">Contact Number:</label>
                <input type="text" id="contactNumber" name="contactNumber" required><br>

                <label for="emailID">Email ID:</label>
                <input type="text" id="emailID" name="emailID" required><br>

                <label for="creditLimit">Credit Limit:</label>
                <input type="text" id="creditLimit" name="creditLimit" required><br>

                <label for="shippingAddress">Shipping Address:</label>
                <input type="text" id="shippingAddress" name="shippingAddress" required><br>

                <label for="paymentID">Payment ID:</label>
                <input type="text" id="paymentID" name="paymentID" required><br><br>

                <input type="submit" value="Insert">
            </form>
        </div>

        <!-- Update Form -->
        <div>
            <h2>Update Customer Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="update" value="update">
                <label for="updateCustomerID">CustomerID*:</label>
                <input type="text" id="updateCustomerID" name="customerID" required><br>

                <label for="updateCustomerName">Customer Name:</label>
                <input type="text" id="updateCustomerName" name="customerName"><br>

                <label for="updateContactNumber">Contact Number:</label>
                <input type="text" id="updateContactNumber" name="contactNumber"><br>

                <label for="updateEmailID">Email ID:</label>
                <input type="text" id="updateEmailID" name="emailID"><br>

                <label for="updateCreditLimit">Credit Limit:</label>
                <input type="text" id="updateCreditLimit" name="creditLimit"><br>

                <label for="updateShippingAddress">Shipping Address:</label>
                <input type="text" id="updateShippingAddress" name="shippingAddress"><br>

                <label for="updatePaymentID">Payment ID:</label>
                <input type="text" id="updatePaymentID" name="paymentID"><br><br>

                <input type="submit" value="Update">
            </form>
        </div>

        <!-- Delete Form -->
        <div>
            <h2>Delete Customer Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="delete" value="delete">
                <label for="deleteCustomerID">CustomerID:</label>
                <input type="text" id="deleteCustomerID" name="customerID" required><br>
                <input type="submit" value="Delete">
            </form>
        </div>
    </div>
</body>
</html>
