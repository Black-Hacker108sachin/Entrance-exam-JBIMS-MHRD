<?php
include 'db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=candidates_export.xls");

echo "<table border='1'>";
echo "<tr>
        <th>Name</th>
        <th>Phone</th>
        <th>Status</th>
        <th>Seat No</th>
        <th>Reporting Date</th>
        <th>Venue</th>
      </tr>";

$result = $conn->query("SELECT * FROM candidates ORDER BY id DESC");

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".htmlspecialchars($row['name'])."</td>";
    echo "<td>".htmlspecialchars($row['phone'])."</td>";
    echo "<td>".htmlspecialchars($row['status'])."</td>";
    echo "<td>".htmlspecialchars($row['seat_no'])."</td>";
    echo "<td>".htmlspecialchars($row['reporting_date'])."</td>";
    echo "<td>".htmlspecialchars($row['venue'])."</td>";
    echo "</tr>";
}

echo "</table>";
?>
