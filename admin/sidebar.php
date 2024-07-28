<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <div class="d-flex gap-2 justify-content-center mb-2 align-items-center mt-4">
                <a href="index.php"><img src="assets\images\logo.png" alt="" width="50" height="50"></a>
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
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Menu</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./index.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">Home</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./courses.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-book"></i>
                        </span>
                        <span class="hide-menu">Courses</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./modules.php" aria-expanded="false">
                        <span>
                            <i class="bi bi-folder"></i>
                        </span>
                        <span class="hide-menu">Modules</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="./videos.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-video"></i>
                        </span>
                        <span class="hide-menu">Videos</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./users.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-user"></i>
                        </span>
                        <span class="hide-menu">Users</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./pertemuan.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="hide-menu">Meetings</span>
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
        margin-bottom: 1rem;
        /* Adjust margin as needed */
    }
</style>