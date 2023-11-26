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

// Function to fetch data from the 'product' table
function fetchProductData() {
    $conn = connectDB();
    if ($conn) {
        $query = "SELECT * FROM product ORDER BY ProductID";
        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $productData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $productData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $productData;
    } else {
        return array();
    }
}

// Insert new product data if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['insert'])) {
    $conn = connectDB();
    if ($conn) {
        // Retrieve form data
        $productID = $_POST["productID"];
        $productName = $_POST["productName"];
        $unitPrice = $_POST["unitPrice"];
        $description = $_POST["description"];
        $weight = $_POST["weight"];
        $subCategoryID = $_POST["subCategoryID"];

        // Prepare and execute the SQL query for inserting data into the product table
        $query = "INSERT INTO product (ProductID, ProductName, UnitPrice, Description, Weight, SubCategoryID) 
                  VALUES (:productID, :productName, :unitPrice, :description, :weight, :subCategoryID)";

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':productID', $productID);
        oci_bind_by_name($stmt, ':productName', $productName);
        oci_bind_by_name($stmt, ':unitPrice', $unitPrice);
        oci_bind_by_name($stmt, ':description', $description);
        oci_bind_by_name($stmt, ':weight', $weight);
        oci_bind_by_name($stmt, ':subCategoryID', $subCategoryID);

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
        $productID = $_POST["productID"];

        $query = "DELETE FROM product WHERE ProductID = :productID";

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':productID', $productID);

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
        $productID = $_POST["productID"];

        // Construct the dynamic UPDATE query
        $updateValues = array();
        if (!empty($_POST["productName"])) {
            $updateValues[] = "ProductName = :productName";
            $productName = $_POST["productName"];
        }
        if (!empty($_POST["unitPrice"])) {
            $updateValues[] = "UnitPrice = :unitPrice";
            $unitPrice = $_POST["unitPrice"];
        }
        if (!empty($_POST["description"])) {
            $updateValues[] = "Description = :description";
            $description = $_POST["description"];
        }
        if (!empty($_POST["weight"])) {
            $updateValues[] = "Weight = :weight";
            $weight = $_POST["weight"];
        }
        if (!empty($_POST["subCategoryID"])) {
            $updateValues[] = "SubCategoryID = :subCategoryID";
            $subCategoryID = $_POST["subCategoryID"];
        }

        // Prepare and execute the SQL query for updating specific columns in the product table
        $query = "UPDATE product SET " . implode(", ", $updateValues) . " WHERE ProductID = :productID";

        $stmt = oci_parse($conn, $query);
        if (!empty($productName)) oci_bind_by_name($stmt, ':productName', $productName);
        if (!empty($unitPrice)) oci_bind_by_name($stmt, ':unitPrice', $unitPrice);
        if (!empty($description)) oci_bind_by_name($stmt, ':description', $description);
        if (!empty($weight)) oci_bind_by_name($stmt, ':weight', $weight);
        if (!empty($subCategoryID)) oci_bind_by_name($stmt, ':subCategoryID', $subCategoryID);
        oci_bind_by_name($stmt, ':productID', $productID);

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

// Fetch product data initially
$products = fetchProductData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Management </title>
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
    <h2>Product Information Dashboard</h2>

    <!-- Display product data in a table -->
    <table>
        <!-- Table headers -->
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Unit Price</th>
                <th>Description</th>
                <th>Weight</th>
                <th>SubCategory ID</th>
            </tr>
        </thead>
        <!-- Table data -->
        <tbody>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td><?php echo $product['PRODUCTID']; ?></td>
                    <td><?php echo $product['PRODUCTNAME']; ?></td>
                    <td><?php echo $product['UNITPRICE']; ?></td>
                    <td><?php echo $product['DESCRIPTION']; ?></td>
                    <td><?php echo $product['WEIGHT']; ?></td>
                    <td><?php echo $product['SUBCATEGORYID']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Insert, Update, Delete Forms -->
    <h2>Manage Products</h2>
    <div class="flex-container">
        <!-- Insert Form -->
        <div>
            <h2>Insert Product Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="insert" value="insert">
                <label for="productID">Product ID:</label>
                <input type="text" id="productID" name="productID" required><br>

                <label for="productName">Product Name:</label>
                <input type="text" id="productName" name="productName" required><br>

                <label for="unitPrice">Unit Price:</label>
                <input type="text" id="unitPrice" name="unitPrice" required><br>

                <label for="description">Description:</label>
                <input type="text" id="description" name="description" required><br>

                <label for="weight">Weight:</label>
                <input type="text" id="weight" name="weight" required><br>

                <label for="subCategoryID">SubCategory ID:</label>
                <input type="text" id="subCategoryID" name="subCategoryID" required><br><br>

                <input type="submit" value="Insert">
            </form>
        </div>

        <!-- Update Form -->
        <div>
            <h2>Update Product Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="update" value="update">
                <label for="updateProductID">Product ID*:</label>
                <input type="text" id="updateProductID" name="productID" required><br>

                <label for="updateProductName">Product Name:</label>
                <input type="text" id="updateProductName" name="productName"><br>

                <label for="updateUnitPrice">Unit Price:</label>
                <input type="text" id="updateUnitPrice" name="unitPrice"><br>

                <label for="updateDescription">Description:</label>
                <input type="text" id="updateDescription" name="description"><br>

                <label for="updateWeight">Weight:</label>
                <input type="text" id="updateWeight" name="weight"><br>

                <label for="updateSubCategoryID">SubCategory ID:</label>
                <input type="text" id="updateSubCategoryID" name="subCategoryID"><br><br>

                <input type="submit" value="Update">
            </form>
        </div>

        <!-- Delete Form -->
        <div>
            <h2>Delete Product Information</h2>
            <form action="#" method="post">
                <input type="hidden" name="delete" value="delete">
                <label for="deleteProductID">Product ID:</label>
                <input type="text" id="deleteProductID" name="productID" required><br>
                <input type="submit" value="Delete">
            </form>
        </div>
    </div>
</body>
</html>
