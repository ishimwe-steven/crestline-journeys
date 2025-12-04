<?php
include('../includes/db_connect.php');

// Get enquiry ID from URL
$enquiryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($enquiryId <= 0) {
    header("Location: ../index.php");
    exit;
}

// Fetch enquiry from database
$enquiryQuery = $conn->prepare("SELECT * FROM enquiries WHERE id = ?");
$enquiryQuery->bind_param("i", $enquiryId);
$enquiryQuery->execute();
$result = $enquiryQuery->get_result();

if ($result->num_rows == 0) {
    header("Location: ../index.php");
    exit;
}

$enquiry = $result->fetch_assoc();
$enquiryQuery->close();

// Get base URL for redirect
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$baseDir = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'])));
if ($baseDir == '/') $baseDir = '';
$websiteUrl = $protocol . $host . $baseDir;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enquiry Details - Crestline Journeys</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0c2d1a 0%, #1a4d2e 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid gold;
        }
        .header h1 {
            color: #0c2d1a;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .header p {
            color: #666;
            font-size: 1.1rem;
        }
        .info-section {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid gold;
        }
        .info-section h3 {
            color: #0c2d1a;
            font-size: 1.3rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #0c2d1a;
        }
        .info-value {
            color: #666;
        }
        .trip-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 10px;
            line-height: 1.8;
            color: #333;
        }
        .btn-container {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
        }
        .btn {
            display: inline-block;
            background: gold;
            color: #0c2d1a;
            padding: 15px 40px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            margin: 0 10px;
        }
        .btn:hover {
            background: #e6b800;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }
        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            background: #d4edda;
            color: #155724;
        }
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            .header h1 {
                font-size: 1.8rem;
            }
            .info-row {
                grid-template-columns: 1fr;
                gap: 5px;
            }
            .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Travel Enquiry Details</h1>
            <p>Enquiry ID: #<?php echo $enquiry['id']; ?></p>
        </div>

        <!-- Contact Information -->
        <div class="info-section">
            <h3>👤 Contact Information</h3>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value"><?php echo htmlspecialchars($enquiry['first_name'] . ' ' . $enquiry['last_name']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><a href="mailto:<?php echo htmlspecialchars($enquiry['email']); ?>" style="color: #0c2d1a;"><?php echo htmlspecialchars($enquiry['email']); ?></a></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value"><?php echo htmlspecialchars($enquiry['phone'] ?? 'Not provided'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Preferred Contact:</span>
                <span class="info-value"><?php echo htmlspecialchars($enquiry['preferred_contact'] ?? 'Email'); ?></span>
            </div>
        </div>

        <!-- Travel Preferences -->
        <div class="info-section">
            <h3>✈️ Travel Preferences</h3>
            <div class="info-row">
                <span class="info-label">Destination Known:</span>
                <span class="info-value"><?php echo htmlspecialchars($enquiry['destination_known'] ?? 'Not specified'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Countries:</span>
                <span class="info-value"><?php echo htmlspecialchars($enquiry['countries'] ?? 'Not specified'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Region/Camp:</span>
                <span class="info-value"><?php echo htmlspecialchars($enquiry['region'] ?? 'Not specified'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Travel Time:</span>
                <span class="info-value"><?php echo htmlspecialchars($enquiry['travel_time_choice'] ?? 'Not specified'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Travel Dates:</span>
                <span class="info-value"><?php echo htmlspecialchars($enquiry['travel_dates'] ?? 'Not specified'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Traveling With:</span>
                <span class="info-value"><?php echo htmlspecialchars($enquiry['travel_with'] ?? 'Not specified'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Budget:</span>
                <span class="info-value"><strong><?php echo htmlspecialchars($enquiry['budget'] ?? 'Not specified'); ?></strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Referred:</span>
                <span class="info-value"><?php echo htmlspecialchars($enquiry['referred'] ?? 'No'); ?></span>
            </div>
        </div>

        <?php if (!empty($enquiry['trip_details'])): ?>
        <!-- Trip Details -->
        <div class="info-section">
            <h3>📝 Trip Details</h3>
            <div class="trip-details">
                <?php echo nl2br(htmlspecialchars($enquiry['trip_details'])); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Submission Info -->
        <div class="info-section">
            <h3>⏰ Submission Information</h3>
            <div class="info-row">
                <span class="info-label">Submitted:</span>
                <span class="info-value"><?php echo date('F j, Y \a\t g:i A', strtotime($enquiry['created_at'])); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span class="badge"><?php echo ucfirst($enquiry['status'] ?? 'New'); ?></span>
                </span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="btn-container">
            <a href="<?php echo $websiteUrl; ?>" class="btn">Visit Our Website</a>
            <a href="<?php echo $websiteUrl; ?>/pages/contact.php" class="btn">Contact Us</a>
        </div>
    </div>

    <script>
        // Auto-redirect to website after 10 seconds
        setTimeout(function() {
            window.location.href = '<?php echo $websiteUrl; ?>';
        }, 10000);
    </script>
</body>
</html>

