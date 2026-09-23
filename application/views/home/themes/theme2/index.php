<!-- Theme 2 Modern Homepage View -->
<div class="theme2-banner bg-dark text-white text-center py-5 mb-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <div class="container py-4">
        <h1 class="display-4 font-weight-bold"><?php echo !empty($cms_setting['application_title']) ? $cms_setting['application_title'] : 'Welcome to School'; ?></h1>
        <p class="lead mb-4"><?php echo !empty($wellcome['subtitle']) ? $wellcome['subtitle'] : 'Excellence in Education & Character'; ?></p>
        <div class="d-flex justify-content-center gap-3">
            <?php if (!empty($cms_setting['online_admission']) && $cms_setting['online_admission'] == 1): ?>
                <a href="<?php echo base_url($cms_setting['url_alias'] . '/admission'); ?>" class="btn btn-warning btn-lg me-2"><i class="fas fa-edit"></i> Apply For Admission</a>
            <?php endif; ?>
            <a href="<?php echo base_url($cms_setting['url_alias'] . '/about'); ?>" class="btn btn-outline-light btn-lg"><i class="fas fa-info-circle"></i> About Us</a>
        </div>
    </div>
</div>

<div class="container px-md-0 main-container mb-5">
    <!-- Features Grid -->
    <?php if (!empty($features)): ?>
        <div class="row g-4 mb-5">
            <?php foreach ($features as $key => $value): 
                $elements = json_decode($value['elements'], true); ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 text-center p-4">
                        <div class="card-body">
                            <div class="icon-box mb-3 text-primary" style="font-size: 2.5rem;">
                                <i class="<?php echo !empty($elements['icon']) ? $elements['icon'] : 'fas fa-graduation-cap'; ?>"></i>
                            </div>
                            <h4 class="card-title font-weight-bold mb-3"><?php echo $value['title']; ?></h4>
                            <p class="card-text text-muted"><?php echo $value['description']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Welcome Section -->
    <?php if (!empty($wellcome)): ?>
        <div class="row align-items-center my-5 p-4 rounded shadow-sm bg-white">
            <div class="col-md-6 mb-3 mb-md-0">
                <img src="<?php echo base_url('uploads/frontend/home_page/' . $wellcome['photo']); ?>" class="img-fluid rounded shadow" alt="Welcome">
            </div>
            <div class="col-md-6">
                <h2 class="text-primary mb-3"><?php echo $wellcome['title']; ?></h2>
                <h5 class="text-secondary mb-3"><?php echo $wellcome['subtitle']; ?></h5>
                <p class="text-muted"><?php echo $wellcome['description']; ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>
