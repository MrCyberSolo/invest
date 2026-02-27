<?php
// Database connection details
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database_name";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Number of records per page
$recordsPerPage = 50; // Change this as needed

// Get current page
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = $_GET['page'];
} else {
    $currentPage = 1;
}

// Calculate the starting record for the query based on the current page
$startFrom = ($currentPage - 1) * $recordsPerPage;

// Fetch data with pagination
$sql = "SELECT * FROM large_data LIMIT $startFrom, $recordsPerPage";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Data Column 1</th>
                <th>Data Column 2</th>
                <th>Data Column 3</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["id"] . "</td>
                <td>" . $row["data_column1"] . "</td>
                <td>" . $row["data_column2"] . "</td>
                <td>" . $row["data_column3"] . "</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

// Pagination links
$sql = "SELECT COUNT(id) AS total FROM large_data";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalRecords = $row['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

echo "<br><br>Pages: ";
for ($i = 1; $i <= $totalPages; $i++) {
    echo "<a href='?page=" . $i . "'>" . $i . "</a> ";
}

// Close connection
$conn->close();
?>
