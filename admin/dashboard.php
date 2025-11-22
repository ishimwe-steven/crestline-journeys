<?php
include 'auth_check.php';

// Handle settings update (username/password)
$settingsSuccess = '';
$settingsError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    $newUsername = trim($_POST['new_username'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Get current admin info
    $adminId = $_SESSION['admin_id'];
    $currentInfo = $conn->query("SELECT username, password FROM admin_users WHERE id = $adminId")->fetch_assoc();
    
    // Verify current password
    if (!password_verify($currentPassword, $currentInfo['password'])) {
        $settingsError = "Current password is incorrect.";
    } else {
        // Update username if provided
        if (!empty($newUsername) && $newUsername != $currentInfo['username']) {
            $checkUser = $conn->prepare("SELECT id FROM admin_users WHERE username = ? AND id != ?");
            $checkUser->bind_param("si", $newUsername, $adminId);
            $checkUser->execute();
            
            if ($checkUser->get_result()->num_rows > 0) {
                $settingsError = "Username already exists. Please choose another.";
            } else {
                $updateUser = $conn->prepare("UPDATE admin_users SET username = ? WHERE id = ?");
                $updateUser->bind_param("si", $newUsername, $adminId);
                $updateUser->execute();
                $updateUser->close();
                $_SESSION['admin_username'] = $newUsername;
                $settingsSuccess = "Username updated successfully!";
            }
            $checkUser->close();
        }
        
        // Update password if provided
        if (!empty($newPassword)) {
            if ($newPassword == $confirmPassword) {
                if (strlen($newPassword) >= 6) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updatePass = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                    $updatePass->bind_param("si", $hashedPassword, $adminId);
                    $updatePass->execute();
                    $updatePass->close();
                    $settingsSuccess .= ($settingsSuccess ? "<br>" : "") . "Password updated successfully!";
                } else {
                    $settingsError = "Password must be at least 6 characters long.";
                }
            } else {
                $settingsError = "New passwords do not match.";
            }
        }
    }
}

// Handle QR code generation
$qrSuccess = '';
$qrError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_qr'])) {
    $siteLink = trim($_POST['site_link'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    if (empty($siteLink) && empty($email) && empty($instagram) && empty($phone)) {
        $qrError = "Please fill in at least one field.";
    } else {
        // Format data for QR code - prefer vCard format for multiple fields
        $fieldCount = (!empty($siteLink) ? 1 : 0) + (!empty($email) ? 1 : 0) + 
                      (!empty($instagram) ? 1 : 0) + (!empty($phone) ? 1 : 0);
        
        if ($fieldCount == 1 && !empty($siteLink)) {
            // If only website link, use simple URL
            $qrData = $siteLink;
        } else {
            // For multiple fields, use vCard format
            $vcard = "BEGIN:VCARD\n";
            $vcard .= "VERSION:3.0\n";
            $vcard .= "FN:Crestline Journeys\n";
            $vcard .= "ORG:Crestline Journeys\n";
            if (!empty($siteLink)) {
                $vcard .= "URL:$siteLink\n";
            }
            if (!empty($email)) {
                $vcard .= "EMAIL:$email\n";
            }
            if (!empty($phone)) {
                // Clean phone number
                $phone = preg_replace('/[^0-9+]/', '', $phone);
                $vcard .= "TEL:$phone\n";
            }
            if (!empty($instagram)) {
                // Clean Instagram handle
                $instagram = ltrim($instagram, '@');
                $vcard .= "X-SOCIALPROFILE;TYPE=instagram:https://instagram.com/$instagram\n";
            }
            $vcard .= "NOTE:Where every journey finds its peak\n";
            $vcard .= "END:VCARD";
            $qrData = $vcard;
        }
        
        // Redirect to QR generator page
        $_SESSION['qr_data'] = $qrData;
        header("Location: generate_qr.php");
        exit;
    }
}

// Handle image upload
$uploadSuccess = '';
$uploadError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_image'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? 'general');
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['image']['type'];
        
        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = '../assets/images/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = 'assets/images/uploads/' . $fileName;
                $stmt = $conn->prepare("INSERT INTO site_images (title, description, image_path, category, uploaded_by) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $title, $description, $imagePath, $category, $_SESSION['admin_id']);
                
                if ($stmt->execute()) {
                    $uploadSuccess = "Image uploaded successfully!";
                } else {
                    $uploadError = "Error saving to database: " . $stmt->error;
                    unlink($targetPath); // Delete uploaded file if DB insert fails
                }
                $stmt->close();
            } else {
                $uploadError = "Error uploading file.";
            }
        } else {
            $uploadError = "Invalid file type. Please upload JPEG, PNG, GIF, or WebP images.";
        }
    } else {
        $uploadError = "Please select an image file.";
    }
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $imageId = (int)$_GET['delete'];
    $getImage = $conn->query("SELECT image_path FROM site_images WHERE id = $imageId");
    if ($getImage->num_rows > 0) {
        $imgData = $getImage->fetch_assoc();
        $filePath = '../' . $imgData['image_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $conn->query("DELETE FROM site_images WHERE id = $imageId");
        header("Location: dashboard.php?deleted=1");
        exit;
    }
}

// Get all images
$imagesQuery = $conn->query("SELECT * FROM site_images ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Crestline Journeys</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
        }
        .admin-header {
            background: #0c2d1a;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .admin-header h1 {
            font-size: 1.5rem;
        }
        .admin-header .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .admin-header a {
            color: white;
            text-decoration: none;
            padding: 8px 20px;
            background: gold;
            color: #0c2d1a;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .admin-header a:hover {
            background: #e6b800;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .dashboard-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
        }
        .tab {
            padding: 12px 25px;
            background: white;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        .tab.active {
            color: #0c2d1a;
            border-bottom-color: gold;
            font-weight: 600;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: #0c2d1a;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: gold;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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
            transition: all 0.3s;
        }
        .btn:hover {
            background: #e6b800;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .image-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        .image-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .image-card-body {
            padding: 15px;
        }
        .image-card-body h3 {
            color: #0c2d1a;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }
        .image-card-body p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        .image-card-body .meta {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 15px;
        }
        .image-actions {
            display: flex;
            gap: 10px;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        .btn-delete:hover {
            background: #c82333;
        }
        .btn-view {
            background: #0c2d1a;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-view:hover {
            background: #1a4d2e;
        }
        .qr-preview {
            text-align: center;
            padding: 20px;
            margin-top: 20px;
        }
        .qr-preview img {
            max-width: 400px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 10px;
            background: white;
        }
        .btn-download {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-download:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Crestline Journeys - Admin Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($adminInfo['username']); ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="dashboard-tabs">
            <button class="tab active" onclick="showTab('upload')">Upload Image</button>
            <button class="tab" onclick="showTab('images')">Manage Images</button>
            <button class="tab" onclick="showTab('qrcode')">QR Code Generator</button>
            <button class="tab" onclick="showTab('settings')">Settings</button>
        </div>

        <!-- Upload Tab -->
        <div id="upload" class="tab-content active">
            <div class="card">
                <h2 style="color: #0c2d1a; margin-bottom: 20px;">Upload New Image</h2>
                
                <?php if ($uploadSuccess): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($uploadSuccess); ?></div>
                <?php endif; ?>
                
                <?php if ($uploadError): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($uploadError); ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="image">Image File *</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" placeholder="Image title">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Image description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category">
                            <option value="general">General</option>
                            <option value="destination">Destination</option>
                            <option value="safari">Safari</option>
                            <option value="wildlife">Wildlife</option>
                            <option value="hotel">Hotel</option>
                            <option value="culture">Culture</option>
                        </select>
                    </div>
                    <button type="submit" name="upload_image" class="btn">Upload Image</button>
                </form>
            </div>
        </div>

        <!-- Images Tab -->
        <div id="images" class="tab-content">
            <div class="card">
                <h2 style="color: #0c2d1a; margin-bottom: 20px;">Uploaded Images (<?php echo $imagesQuery->num_rows; ?>)</h2>
                
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert alert-success">Image deleted successfully!</div>
                <?php endif; ?>

                <?php if ($imagesQuery->num_rows > 0): ?>
                    <div class="images-grid">
                        <?php while ($image = $imagesQuery->fetch_assoc()): ?>
                            <div class="image-card">
                                <img src="../<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['title']); ?>">
                                <div class="image-card-body">
                                    <h3><?php echo htmlspecialchars($image['title'] ?: 'Untitled'); ?></h3>
                                    <?php if ($image['description']): ?>
                                        <p><?php echo htmlspecialchars(substr($image['description'], 0, 100)); ?>...</p>
                                    <?php endif; ?>
                                    <div class="meta">
                                        Category: <?php echo htmlspecialchars($image['category']); ?> | 
                                        Uploaded: <?php echo date('M j, Y', strtotime($image['created_at'])); ?>
                                    </div>
                                    <div class="image-actions">
                                        <a href="../<?php echo htmlspecialchars($image['image_path']); ?>" target="_blank" class="btn-view">View</a>
                                        <a href="?delete=<?php echo $image['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 40px;">No images uploaded yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- QR Code Generator Tab -->
        <div id="qrcode" class="tab-content">
            <div class="card">
                <h2 style="color: #0c2d1a; margin-bottom: 20px;">Generate QR Code with Logo</h2>
                
                <?php if ($qrSuccess): ?>
                    <div class="alert alert-success"><?php echo $qrSuccess; ?></div>
                <?php endif; ?>
                
                <?php if ($qrError): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($qrError); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="site_link">Website Link</label>
                        <input type="url" id="site_link" name="site_link" placeholder="https://yourwebsite.com">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="info@crestlinejourneys.com">
                    </div>
                    <div class="form-group">
                        <label for="instagram">Instagram Handle</label>
                        <input type="text" id="instagram" name="instagram" placeholder="@crestlinejourneys">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="+1 234 567 8900">
                    </div>
                    <button type="submit" name="generate_qr" class="btn">Generate QR Code</button>
                </form>
            </div>
        </div>

        <!-- Settings Tab -->
        <div id="settings" class="tab-content">
            <div class="card">
                <h2 style="color: #0c2d1a; margin-bottom: 20px;">Account Settings</h2>
                
                <?php if ($settingsSuccess): ?>
                    <div class="alert alert-success"><?php echo $settingsSuccess; ?></div>
                <?php endif; ?>
                
                <?php if ($settingsError): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($settingsError); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="current_username">Current Username</label>
                        <input type="text" id="current_username" value="<?php echo htmlspecialchars($adminInfo['username']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="new_username">New Username</label>
                        <input type="text" id="new_username" name="new_username" placeholder="Enter new username">
                    </div>
                    <div class="form-group">
                        <label for="current_password">Current Password *</label>
                        <input type="password" id="current_password" name="current_password" required placeholder="Enter current password">
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password (leave blank to keep current)</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password (min 6 characters)">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                    </div>
                    <button type="submit" name="update_settings" class="btn">Update Settings</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>

