<?php
require_once __DIR__ . '/config/database.php';

try {
    // Clear existing schedules first (optional)
    // $pdo->exec("DELETE FROM schedules");
    
    // Insert test schedules
    $schedules = [
        ['2025-12-30', 10],
        ['2025-12-11', 5],
        ['2025-12-25', 8],
        ['2026-01-10', 10],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO schedules (schedule_date, total_slots, remaining_slots, is_active) VALUES (?, ?, ?, 1)");
    
    foreach ($schedules as $schedule) {
        $stmt->execute([$schedule[0], $schedule[1], $schedule[1]]);
        echo "✅ Added schedule: {$schedule[0]} with {$schedule[1]} slots<br>";
    }
    
    echo "<br>✅ All schedules added successfully!<br>";
    echo "<br><a href='front_end/apply.php?section=appointment'>Go to Apply Form</a>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
