<?php
include 'auth_check.php';

if (!isset($_SESSION['qr_data'])) {
    header("Location: dashboard.php");
    exit;
}

$qrData = $_SESSION['qr_data'];
unset($_SESSION['qr_data']);

// Generate QR code with logo embedding
$qrUrl = generateQRCodeWithLogo($qrData);

function generateQRCodeWithLogo($data) {
    // Create directory for QR codes
    $qrDir = '../assets/qrcodes/';
    if (!is_dir($qrDir)) {
        mkdir($qrDir, 0777, true);
    }
    
    // Check if phpqrcode library exists
    $qrLibPath = '../includes/phpqrcode/qrlib.php';
    $useLibrary = file_exists($qrLibPath);
    
    $qrFileName = 'qrcode_' . time() . '.png';
    $qrFilePath = $qrDir . $qrFileName;
    
    if ($useLibrary) {
        // Use phpqrcode library if available
        require_once $qrLibPath;
        QRcode::png($data, $qrFilePath, QR_ECLEVEL_H, 10, 2);
        $qrImage = imagecreatefrompng($qrFilePath);
    } else {
        // Use QR Server API as fallback
        $qrDataUrl = urlencode($data);
        $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=" . $qrDataUrl;
        
        // Download QR code
        $qrImageData = @file_get_contents($apiUrl);
        if ($qrImageData === false) {
            // Fallback to alternative API
            $apiUrl = "https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=" . $qrDataUrl;
            $qrImageData = @file_get_contents($apiUrl);
        }
        
        if ($qrImageData !== false) {
            file_put_contents($qrFilePath, $qrImageData);
            $qrImage = imagecreatefrompng($qrFilePath);
        } else {
            die("Error: Could not generate QR code. Please check your internet connection or install phpqrcode library.");
        }
    }
    
    // Embed logo in QR code center
    $logoPath = '../assets/images/Pacific.png';
    if (file_exists($logoPath)) {
        $logo = @imagecreatefrompng($logoPath);
        
        if ($logo !== false) {
            // Get dimensions
            $qrWidth = imagesx($qrImage);
            $qrHeight = imagesy($qrImage);
            $logoWidth = imagesx($logo);
            $logoHeight = imagesy($logo);
            
            // Calculate logo size (18% of QR code for better readability)
            $logoSize = intval($qrWidth * 0.18);
            $logoX = ($qrWidth - $logoSize) / 2;
            $logoY = ($qrHeight - $logoSize) / 2;
            
            // Create white background for logo
            $whiteBg = imagecreatetruecolor($logoSize + 10, $logoSize + 10);
            $white = imagecolorallocate($whiteBg, 255, 255, 255);
            imagefill($whiteBg, 0, 0, $white);
            
            // Resize logo
            $resizedLogo = imagecreatetruecolor($logoSize, $logoSize);
            imagealphablending($resizedLogo, false);
            imagesavealpha($resizedLogo, true);
            $transparent = imagecolorallocatealpha($resizedLogo, 0, 0, 0, 127);
            imagefill($resizedLogo, 0, 0, $transparent);
            imagealphablending($resizedLogo, true);
            
            imagecopyresampled($resizedLogo, $logo, 0, 0, 0, 0, $logoSize, $logoSize, $logoWidth, $logoHeight);
            
            // Place logo on white background
            imagecopymerge($whiteBg, $resizedLogo, 5, 5, 0, 0, $logoSize, $logoSize, 100);
            
            // Embed logo with white background in QR code
            imagecopymerge($qrImage, $whiteBg, $logoX - 5, $logoY - 5, 0, 0, $logoSize + 10, $logoSize + 10, 100);
            
            // Overlay the logo on top with transparency
            imagealphablending($qrImage, true);
            imagecopymerge($qrImage, $resizedLogo, $logoX, $logoY, 0, 0, $logoSize, $logoSize, 100);
            
            // Clean up
            imagedestroy($logo);
            imagedestroy($resizedLogo);
            imagedestroy($whiteBg);
        }
    }
    
    // Save final QR code
    imagepng($qrImage, $qrFilePath, 9); // Highest quality
    imagedestroy($qrImage);
    
    return 'assets/qrcodes/' . $qrFileName;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generated - Crestline Journeys</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0c2d1a;
            margin-bottom: 20px;
            text-align: center;
        }
        .qr-preview {
            text-align: center;
            margin: 30px 0;
        }
        .qr-preview img {
            max-width: 100%;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            background: white;
        }
        .btn {
            background: gold;
            color: #0c2d1a;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #e6b800;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #0c2d1a;
            color: white;
        }
        .btn-secondary:hover {
            background: #1a4d2e;
        }
        .actions {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>QR Code Generated Successfully!</h1>
        
        <div class="qr-preview">
            <img src="../<?php echo htmlspecialchars($qrUrl); ?>" alt="QR Code">
        </div>
        
        <div class="actions">
            <a href="../<?php echo htmlspecialchars($qrUrl); ?>" download class="btn">Download QR Code</a>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>

