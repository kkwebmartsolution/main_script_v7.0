<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --school-primary: #1e3a8a;
    --school-accent: #2563eb;
    --school-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #312e81 100%);
    --school-gold: #f59e0b;
}

body {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    background-color: #f8fafc;
}

.school-hero-container {
    background: var(--school-gradient);
    position: relative;
    overflow: hidden;
    padding: 90px 0 110px 0;
    color: #ffffff;
}

.school-hero-container::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(0,0,0,0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.hero-badge-pill {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fbbf24;
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.hero-title-gradient {
    font-size: 3.8rem;
    font-weight: 800;
    letter-spacing: -1px;
    background: linear-gradient(180deg, #ffffff 0%, #cbd5e1 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1.15;
}

.glass-action-btn {
    background: #2563eb !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    padding: 14px 32px;
    border-radius: 14px;
    border: none;
    box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);
    transition: all 0.3s ease;
    text-decoration: none !important;
    display: inline-block !important;
}

.glass-action-btn:hover, .glass-action-btn:focus {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.6);
    background: #1d4ed8 !important;
    color: #ffffff !important;
}

.glass-outline-btn {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff !important;
    font-weight: 600;
    padding: 14px 32px;
    border-radius: 14px;
    transition: all 0.3s ease;
}

.glass-outline-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-3px);
}

.floating-stats-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08);
    margin-top: -50px;
    position: relative;
    z-index: 10;
    padding: 30px;
    border: 1px solid rgba(226, 232, 240, 0.8);
}

.stat-icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
}

.quick-portal-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    padding: 28px 24px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    position: relative;
    overflow: hidden;
}

.quick-portal-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--school-accent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.quick-portal-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 35px -10px rgba(30, 58, 138, 0.12);
    border-color: #cbd5e1;
}

.quick-portal-card:hover::before {
    opacity: 1;
}

.section-header-pill {
    color: var(--school-accent);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-size: 0.85rem;
}

.section-main-title {
    font-weight: 800;
    color: #0f172a;
    font-size: 2.3rem;
}

/* Facility Card Styling */
.facility-card-premium {
    background: #ffffff;
    border-radius: 16px;
    padding: 28px 22px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    height: 100%;
}

.facility-card-premium:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(30, 58, 138, 0.08);
    border-color: #93c5fd;
}

.facility-icon {
    width: 55px;
    height: 55px;
    border-radius: 14px;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.facility-card-premium:hover .facility-icon {
    background: #2563eb;
    color: #ffffff;
}

/* Enhanced Teachers Card Styling (2 Rows) */
.teacher-card-wrapper {
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    height: 100%;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

.teacher-card-wrapper:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 35px rgba(15, 23, 42, 0.1);
    border-color: #cbd5e1;
}

.teacher-img-box {
    position: relative;
    overflow: hidden;
    padding-top: 100%; /* 1:1 Aspect Ratio */
    background: #f1f5f9;
}

.teacher-img-box img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.teacher-card-wrapper:hover .teacher-img-box img {
    transform: scale(1.06);
}

.teacher-social-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 12px;
    background: linear-gradient(to top, rgba(15,23,42,0.85), transparent);
    display: flex;
    justify-content: center;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.teacher-card-wrapper:hover .teacher-social-overlay {
    opacity: 1;
}

.teacher-social-overlay a {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(255,255,255,0.25);
    backdrop-filter: blur(5px);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.teacher-social-overlay a:hover {
    background: #2563eb;
    color: #ffffff;
    transform: scale(1.1);
}

.teacher-info {
    padding: 20px 16px;
    text-align: center;
}

.teacher-info h5 {
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
    font-size: 1.1rem;
}

.teacher-dept-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 50px;
    background: #eff6ff;
    color: #2563eb;
    font-size: 0.78rem;
    font-weight: 600;
}

@media (max-width: 768px) {
    .school-hero-container {
        padding: 50px 0 70px 0 !important;
        text-align: center !important;
    }
    .hero-title-gradient {
        font-size: 2.2rem !important;
    }
    .hero-badge-pill {
        font-size: 0.8rem !important;
        padding: 6px 14px !important;
    }
    .glass-action-btn, .glass-outline-btn {
        width: 100% !important;
        padding: 12px 20px !important;
        margin-bottom: 10px !important;
        text-align: center !important;
        display: block !important;
    }
    .floating-stats-card {
        margin-top: -30px !important;
        padding: 20px 15px !important;
    }
    .floating-stats-card .border-end {
        border-right: none !important;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
}
</style>

<?php $alias = !empty($cms_setting['url_alias']) ? $cms_setting['url_alias'] : ''; ?>
<?php
$slider_images = array();
if (!empty($sliders) && is_array($sliders)) {
    foreach ($sliders as $s) {
        $elem = json_decode($s['elements'], true);
        if (!empty($elem['image']) && file_exists('uploads/frontend/slider/' . $elem['image'])) {
            $slider_images[] = base_url('uploads/frontend/slider/' . $elem['image']);
        }
    }
}
$first_bg = !empty($slider_images[0]) ? "background: linear-gradient(135deg, rgba(15, 23, 42, 0.82) 0%, rgba(30, 27, 75, 0.86) 100%), url('{$slider_images[0]}') center/cover no-repeat !important;" : "";
?>

<!-- Premium Enhanced School Hero Section -->
<section class="school-hero-container" <?php echo !empty($first_bg) ? 'style="' . $first_bg . '"' : ''; ?>>
    <div class="container px-md-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7 text-center text-lg-start">
                <div class="hero-badge-pill mb-4">
                    <span class="badge bg-warning text-dark me-2 px-2 py-1" style="border-radius: 20px;">NEW</span>
                    <i class="fas fa-certificate"></i> Official School Portal & Digital Campus
                </div>
                <h1 class="hero-title-gradient mb-3"><?php echo !empty($cms_setting['application_title']) ? $cms_setting['application_title'] : 'School Campus'; ?></h1>
                <p class="lead text-white-50 mb-4" style="max-width: 650px; font-size: 1.2rem; line-height: 1.6;">
                    Nurturing academic excellence, modern innovation, and future-ready character for every student with world-class facilities and digital learning.
                </p>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 mb-4">
                    <a href="<?php echo base_url($alias . '/authentication'); ?>" class="btn glass-action-btn me-2">
                        <i class="fas fa-sign-in-alt me-2"></i> Student / Staff Login
                    </a>
                    <a href="<?php echo base_url($alias . '/admission'); ?>" class="btn glass-outline-btn me-2">
                        <i class="fas fa-file-alt me-2"></i> Online Admission
                    </a>
                    <a href="<?php echo base_url($alias . '/contact'); ?>" class="btn glass-outline-btn">
                        <i class="fas fa-envelope me-2"></i> Contact Us
                    </a>
                </div>

                <!-- Quick Highlight Tags -->
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 pt-2">
                    <small class="text-white-50"><i class="fas fa-check-circle text-success me-1"></i> A+ Academic Grade</small>
                    <small class="text-white-50"><i class="fas fa-check-circle text-success me-1"></i> Smart Classrooms</small>
                    <small class="text-white-50"><i class="fas fa-check-circle text-success me-1"></i> 100% Safe Campus</small>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="p-4 rounded-4 shadow-lg" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2);">
                    <div class="d-flex align-items-center mb-4">
                        <div class="stat-icon-wrapper bg-warning text-dark me-3 shadow-sm">
                            <i class="fas fa-award"></i>
                        </div>
                        <div>
                            <h5 class="text-white mb-1 font-weight-bold">A+ Accredited Institution</h5>
                            <small class="text-white-50">Recognized for Excellence in Education</small>
                        </div>
                    </div>
                    <div class="p-3 rounded-3 mb-3" style="background: rgba(0,0,0,0.2); border-left: 4px solid #3b82f6;">
                        <small class="text-warning font-weight-bold d-block mb-1"><i class="fas fa-bullhorn me-1"></i> Admission Notice</small>
                        <small class="text-white">Admissions Open for Academic Session. Apply online now through the portal!</small>
                    </div>
                    <hr style="border-color: rgba(255,255,255,0.15);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon-wrapper bg-info text-white me-3 shadow-sm">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-0 font-weight-bold">Digital Campus</h6>
                                <small class="text-white-50">24/7 Portal Access</small>
                            </div>
                        </div>
                        <a href="<?php echo base_url($alias . '/admission'); ?>" class="btn btn-sm btn-light font-weight-bold rounded-3">Apply <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Floating Stats Counter -->
<?php 
$st_count = isset($real_stats['students']) ? $real_stats['students'] : 0;
$fa_count = isset($real_stats['faculty']) ? $real_stats['faculty'] : 0;
$cl_count = isset($real_stats['classes']) ? $real_stats['classes'] : 0;
$su_count = isset($real_stats['subjects']) ? $real_stats['subjects'] : 0;
?>
<div class="container px-md-4">
    <div class="floating-stats-card">
        <div class="row g-4 text-center text-md-start">
            <div class="col-6 col-md-3 border-end">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                    <div class="stat-icon-wrapper bg-primary text-white me-3">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 font-weight-bold text-dark"><?php echo $st_count; ?></h3>
                        <small class="text-muted font-weight-bold">Enrolled Students</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 border-end">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                    <div class="stat-icon-wrapper bg-success text-white me-3">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 font-weight-bold text-dark"><?php echo $fa_count; ?></h3>
                        <small class="text-muted font-weight-bold">Expert Faculty</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 border-end">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                    <div class="stat-icon-wrapper bg-warning text-dark me-3">
                        <i class="fas fa-school"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 font-weight-bold text-dark"><?php echo $cl_count; ?></h3>
                        <small class="text-muted font-weight-bold">Active Classes</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                    <div class="stat-icon-wrapper bg-info text-white me-3">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 font-weight-bold text-dark"><?php echo $su_count; ?></h3>
                        <small class="text-muted font-weight-bold">Subjects / Courses</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Welcome / About Section -->
<?php if (!empty($wellcome)) { 
    $wel_elem = json_decode($wellcome['elements'], true);
?>
<div class="container my-5 px-md-4 py-3">
    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <span class="section-header-pill"><?php echo !empty($wellcome['title']) ? $wellcome['title'] : 'Welcome To Our School'; ?></span>
            <h2 class="section-main-title mt-2 mb-3"><?php echo !empty($wellcome['subtitle']) ? $wellcome['subtitle'] : 'Empowering Young Minds For A Better Future'; ?></h2>
            <p class="text-muted lead fs-6 mb-4"><?php echo nl2br($wellcome['description']); ?></p>
            <div class="d-flex gap-3">
                <a href="<?php echo base_url($alias . '/about'); ?>" class="btn btn-primary font-weight-bold px-4 py-2 rounded-3">Learn More <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="position-relative">
                <img src="<?php echo base_url('uploads/frontend/home_page/' . $wel_elem['image'] . img_reload()); ?>" alt="School Campus" class="img-fluid rounded-4 shadow-lg w-100" style="max-height: 400px; object-fit: cover;">
            </div>
        </div>
    </div>
</div>
<?php } ?>

<!-- Campus Facilities & Highlights Section (NEW SECTION) -->
<div class="container my-5 px-md-4 py-4">
    <div class="text-center mb-5">
        <span class="section-header-pill">World-Class Infrastructure</span>
        <h2 class="section-main-title mt-2">Campus Facilities & Features</h2>
    </div>
    <div class="row g-4">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="facility-card-premium">
                <div class="facility-icon"><i class="fas fa-laptop-code"></i></div>
                <h5 class="font-weight-bold text-dark mb-2">Smart Classrooms</h5>
                <p class="text-muted small mb-0">Interactive digital boards and multimedia teaching aids for immersive learning.</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="facility-card-premium">
                <div class="facility-icon"><i class="fas fa-flask"></i></div>
                <h5 class="font-weight-bold text-dark mb-2">Science & Computer Labs</h5>
                <p class="text-muted small mb-0">Fully-equipped modern laboratories for practical science and high-speed IT sessions.</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="facility-card-premium">
                <div class="facility-icon"><i class="fas fa-book-reader"></i></div>
                <h5 class="font-weight-bold text-dark mb-2">Library & E-Resources</h5>
                <p class="text-muted small mb-0">Thousands of books, journals, and digital e-learning materials for student research.</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="facility-card-premium">
                <div class="facility-icon"><i class="fas fa-volleyball-ball"></i></div>
                <h5 class="font-weight-bold text-dark mb-2">Sports & Activities</h5>
                <p class="text-muted small mb-0">Expansive sports grounds and expert coaching in indoor and outdoor athletics.</p>
            </div>
        </div>
    </div>
</div>

<!-- Teachers Section (2 ROWS / 8 TEACHERS GRID) -->
<?php
$teacher_list = $this->home_model->get_teacher_list(0, $branchID);
if (!empty($teacher_list)) {
?>
<div class="container my-5 px-md-4 py-4">
    <div class="d-flex flex-wrap align-items-end justify-content-between mb-5">
        <div>
            <span class="section-header-pill">Our Faculty</span>
            <h2 class="section-main-title mt-2 mb-0">Meet Our Experienced Teachers</h2>
        </div>
        <div>
            <a href="<?php echo base_url($alias . '/teachers'); ?>" class="btn btn-outline-primary font-weight-bold px-4 py-2 rounded-3 mt-3 mt-md-0">
                View All Faculty <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <!-- 2 Rows Grid (Col-lg-3 = 4 per row, total 8) -->
    <div class="row g-4">
        <?php foreach ($teacher_list as $row) { ?>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="teacher-card-wrapper">
                    <div class="teacher-img-box">
                        <img src="<?php echo get_image_url('staff', $row['photo']); ?>" alt="<?php echo $row['name']; ?>">
                        <div class="teacher-social-overlay">
                            <?php if (!empty($row['facebook_url'])) { ?><a href="<?php echo $row['facebook_url']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a><?php } ?>
                            <?php if (!empty($row['twitter_url'])) { ?><a href="<?php echo $row['twitter_url']; ?>" target="_blank"><i class="fab fa-twitter"></i></a><?php } ?>
                            <?php if (!empty($row['linkedin_url'])) { ?><a href="<?php echo $row['linkedin_url']; ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a><?php } ?>
                        </div>
                    </div>
                    <div class="teacher-info">
                        <h5><?php echo $row['name']; ?></h5>
                        <span class="teacher-dept-badge"><?php echo !empty($row['department_name']) ? $row['department_name'] : 'Faculty'; ?></span>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php } ?>

<!-- Main Services & Portals Grid -->
<div class="container my-5 px-md-4 py-4">
    <div class="text-center mb-5">
        <span class="section-header-pill">Digital Services</span>
        <h2 class="section-main-title mt-2">School Portals & Quick Access</h2>
    </div>

    <div class="row g-4">
        <div class="col-md-4 mb-4">
            <div class="quick-portal-card">
                <div class="stat-icon-wrapper bg-primary text-white mb-3">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <h4 class="font-weight-bold text-dark mb-2">Online Admission</h4>
                <p class="text-muted mb-4">Submit online admission forms, check application status and document updates online.</p>
                <a href="<?php echo base_url($alias . '/admission'); ?>" class="btn btn-outline-primary w-100 font-weight-bold py-2 rounded-3">Apply Now <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="quick-portal-card">
                <div class="stat-icon-wrapper bg-success text-white mb-3">
                    <i class="fas fa-poll-h"></i>
                </div>
                <h4 class="font-weight-bold text-dark mb-2">Exam Results</h4>
                <p class="text-muted mb-4">Check student examination marks, progress cards, and academic performance online.</p>
                <a href="<?php echo base_url($alias . '/exam_results'); ?>" class="btn btn-outline-success w-100 font-weight-bold py-2 rounded-3">Check Result <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="quick-portal-card">
                <div class="stat-icon-wrapper bg-danger text-white mb-3">
                    <i class="fas fa-id-card"></i>
                </div>
                <h4 class="font-weight-bold text-dark mb-2">Admit Card</h4>
                <p class="text-muted mb-4">Download official examination admit cards and student roll number slips instantly.</p>
                <a href="<?php echo base_url($alias . '/admit_card'); ?>" class="btn btn-outline-danger w-100 font-weight-bold py-2 rounded-3">Download Card <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<?php
$this->db->where('branch_id', $branchID);
$testimonials = $this->db->get('front_cms_testimonial')->result_array();
if (!empty($testimonials)) {
?>
<div class="container my-5 px-md-4 py-4">
    <div class="text-center mb-5">
        <span class="section-header-pill">Parent & Alumni Feedback</span>
        <h2 class="section-main-title mt-2">What People Say About Us</h2>
    </div>
    <div class="row g-4">
        <?php foreach (array_slice($testimonials, 0, 3) as $t_item) { ?>
            <div class="col-md-4 mb-4">
                <div class="p-4 bg-white rounded-4 border h-100 shadow-sm">
                    <div class="text-warning mb-3">
                        <?php for ($star = 1; $star <= 5; $star++) {
                            echo ($star <= $t_item['rank']) ? '<i class="fas fa-star me-1"></i>' : '<i class="far fa-star me-1"></i>';
                        } ?>
                    </div>
                    <p class="text-muted fst-italic mb-4">"<?php echo nl2br($t_item['description']); ?>"</p>
                    <div class="d-flex align-items-center">
                        <img src="<?php echo $this->testimonial_model->get_image_url($t_item['image']); ?>" alt="Parent" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 class="font-weight-bold mb-0 text-dark"><?php echo $t_item['name']; ?></h6>
                            <small class="text-muted"><?php echo $t_item['surname']; ?></small>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php } ?>

<!-- Call To Action Banner -->
<?php if (!empty($cta_box)) { 
    $cta_elem = json_decode($cta_box['elements'], true);
?>
<div class="container my-5 px-md-4">
    <div class="p-5 rounded-4 text-white text-center text-lg-start" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e1b4b 100%);">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h3 class="font-weight-bold text-white mb-2"><?php echo $cta_box['title']; ?></h3>
                <p class="text-white-50 mb-0"><i class="fas fa-phone-alt me-2 text-warning"></i> Call Us Directly: <strong><?php echo !empty($cta_elem['mobile_no']) ? $cta_elem['mobile_no'] : ''; ?></strong></p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo !empty($cta_elem['button_url']) ? $cta_elem['button_url'] : base_url($alias . '/contact'); ?>" class="btn btn-warning font-weight-bold px-4 py-3 rounded-3 text-dark">
                    <?php echo !empty($cta_elem['button_text']) ? $cta_elem['button_text'] : 'Contact Us'; ?> <i class="fas fa-paper-plane ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var heroImages = <?php echo json_encode($slider_images); ?>;
    if (heroImages && heroImages.length > 1) {
        var heroSec = document.querySelector('.school-hero-container');
        if (heroSec) {
            var currentIndex = 0;
            setInterval(function() {
                currentIndex = (currentIndex + 1) % heroImages.length;
                heroSec.style.background = "linear-gradient(135deg, rgba(15, 23, 42, 0.82) 0%, rgba(30, 27, 75, 0.86) 100%), url('" + heroImages[currentIndex] + "') center/cover no-repeat";
            }, 5000);
        }
    }
});
</script>