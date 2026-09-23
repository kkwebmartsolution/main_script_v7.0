<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --school-primary: #1e3a8a;
    --school-accent: #3b82f6;
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

<!-- Premium School Hero Section -->
<section class="school-hero-container" <?php echo !empty($first_bg) ? 'style="' . $first_bg . '"' : ''; ?>>
    <div class="container px-md-4">
        <div class="row align-items-center">
            <div class="col-lg-8 text-center text-lg-start">
                <div class="hero-badge-pill mb-4">
                    <i class="fas fa-certificate"></i> Official School Portal & Bulletin
                </div>
                <h1 class="hero-title-gradient mb-3"><?php echo !empty($cms_setting['application_title']) ? $cms_setting['application_title'] : 'SVN Bhakatgaon'; ?></h1>
                <p class="lead text-white-50 mb-4" style="max-width: 650px; font-size: 1.25rem;">
                    Building academic excellence, modern innovation, and future-ready character for every student.
                </p>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                    <a href="<?php echo base_url('bhakatgaon/authentication'); ?>" class="btn glass-action-btn me-3">
                        <i class="fas fa-sign-in-alt me-2"></i> Student / Staff Login
                    </a>
                    <a href="<?php echo base_url('bhakatgaon/admission'); ?>" class="btn glass-outline-btn">
                        <i class="fas fa-file-alt me-2"></i> Online Admission
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block">
                <div class="p-4 rounded-4" style="background: rgba(255,255,255,0.06); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.15);">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-wrapper bg-warning text-dark me-3">
                            <i class="fas fa-award"></i>
                        </div>
                        <div>
                            <h6 class="text-white mb-0 font-weight-bold">A+ Certified School</h6>
                            <small class="text-white-50">Excellence in Education</small>
                        </div>
                    </div>
                    <hr style="border-color: rgba(255,255,255,0.15);">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-wrapper bg-info text-white me-3">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <h6 class="text-white mb-0 font-weight-bold">Digital Campus</h6>
                            <small class="text-white-50">24/7 Online Portal Access</small>
                        </div>
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
                <a href="<?php echo base_url('bhakatgaon/admission'); ?>" class="btn btn-outline-primary w-100 font-weight-bold py-2 rounded-3">Apply Now <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="quick-portal-card">
                <div class="stat-icon-wrapper bg-success text-white mb-3">
                    <i class="fas fa-poll-h"></i>
                </div>
                <h4 class="font-weight-bold text-dark mb-2">Exam Results</h4>
                <p class="text-muted mb-4">Check student examination marks, progress cards, and academic performance online.</p>
                <a href="<?php echo base_url('bhakatgaon/exam_results'); ?>" class="btn btn-outline-success w-100 font-weight-bold py-2 rounded-3">Check Result <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="quick-portal-card">
                <div class="stat-icon-wrapper bg-danger text-white mb-3">
                    <i class="fas fa-id-card"></i>
                </div>
                <h4 class="font-weight-bold text-dark mb-2">Admit Card</h4>
                <p class="text-muted mb-4">Download official examination admit cards and student roll number slips instantly.</p>
                <a href="<?php echo base_url('bhakatgaon/admit_card'); ?>" class="btn btn-outline-danger w-100 font-weight-bold py-2 rounded-3">Download Card <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

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
