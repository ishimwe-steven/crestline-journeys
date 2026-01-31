<?php
include 'auth_check.php';

// Redirect the main admin account back to the full dashboard
if ($adminInfo['role'] === 'admin') {
    header("Location: dashboard.php");
    exit;
}

$uploadSuccess = '';
$uploadError = '';
$updateSuccess = '';
$updateError = '';

// Handle upload and update actions for this user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload new image
    if (isset($_POST['upload_image'])) {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? 'general');

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = $_FILES['image']['type'];

            if (in_array($fileType, $allowedTypes, true)) {
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
                        unlink($targetPath);
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

    // Update existing image details (only metadata, not file)
    if (isset($_POST['update_image'])) {
        $imageId = isset($_POST['image_id']) ? (int) $_POST['image_id'] : 0;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? 'general');

        if ($imageId > 0) {
            $stmt = $conn->prepare("UPDATE site_images SET title = ?, description = ?, category = ? WHERE id = ? AND uploaded_by = ?");
            $userId = (int) $_SESSION['admin_id'];
            $stmt->bind_param("sssii", $title, $description, $category, $imageId, $userId);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $updateSuccess = "Image updated successfully!";
                } else {
                    $updateError = "No changes saved. Make sure the image belongs to your account.";
                }
            } else {
                $updateError = "Error updating image: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $updateError = "Invalid image selected.";
        }
    }
}

// Get only this user's images
$userId = (int) $_SESSION['admin_id'];
$imagesStmt = $conn->prepare("SELECT * FROM site_images WHERE uploaded_by = ? ORDER BY created_at DESC");
$imagesStmt->bind_param("i", $userId);
$imagesStmt->execute();
$imagesResult = $imagesStmt->get_result();

// Which image is currently being edited (if any)
$editImageId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Gallery Account - Crestline Journeys</title>
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
        .user-header {
            background: #0c2d1a;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .user-header h1 {
            font-size: 1.4rem;
        }
        .user-header .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-header a {
            color: #0c2d1a;
            background: gold;
            padding: 8px 18px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .user-header a:hover {
            background: #e6b800;
        }
        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .layout {
            display: grid;
            grid-template-columns: 1.1fr 2fr;
            gap: 25px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card h2 {
            color: #0c2d1a;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        .card p.subtitle {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #0c2d1a;
            font-weight: 500;
            font-size: 0.95rem;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: gold;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }
        .btn {
            background: gold;
            color: #0c2d1a;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #e6b800;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(255, 215, 0, 0.3);
        }
        .alert {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 0.9rem;
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
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 18px;
        }
        .image-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .image-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .image-card-body {
            padding: 12px 14px 14px;
        }
        .image-card-body h3 {
            font-size: 1rem;
            color: #0c2d1a;
            margin-bottom: 6px;
        }
        .image-card-body p {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 8px;
        }
        .image-card-body .meta {
            font-size: 0.8rem;
            color: #999;
            margin-bottom: 8px;
        }
        .image-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-small {
            font-size: 0.8rem;
            padding: 6px 10px;
        }
        .btn-view {
            background: #0c2d1a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .edit-form {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #e0e0e0;
        }
        .edit-form .form-group {
            margin-bottom: 10px;
        }
        .edit-form-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .link-cancel {
            font-size: 0.85rem;
            color: #666;
            text-decoration: none;
        }
        .link-cancel:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 768px) {
            .user-header {
                padding: 15px 20px;
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .user-header h1 {
                font-size: 1.1rem;
            }
            .user-header .user-info {
                width: 100%;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            .container {
                margin: 20px auto;
                padding: 0 15px;
            }
        }
        @media (max-width: 480px) {
            .user-header a {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
            .card {
                padding: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="user-header">
        <h1>Your Gallery Account</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($adminInfo['username']); ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="layout">
            <!-- Upload card -->
            <div class="card">
                <h2>Upload New Image</h2>
                <p class="subtitle">You can upload new photos and later update their title, description, and category.</p>

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
                        <textarea id="description" name="description" placeholder="Short description (optional)"></textarea>
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

            <!-- Images list card -->
            <div class="card">
                <h2>Your Uploaded Images (<?php echo $imagesResult->num_rows; ?>)</h2>
                <p class="subtitle">You can view your own images and update their details. Delete is disabled on this account.</p>

                <?php if ($updateSuccess): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($updateSuccess); ?></div>
                <?php endif; ?>

                <?php if ($updateError): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($updateError); ?></div>
                <?php endif; ?>

                <?php if ($imagesResult->num_rows > 0): ?>
                    <div class="images-grid">
                        <?php while ($image = $imagesResult->fetch_assoc()): ?>
                            <div class="image-card" id="image-<?php echo $image['id']; ?>">
                                <img src="../<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['title'] ?: 'Untitled'); ?>">
                                <div class="image-card-body">
                                    <h3><?php echo htmlspecialchars($image['title'] ?: 'Untitled'); ?></h3>
                                    <?php if ($image['description']): ?>
                                        <p><?php echo htmlspecialchars(substr($image['description'], 0, 120)); ?><?php echo strlen($image['description']) > 120 ? '...' : ''; ?></p>
                                    <?php endif; ?>
                                    <div class="meta">
                                        Category: <?php echo htmlspecialchars($image['category'] ?: 'general'); ?> |
                                        Uploaded: <?php echo date('M j, Y', strtotime($image['created_at'])); ?>
                                    </div>
                                    <div class="image-actions">
                                        <a href="../<?php echo htmlspecialchars($image['image_path']); ?>" target="_blank" class="btn btn-small btn-view">View</a>
                                        <a href="?edit=<?php echo $image['id']; ?>#image-<?php echo $image['id']; ?>" class="btn btn-small">Edit details</a>
                                    </div>

                                    <?php if ($editImageId === (int) $image['id']): ?>
                                        <form method="POST" class="edit-form">
                                            <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                            <div class="form-group">
                                                <label for="edit-title-<?php echo $image['id']; ?>">Title</label>
                                                <input
                                                    type="text"
                                                    id="edit-title-<?php echo $image['id']; ?>"
                                                    name="title"
                                                    value="<?php echo htmlspecialchars($image['title']); ?>"
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="edit-description-<?php echo $image['id']; ?>">Description</label>
                                                <textarea
                                                    id="edit-description-<?php echo $image['id']; ?>"
                                                    name="description"
                                                ><?php echo htmlspecialchars($image['description']); ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="edit-category-<?php echo $image['id']; ?>">Category</label>
                                                <select id="edit-category-<?php echo $image['id']; ?>" name="category">
                                                    <?php
                                                        $categories = ['general', 'destination', 'safari', 'wildlife', 'hotel', 'culture'];
                                                        foreach ($categories as $cat):
                                                    ?>
                                                        <option value="<?php echo $cat; ?>" <?php echo ($image['category'] === $cat ? 'selected' : ''); ?>>
                                                            <?php echo ucfirst($cat); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="edit-form-actions">
                                                <button type="submit" name="update_image" class="btn btn-small">Save changes</button>
                                                <a href="user_gallery.php#image-<?php echo $image['id']; ?>" class="link-cancel">Cancel</a>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p style="margin-top: 10px; color: #666; font-size: 0.9rem;">You haven't uploaded any images yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

