<?php
    include("database.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

        $adminid = $_POST['adminid'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!empty($adminid) && !empty($password)) {
            
            $stmt = $connection->prepare("INSERT INTO tbladmin (adminid, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $adminid, $password);

            if ($stmt->execute()) {
                echo "<script>alert('Sign up successful!');</script>";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="adminlogin&signup1.css" rel="stylesheet">
    <title>URS | Admin Portal Signup</title>
</head>
<body>
    <div class="hero-side">
        <h1>College of Science Key Register Log</h1>
        <p>A professional digital gateway for the University of Rizal System.</p>
    </div>

    <div class="form-side">
        <div class="logo-area">
            <img src="assets/logos/urslogo.png" alt="URS Logo" width="50">
            <div>
                <p style="font-weight: 800; font-size: 0.8rem;">UNIVERSITY OF RIZAL SYSTEM</p>
            </div>
        </div>

        <div class="welcome-text">
            <h2>Sign Up</h2>
            <p>Make an account to access your dashboard.</p>
        </div>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

            <!-- FROM GOOGLE -->
            <div class="input-group">
                <label for="admin-id">Admin ID</label>
                <input type="text" id="admin-id" name="adminid" placeholder="Enter your ID" required>
            </div>


            <div class="input-group">
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <img src="assets/images/eye.png" id="togglePassword" class="eye-icon" alt="Toggle Password" width="20">
                </div>
            </div>

            <input type="submit" class="signin-btn" value="Sign Up" name="submit">
        </form>

        <a href="adminlogin.php" class="back-home-btn">← Back</a>
        <div class="form-footer" style="margin-top: 210px;">
            &copy; 2026 University of Rizal System | Developed by Cenir Arada Encinares
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
        });
    </script>
</body>
</html>