<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Management </title>
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

    // Function to fetch data from the 'supplier' table
    function fetchSupplierData() {
        $conn = connectDB();
        if ($conn) {
            $query = "SELECT * FROM supplier ORDER BY SupplierID";
            $stmt = oci_parse($conn, $query);
            oci_execute($stmt);

            $supplierData = array();
            while ($row = oci_fetch_assoc($stmt)) {
                $supplierData[] = $row;
            }

            oci_free_statement($stmt);
            oci_close($conn);

            return $supplierData;
        } else {
            return array();
        }
    }

    // Fetch supplier data initially
    $suppliers = fetchSupplierData();
    ?>

    <h2>Supplier Information Dashboard</h2>

    <!-- Display supplier data in a table -->
    <table>
        <!-- Table headers -->
        <thead>
            <tr>
                <th>Supplier ID</th>
                <th>Supplier Name</th>
                <th>Street</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
                <th>Postal Code</th>
                <th>Contact Name</th>
                <th>Contact Designation</th>
            </tr>
        </thead>
        <!-- Table data -->
        <tbody>
            <?php foreach ($suppliers as $supplier) : ?>
                <tr>
                    <td><?php echo $supplier['SUPPLIERID']; ?></td>
                    <td><?php echo $supplier['SUPPLIERNAME']; ?></td>
                    <td><?php echo $supplier['STREET']; ?></td>
                    <td><?php echo $supplier['CITY']; ?></td>
                    <td><?php echo $supplier['STATE']; ?></td>
                    <td><?php echo $supplier['COUNTRY']; ?></td>
                    <td><?php echo $supplier['POSTALCODE']; ?></td>
                    <td><?php echo $supplier['CONTACTNAME']; ?></td>
                    <td><?php echo $supplier['CONTACTDESIGNATION']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Insert, Update, Delete Forms -->
    <h2>Manage Suppliers</h2>
    <div class="flex-container">
        <!-- Insert Form -->
        <div>
            <h2>Insert Supplier Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="insert" value="insert">
                <label for="supplierID">Supplier ID:</label>
                <input type="text" id="supplierID" name="supplierID" required><br>

                <label for="supplierName">Supplier Name:</label>
                <input type="text" id="supplierName" name="supplierName" required><br>

                <label for="street">Street:</label>
                <input type="text" id="street" name="street" required><br>

                <label for="city">City:</label>
                <input type="text" id="city" name="city" required><br>

                <label for="state">State:</label>
                <input type="text" id="state" name="state" required><br>

                <label for="country">Country:</label>
                <input type="text" id="country" name="country" required><br>

                <label for="postalCode">Postal Code:</label>
                <input type="text" id="postalCode" name="postalCode" required><br>

                <label for="contactName">Contact Name:</label>
                <input type="text" id="contactName" name="contactName" required><br>

                <label for="contactDesignation">Contact Designation:</label>
                <input type="text" id="contactDesignation" name="contactDesignation" required><br><br>

                <input type="submit" value="Insert">
            </form>
        </div>

        <!-- Update Form -->
<div>
    <h2>Update Supplier Information</h2>
    <form action="#" method="post">
        <input type="hidden" name="update" value="update">
        <label for="updateSupplierID">Supplier ID*:</label>
        <input type="text" id="updateSupplierID" name="updateSupplierID" required><br>

        <label for="updateSupplierName">Supplier Name:</label>
        <input type="text" id="updateSupplierName" name="updateSupplierName"><br>

        <label for="updateStreet">Street:</label>
        <input type="text" id="updateStreet" name="updateStreet"><br>

        <label for="updateCity">City:</label>
        <input type="text" id="updateCity" name="updateCity"><br>

        <label for="updateState">State:</label>
        <input type="text" id="updateState" name="updateState"><br>

        <label for="updateCountry">Country:</label>
        <input type="text" id="updateCountry" name="updateCountry"><br>

        <label for="updatePostalCode">Postal Code:</label>
        <input type="text" id="updatePostalCode" name="updatePostalCode"><br>

        <label for="updateContactName">Contact Name:</label>
        <input type="text" id="updateContactName" name="updateContactName"><br>

        <label for="updateContactDesignation">Contact Designation:</label>
        <input type="text" id="updateContactDesignation" name="updateContactDesignation"><br><br>

        <input type="submit" value="Update">
    </form>
</div>


        <!-- Delete Form -->
        <div>
            <h2>Delete Supplier Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="delete" value="delete">
                <label for="deleteSupplierID">Supplier ID:</label>
                <input type="text" id="deleteSupplierID" name="deleteSupplierID" required><br>
                <input type="submit" value="Delete">
            </form>
        </div>
    </div>
</body>
</html>
