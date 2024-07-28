<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <div class="d-flex gap-2 justify-content-center mb-2 align-items-center mt-4">
                <a href="index.php"><img src="assets/img/logo.png" alt="" width="50" height="50"></a>
                <h4 class="fw-bolder" style="color: #2a3547;">Lab SIMI</h4>
                <br>
            </div>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
    
        <!-- Sidebar navigation--> 
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
            <li class="sidebar-item">
                    <a class="sidebar-link mt-4" href="index.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-user"></i>
                        </span>
                        <span class="hide-menu">Course Dashboard</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Profile</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="profile.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-user"></i>
                        </span>
                        <span class="hide-menu">My Profile</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="change_password.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-password"></i>
                        </span>
                        <span class="hide-menu">Change Password</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Menu</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./notes.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-book"></i>
                        </span>
                        <span class="hide-menu">Notes</span>
                    </a>
                </li>

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                </li>
            </ul>
            <!-- Logout item at the bottom -->
            <ul id="sidebar-bottom">
                <li class="sidebar-item">
                    <a class="sidebar-link" href="../logout.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-login"></i>
                        </span>
                        <span class="hide-menu">Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>

<style>
    .left-sidebar {
        position: relative;
    }
    #sidebar-bottom {
        position: absolute;
        bottom: 0;
        width: 100%;
    }
    #sidebar-bottom .sidebar-item {
        margin-bottom: 1rem; /* Adjust margin as needed */
    }
</style>
