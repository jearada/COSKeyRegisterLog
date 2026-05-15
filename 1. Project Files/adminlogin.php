<?php
    include("database.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $adminid = $_POST['adminid'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($adminid) || empty($password)) {
            echo "<script>alert('Please fill in all fields');</script>";
        } else {
            $sql = "SELECT * FROM tbladmin WHERE adminid='$adminid' AND password='$password'";
            $result = mysqli_query($connection, $sql);

            if (mysqli_num_rows($result) > 0) {
                header("Location: adminmanipulation.php");
                exit();
            } else {
                echo "<script>alert('Invalid adminID or password');</script>";
            }
        }
    }

    // mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="adminlogin&signup1.css" rel="stylesheet">
    <title>URS | Admin Portal Login</title>
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
                <p style="font-weight: 800; font-size: 1.1rem;">UNIVERSITY OF RIZAL SYSTEM</p>
            </div>
        </div>

        <div class="welcome-text">
            <h2>Log In</h2>
            <p>Log in to continue to your dashboard.</p>
        </div>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

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

            <button type="submit" class="signin-btn">Log In</button>
        </form>

        <!-- // leski ayaw n;ya mag href as button -->
        <button type="button" class="signin-btn" onclick="window.location.href='adminsignup.php'">Sign Up</button>

        <a href="index.php" class="back-home-btn">← Back</a>
        <div class="form-footer">
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