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

// Function to fetch data from the 'supplyinventory' table
function fetchSupplyInventoryData() {
    $conn = connectDB();
    if ($conn) {
        $query = "SELECT * FROM supplyinventory ORDER BY SupplyInvID";
        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $supplyInventoryData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $supplyInventoryData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $supplyInventoryData;
    } else {
        return array();
    }
}

// Insert new supply inventory data if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['insert'])) {
    $conn = connectDB();
    if ($conn) {
        // Retrieve form data
        $supplyInvID = $_POST["supplyInvID"];
        $inventoryName = $_POST["inventoryName"];
        $unitsInStock = $_POST["unitsInStock"];
        $street = $_POST["street"];
        $city = $_POST["city"];
        $state = $_POST["state"];
        $country = $_POST["country"];
        $postalCode = $_POST["postalCode"];

        // Prepare and execute the SQL query for inserting data into the supplyinventory table
        $query = "INSERT INTO supplyinventory (SupplyInvID, InventoryName, UnitsInStock, Street, City, State, Country, PostalCode) 
                  VALUES (:supplyInvID, :inventoryName, :unitsInStock, :street, :city, :state, :country, :postalCode)";

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':supplyInvID', $supplyInvID);
        oci_bind_by_name($stmt, ':inventoryName', $inventoryName);
        oci_bind_by_name($stmt, ':unitsInStock', $unitsInStock);
        oci_bind_by_name($stmt, ':street', $street);
        oci_bind_by_name($stmt, ':city', $city);
        oci_bind_by_name($stmt, ':state', $state);
        oci_bind_by_name($stmt, ':country', $country);
        oci_bind_by_name($stmt, ':postalCode', $postalCode);

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
        $supplyInvID = $_POST["supplyInvID"];

        $query = "DELETE FROM supplyinventory WHERE SupplyInvID = :supplyInvID";

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':supplyInvID', $supplyInvID);

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
        $supplyInvID = $_POST["supplyInvID"];

        // Construct the dynamic UPDATE query
        $updateValues = array();
        if (!empty($_POST["inventoryName"])) {
            $updateValues[] = "InventoryName = :inventoryName";
            $inventoryName = $_POST["inventoryName"];
        }
        if (!empty($_POST["unitsInStock"])) {
            $updateValues[] = "UnitsInStock = :unitsInStock";
            $unitsInStock = $_POST["unitsInStock"];
        }
        if (!empty($_POST["street"])) {
            $updateValues[] = "Street = :street";
            $street = $_POST["street"];
        }
        if (!empty($_POST["city"])) {
            $updateValues[] = "City = :city";
            $city = $_POST["city"];
        }
        if (!empty($_POST["state"])) {
            $updateValues[] = "State = :state";
            $state = $_POST["state"];
        }
        if (!empty($_POST["country"])) {
            $updateValues[] = "Country = :country";
            $country = $_POST["country"];
        }
        if (!empty($_POST["postalCode"])) {
            $updateValues[] = "PostalCode = :postalCode";
            $postalCode = $_POST["postalCode"];
        }

        // Prepare and execute the SQL query for updating specific columns in the supplyinventory table
        $query = "UPDATE supplyinventory SET " . implode(", ", $updateValues) . " WHERE SupplyInvID = :supplyInvID";

        $stmt = oci_parse($conn, $query);
        if (!empty($inventoryName)) oci_bind_by_name($stmt, ':inventoryName', $inventoryName);
        if (!empty($unitsInStock)) oci_bind_by_name($stmt, ':unitsInStock', $unitsInStock);
        if (!empty($street)) oci_bind_by_name($stmt, ':street', $street);
        if (!empty($city)) oci_bind_by_name($stmt, ':city', $city);
        if (!empty($state)) oci_bind_by_name($stmt, ':state', $state);
        if (!empty($country)) oci_bind_by_name($stmt, ':country', $country);
        if (!empty($postalCode)) oci_bind_by_name($stmt, ':postalCode', $postalCode);
        oci_bind_by_name($stmt, ':supplyInvID', $supplyInvID);

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

// Fetch supply inventory data initially
$supplyInventory = fetchSupplyInventoryData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supply Inventory Management  </title>
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
    <h2>Supply Inventory Information Dashboard</h2>

    <!-- Display supply inventory data in a table -->
    <table>
        <!-- Table headers -->
        <thead>
            <tr>
                <th>SupplyInv ID</th>
                <th>Inventory Name</th>
                <th>Units in Stock</th>
                <th>Street</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
                <th>Postal Code</th>
            </tr>
        </thead>
        <!-- Table data -->
        <tbody>
            <?php foreach ($supplyInventory as $supply) : ?>
                <tr>
                    <td><?php echo $supply['SUPPLYINVID']; ?></td>
                    <td><?php echo $supply['INVENTORYNAME']; ?></td>
                    <td><?php echo $supply['UNITSINSTOCK']; ?></td>
                    <td><?php echo $supply['STREET']; ?></td>
                    <td><?php echo $supply['CITY']; ?></td>
                    <td><?php echo $supply['STATE']; ?></td>
                    <td><?php echo $supply['COUNTRY']; ?></td>
                    <td><?php echo $supply['POSTALCODE']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Insert, Update, Delete Forms -->
    <h2>Manage Supply Inventory</h2>
    <div class="flex-container">
        <!-- Insert Form -->
        <div>
            <h2>Insert Supply Inventory Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="insert" value="insert">
                <label for="supplyInvID">SupplyInv ID:</label>
                <input type="text" id="supplyInvID" name="supplyInvID" required><br>

                <label for="inventoryName">Inventory Name:</label>
                <input type="text" id="inventoryName" name="inventoryName" required><br>

                <label for="unitsInStock">Units in Stock:</label>
                <input type="text" id="unitsInStock" name="unitsInStock" required><br>

                <label for="street">Street:</label>
                <input type="text" id="street" name="street" required><br>

                <label for="city">City:</label>
                <input type="text" id="city" name="city" required><br>

                <label for="state">State:</label>
                <input type="text" id="state" name="state" required><br>

                <label for="country">Country:</label>
                <input type="text" id="country" name="country" required><br>

                <label for="postalCode">Postal Code:</label>
                <input type="text" id="postalCode" name="postalCode" required><br><br>

                <input type="submit" value="Insert">
            </form>
        </div>

        <!-- Update Form -->
        <div>
            <h2>Update Supply Inventory Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="update" value="update">
                <label for="updateSupplyInvID">SupplyInv ID*:</label>
                <input type="text" id="updateSupplyInvID" name="supplyInvID" required><br>

                <label for="updateInventoryName">Inventory Name:</label>
                <input type="text" id="updateInventoryName" name="inventoryName"><br>

                <label for="updateUnitsInStock">Units in Stock:</label>
                <input type="text" id="updateUnitsInStock" name="unitsInStock"><br>

                <label for="updateStreet">Street:</label>
                <input type="text" id="updateStreet" name="street"><br>

                <label for="updateCity">City:</label>
                <input type="text" id="updateCity" name="city"><br>

                <label for="updateState">State:</label>
                <input type="text" id="updateState" name="state"><br>

                <label for="updateCountry">Country:</label>
                <input type="text" id="updateCountry" name="country"><br>

                <label for="updatePostalCode">Postal Code:</label>
                <input type="text" id="updatePostalCode" name="postalCode"><br><br>

                <input type="submit" value="Update">
            </form>
        </div>

        <!-- Delete Form -->
        <div>
            <h2>Delete Supply Inventory Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="delete" value="delete">
                <label for="deleteSupplyInvID">SupplyInv ID:</label>
                <input type="text" id="deleteSupplyInvID" name="supplyInvID" required><br>
                <input type="submit" value="Delete">
            </form>
        </div>
    </div>
</body>
</html>
