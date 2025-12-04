<?php
// Generate QR code for enquiry
function generateEnquiryQRCode($enquiryId, $data) {
    // Create directory for QR codes if it doesn't exist
    $qrDir = '../assets/qrcodes/enquiries/';
    if (!is_dir($qrDir)) {
        mkdir($qrDir, 0777, true);
    }
    
    // Check if phpqrcode library exists
    $qrLibPath = '../includes/phpqrcode/qrlib.php';
    $useLibrary = file_exists($qrLibPath);
    
    $qrFileName = 'enquiry_' . $enquiryId . '_' . time() . '.png';
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
            return false;
        }
    }
    
    // Embed logo in QR code center if logo exists
    $logoPath = '../assets/images/Pacific.png';
    if (file_exists($logoPath) && $qrImage) {
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
    if ($qrImage) {
        imagepng($qrImage, $qrFilePath, 9); // Highest quality
        imagedestroy($qrImage);
    }
    
    return 'assets/qrcodes/enquiries/' . $qrFileName;
}
?>

