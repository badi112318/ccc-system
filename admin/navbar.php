<head>
    <style>
        /* Sidebar styling */
        #sidebar {
            position: fixed;
            left: -250px;
            /* Initially hidden */
            top: 56px;
            /* To prevent overlap with topbar */
            height: 100%;
            width: 250px;
            background: #44235e;
            /* Updated background color */
            color: white;
            padding-top: 20px;
            transition: left 0.3s ease-in-out;
        }

        #sidebar.show {
            left: 0;
            /* Show sidebar when toggled */
        }

        .sidebar-list {
            list-style: none;
            padding: 0;
        }

        .sidebar-list .nav-item {
            display: block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        .sidebar-list .nav-item:hover {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .section-title {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #adb5bd;
        }

        /* Hamburger Menu */
        #menu-toggle {
            position: fixed;
            left: 15px;
            top: 15px;
            background: #343a40;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 20px;
            border-radius: 5px;
            z-index: 1000;
        }

        #menu-toggle:hover {
            background: #495057;
        }
    </style>
</head>

<!-- Hamburger Button -->
<button id="menu-toggle">
    <i class="fa fa-bars"></i>
</button>

<!-- Sidebar -->
<nav id="sidebar">
    <ul class="sidebar-list">
        <li><a href="index.php?page=home" class="nav-item nav-home">
                <i class="fa fa-tachometer-alt"></i> Dashboard
            </a></li>

        <li><a href="index.php?page=daily" class="nav-item nav-complaints">
                <i class="fa fa-list-alt"></i> Daily Observation Logs
            </a></li>


                <!--
        <div class="section-title">Master List</div>
        <li><a href="index.php?page=complainants" class="nav-item nav-complainants">
                <i class="fa fa-user-secret"></i> Complainants
            </a></li>
            -->

        <!--
        <li><a href="index.php?page=responders" class="nav-item nav-responders">
                <i class="fa fa-user-shield"></i> Responders
            </a></li>
                        

        <li><a href="index.php?page=stations" class="nav-item nav-stations">
                <i class="fa fa-building"></i> Stations
            </a></li>
                -->
        <div class="section-title">Reports</div>
        <li><a href="index.php?page=complaints_report" class="nav-item nav-complaints_report">
                <i class="fa fa-th-list"></i> Complaints Report
            </a></li>

        <div class="section-title">System Settings</div>
        <?php if ($_SESSION['login_type'] == 1): ?>
            <li><a href="index.php?page=users" class="nav-item nav-users">
                    <i class="fa fa-users"></i> Users
                </a></li>
            <li><a href="index.php?page=site_settings" class="nav-item nav-site_settings">
                    <i class="fa fa-cogs"></i> System Settings
                </a></li>
        <?php endif; ?>
            
        <div class="section-title">User Settings</div>
        <li><a href="javascript:void(0)" id="sidebar_manage_account" class="nav-item">
                <i class="fa fa-cog"></i> Manage Account
            </a></li>
        <li><a href="ajax.php?action=logout" class="nav-item">
                <i class="fa fa-power-off"></i> Logout
            </a></li>
        <li><a href="ajax.php?action=history" class="nav-item">
            <i class="fa fa-history"></i> History
        </a></li>
    </ul>
</nav>

<script>
    document.getElementById('sidebar_manage_account').addEventListener('click', function () {
        uni_modal("Manage Account", "manage_user.php?id=<?php echo $_SESSION['login_id']; ?>&mtype=own");
    });

    // Hamburger Menu Toggle
    document.getElementById('menu-toggle').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>

