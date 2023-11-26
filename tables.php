
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Management System</title>
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
            
                <tr>
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                    <td>4</td>
                    <td>5</td>
                    <td>6</td>
                    <td>7</td>
                </tr>
            
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
 