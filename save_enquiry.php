<?php
// save_enquiry.php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create a debug log (optional)
$logFile = __DIR__ . '/logs/enquiry_debug.log';
function dbg($msg) {
    global $logFile;
    $t = date('Y-m-d H:i:s');
    @file_put_contents($logFile, "[$t] $msg\n", FILE_APPEND | LOCK_EX);
}

dbg("save_enquiry.php called");

// Include DB connection
include __DIR__ . '/includes/db_connect.php';

// Log POST data
dbg("POST_ARRAY: " . json_encode($_POST));

// JSON response helper
function respond($status, $msg, $extra = []) {
    $out = array_merge(['status' => $status, 'message' => $msg], $extra);
    echo json_encode($out);
    dbg("RESPONSE: " . json_encode($out));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond('error', 'Invalid request method, POST required.');
}

// Collect data safely
$destination_known = $_POST['know_where'] ?? ''; // keep this from frontend, maps to destination_known
$countries = isset($_POST['countries']) ? (is_array($_POST['countries']) ? implode(',', $_POST['countries']) : $_POST['countries']) : '';
$region = trim($_POST['region'] ?? '');
$travel_time_choice = $_POST['travel_time_choice'] ?? '';
// Handle date range - prefer combined, fallback to individual dates
$travel_dates = $_POST['travel_dates'] ?? '';
if (empty($travel_dates) && (isset($_POST['travel_dates_start']) || isset($_POST['travel_dates_end']))) {
    $start = $_POST['travel_dates_start'] ?? '';
    $end = $_POST['travel_dates_end'] ?? '';
    if ($start && $end) {
        $travel_dates = $start . ' to ' . $end;
    } elseif ($start) {
        $travel_dates = $start;
    } elseif ($end) {
        $travel_dates = $end;
    }
}
$travel_with = $_POST['travel_with'] ?? '';
$budget = $_POST['budget'] ?? '';
$trip_details = $_POST['trip_details'] ?? '';
$referred = $_POST['referred'] ?? '';
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$preferred_contact = $_POST['preferred_contact'] ?? '';

// Basic validation
if (empty($first_name) || empty($email)) {
    respond('error', 'Missing required fields: first_name and email are required.');
}

// Prepare insert
$sql = "INSERT INTO enquiries 
    (destination_known, countries, region, travel_time_choice, travel_dates, travel_with, budget, trip_details, referred, email, first_name, last_name, phone, preferred_contact)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    respond('error', 'Prepare failed: ' . $conn->error);
}

$bind = $stmt->bind_param(
    "ssssssssssssss",
    $destination_known,
    $countries,
    $region,
    $travel_time_choice,
    $travel_dates,
    $travel_with,
    $budget,
    $trip_details,
    $referred,
    $email,
    $first_name,
    $last_name,
    $phone,
    $preferred_contact
);
if ($bind === false) {
    respond('error', 'Bind failed: ' . $stmt->error);
}

// Execute
if (!$stmt->execute()) {
    respond('error', 'Execute failed: ' . $stmt->error);
}

$enquiryId = $stmt->insert_id;

// ============================================
// EMAIL HANDLING - NOW USING EMAILJS
// ============================================
// Emails are now sent via EmailJS (client-side in main.js)
// PHP mail() code below is kept as fallback only (commented out)
// To use PHP mail instead, uncomment the code below
// ============================================

/*
// Email Configuration (for PHP mail fallback - optional)
$adminEmail = "stevenishimwe28@gmail.com"; // Change to your admin email
$fromEmail = "stevenishimwe28@gmail.com";
$siteName = "Crestline Journeys";

// ========== Send Thank You Email to Customer ==========
$customerSubject = "Thank you for your enquiry - " . $siteName;
$customerMessage = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0c2d1a; color: gold; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; }
        .footer { background: #0c2d1a; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .btn { display: inline-block; background: gold; color: #0c2d1a; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>Crestline Journeys</h2>
        </div>
        <div class='content'>
            <h3>Dear $first_name,</h3>
            <p>Thank you for your enquiry! We're excited to help you plan your journey through Africa.</p>
            <p>Our expert Travel Designers have received your request and will contact you within 24-48 hours to discuss your dream adventure.</p>
            <p><strong>Your Enquiry Details:</strong></p>
            <ul>
                <li>Destination: " . ($destination_known ?: 'Anywhere in Africa') . "</li>
                <li>Countries: " . ($countries ?: 'Not specified') . "</li>
                <li>Travel With: " . ($travel_with ?: 'Not specified') . "</li>
                <li>Budget: " . ($budget ?: 'Not specified') . "</li>
            </ul>
            <p>If you have any urgent questions, please don't hesitate to contact us.</p>
            <p>Best regards,<br>The Crestline Journeys Team</p>
        </div>
        <div class='footer'>
            <p>&copy; " . date('Y') . " Crestline Journeys. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
";

$customerHeaders = "MIME-Version: 1.0\r\n";
$customerHeaders .= "Content-type: text/html; charset=UTF-8\r\n";
$customerHeaders .= "From: $siteName <$fromEmail>\r\n";
$customerHeaders .= "Reply-To: $adminEmail\r\n";

$customerMailOk = @mail($email, $customerSubject, $customerMessage, $customerHeaders);
dbg("CUSTOMER_MAIL_OK: " . ($customerMailOk ? '1' : '0'));

// ========== Send Order/Enquiry Email to Admin ==========
$adminSubject = "New Enquiry Received - $first_name $last_name";
$adminMessage = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
        .header { background: #0c2d1a; color: gold; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; }
        .info-box { background: white; padding: 20px; margin: 15px 0; border-left: 4px solid gold; }
        .info-box h4 { color: #0c2d1a; margin-top: 0; }
        .footer { background: #0c2d1a; color: white; padding: 15px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Enquiry Received</h2>
        </div>
        <div class='content'>
            <p><strong>A new travel enquiry has been submitted through the website.</strong></p>
            
            <div class='info-box'>
                <h4>Contact Information</h4>
                <p><strong>Name:</strong> $first_name $last_name</p>
                <p><strong>Email:</strong> <a href='mailto:$email'>$email</a></p>
                <p><strong>Phone:</strong> $phone</p>
                <p><strong>Preferred Contact Method:</strong> $preferred_contact</p>
            </div>

            <div class='info-box'>
                <h4>Travel Preferences</h4>
                <p><strong>Destination Known:</strong> $destination_known</p>
                <p><strong>Countries:</strong> " . ($countries ?: 'Not specified') . "</p>
                <p><strong>Region/Camp:</strong> " . ($region ?: 'Not specified') . "</p>
                <p><strong>Travel Time Choice:</strong> " . ($travel_time_choice ?: 'Not specified') . "</p>
                <p><strong>Travel Dates:</strong> " . ($travel_dates ?: 'Not specified') . "</p>
                <p><strong>Traveling With:</strong> " . ($travel_with ?: 'Not specified') . "</p>
                <p><strong>Budget:</strong> " . ($budget ?: 'Not specified') . "</p>
                <p><strong>Referred:</strong> " . ($referred ?: 'No') . "</p>
            </div>

            " . (!empty($trip_details) ? "
            <div class='info-box'>
                <h4>Trip Details</h4>
                <p>" . nl2br(htmlspecialchars($trip_details)) . "</p>
            </div>
            " : "") . "

            <p><strong>Enquiry ID:</strong> #$enquiryId</p>
            <p><strong>Submitted:</strong> " . date('F j, Y \a\t g:i A') . "</p>
            
            <p style='margin-top: 30px;'>
                <a href='mailto:$email?subject=Re: Your Travel Enquiry' style='background: gold; color: #0c2d1a; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>Reply to Customer</a>
            </p>
        </div>
        <div class='footer'>
            <p>&copy; " . date('Y') . " Crestline Journeys Admin Portal</p>
        </div>
    </div>
</body>
</html>
";

$adminHeaders = "MIME-Version: 1.0\r\n";
$adminHeaders .= "Content-type: text/html; charset=UTF-8\r\n";
$adminHeaders .= "From: $siteName Website <$fromEmail>\r\n";
$adminHeaders .= "Reply-To: $email\r\n";

$adminMailOk = @mail($adminEmail, $adminSubject, $adminMessage, $adminHeaders);
dbg("ADMIN_MAIL_OK: " . ($adminMailOk ? '1' : '0'));
*/

// Done - Emails are sent via EmailJS (see assets/js/main.js)
$res = ['insert_id' => $enquiryId];
$stmt->close();
$conn->close();

respond('success', 'Enquiry saved successfully.', $res);
?>
