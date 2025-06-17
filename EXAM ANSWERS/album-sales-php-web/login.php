<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'config/database.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = get_db_connection();
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        }
    }
    $error = "Invalid username or password.";
    $stmt->close();
    $conn->close();
}

$page_title = "Login";
// We don't include the standard header/footer as this is a full-page layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- Frameworks & Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom Styles from header.php -->
    <style>
        /* Paste the custom CSS from Step 2 here */
        body, html {
            height: 100%;
            overflow: hidden; /* Prevent scrollbars on the main view */
        }
        .login-page-container {
            height: 100%;
            width: 100%;
        }
        .branding-panel {
            background-color: #4A69E2;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 3rem;
        }
        .branding-panel img {
            max-width: 80%;
            max-height: 300px;
            height: auto;
            margin-bottom: 2rem;
        }
        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            height: 100%;
        }
        .form-wrapper {
            width: 100%;
            max-width: 420px;
        }
        .input-group-custom {
            position: relative;
        }
        .form-control-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 3;
        }
        .form-control.with-icon {
            padding-left: 3rem; /* Make space for the icon */
        }
    </style>
</head>
<body>

<div class="container-fluid login-page-container p-0">
    <div class="row g-0 h-100">

        <!-- Left: Form Panel -->
        <div class="col-lg-6 form-panel">
            <div class="form-wrapper">
                <div class="mb-5">
                    <h2 class="fw-bold">Log in to your Account</h2>
                    <p class="text-muted">Welcome back! Please enter your details.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="post">
                    <div class="mb-3 input-group-custom">
                        <label for="username" class="form-label">Username</label>
                        <i class="bi bi-person form-control-icon"></i>
                        <input type="text" name="username" class="form-control with-icon p-3" id="username" required autofocus>
                    </div>

                    <div class="mb-3 input-group-custom">
                        <div class="d-flex justify-content-between">
                            <label for="password" class="form-label">Password</label>
                            <a href="#" class="form-text text-decoration-none">Forgot Password?</a>
                        </div>
                        <i class="bi bi-lock form-control-icon"></i>
                        <input type="password" name="password" class="form-control with-icon p-3" id="password" required>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg p-3 fw-bold">Log In</button>
                    </div>
                </form>
                
                <p class="text-center text-muted mt-5 small">
                    This is a protected admin panel for authorized users only.
                </p>
            </div>
        </div>

        <!-- Right: Branding Panel (Hidden on screens smaller than LG) -->
        <div class="col-lg-6 d-none d-lg-flex branding-panel">
            <div>
                <img src="src/undraw_website-builder_4go7.svg" alt="Data Illustration">
                <h3 class="fw-light mb-3">EXAM FOR WEB DEVELOPER</h3>
                <p class="lead w-75 mx-auto">
                   Thank you for your time for checking this website as my exam - Archie Antone </p>
            </div>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>