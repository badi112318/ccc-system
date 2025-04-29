<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('admin/db_connect.php');
ob_start();
include('header.php');
?>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">

  <title>CCTV UNIT</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Additional CSS Files -->
  <link rel="stylesheet" href="assets/css/fontawesome.css">
  <link rel="stylesheet" href="assets/css/templatemo-style.css">
  <link rel="stylesheet" href="assets/css/owl.css">

</head>

<style>
  body,
  html {
    height: 100%;
    margin: 0;
    padding: 0;
    background-image: url('picture/cctv.png');
    background-size: cover;
    background-position: center;
    color: white !important;
  }

  #overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: -1;
  }

  #main-field {
    min-height: 100vh;
    background-color: rgba(0, 0, 0, 0.7) !important;
    color: white;
    padding: 20px;
    border-radius: 10px;
  }

  .navbar {
    background-color: black !important;
  }

  .footer {
    background-color: black !important;
    padding: 20px 0;
    text-align: center;
  }

  .navbar-nav-center {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
  }

  .logo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
  }
</style>

<body id="page-top">
  <div id="overlay"></div>

  <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container d-flex justify-content-center align-items-center">
      <a class="navbar-brand" href="http://localhost/cctv/admin/index.php">
        <img src="picture/eye.png" alt="Logo" class="logo">
      </a>
    </div>
  </nav>

  <main id="main-field" class="container-fluid">
    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    if (file_exists("$page.php")) {
      include "$page.php";
    } else {
      echo "<h3 class='text-white text-center'>Page not found</h3>";
    }
    ?>
  </main>

  <footer class="footer">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h2 class="mt-0">Contact us</h2>
          <hr class="divider my-4" />
        </div>
      </div>
      <div class="row">
        <div class="col-lg-4 ml-auto text-center mb-5 mb-lg-0">
          <i class="fas fa-phone fa-3x mb-3 text-muted"></i>
          <div><?php echo $_SESSION['system']['contact'] ?? '9-1-1'; ?></div>
        </div>
        <div class="col-lg-4 mr-auto text-center">
          <i class="fas fa-envelope fa-3x mb-3 text-muted"></i>
          <a class="d-block text-white" href="mailto:<?php echo $_SESSION['system']['email'] ?? 'cctv@gmail.com'; ?>">
            <?php echo $_SESSION['system']['email'] ?? 'cctv@gmail.com'; ?>
          </a>
        </div>
      </div>
    </div>
    <br>
    <div class="container small text-center text-muted">
      CDRRMO &copy; 2025 - <?php echo $_SESSION['system']['name'] ?? 'CDRRMO'; ?>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script src="assets/js/browser.min.js"></script>
  <script src="assets/js/breakpoints.min.js"></script>
  <script src="assets/js/transition.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/custom.js"></script>

  <?php $conn->close(); ?>
</body>

</html>