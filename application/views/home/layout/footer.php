<style>
/* Modern School Footer Styles */
.modern-footer {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #0f172a;
    color: #cbd5e1;
    position: relative;
    overflow: hidden;
}

.footer-top-section {
    padding: 70px 0 50px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.footer-brand-title {
    font-weight: 800;
    color: #ffffff;
    font-size: 1.4rem;
}

.footer-desc {
    color: #94a3b8;
    line-height: 1.7;
    font-size: 0.95rem;
}

.footer-section-title {
    color: #ffffff;
    font-weight: 700;
    font-size: 1.15rem;
    margin-bottom: 24px;
    position: relative;
    padding-bottom: 10px;
}

.footer-section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 35px;
    height: 3px;
    background: #3b82f6;
    border-radius: 2px;
}

.footer-links-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links-list li {
    margin-bottom: 12px;
}

.footer-links-list a {
    color: #94a3b8;
    text-decoration: none;
    transition: all 0.2s ease;
    font-size: 0.95rem;
}

.footer-links-list a:hover {
    color: #3b82f6;
    padding-left: 6px;
}

.social-round-btn {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-round-btn:hover {
    background: #3b82f6;
    color: #ffffff;
    transform: translateY(-3px);
}

.copyright-section {
    padding: 24px 0;
    background: #090d16;
    color: #64748b;
    font-size: 0.9rem;
}
</style>

<!-- Footer Starts -->
<footer class="modern-footer">
    <div class="footer-top-section">
        <div class="container px-md-4">
            <div class="row g-4">
                <!-- Column 1: About School -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="pe-lg-3">
                        <?php if (!empty($cms_setting['logo'])): ?>
                            <img src="<?php echo base_url('uploads/frontend/images/' . $cms_setting['logo'] . img_reload()); ?>" alt="School Logo" class="mb-3" style="max-height: 50px;">
                        <?php else: ?>
                            <h3 class="footer-brand-title mb-3"><i class="fas fa-graduation-cap text-warning me-2"></i><?php echo !empty($cms_setting['application_title']) ? $cms_setting['application_title'] : 'School Campus'; ?></h3>
                        <?php endif; ?>

                        <p class="footer-desc mb-4">
                            <?php echo !empty($cms_setting['footer_about_text']) ? $cms_setting['footer_about_text'] : 'Dedicated to delivering academic excellence, character building, and digital learning for students.'; ?>
                        </p>

                        <!-- Social Buttons -->
                        <div class="d-flex gap-2">
                            <?php if (!empty($cms_setting['facebook_url'])): ?>
                                <a href="<?php echo $cms_setting['facebook_url']; ?>" target="_blank" class="social-round-btn"><i class="fab fa-facebook-f"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($cms_setting['twitter_url'])): ?>
                                <a href="<?php echo $cms_setting['twitter_url']; ?>" target="_blank" class="social-round-btn"><i class="fab fa-twitter"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($cms_setting['youtube_url'])): ?>
                                <a href="<?php echo $cms_setting['youtube_url']; ?>" target="_blank" class="social-round-btn"><i class="fab fa-youtube"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($cms_setting['instagram_url'])): ?>
                                <a href="<?php echo $cms_setting['instagram_url']; ?>" target="_blank" class="social-round-btn"><i class="fab fa-instagram"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="footer-section-title">Quick Portals</h5>
                    <ul class="footer-links-list">
                        <?php
                        $school = $this->uri->segment(1);
                        if (empty($school)) {
                            $school = !empty($cms_setting['url_alias']) ? $cms_setting['url_alias'] : '';
                        }
                        ?>
                        <li><a href="<?php echo base_url($school . '/admission'); ?>"><i class="fas fa-angle-right me-2 text-primary"></i> Online Admission</a></li>
                        <li><a href="<?php echo base_url($school . '/exam_results'); ?>"><i class="fas fa-angle-right me-2 text-primary"></i> Exam Results Portal</a></li>
                        <li><a href="<?php echo base_url($school . '/admit_card'); ?>"><i class="fas fa-angle-right me-2 text-primary"></i> Download Admit Card</a></li>
                        <li><a href="<?php echo base_url($school . '/authentication'); ?>"><i class="fas fa-angle-right me-2 text-primary"></i> Student / Staff Login</a></li>
                    </ul>
                </div>

                <!-- Column 3: Contact & Address -->
                <div class="col-lg-4 col-md-12 mb-4">
                    <h5 class="footer-section-title">Contact & Location</h5>
                    <ul class="list-unstyled mb-0" style="color: #94a3b8; font-size: 0.95rem;">
                        <?php if (!empty($cms_setting['address'])): ?>
                            <li class="d-flex mb-3">
                                <i class="fas fa-map-marker-alt text-warning me-3 mt-1"></i>
                                <span><?php echo $cms_setting['address']; ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($cms_setting['mobile_no'])): ?>
                            <li class="d-flex mb-3">
                                <i class="fas fa-phone-alt text-success me-3 mt-1"></i>
                                <span><?php echo $cms_setting['mobile_no']; ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($cms_setting['email'])): ?>
                            <li class="d-flex">
                                <i class="fas fa-envelope text-info me-3 mt-1"></i>
                                <span><a href="mailto:<?php echo $cms_setting['email']; ?>" class="text-decoration-none text-light"><?php echo $cms_setting['email']; ?></a></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright Bottom Bar -->
    <div class="copyright-section text-center">
        <div class="container px-md-4">
            <p class="mb-0">
                <?php echo !empty($cms_setting['copyright_text']) ? $cms_setting['copyright_text'] : '© ' . date('Y') . ' All Rights Reserved.'; ?>
            </p>
        </div>
    </div>
</footer>
<!-- Footer Ends -->