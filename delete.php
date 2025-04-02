<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    

    $query = "DELETE FROM moviez WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
    
    header("Location: favourites.php");
    exit();
}
?>
