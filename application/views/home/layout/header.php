<style>
    /* Reset Top Gap Above Navbar & Inner Pages */
    html,
    body {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .modern-header,
    .main-header {
        font-family: 'Plus Jakarta Sans', sans-serif;
        position: relative;
        z-index: 100;
        margin-top: 0 !important;
        padding-top: 0 !important;
        top: 0 !important;
    }

    .main-banner {
        padding-top: 40px !important;
        padding-bottom: 35px !important;
        margin-top: 0 !important;
    }

    .breadcrumb {
        padding: 12px 0 !important;
        margin-bottom: 25px !important;
    }

    .modern-top-bar {
        background: #0f172a;
        color: #94a3b8;
        font-size: 0.8rem;
        padding: 6px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .modern-top-bar a {
        color: #cbd5e1;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .modern-top-bar a:hover {
        color: #3b82f6;
    }

    .topbar-pill {
        background: rgba(255, 255, 255, 0.06);
        padding: 3px 12px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .modern-navbar {
        background: #1e293b !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.18);
        padding: 6px 0 !important;
        transition: all 0.3s ease;
    }

    .modern-navbar .navbar-brand img {
        max-height: 36px;
        object-fit: contain;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }

    .modern-navbar .nav-link {
        color: #e2e8f0 !important;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 6px 14px !important;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .modern-navbar .nav-link:hover,
    .modern-navbar .nav-item.active .nav-link {
        color: #ffffff !important;
        background: rgba(59, 130, 246, 0.2);
    }

    .modern-nav-btn {
        background: #2563eb !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        padding: 6px 18px !important;
        border-radius: 8px !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35) !important;
        transition: all 0.25s ease !important;
        text-decoration: none !important;
        display: inline-block !important;
    }

    .modern-nav-btn:hover,
    .modern-nav-btn:focus,
    .modern-nav-btn:active {
        background: #1d4ed8 !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.5) !important;
    }

    .modern-navbar .dropdown-menu {
        background: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 10px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3) !important;
    }

    .modern-navbar .dropdown-item {
        color: #cbd5e1 !important;
        padding: 8px 16px !important;
        font-weight: 500 !important;
        font-size: 0.88rem !important;
    }

    .modern-navbar .dropdown-item:hover {
        background: rgba(59, 130, 246, 0.2) !important;
        color: #ffffff !important;
    }

    /* Mobile Responsiveness & Hamburger Button Fixes */
    .modern-toggler {
        background: rgba(255, 255, 255, 0.1) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        border-radius: 8px !important;
        padding: 4px 10px !important;
        color: #ffffff !important;
        outline: none !important;
        box-shadow: none !important;
    }

    .modern-toggler i {
        font-size: 1.1rem;
        color: #ffffff !important;
    }

    @media (max-width: 991px) {
        .modern-navbar {
            padding: 4px 0 !important;
        }

        .modern-navbar .navbar-brand img {
            max-height: 30px !important;
        }

        .modern-navbar .navbar-brand span {
            font-size: 1.05rem !important;
        }

        .modern-toggler {
            padding: 4px 9px !important;
            border-radius: 6px !important;
        }

        .modern-toggler i {
            font-size: 0.95rem !important;
        }

        .modern-navbar #mainNav {
            background: #0f172a !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 10px !important;
            padding: 10px 12px !important;
            margin-top: 8px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4) !important;
        }

        .modern-navbar #mainNav.show {
            display: block !important;
        }

        .modern-navbar .navbar-nav {
            align-items: stretch !important;
            gap: 3px !important;
        }

        .modern-navbar .nav-link {
            font-size: 0.88rem !important;
            padding: 7px 10px !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 6px !important;
        }

        .modern-nav-btn {
            width: 100% !important;
            text-align: center !important;
            margin-top: 5px !important;
            font-size: 0.88rem !important;
            padding: 7px 14px !important;
            border-radius: 6px !important;
        }

        .ms-lg-3 {
            margin-left: 0 !important;
        }
    }
</style>

<!-- Header Starts -->
<header class="main-header modern-header">
    <!-- Top Bar Starts -->
    <div class="modern-top-bar d-none d-md-block">
        <div class="container px-md-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span class="topbar-pill"><i class="far fa-clock me-2 text-warning"></i>
                        <?php echo !empty($cms_setting['working_hours']) ? $cms_setting['working_hours'] : 'Mon - Sat: 8:00 AM - 3:00 PM'; ?></span>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <?php if (!empty($cms_setting['email'])): ?>
                        <a href="mailto:<?php echo $cms_setting['email']; ?>"><i class="far fa-envelope text-info me-1"></i>
                            <?php echo $cms_setting['email']; ?></a>
                    <?php endif; ?>
                    <?php if (!empty($cms_setting['mobile_no'])): ?>
                        <span><i class="fas fa-phone-alt text-success me-1"></i>
                            <?php echo $cms_setting['mobile_no']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Top Bar Ends -->

    <!-- Navbar Starts -->
    <div class="modern-navbar"
        style="background-color: <?php echo !empty($cms_setting['menu_color']) ? $cms_setting['menu_color'] : '#1e293b'; ?>;">
        <div class="container px-md-4">
            <nav id="nav" class="navbar navbar-expand-lg navbar-dark p-0">
                <!-- Logo -->
                <?php $homeURL = base_url(!empty($cms_setting['url_alias']) ? $cms_setting['url_alias'] : ''); ?>
                <a href="<?php echo $homeURL ?>" class="navbar-brand">
                    <?php if (!empty($cms_setting['logo'])): ?>
                        <img src="<?php echo base_url('uploads/frontend/images/' . $cms_setting['logo'] . img_reload()); ?>"
                            alt="School Logo">
                    <?php else: ?>
                        <span class="font-weight-bold text-white fs-4"><i
                                class="fas fa-graduation-cap text-warning me-2"></i><?php echo !empty($cms_setting['application_title']) ? $cms_setting['application_title'] : 'School'; ?></span>
                    <?php endif; ?>
                </a>

                <!-- Mobile Hamburger Toggle Button -->
                <button class="navbar-toggler modern-toggler" type="button" data-toggle="collapse"
                    data-bs-toggle="collapse" data-target="#mainNav" data-bs-target="#mainNav" aria-controls="mainNav"
                    aria-expanded="false" aria-label="Toggle navigation" id="mobileNavToggleBtn">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Menu Items -->
                <div id="mainNav" class="navbar-collapse collapse flex-grow-1">
                    <ul class="navbar-nav mx-auto align-items-center gap-2">
                        <?php
                        $school = $this->uri->segment(1);
                        $result = $this->home_model->menuList($school);
                        $authenticationURL = base_url((!empty($cms_setting['url_alias']) ? $cms_setting['url_alias'] : '') . '/authentication');

                        foreach ($result as $key => $row) {
                            $active_menu = ($currentURL == $row['url']) ? ' active' : '';
                            $op_new_tab = $row['open_new_tab'] ? "target='_blank'" : "";
                            if (isset($cms_setting['online_admission']) && $cms_setting['online_admission'] == 0 && $row['alias'] == 'admission')
                                continue;
                            ?>
                            <li class="nav-item <?php echo $active_menu; ?>">
                                <a href="<?php echo $row['url']; ?>" class="nav-link" <?php echo $op_new_tab; ?>><?php echo $row['title']; ?></a>
                            </li>
                        <?php } ?>

                        <li class="nav-item d-lg-none mt-2 w-100">
                            <?php if (!is_loggedin()): ?>
                                <a href="<?php echo $authenticationURL; ?>" class="btn modern-nav-btn w-100"><i
                                        class="fas fa-user me-2"></i> Login</a>
                            <?php else: ?>
                                <a href="<?php echo base_url('dashboard'); ?>" class="btn modern-nav-btn w-100"><i
                                        class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>

                <!-- Desktop Right-Pinned Login Button -->
                <div class="d-none d-lg-flex align-items-center ms-auto">
                    <?php if (!is_loggedin()): ?>
                        <a href="<?php echo $authenticationURL; ?>" class="btn modern-nav-btn"><i
                                class="fas fa-user me-2"></i> Login</a>
                    <?php else: ?>
                        <a href="<?php echo base_url('dashboard'); ?>" class="btn modern-nav-btn"><i
                                class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar Ends -->
</header>

<script type="text/javascript">
    $(document).ready(function () {
        $('#mobileNavToggleBtn').on('click', function (e) {
            e.preventDefault();
            $('#mainNav').toggleClass('show');
        });
    });
</script>