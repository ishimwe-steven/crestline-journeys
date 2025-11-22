<?php
// Database setup script for admin system
include '../includes/db_connect.php';

// Create admin_users table
$adminTable = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Create site_images table
$imagesTable = "CREATE TABLE IF NOT EXISTS site_images (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    image_path VARCHAR(500) NOT NULL,
    category VARCHAR(50),
    display_order INT(11) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    uploaded_by INT(11),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Execute table creation
if ($conn->query($adminTable)) {
    echo "Admin users table created successfully.<br>";
} else {
    echo "Error creating admin_users table: " . $conn->error . "<br>";
}

if ($conn->query($imagesTable)) {
    echo "Site images table created successfully.<br>";
} else {
    echo "Error creating site_images table: " . $conn->error . "<br>";
}

// Create default admin user (username: admin, password: admin123)
$defaultUsername = "admin";
$defaultEmail = "admin@crestlinejourneys.com";
$defaultPassword = password_hash("admin123", PASSWORD_DEFAULT);

// Check if admin already exists
$checkAdmin = $conn->query("SELECT id FROM admin_users WHERE username = '$defaultUsername'");

if ($checkAdmin->num_rows == 0) {
    $insertAdmin = "INSERT INTO admin_users (username, email, password) VALUES ('$defaultUsername', '$defaultEmail', '$defaultPassword')";
    if ($conn->query($insertAdmin)) {
        echo "Default admin user created successfully!<br>";
        echo "<strong>Username:</strong> admin<br>";
        echo "<strong>Password:</strong> admin123<br>";
        echo "<strong style='color: red;'>Please change the password after first login!</strong><br>";
    } else {
        echo "Error creating default admin: " . $conn->error . "<br>";
    }
} else {
    echo "Admin user already exists.<br>";
}

$conn->close();
echo "<br><a href='login.php'>Go to Admin Login</a>";
?>

