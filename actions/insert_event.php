<?php
session_start();
require '../db.php';

// Redirect if not logged in or not admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Get and validate form data
$event_category = trim($_POST['event_category'] ?? '');
$bil            = trim($_POST['bil'] ?? '');
$year           = intval($_POST['year'] ?? 0);
$event_date     = $_POST['event_date'] ?? '';
$location       = trim($_POST['location'] ?? '');

// Validation (basic)
if (!$event_category || !$bil || !$year || !$event_date || !$location) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: ../admin.php?section=form");
    exit;
}

// Optional: Construct a name like MLK/01/2025
$name = "{$event_category}/{$bil}/{$year}";
$event_type = $event_category; // You can change this if needed

// Insert into event table
$stmt = $conn->prepare("INSERT INTO event (location, event_category,event_date, year, bil ) VALUES (?, ?, ?, ?,?)");
$stmt->bind_param("sssds", $location,$event_category,  $event_date, $year, $bil);
$stmt->execute();

$_SESSION['success'] = "Event created successfully.";
header("Location: ../admin.php?section=events");
exit;
