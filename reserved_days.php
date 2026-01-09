<?php
// Sample reserved dates (in real app, these would come from database)
$reservedDates = [
    '2026-03-15',
    '2026-03-16',
    '2026-03-17',
    '2026-03-20',
    '2026-03-21'
];

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checkIn = $_POST['checkIn'] ?? '';
    $checkOut = $_POST['checkOut'] ?? '';

    if ($checkIn && $checkOut) {
        // Check if selected dates are available
        $conflict = false;
        $selectedDates = getDatesBetween($checkIn, $checkOut);

        foreach ($selectedDates as $date) {
            if (in_array($date, $reservedDates)) {
                $conflict = true;
                $message = "⚠️ Sorry, $date is already booked!";
                break;
            }
        }

        if (!$conflict) {
            $message = "✅ Dates available! Booking confirmed for " . count($selectedDates) . " nights.";
        }
    }
}

// Function to get all dates between two dates
/**
 * @throws DateMalformedStringException
 */
function getDatesBetween($start, $end) {
    $dates = [];
    $current = new DateTime($start);
    $endDate = new DateTime($end);

    while ($current < $endDate) {
        $dates[] = $current->format('Y-m-d');
        $current->modify('+1 day');
    }

    return $dates;
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        .form-group {
            margin: 15px 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        .message {
            padding: 10px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .calendar {
            margin-top: 30px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
        }
        .date-item {
            display: inline-block;
            margin: 5px;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
        }
        .available {
            background: #d4edda;
            color: #155724;
        }
        .reserved {
            background: #f8d7da;
            color: #721c24;
            text-decoration: line-through;
        }
        .legend {
            margin: 10px 0;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
<h2>Hotel Booking</h2>

<?php if ($message): ?>
    <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label for="checkIn">Check-in Date:</label>
        <input type="date" id="checkIn" name="checkIn" required
               min="<?= date('Y-m-d') ?>"
               value="<?= htmlspecialchars($_POST['checkIn'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="checkOut">Check-out Date:</label>
        <input type="date" id="checkOut" name="checkOut" required
               value="<?= htmlspecialchars($_POST['checkOut'] ?? '') ?>">
    </div>

    <button type="submit">Check Availability & Book</button>
</form>

<!-- Display reserved dates calendar -->
<div class="calendar">
    <h3>Already Reserved Dates:</h3>
    <div class="legend">
        <span style="color: #155724;">■ Available</span> |
        <span style="color: #721c24;">■ Booked/Reserved</span>
    </div>

    <?php
    // Show next 30 days
    $today = new DateTime();
    echo "<p>Next 30 days availability:</p>";

    for ($i = 0; $i < 30; $i++) {
        $currentDate = clone $today;
        $currentDate->modify("+$i days");
        $dateStr = $currentDate->format('Y-m-d');
        $displayStr = $currentDate->format('M d');

        $class = in_array($dateStr, $reservedDates) ? 'reserved' : 'available';

        echo "<span class='date-item $class' title='$dateStr'>$displayStr</span>";
    }
    ?>
</div>
</body>
</html>