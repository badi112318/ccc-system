<?php
session_start();

// Ensure session variables exist
$login_type = $_SESSION['login_type'] ?? null;
$login_id = $_SESSION['login_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? "User"; // Default to 'User' if no name exists
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CCTV UNIT</title>

  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Additional CSS Files -->
  <link rel="stylesheet" href="assets/css/fontawesome.css">
  <link rel="stylesheet" href="assets/css/templatemo-style.css">
  <link rel="stylesheet" href="assets/css/owl.css">

  <style>
    body {
      background: white;
      display: flex;
      height: 100vh;
      margin: 0;
      color: black;
    }

    #sidebar {
      width: 250px;
      height: 100vh;
      background: #44235e;
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      overflow-y: auto;
      transition: all 0.3s ease;
      z-index: 1000;
      display: flex;
      flex-direction: column;
    }

    #sidebar.active {
      left: -250px;
    }

    #sidebar .nav-item {
      padding: 15px;
      font-size: 16px;
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      transition: 0.3s;
    }

    #sidebar .nav-item:hover,
    #sidebar .nav-item.active {
      background: rgb(63, 146, 209);
      border-radius: 5px;
    }

    #sidebar .section-title {
      padding: 10px;
      font-size: 14px;
      color: #e0e0e0;
      text-transform: uppercase;
      font-weight: bold;
    }

    #sidebar-header {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      padding: 15px;
    }
    #sidebar-header .hamburger {
      font-size: 24px;
      color: white;
      cursor: pointer;
      transition: 0.3s;
      z-index: 1100;
      /* Ensure it's on top of the sidebar */
    }

    #sidebar-header .hamburger:hover {
      color: #9dd7f8;
    }

    #content {
      margin-left: 250px;
      padding: 20px;
      width: calc(100% - 250px);
      overflow-y: auto;
      max-width: 31.89in;
      /* Max width of the content to fit screen width */
      max-height: 19.09in;
      /* Max height of the content to fit screen height */
      transition: all 0.3s ease;
    }

    #content.expanded {
      margin-left: 0;
      width: 100%;
    }

    .navbar {
      background: rgb(44, 56, 89);
      color: white;
    }

    .navbar-brand {
      font-weight: bold;
      color: white;
    }

    .navbar-brand:hover {
      color: #9dd7f8;
    }

    @media (max-width: 768px) {
      #sidebar {
        left: -250px;
      }

      #sidebar.active {
        left: 0;
      }

      #content {
        margin-left: 0;
        width: 100%;
      }
    }

    /* Style for the logo */
    .sidebar-logo {
      margin-top: auto;
      /* Ensures logo is at the bottom */
      padding: 15px;
      text-align: center;
    }

    .sidebar-logo img {
      width: 220px;
      height: auto;
      display: block;
      margin: 15 auto;
      border: px solid #44235e;
    }
    .icon-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 8vh; /* Adjust height as needed */
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <nav id="sidebar">
    <div id="sidebar-header">
      <div class="hamburger" id="sidebarToggle">
      <div class="icon-container">
          <i class="fa fa-bars"></i>
          <i class="fa fa-bars"></i>
          <i class="fa fa-bars"></i>
          <i class="fa fa-c"></i>
          <i class="fa fa-c"></i>
          <i class="fa fa-t "></i>
          <i class="fa fa-v"></i>
          <i class="fa fa-bars"></i>
          <i class="fa fa-bars"></i>
          <i class="fa fa-bars"></i>
          <i class="fa fa-bars"></i>
      </div>
      </div>
    </div>

    <div class="sidebar-list">
      <a href="index.php?page=home" class="nav-item nav-home">
        <i class="fa fa-tachometer-alt"></i>&nbsp; Dashboard
      </a>
      <div class="section-title">Reports</div>
      <a href="index.php?page=daily" class="nav-item nav-daily">
        <i class="fas fa-clipboard-list"></i>&nbsp; Daily Observation
      </a>
      <a href="index.php?page=footages" class="nav-item nav-footages" id="footagesLink">
          <i class="fas fa-eye"></i>&nbsp; Footages
      </a>

    <div   id="footagesCategory" class="footages-category" style="display: none;">
        <!-- Category content goes here -->
        <p>Category content goes here...</p>
    </div>


      <div class="section-title">Summary</div>
      <a href="index.php?page=annual" class="nav-item nav-annual">
        <i class="fas fa-calendar"></i>&nbsp; Annual
      </a>
      <a href="index.php?page=monthly" class="nav-item nav-monthly">
        <i class="fas fa-calendar-alt"></i>&nbsp; Monthly
      </a>
      <a href="index.php?page=history" class="nav-item nav-history">
        <i class="fas fa-history"></i>&nbsp; History
      </a>

      <a href="logout.php" class="nav-item">
        <i class="fa fa-power-off"></i>&nbsp; Logout
      </a>

      <div class="sidebar-logo">
        <img src="picture/cctv.png" alt="CCTV Logo">
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div id="content">
    <nav class="navbar navbar-expand-lg navbar-dark"
      style="background-color: #44235e; padding-left: 1rem; padding-right: 1rem;">
      <a class="navbar-brand mx-auto" href="index.php?page=home">COMMUNICATION COMMAND CENTRAL - CCTV UNIT</a>
    </nav>

    <div class="p-3">
      <?php
      // Sanitize page parameter to prevent directory traversal
      $page = isset($_GET['page']) ? basename($_GET['page']) : 'home';
      $file = "{$page}.php";

      // Include the requested page or fallback to home
      if (file_exists($file)) {
        include $file;
      } else {
        include "home.php";
      }
      ?>
    </div>
  </div>

  <!-- Scripts -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/browser.min.js"></script>
  <script src="assets/js/breakpoints.min.js"></script>
  <script src="assets/js/transition.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/custom.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const sidebar = document.getElementById('sidebar');
      const content = document.getElementById('content');
      const sidebarToggle = document.getElementById('sidebarToggle');

      // Sidebar toggle functionality
      sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        content.classList.toggle('expanded');

        const icon = sidebarToggle.querySelector('i');
        icon.classList.toggle('fa-bars');  // Switch to "bars" icon
        icon.classList.toggle('fa-times');  // Switch to "times" icon

        // Ensure the hamburger icon stays visible by keeping it on top
        sidebarToggle.style.zIndex = 1100;
      });

      // Highlight active navigation item
      const page = "<?php echo $page; ?>";
      const activeNav = document.querySelector(`.nav-${page}`);
      if (activeNav) {
        activeNav.classList.add("active");
      }

      // Initialize DataTables if available
      if (window.jQuery && $.fn.DataTable) {
        $('.table').DataTable();
      }
    });
  </script>

</body>

</html>
