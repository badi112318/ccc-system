<style>
  .logo {
    margin: auto;
    font-size: 20px;
    background: white;
    padding: 7px 11px;
    border-radius: 50%;
    color: #000000b3;
  }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container-fluid">
    <div class="d-flex align-items-center">
      <span class="text-white fs-5 fw-bold">
        <?php echo isset($_SESSION['system']['name']) ? $_SESSION['system']['name'] : 'System Name'; ?>
      </span>
    </div>

    <div class="dropdown">
      <a href="#" class="text-white dropdown-toggle" id="account_settings" data-bs-toggle="dropdown"
        aria-expanded="false">
        <i class="fa fa-user-circle"></i> <?php echo $_SESSION['login_name']; ?>
      </a>

      <!---
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="account_settings">
        <li><a class="dropdown-item" href="javascript:void(0)" id="manage_my_account">
            <i class="fa fa-cog"></i> Manage Account</a>
        </li>
          -->

        <li><a class="dropdown-item" href="ajax.php?action=logout">
            <i class="fa fa-power-off"></i> Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script>
  document.getElementById('manage_my_account').addEventListener('click', function () {
    uni_modal("Manage Account", "manage_user.php?id=<?php echo $_SESSION['login_id']; ?>&mtype=own");
  });
</script>