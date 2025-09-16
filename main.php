<?php
// Database connection
$host = 'localhost';
$dbname = 'latecomers_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_employee'])) {
        $name = $_POST['name'];
        $department = $_POST['department'];
        
        $stmt = $pdo->prepare("INSERT INTO employees (name, department, late_count) VALUES (?, ?, 0)");
        $stmt->execute([$name, $department]);
    }
    
    if (isset($_POST['add_late_record'])) {
        $employeeId = $_POST['employee_id'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $reason = $_POST['reason'] ?: 'Not specified';
        
        // Add late record
        $stmt = $pdo->prepare("INSERT INTO late_records (employee_id, date, time, reason) VALUES (?, ?, ?, ?)");
        $stmt->execute([$employeeId, $date, $time, $reason]);
        
        // Update employee late count
        $stmt = $pdo->prepare("UPDATE employees SET late_count = late_count + 1 WHERE id = ?");
        $stmt->execute([$employeeId]);
    }
}

// Fetch data
$employees = $pdo->query("SELECT * FROM employees")->fetchAll(PDO::FETCH_ASSOC);
$lateRecords = $pdo->query("SELECT lr.*, e.name as employee_name FROM late_records lr JOIN employees e ON lr.employee_id = e.id ORDER BY lr.date DESC, lr.time DESC")->fetchAll(PDO::FETCH_ASSOC);

// Calculate statistics
$totalEmployees = count($employees);
$totalLateArrivals = count($lateRecords);

$today = date('Y-m-d');
$todayLateArrivals = $pdo->query("SELECT COUNT(*) as count FROM late_records WHERE date = '$today'")->fetch(PDO::FETCH_ASSOC)['count'];

// Find most late employee
$mostLateEmployee = $pdo->query("SELECT * FROM employees ORDER BY late_count DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
?>
>>>>>>> REPLACE
