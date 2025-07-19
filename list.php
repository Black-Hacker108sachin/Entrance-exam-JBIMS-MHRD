<?php
include 'db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build WHERE clause for search
$where = '';
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $where = "WHERE name LIKE '%$search%' OR phone LIKE '%$search%'";
}

// Total rows
$totalResult = $conn->query("SELECT COUNT(*) as total FROM candidates $where");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Get data
$query = "SELECT * FROM candidates $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Candidate List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #e0f7fa, #e1f5fe);
            margin: 40px;
        }

        h2 {
            text-align: center;
            color: #1e3a8a;
        }

        .search-box {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 8px;
            width: 250px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            padding: 8px 12px;
            border-radius: 6px;
            background-color: #3b82f6;
            color: white;
            border: none;
            cursor: pointer;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: center;
        }

        th {
            background-color: #3b82f6;
            color: white;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            color: white;
        }

        .shortlisted {
            background-color: #10b981;
        }

        .not-shortlisted {
            background-color: #ef4444;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            background: #eee;
            padding: 8px 12px;
            border-radius: 4px;
            color: #333;
        }

        .pagination a.active {
            background: #3b82f6;
            color: white;
        }

        .export {
            margin: 10px auto;
            text-align: center;
        }

        .export a {
            background: #6366f1;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<h2>Candidate List</h2>

<div class="search-box">
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search by name or phone" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>
</div>

<div class="export">
    <a href="export.php" target="_blank">Export to Excel</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Seat No</th>
            <th>Reporting Date</th>
            <th>Venue</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): $i = $offset + 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['seat_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['reporting_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['venue']); ?></td>
                    <td>
                        <form method="POST" action="update_status.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="status" value="Shortlisted">
                            <button class="btn shortlisted" type="submit">Shortlisted</button>
                        </form>
                        <form method="POST" action="update_status.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="status" value="Not Shortlisted">
                            <button class="btn not-shortlisted" type="submit">Not Shortlisted</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No candidates found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="pagination">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $p; ?>" class="<?php echo ($p == $page) ? 'active' : ''; ?>"><?php echo $p; ?></a>
    <?php endfor; ?>
</div>

</body>
</html>
