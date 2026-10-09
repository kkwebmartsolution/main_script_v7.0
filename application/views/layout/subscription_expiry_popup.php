<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Strictly only for Admin Panel (Exclude frontend, students, and parents)
if (!is_loggedin() || is_student_loggedin() || is_parent_loggedin()) {
    return;
}

if (!isset($this->saas_model)) {
    $this->load->model('saas_model');
}
$sub_details = $this->saas_model->getSubscriptionExpiryDetails();

if (!$sub_details || empty($sub_details['is_expiring'])) {
    return;
}

$is_expired = !empty($sub_details['is_expired']);
$diff_seconds = max(0, intval($sub_details['diff_seconds']));
$init_days = floor($diff_seconds / 86400);
$init_hours = floor(($diff_seconds % 86400) / 3600);
$init_mins = floor(($diff_seconds % 3600) / 60);
$init_secs = $diff_seconds % 60;

$days_left = $sub_details['days_left'];
$school_name = html_escape($sub_details['school_name']);
$package_name = html_escape($sub_details['package_name']);
$expire_date_formatted = html_escape($sub_details['expire_date_formatted']);
$target_timestamp_ms = $sub_details['expire_timestamp_ms'];
$renew_url = $sub_details['renew_url'];
?>

<!-- Modern Subscription Expiry Countdown Modal -->
<style>
#subscriptionExpiryModal.modal {
    z-index: 10550 !important;
}
#subscriptionExpiryModal .modal-backdrop {
    z-index: 10540 !important;
}
.sub-modal-dialog {
    max-width: 520px;
    margin: 45px auto;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}
.sub-modal-content {
    border: none;
    border-radius: 24px;
    box-shadow: 0 25px 60px -12px rgba(15, 23, 42, 0.35), 0 0 0 1px rgba(226, 232, 240, 0.8);
    overflow: hidden;
    background: #ffffff;
    position: relative;
    animation: subModalFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes subModalFadeIn {
    0% {
        opacity: 0;
        transform: scale(0.92) translateY(20px);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}
/* Top Glowing Gradient Bar */
.sub-modal-accent-bar {
    height: 6px;
    width: 100%;
    background: <?php echo $is_expired ? 'linear-gradient(90deg, #dc2626, #b91c1c)' : 'linear-gradient(90deg, #f59e0b, #ea580c, #dc2626)'; ?>;
}
.sub-modal-header {
    padding: 24px 28px 12px;
    border-bottom: none;
    position: relative;
    text-align: center;
}
.sub-modal-header .close {
    position: absolute;
    top: 18px;
    right: 20px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #64748b;
    opacity: 0.85;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    line-height: 1;
    border: none;
    outline: none;
    transition: all 0.2s ease;
    cursor: pointer;
}
.sub-modal-header .close:hover {
    background: #e2e8f0;
    color: #0f172a;
    transform: rotate(90deg);
}
/* Pulsing Icon Badge */
.sub-icon-beacon {
    width: 72px;
    height: 72px;
    margin: 0 auto 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    position: relative;
    color: #ffffff;
    background: <?php echo $is_expired ? 'linear-gradient(135deg, #ef4444, #b91c1c)' : 'linear-gradient(135deg, #f59e0b, #ea580c)'; ?>;
    box-shadow: 0 10px 25px <?php echo $is_expired ? 'rgba(239, 68, 68, 0.4)' : 'rgba(245, 158, 11, 0.4)'; ?>;
    animation: subPulseRing 2.2s infinite;
}
@keyframes subPulseRing {
    0% {
        box-shadow: 0 0 0 0 <?php echo $is_expired ? 'rgba(239, 68, 68, 0.6)' : 'rgba(245, 158, 11, 0.6)'; ?>;
    }
    70% {
        box-shadow: 0 0 0 16px rgba(245, 158, 11, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
    }
}
.sub-alert-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 10px;
    background: <?php echo $is_expired ? '#fef2f2' : '#fffbeb'; ?>;
    color: <?php echo $is_expired ? '#b91c1c' : '#b45309'; ?>;
    border: 1px solid <?php echo $is_expired ? '#fecaca' : '#fde68a'; ?>;
}
.sub-modal-title {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px 0;
    line-height: 1.3;
}
.sub-modal-subtitle {
    font-size: 13.5px;
    color: #64748b;
    margin: 0 auto;
    max-width: 420px;
    line-height: 1.5;
}

/* Modal Body */
.sub-modal-body {
    padding: 16px 28px 24px;
}

/* Countdown Grid */
.sub-countdown-container {
    background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
    border-radius: 18px;
    padding: 18px 16px;
    margin-bottom: 20px;
    box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.05), 0 10px 25px -5px rgba(15, 23, 42, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.sub-countdown-grid {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.sub-time-box {
    flex: 1;
    min-width: 65px;
    text-align: center;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 10px 6px;
    transition: transform 0.2s ease;
}
.sub-time-box:hover {
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 0.08);
}
.sub-time-digit {
    font-size: 28px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1;
    font-variant-numeric: tabular-nums;
    text-shadow: 0 0 14px <?php echo $is_expired ? 'rgba(239, 68, 68, 0.6)' : 'rgba(245, 158, 11, 0.5)'; ?>;
    display: block;
    margin-bottom: 5px;
}
.sub-time-label {
    font-size: 10px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    display: block;
}
.sub-time-separator {
    color: #f59e0b;
    font-size: 24px;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 14px;
    animation: subColonPulse 1s ease-in-out infinite;
    user-select: none;
}
@keyframes subColonPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.35; transform: scale(0.9); }
}

/* Info Pill Details */
.sub-info-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px 16px;
    margin-bottom: 22px;
}
.sub-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    padding: 5px 0;
}
.sub-info-row:not(:last-child) {
    border-bottom: 1px dashed #e2e8f0;
}
.sub-info-label {
    color: #64748b;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}
.sub-info-value {
    color: #0f172a;
    font-weight: 700;
    text-align: right;
}

/* Modal Actions */
.sub-actions {
    display: flex;
    gap: 12px;
}
.btn-sub-renew {
    flex: 2;
    background: <?php echo $is_expired ? 'linear-gradient(135deg, #dc2626 0%, #991b1b 100%)' : 'linear-gradient(135deg, #ea580c 0%, #dc2626 100%)'; ?>;
    color: #ffffff !important;
    border: none;
    border-radius: 12px;
    padding: 12px 20px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none !important;
    box-shadow: 0 8px 20px -4px <?php echo $is_expired ? 'rgba(220, 38, 38, 0.45)' : 'rgba(234, 88, 12, 0.45)'; ?>;
    transition: all 0.25s ease;
    cursor: pointer;
}
.btn-sub-renew:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px -4px <?php echo $is_expired ? 'rgba(220, 38, 38, 0.6)' : 'rgba(234, 88, 12, 0.6)'; ?>;
    color: #ffffff !important;
}
.btn-sub-renew:active {
    transform: translateY(0);
}
.btn-sub-dismiss {
    flex: 1;
    background: #f1f5f9;
    color: #475569 !important;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    cursor: pointer;
}
.btn-sub-dismiss:hover {
    background: #e2e8f0;
    color: #1e293b !important;
}

@media (max-width: 480px) {
    .sub-modal-dialog {
        margin: 15px;
    }
    .sub-countdown-grid {
        gap: 4px;
    }
    .sub-time-box {
        min-width: 50px;
        padding: 8px 4px;
    }
    .sub-time-digit {
        font-size: 22px;
    }
    .sub-actions {
        flex-direction: column-reverse;
    }
}
</style>

<div class="modal fade" id="subscriptionExpiryModal" tabindex="-1" role="dialog" aria-labelledby="subModalTitle" aria-hidden="true" data-backdrop="true" data-keyboard="true">
    <div class="modal-dialog sub-modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content sub-modal-content">
            <!-- Accent Top Line -->
            <div class="sub-modal-accent-bar"></div>

            <!-- Modal Header -->
            <div class="sub-modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="<?php echo translate('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
                
                <!-- Animated Beacon Icon -->
                <div class="sub-icon-beacon">
                    <i class="fas <?php echo $is_expired ? 'fa-ban' : 'fa-hourglass-half'; ?>"></i>
                </div>

                <div class="sub-alert-tag" id="subStatusTag">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php if ($is_expired): ?>
                        <span>Subscription Expired</span>
                    <?php else: ?>
                        <span>Expires In <?php echo $days_left; ?> Day<?php echo $days_left > 1 ? 's' : ''; ?></span>
                    <?php endif; ?>
                </div>

                <h3 class="sub-modal-title" id="subModalTitle">
                    <?php echo $is_expired ? 'Subscription Has Expired!' : 'Subscription Expiring Soon!'; ?>
                </h3>
                <p class="sub-modal-subtitle">
                    <?php echo $is_expired ? 'Your school plan subscription has expired. Please renew now to maintain uninterrupted access to all school management features.' : 'Your school subscription will expire shortly. Renew your plan before the expiration deadline to avoid service disruption.'; ?>
                </p>
            </div>

            <!-- Modal Body -->
            <div class="sub-modal-body">
                <!-- Countdown Timer -->
                <div class="sub-countdown-container" id="subCountdownWrapper" data-target-time="<?php echo $target_timestamp_ms; ?>" data-remaining-seconds="<?php echo $diff_seconds; ?>" data-is-expired="<?php echo $is_expired ? '1' : '0'; ?>">
                    <div class="sub-countdown-grid">
                        <!-- Days -->
                        <div class="sub-time-box">
                            <span class="sub-time-digit" id="subTimerDays"><?php echo sprintf('%02d', $init_days); ?></span>
                            <span class="sub-time-label"><?php echo translate('days'); ?></span>
                        </div>
                        <div class="sub-time-separator">:</div>
                        <!-- Hours -->
                        <div class="sub-time-box">
                            <span class="sub-time-digit" id="subTimerHours"><?php echo sprintf('%02d', $init_hours); ?></span>
                            <span class="sub-time-label">Hours</span>
                        </div>
                        <div class="sub-time-separator">:</div>
                        <!-- Minutes -->
                        <div class="sub-time-box">
                            <span class="sub-time-digit" id="subTimerMinutes"><?php echo sprintf('%02d', $init_mins); ?></span>
                            <span class="sub-time-label">Mins</span>
                        </div>
                        <div class="sub-time-separator">:</div>
                        <!-- Seconds -->
                        <div class="sub-time-box">
                            <span class="sub-time-digit" id="subTimerSeconds"><?php echo sprintf('%02d', $init_secs); ?></span>
                            <span class="sub-time-label">Secs</span>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="sub-info-card">
                    <div class="sub-info-row">
                        <span class="sub-info-label"><i class="fas fa-school text-primary"></i> School Name</span>
                        <span class="sub-info-value"><?php echo $school_name; ?></span>
                    </div>
                    <div class="sub-info-row">
                        <span class="sub-info-label"><i class="fas fa-layer-group text-info"></i> Current Package</span>
                        <span class="sub-info-value"><?php echo $package_name; ?></span>
                    </div>
                    <div class="sub-info-row">
                        <span class="sub-info-label"><i class="far fa-calendar-times text-danger"></i> Expiry Date</span>
                        <span class="sub-info-value text-danger"><?php echo $expire_date_formatted; ?></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="sub-actions">
                    <button type="button" class="btn btn-sub-dismiss" data-dismiss="modal">
                        <i class="far fa-clock mr-1"></i> Remind Later
                    </button>
                    <a href="<?php echo $renew_url; ?>" class="btn btn-sub-renew">
                        <i class="fas fa-sync-alt"></i> Renew Subscription Now <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
(function() {
    function startSubscriptionCountdown() {
        var wrapper = document.getElementById('subCountdownWrapper');
        if (!wrapper) return;

        var initialSecs = parseInt(wrapper.getAttribute('data-remaining-seconds'), 10);
        var isExpiredInitial = wrapper.getAttribute('data-is-expired') === '1';
        var pageLoadTime = new Date().getTime();

        var elDays = document.getElementById('subTimerDays');
        var elHours = document.getElementById('subTimerHours');
        var elMins = document.getElementById('subTimerMinutes');
        var elSecs = document.getElementById('subTimerSeconds');

        function padZero(num) {
            return num < 10 ? '0' + num : '' + num;
        }

        function tick() {
            var elapsed = Math.floor((new Date().getTime() - pageLoadTime) / 1000);
            var remaining = initialSecs - elapsed;

            if (remaining <= 0 || isExpiredInitial) {
                if (elDays) elDays.textContent = '00';
                if (elHours) elHours.textContent = '00';
                if (elMins) elMins.textContent = '00';
                if (elSecs) elSecs.textContent = '00';

                var tag = document.getElementById('subStatusTag');
                if (tag) {
                    tag.innerHTML = '<i class="fas fa-ban"></i> <span>Subscription Expired</span>';
                    tag.style.background = '#fef2f2';
                    tag.style.color = '#b91c1c';
                    tag.style.borderColor = '#fecaca';
                }
                var title = document.getElementById('subModalTitle');
                if (title) {
                    title.textContent = 'Subscription Has Expired!';
                }
                return;
            }

            var days = Math.floor(remaining / 86400);
            var hours = Math.floor((remaining % 86400) / 3600);
            var minutes = Math.floor((remaining % 3600) / 60);
            var seconds = remaining % 60;

            if (elDays) elDays.textContent = padZero(days);
            if (elHours) elHours.textContent = padZero(hours);
            if (elMins) elMins.textContent = padZero(minutes);
            if (elSecs) elSecs.textContent = padZero(seconds);
        }

        tick();
        var timerInterval = setInterval(tick, 1000);

        $('#subscriptionExpiryModal').on('hidden.bs.modal', function() {
            // Keep interval running or can clear if modal destroyed
        });
    }

    // Always show popup on page refresh/load as requested
    function showModalOnLoad() {
        var modalEl = document.getElementById('subscriptionExpiryModal');
        if (!modalEl) return;

        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $('#subscriptionExpiryModal').modal({
                backdrop: true,
                keyboard: true,
                show: true
            });
            startSubscriptionCountdown();
        } else if (typeof $ !== 'undefined') {
            // Standalone fallback if bootstrap.js is not loaded
            var $modal = $('#subscriptionExpiryModal');
            $modal.css({
                'display': 'block',
                'background': 'rgba(15, 23, 42, 0.75)',
                'opacity': '1'
            }).addClass('in show');
            startSubscriptionCountdown();

            $modal.find('[data-dismiss="modal"]').off('click').on('click', function(e) {
                e.preventDefault();
                $modal.removeClass('in show').css('display', 'none');
            });
        } else {
            setTimeout(showModalOnLoad, 100);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showModalOnLoad);
    } else {
        showModalOnLoad();
    }
})();
</script>

