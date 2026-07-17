<?php
require_once __DIR__ . '/config/Database.php';

$database = new Database();
$conn = $database->connect();

$stmt = $conn->prepare("SELECT doctor_id, fullname FROM doctors WHERE LOWER(fullname) LIKE '%alice smith%'");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    echo "No Dr. Alice Smith found.\n";
    exit(0);
}

foreach ($rows as $row) {
    $id = (int) $row['doctor_id'];
    foreach (['appointments', 'treatments'] as $table) {
        $deleteRelated = $conn->prepare("DELETE FROM {$table} WHERE doctor_id = :id");
        $deleteRelated->execute([':id' => $id]);
    }

    $deleteDoctor = $conn->prepare('DELETE FROM doctors WHERE doctor_id = :id');
    $deleteDoctor->execute([':id' => $id]);
    echo "Deleted doctor ID {$id} ({$row['fullname']}).\n";
}
