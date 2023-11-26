<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Oracle database connection details
$tns = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = navydb.artg.arizona.edu)(PORT = 1521))(CONNECT_DATA = (SERVER = dedicated)(SERVICE_NAME = COMPDB)))";
$username = "mis531groupS1E";
$password = "qvQTYAS58$(H:qG";

// Function to establish a database connection
function connectDB() {
    global $tns, $username, $password;
    return oci_connect($username, $password, $tns);
}

// Function to fetch top 3 warehouses by total shipment weight
function fetchTopWarehousesByWeight() {
    $conn = connectDB();
    if ($conn) {
        $query = "SELECT w.WarehouseId, w.WarehouseName, c.CarrierName, SUM(s.ShipmentWeight) AS TotalShipmentWeight
        FROM WAREHOUSE w
        JOIN SHIPMENT s ON w.WarehouseId = s.WarehouseID
        JOIN CARRIER c ON s.CarrierID = c.CarrierID
        GROUP BY w.WarehouseId, w.WarehouseName, c.CarrierName
        ORDER BY TotalShipmentWeight DESC
        FETCH FIRST 3 ROWS ONLY
        ";

        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $warehouseData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $warehouseData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $warehouseData;
    } else {
        return array();
    }
}

// Function to fetch data based on the second provided query
function fetchSecondQueryData() {
    $conn = connectDB();
    if ($conn) {
        $query = "WITH ShipmentCTE AS (
                    SELECT 
                        carrierid,
                        TO_DATE(ShipmentDate, 'DD-MON-YYYY') AS ShipmentDate,
                        LEAD(TO_DATE(ShipmentDate, 'DD-MON-YYYY')) OVER (PARTITION BY carrierid ORDER BY TO_DATE(ShipmentDate, 'DD-MON-YYYY')) AS NextShipmentDate
                    FROM 
                        SHIPMENT  
                )
                SELECT 
                    c.CarrierName,
                    COUNT(s.ShipmentID) AS ShipmentCount,
                    MIN(s.ShipmentDate) AS CURRENT_SHIPMENT_DATE,
                    TO_CHAR(MAX(sc.NextShipmentDate), 'DD-MON-YYYY') AS Next_Shipment_Date,
                    COALESCE(TO_CHAR((MAX(sc.NextShipmentDate) - MIN(TO_DATE(s.ShipmentDate, 'DD-MON-YYYY'))), '99999'), '') AS Days_Between_Shipments
                FROM 
                    ShipmentCTE sc
                JOIN 
                    carrier c ON sc.carrierid = c.carrierid
                LEFT JOIN 
                    SHIPMENT s ON sc.carrierid = s.carrierid AND sc.NextShipmentDate = TO_DATE(s.ShipmentDate, 'DD-MON-YYYY')
                GROUP BY 
                    c.CarrierName
                ORDER BY 
                    c.CarrierName";

        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $modifiedQueryData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $modifiedQueryData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $modifiedQueryData;
    } else {
        return array();
    }
}

// Function to fetch data based on the third provided query
function fetchThirdQueryData() {
    $conn = connectDB();
    if ($conn) {
        $query = "WITH SalesData AS (
                    SELECT
                        s.EmployeeID,
                        e.FirstName || ' ' || e.LastName AS SalesRepresentative,
                        SUM(o.OrderTotalValue) AS TotalSalesValue
                    FROM
                        OFFLINEORDER s
                    JOIN
                        Orders o ON s.OrderID = o.OrderID
                    JOIN
                        SALESREPRESENTATIVE sr ON s.EmployeeID = sr.EmployeeID
                    JOIN
                        EMPLOYEE e ON sr.EmployeeID = e.EmployeeID
                    GROUP BY
                        s.EmployeeID, e.FirstName, e.LastName
                )
                SELECT
                    EmployeeID,
                    SalesRepresentative,
                    TotalSalesValue
                FROM
                    SalesData
                ORDER BY
                    TotalSalesValue DESC";

        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $modifiedQueryData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $modifiedQueryData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $modifiedQueryData;
    } else {
        return array();
    }
}

// Function to fetch data for the new SQL query
function fetchFourthQueryData() {
    $conn = connectDB();
    if ($conn) {
        $query = "SELECT
        c.CustomerID,
        c.CustomerName,
        COUNT(o.OrderID) AS TotalOrders,
        CASE
            WHEN COUNT(o.OrderID) < AVG(COUNT(o.OrderID)) OVER () - STDDEV(COUNT(o.OrderID)) OVER () THEN 'Low Order Frequency'
            WHEN COUNT(o.OrderID) > AVG(COUNT(o.OrderID)) OVER () + STDDEV(COUNT(o.OrderID)) OVER () THEN 'High Order Frequency'
            ELSE 'Normal Order Frequency'
        END AS OrderFrequencyStatus
    FROM
        CUSTOMER c
    JOIN
        ORDERS o ON c.CustomerID = o.CustomerID
    GROUP BY
        c.CustomerID, c.CustomerName
    ORDER BY
        TotalOrders DESC
    ";

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

function fetchFifthQueryData() {
    $conn = connectDB();
    if ($conn) {
        $query = "SELECT
                    P.ProductID,
                    P.ProductName,
                    C.CategoryName,
                    S.SubCategoryName,
                    COUNT(OD.OrderID) AS NumberOfOrders
                FROM
                    PRODUCT P
                JOIN
                    SUBCATEGORY S ON P.SubCategoryID = S.SubCategoryID
                JOIN
                    CATEGORY C ON S.CategoryID = C.CategoryID
                LEFT JOIN
                    ORDERDETAILS OD ON P.ProductID = OD.ProductID
                GROUP BY
                    P.ProductID, P.ProductName, C.CategoryName, S.SubCategoryName
                ORDER BY
                    NumberOfOrders DESC";

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

function fetchSixthQueryData() {
    $conn = connectDB();
    if ($conn) {
        $query = "SELECT w.WarehouseId, w.WarehouseName, round(((SUM(w.UnitsInStock) / w.Capacity) * 100),2) AS StockPercentage
        FROM WAREHOUSE w
        GROUP BY w.WarehouseId, w.WarehouseName, w.Capacity
        ";

        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $warehouseStockData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $warehouseStockData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $warehouseStockData;
    } else {
        return array();
    }
}
function fetchSeventhQueryData() {
    $conn = connectDB();
    if ($conn) {
        $query = "SELECT
                    c.CustomerID,
                    c.CustomerName,
                    c.CreditLimit,
                    SUM(o.OrderTotalValue) AS TotalOrders
                FROM
                    CUSTOMER c
                JOIN
                    ORDERS o ON c.CustomerID = o.CustomerID
                GROUP BY
                    c.CustomerID, c.CustomerName, c.CreditLimit
                HAVING
                    SUM(o.OrderTotalValue) > c.CreditLimit";

        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $customerCreditData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $customerCreditData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $customerCreditData;
    } else {
        return array();
    }
}
function fetchEighthQueryData() {
    $conn = connectDB();
    if ($conn) {
        $query = "SELECT
                    p.ProductID,
                    p.ProductName,
                    AVG(r.Rating) AS AvgRating
                FROM
                    PRODUCT p
                JOIN
                    REVIEWDETAILS r ON p.ProductID = r.ProductID
                GROUP BY
                    p.ProductID, p.ProductName
                ORDER BY
                    AvgRating DESC
                FETCH FIRST 3 ROWS ONLY";

        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $productRatingData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $productRatingData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $productRatingData;
    } else {
        return array();
    }
}
function fetchNinthQueryData() {
    $conn = connectDB();
    if ($conn) {
        $query = "SELECT
                    s.SupplierID,
                    s.SupplierName,
                    SUM(o.TotalValue) AS TotalOrderValue
                FROM
                    SUPPLIER s
                JOIN
                    SUPPLYORDER o ON s.SupplierID = o.SupplierID
                GROUP BY
                    s.SupplierID, s.SupplierName
                ORDER BY
                    TotalOrderValue DESC
                FETCH FIRST 5 ROWS ONLY";

        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $supplierOrderData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $supplierOrderData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $supplierOrderData;
    } else {
        return array();
    }
}

function fetchTenthQueryData() {
    $conn = connectDB();
    if ($conn) {
        $query = "WITH MonthlyProductSales AS (
                    SELECT
                        p.ProductID,
                        p.ProductName,
                        TO_CHAR(od.OrderDate, 'YYYY-MM') AS OrderMonth,
                        SUM(od.OrderItems) AS MonthlySales
                    FROM
                        PRODUCT p
                    LEFT JOIN
                        ORDERDETAILS od ON p.ProductID = od.ProductID
                    GROUP BY
                        p.ProductID, p.ProductName, TO_CHAR(od.OrderDate, 'YYYY-MM')
                ),
                SalesFluctuationCTE AS (
                    SELECT
                        ProductID,
                        ProductName,
                        OrderMonth,
                        MonthlySales,
                        LAG(MonthlySales) OVER (PARTITION BY ProductID ORDER BY OrderMonth) AS PreviousMonthSales,
                        CASE
                            WHEN LAG(MonthlySales) OVER (PARTITION BY ProductID ORDER BY OrderMonth) IS NOT NULL
                            THEN MonthlySales - LAG(MonthlySales) OVER (PARTITION BY ProductID ORDER BY OrderMonth)
                            ELSE NULL
                        END AS SalesFluctuation
                    FROM
                        MonthlyProductSales
                )
                 
                SELECT
                    ProductID,
                    ProductName,
                    OrderMonth,
                    MonthlySales,
                    PreviousMonthSales,
                    SalesFluctuation
                FROM
                    SalesFluctuationCTE
                WHERE
                    SalesFluctuation IS NOT NULL
                ORDER BY
                    ProductID, OrderMonth";

        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $modifiedQueryData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $modifiedQueryData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $modifiedQueryData;
    } else {
        return array();
    }
}

function fetchEleventhQueryData() {
    $conn = connectDB(); // Establish database connection

    if ($conn) {
        $query = "WITH RankedProducts AS (
                    SELECT
                        p.ProductID,
                        p.ProductName,
                        c.CategoryName,
                        od.OrderItems,
                        RANK() OVER (PARTITION BY c.CategoryID ORDER BY od.OrderItems DESC) AS SalesRank
                    FROM
                        PRODUCT p
                    JOIN
                        SUBCATEGORY sc ON p.SubCategoryID = sc.SubCategoryID
                    JOIN
                        CATEGORY c ON sc.CategoryID = c.CategoryID
                    JOIN
                        ORDERDETAILS od ON p.ProductID = od.ProductID
                 )
                 
                 SELECT
                     ProductID,
                     ProductName,
                     CategoryName,
                     OrderItems
                 FROM
                     RankedProducts
                 WHERE
                     SalesRank = 1";

        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $modifiedQueryData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $modifiedQueryData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $modifiedQueryData;
    } else {
        return array();
    }
}

function fetchThirteenthQueryData() {
    $conn = connectDB();
    if ($conn) {
        $query = "SELECT 
                    p.ProductName,
                    SUM(ODTotalValue) AS TotalSales,
                    AVG(Rating) AS AvgRating,
                    CASE WHEN w1.Discontinued = 1 THEN 'Discontinued'
                         ELSE 'Not discontinued' END AS DiscontinuedStatus
                FROM PRODUCT p
                JOIN ORDERDETAILS od ON p.ProductID = od.ProductID
                JOIN ORDERS o ON od.OrderID = o.OrderID
                JOIN WAREHOUSINGDETAILS w ON p.ProductID = w.ProductId
                JOIN WAREHOUSE w1 ON w.warehouseid = w1.warehouseid
                JOIN REVIEWDETAILS rd ON p.ProductID = rd.ProductID
                GROUP BY p.ProductName, w1.discontinued";

        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $productDetailsData = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $productDetailsData[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        return $productDetailsData;
    } else {
        return array();
    }
}

// Fetch data for the thirteenth query
$thirteenthQueryData = fetchThirteenthQueryData();
$eleventhQueryData = fetchEleventhQueryData();
$tenthQueryData = fetchTenthQueryData();
$ninthQueryData = fetchNinthQueryData();
$eighthQueryData = fetchEighthQueryData();
$seventhQueryData = fetchSeventhQueryData();
$sixthQueryData = fetchSixthQueryData();
$fifthQueryData = fetchFifthQueryData();    
$topWarehouses = fetchTopWarehousesByWeight();
$secondQueryData = fetchSecondQueryData();
$thirdQueryData = fetchThirdQueryData();
$fourthQueryData = fetchFourthQueryData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nestle Insights</title>
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
    <h2>New Page - Nestle Dashboard</h2>
    <table>
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Total Sales</th>
            <th>Average Rating</th>
            <th>Discontinued Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($thirteenthQueryData as $productDetails) : ?>
            <tr>
                <td><?php echo $productDetails['PRODUCTNAME']; ?></td>
                <td><?php echo $productDetails['TOTALSALES']; ?></td>
                <td><?php echo $productDetails['AVGRATING']; ?></td>
                <td><?php echo $productDetails['DISCONTINUEDSTATUS']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    <br>
    <table>
    <thead>
        <tr>
            <th>Employee ID</th>
            <th>Sales Representative</th>
            <th>Total Sales Value</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($thirdQueryData as $data) : ?>
            <tr>
                <td><?php echo $data['EMPLOYEEID']; ?></td>
                <td><?php echo $data['SALESREPRESENTATIVE']; ?></td>
                <td><?php echo $data['TOTALSALESVALUE']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br>

    <!-- Second Table: Output of the Second Provided SQL Query -->
    <table>
    <thead>
        <tr>
            <th>Carrier Name</th>
            <th>Shipment Count</th>
            <th>Current Shipment Date</th>
            <th>Next Shipment Date</th>
            <th>Days Between Shipments</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($secondQueryData as $data) : ?>
            <tr>
                <td><?php echo $data['CARRIERNAME']; ?></td>
                <td><?php echo $data['SHIPMENTCOUNT']; ?></td>
                <td><?php echo $data['CURRENT_SHIPMENT_DATE']; ?></td>
                <td><?php echo $data['NEXT_SHIPMENT_DATE']; ?></td>
                <td><?php echo $data['DAYS_BETWEEN_SHIPMENTS']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br>
    <!-- Third Table: Output of the Third Provided SQL Query -->
    <table>
        
        <thead>
            <tr>
                <th>Warehouse ID</th>
                <th>Warehouse Name</th>
                <th>Carrier Name</th>
                <th>Total Shipment Weight</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topWarehouses as $warehouse) : ?>
                <tr>
                    <td><?php echo $warehouse['WAREHOUSEID']; ?></td>
                    <td><?php echo $warehouse['WAREHOUSENAME']; ?></td>
                    <td><?php echo $warehouse['CARRIERNAME']; ?></td>
                    <td><?php echo $warehouse['TOTALSHIPMENTWEIGHT']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<br>
    <!-- Fourth Table: Output of the Fourth Provided SQL Query -->
    <table>
        
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>Total Orders</th>
                <th>Order Frequency Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fourthQueryData as $customer) : ?>
                <tr>
                    <td><?php echo $customer['CUSTOMERID']; ?></td>
                    <td><?php echo $customer['CUSTOMERNAME']; ?></td>
                    <td><?php echo $customer['TOTALORDERS']; ?></td>
                    <td><?php echo $customer['ORDERFREQUENCYSTATUS']; ?></td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <br>

    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Category Name</th>
                <th>Subcategory Name</th>
                <th>Number of Orders</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fifthQueryData as $product) : ?>
                <tr>
                    <td><?php echo $product['PRODUCTID']; ?></td>
                    <td><?php echo $product['PRODUCTNAME']; ?></td>
                    <td><?php echo $product['CATEGORYNAME']; ?></td>
                    <td><?php echo $product['SUBCATEGORYNAME']; ?></td>
                    <td><?php echo $product['NUMBEROFORDERS']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <table>
        <thead>
            <tr>
                <th>Warehouse ID</th>
                <th>Warehouse Name</th>
                <th>Stock Percentage</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sixthQueryData as $warehouseStock) : ?>
                <tr>
                    <td><?php echo $warehouseStock['WAREHOUSEID']; ?></td>
                    <td><?php echo $warehouseStock['WAREHOUSENAME']; ?></td>
                    <td><?php echo $warehouseStock['STOCKPERCENTAGE']; ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <table>
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>Credit Limit</th>
                <th>Total Orders</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($seventhQueryData as $customerCredit) : ?>
                <tr>
                    <td><?php echo $customerCredit['CUSTOMERID']; ?></td>
                    <td><?php echo $customerCredit['CUSTOMERNAME']; ?></td>
                    <td><?php echo $customerCredit['CREDITLIMIT']; ?></td>
                    <td><?php echo $customerCredit['TOTALORDERS']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Average Rating</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($eighthQueryData as $productRating) : ?>
                <tr>
                    <td><?php echo $productRating['PRODUCTID']; ?></td>
                    <td><?php echo $productRating['PRODUCTNAME']; ?></td>
                    <td><?php echo $productRating['AVGRATING']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <table>
        <thead>
            <tr>
                <th>Supplier ID</th>
                <th>Supplier Name</th>
                <th>Total Order Value</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ninthQueryData as $supplierOrder) : ?>
                <tr>
                    <td><?php echo $supplierOrder['SUPPLIERID']; ?></td>
                    <td><?php echo $supplierOrder['SUPPLIERNAME']; ?></td>
                    <td><?php echo $supplierOrder['TOTALORDERVALUE']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <table>
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Order Month</th>
            <th>Monthly Sales</th>
            <th>Previous Month Sales</th>
            <th>Sales Fluctuation</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tenthQueryData as $data) : ?>
            <tr>
                <td><?php echo $data['PRODUCTID']; ?></td>
                <td><?php echo $data['PRODUCTNAME']; ?></td>
                <td><?php echo $data['ORDERMONTH']; ?></td>
                <td><?php echo $data['MONTHLYSALES']; ?></td>
                <td><?php echo $data['PREVIOUSMONTHSALES']; ?></td>
                <td><?php echo $data['SALESFLUCTUATION']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br>
<table>
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Category Name</th>
            <th>Order Items</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($eleventhQueryData as $data) : ?>
            <tr>
                <td><?php echo $data['PRODUCTID']; ?></td>
                <td><?php echo $data['PRODUCTNAME']; ?></td>
                <td><?php echo $data['CATEGORYNAME']; ?></td>
                <td><?php echo $data['ORDERITEMS']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br>

</body>
    </html>
