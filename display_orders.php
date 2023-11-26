<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Management </title>
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
    <?php
    // Oracle database connection details
    $tns = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = navydb.artg.arizona.edu)(PORT = 1521))(CONNECT_DATA = (SERVER = dedicated)(SERVICE_NAME = COMPDB)))";
    $username = "mis531groupS1E";
    $password = "qvQTYAS58$(H:qG";

    // Function to establish a database connection
    function connectDB() {
        global $tns, $username, $password;
        return oci_connect($username, $password, $tns);
    }

    // Function to fetch data from the 'orders' table
    function fetchOrderData() {
        $conn = connectDB();
        if ($conn) {
            $query = "SELECT * FROM orders ORDER BY orderid";
            $stmt = oci_parse($conn, $query);
            oci_execute($stmt);

            $orderData = array();
            while ($row = oci_fetch_assoc($stmt)) {
                $orderData[] = $row;
            }

            oci_free_statement($stmt);
            oci_close($conn);

            return $orderData;
        } else {
            return array();
        }
    }

    // Insert new order data if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['insert'])) {
        $conn = connectDB();
        if ($conn) {
            // Retrieve form data
            $orderID = $_POST["orderID"];
            $orderTotalValue = $_POST["orderTotalValue"];
            $expectedDeliveryDate = $_POST["expectedDeliveryDate"];
            $orderDate = $_POST["orderDate"];
            $orderType = $_POST["orderType"];
            $customerID = $_POST["customerID"];

            // Prepare and execute the SQL query for inserting data into the orders table
            $query = "INSERT INTO orders (OrderID, OrderTotalValue, ExpectedDeliveryDate, OrderDate, OrderType, CustomerID) 
                      VALUES (:orderID, :orderTotalValue, :expectedDeliveryDate, :orderDate, :orderType, :customerID)";

            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':orderID', $orderID);
            oci_bind_by_name($stmt, ':orderTotalValue', $orderTotalValue);
            oci_bind_by_name($stmt, ':expectedDeliveryDate', $expectedDeliveryDate);
            oci_bind_by_name($stmt, ':orderDate', $orderDate);
            oci_bind_by_name($stmt, ':orderType', $orderType);
            oci_bind_by_name($stmt, ':customerID', $customerID);

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
            $orderID = $_POST["orderID"];

            $query = "DELETE FROM orders WHERE OrderID = :orderID";

            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':orderID', $orderID);

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
            $orderID = $_POST["orderID"];

            // Construct the dynamic UPDATE query
            $updateValues = array();
            if (!empty($_POST["orderTotalValue"])) {
                $updateValues[] = "OrderTotalValue = :orderTotalValue";
                $orderTotalValue = $_POST["orderTotalValue"];
            }
            if (!empty($_POST["expectedDeliveryDate"])) {
                $updateValues[] = "ExpectedDeliveryDate = :expectedDeliveryDate";
                $expectedDeliveryDate = $_POST["expectedDeliveryDate"];
            }
            if (!empty($_POST["orderDate"])) {
                $updateValues[] = "OrderDate = :orderDate";
                $orderDate = $_POST["orderDate"];
            }
            if (!empty($_POST["orderType"])) {
                $updateValues[] = "OrderType = :orderType";
                $orderType = $_POST["orderType"];
            }
            if (!empty($_POST["customerID"])) {
                $updateValues[] = "CustomerID = :customerID";
                $customerID = $_POST["customerID"];
            }

            // Prepare and execute the SQL query for updating specific columns in the orders table
            $query = "UPDATE orders SET " . implode(", ", $updateValues) . " WHERE OrderID = :orderID";

            $stmt = oci_parse($conn, $query);
            if (!empty($orderTotalValue)) oci_bind_by_name($stmt, ':orderTotalValue', $orderTotalValue);
            if (!empty($expectedDeliveryDate)) oci_bind_by_name($stmt, ':expectedDeliveryDate', $expectedDeliveryDate);
            if (!empty($orderDate)) oci_bind_by_name($stmt, ':orderDate', $orderDate);
            if (!empty($orderType)) oci_bind_by_name($stmt, ':orderType', $orderType);
            if (!empty($customerID)) oci_bind_by_name($stmt, ':customerID', $customerID);
            oci_bind_by_name($stmt, ':orderID', $orderID);

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

    // Fetch order data initially
    $orders = fetchOrderData();
    ?>

    <h2>Order Information Dashboard</h2>

    <!-- Display order data in a table -->
    <table>
        <!-- Table headers -->
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Total Value</th>
                <th>Expected Delivery Date</th>
                <th>Order Date</th>
                <th>Order Type</th>
                <th>Customer ID</th>
            </tr>
        </thead>
        <!-- Table data -->
        <tbody>
            <?php foreach ($orders as $order) : ?>
                <tr>
                    <td><?php echo $order['ORDERID']; ?></td>
                    <td><?php echo $order['ORDERTOTALVALUE']; ?></td>
                    <td><?php echo $order['EXPECTEDDELIVERYDATE']; ?></td>
                    <td><?php echo $order['ORDERDATE']; ?></td>
                    <td><?php echo $order['ORDERTYPE']; ?></td>
                    <td><?php echo $order['CUSTOMERID']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Insert, Update, Delete Forms -->
    <h2>Manage Orders</h2>
    <div class="flex-container">
        <!-- Insert Form -->
        <div>
            <h2>Insert Order Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="insert" value="insert">
                <label for="orderID">Order ID:</label>
                <input type="text" id="orderID" name="orderID" required><br>

                <label for="orderTotalValue">Total Value:</label>
                <input type="text" id="orderTotalValue" name="orderTotalValue" required><br>

                <label for="expectedDeliveryDate">Expected Delivery Date:</label>
                <input type="text" id="expectedDeliveryDate" name="expectedDeliveryDate" required><br>

                <label for="orderDate">Order Date:</label>
                <input type="text" id="orderDate" name="orderDate" required><br>

                <label for="orderType">Order Type:</label>
                <input type="text" id="orderType" name="orderType" required><br>

                <label for="customerID">Customer ID:</label>
                <input type="text" id="customerID" name="customerID" required><br><br>

                <input type="submit" value="Insert">
            </form>
        </div>

        <!-- Update Form -->
        <div>
            <h2>Update Order Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="update" value="update">
                <label for="updateOrderID">Order ID*:</label>
                <input type="text" id="updateOrderID" name="orderID" required><br>

                <label for="updateOrderTotalValue">Total Value:</label>
                <input type="text" id="updateOrderTotalValue" name="orderTotalValue"><br>

                <label for="updateExpectedDeliveryDate">Expected Delivery Date:</label>
                <input type="text" id="updateExpectedDeliveryDate" name="expectedDeliveryDate"><br>

                <label for="updateOrderDate">Order Date:</label>
                <input type="text" id="updateOrderDate" name="orderDate"><br>

                <label for="updateOrderType">Order Type:</label>
                <input type="text" id="updateOrderType" name="orderType"><br>

                <label for="updateCustomerID">Customer ID:</label>
                <input type="text" id="updateCustomerID" name="customerID"><br><br>

                <input type="submit" value="Update">
            </form>
        </div>

        <!-- Delete Form -->
        <div>
            <h2>Delete Order Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="delete" value="delete">
                <label for="deleteOrderID">Order ID:</label>
                <input type="text" id="deleteOrderID" name="orderID" required><br>
                <input type="submit" value="Delete">
            </form>
        </div>
    </div>
</body>
</html>
