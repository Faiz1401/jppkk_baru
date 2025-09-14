<?php
require '../db.php';

header('Content-Type: application/json');

$events = [];

// Fetch from your `event` table
$result = $conn->query("SELECT event_category, bil, year, event_date FROM event");
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'title' => $row['event_category'] . ' Bil.' . $row['bil'] . '/' . $row['year'],
        'start' => $row['event_date'],
        'color' => '#007bff' // blue for events
    ];
}

// Add public holidays manually or from an API
$holidays = [
    ['title' => 'New Year', 'start' => '2025-01-01', 'color' => '#dc3545'],
    ['title' => 'Hari Merdeka', 'start' => '2025-08-31', 'color' => '#dc3545'],
    // Add more holidays...
];

echo json_encode(array_merge($events, $holidays));
