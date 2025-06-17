<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'Album Sales'); ?></title>
    
    <!-- Frameworks & Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root {
            --sidebar-width: 280px;
        }
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background-color: #212529;
            color: #fff;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s;
        }
        .sidebar-header {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
        }
        .sidebar-header .bi {
            font-size: 2rem;
            margin-right: 1rem;
        }
        .nav-link {
            color: #adb5bd;
            font-size: 1.1rem;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
        }
        .nav-link.active, .nav-link:hover {
            color: #fff;
            background-color: #343a40;
        }
        .kpi-card .card-body {
            display: flex;
            align-items: center;
        }
        .kpi-card .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        /* Responsive: Collapse sidebar on smaller screens */
        @media (max-width: 992px) {
            .sidebar {
                left: -var(--sidebar-width);
            }
            .main-content {
                margin-left: 0;
            }
            /* You would add a toggle button and JS to show/hide sidebar on mobile */
        }
        /* Add these new styles inside the <style> tag in templates/header.php */
body, html {
    height: 100%;
}
.login-page-container {
    height: 100%;
    min-height: 100vh;
    width: 100vw;
    max-width: 100%;
}
.branding-panel {
    background-color: #4A69E2; /* A blue similar to the image */
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
    height: auto;
    margin-bottom: 2rem;
}
.form-panel {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}
.form-wrapper {
    width: 100%;
    max-width: 420px;
}
/* Style for icons inside input fields */
.form-control-icon {
    position: absolute;
    top: 50%;
    left: 1rem;
    transform: translateY(-50%);
    color: #6c757d;
}
.form-control.with-icon {
    padding-left: 3rem; /* Make space for the icon */
}
    </style>
</head>
<body>

<div class="d-flex">
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-disc-fill"></i>
            <span>Album Metrics</span>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php">
                    <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
                </a>
            </li>
            <!-- Add more links here for future pages -->
        </ul>
    </div>

    <div class="main-content flex-grow-1">
        <header class="d-flex justify-content-end align-items-center mb-4">
            <div class="text-end">
                <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                <a class="btn btn-outline-secondary btn-sm ms-3" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </header>

        <!-- Main content starts here -->