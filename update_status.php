<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = (int) $_POST['id'];
    $status = $conn->real_escape_string($_POST['status']);

    $sql = "UPDATE candidates SET status='$status' WHERE id=$id";
    if ($conn->query($sql)) {
        header("Location: list.php");
        exit;
    } else {
        echo "Error updating status: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
