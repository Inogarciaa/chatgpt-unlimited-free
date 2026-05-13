<?php
require 'config.php';
try {
    $pdo->exec("ALTER TABLE reservations ADD COLUMN payment_status ENUM('Unpaid', 'Paid') DEFAULT 'Unpaid';");
    echo "payment_status column added successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
