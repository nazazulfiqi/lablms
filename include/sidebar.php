<div id="layoutSidenav_nav">

                <nav class="sb-sidenav accordion sb-sidenav-primary" id="sidenavAccordion">
                    
                    <div class="sb-sidenav-menu ">
                        <div class="nav text-primary">
                            <div class="sb-sidenav-menu-heading ">Core</div>
                            <a class="nav-link text-primary" href="index.php">
                                <div class="sb-nav-link-icon text-primary"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <a class="nav-link text-primary" href="view-register.php">
                                <div class="sb-nav-link-icon text-primary"><i class="fas fa-users"></i></div>
                                Registered Users
                            </a>
                            <div class="sb-sidenav-menu-heading">Interface</div>
                            <a class="nav-link collapsed text-primary" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon text-primary"><i class="fas fa-columns"></i></div>
                                Category
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse " id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link text-primary" href="category-add.php">Add Category</a>
                                    <a class="nav-link text-primary" href="view-category.php">View Category</a>
                                </nav>
                            </div>

                            <a class="nav-link collapsed text-primary" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePosts" aria-expanded="false" aria-controls="collapsePosts">
                                <div class="sb-nav-link-icon text-primary"><i class="fas fa-columns"></i></div>
                                Posts
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePosts" aria-labelledby="Posts" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link text-primary" href="post-add.php">Add Post</a>
                                    <a class="nav-link text-primary" href="view-post.php">View Post</a>
                                </nav>
                            </div>
                            <a class="nav-link text-primary" href="view-comment.php">
                                <div class="sb-nav-link-icon text-primary"><i class="fas fa-columns"></i></div>
                                Comment
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        <?= $_SESSION['fname'] ?>
                    </div>
                </nav>
            </div>