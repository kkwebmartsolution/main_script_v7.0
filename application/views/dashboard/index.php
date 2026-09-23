<?php
$div = 0;
if (get_permission('employee_count_widget', 'is_view')) {
	$div++;	
}
if (get_permission('student_count_widget', 'is_view')) {
	$div++;	
}
if (get_permission('parent_count_widget', 'is_view')) {
	$div++;	
}
if (get_permission('teacher_count_widget', 'is_view')) {
	$div++;	
}
if ($div == 0) {
	$widget1 = 0;
}else{
	$widget1 = 12 / $div;
}

$div2 = 0;
if (get_permission('admission_count_widget', 'is_view')) {
	$div2++;	
}
if (get_permission('voucher_count_widget', 'is_view')) {
	$div2++;	
}
if (get_permission('transport_count_widget', 'is_view') && moduleIsEnabled('transport')) {
	$div2++;	
}
if (get_permission('hostel_count_widget', 'is_view') && moduleIsEnabled('hostel')) {
	$div2++;	
}
if ($div2 == 0) {
	$widget2 = 0;
}else{
	$widget2 = 12 / $div2;
}

$div3 = 12;
if (get_permission('student_birthday_widget', 'is_view') || get_permission('staff_birthday_widget', 'is_view')) {
	$div3 = 9;	
}
?>
<?php if ($sqlMode == true) { ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> This School management system may not work properly because "ONLY_FULL_GROUP_BY" is enabled, <strong>Strongly recommended</strong> - consult with your hosting provider to disable "ONLY_FULL_GROUP_BY" in sql_mode configuration.
    </div>
<?php } ?>

<?php 
if (!is_superadmin_loggedin()) {
	if (!empty($this->saas_model->getSubscriptionsExpiredNotification())) { ?>
    <div class="alert alert-danger">
        <?php echo $this->saas_model->getSubscriptionsExpiredNotification(); ?>
    </div>
<?php } } ?>

<!-- Modern SaaS Dashboard Styling -->
<style>
.dashboard-page {
	font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
}
.dashboard-page .panel {
	border-radius: 16px !important;
	border: 1px solid #e2e8f0 !important;
	box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04) !important;
	background: #ffffff !important;
	transition: transform 0.25s ease, box-shadow 0.25s ease !important;
	margin-bottom: 24px !important;
	overflow: hidden;
}
.dashboard-page .panel:hover {
	box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08) !important;
}
.dashboard-page .chart-title {
	font-family: 'Outfit', sans-serif !important;
	font-size: 18px !important;
	font-weight: 700 !important;
	color: #0f172a !important;
	letter-spacing: -0.3px !important;
}
.dashboard-page .widget-1 .panel-body,
.dashboard-page .widget-2 .panel-body {
	padding: 22px 24px !important;
}
.dashboard-page .widget-col-in i {
	font-size: 24px !important;
	color: #2563eb !important;
	background: #eff6ff !important;
	width: 50px !important;
	height: 50px !important;
	border-radius: 12px !important;
	display: inline-flex !important;
	align-items: center !important;
	justify-content: center !important;
	margin-bottom: 10px !important;
	box-shadow: 0 4px 12px rgba(37, 99, 235, 0.12) !important;
}
.dashboard-page .widget-col-in h5 {
	font-family: 'Outfit', sans-serif !important;
	font-size: 14.5px !important;
	font-weight: 600 !important;
	color: #64748b !important;
	margin-top: 4px !important;
	margin-bottom: 0 !important;
}
.dashboard-page .counter {
	font-family: 'Outfit', sans-serif !important;
	font-size: 32px !important;
	font-weight: 800 !important;
	color: #0f172a !important;
	margin-top: 0 !important;
}
.dashboard-page .box-top-line {
	border-top: 1px solid #f1f5f9 !important;
	padding-top: 12px !important;
	margin-top: 12px !important;
	font-size: 12px !important;
	font-weight: 700 !important;
	color: #94a3b8 !important;
	letter-spacing: 0.5px !important;
}
.dashboard-page .fc-header-toolbar {
	margin-bottom: 20px !important;
}
.dashboard-page .fc-button {
	background: #ffffff !important;
	border: 1px solid #cbd5e1 !important;
	color: #334155 !important;
	border-radius: 8px !important;
	font-family: 'Outfit', sans-serif !important;
	font-weight: 600 !important;
	box-shadow: none !important;
	text-shadow: none !important;
	padding: 6px 14px !important;
}
.dashboard-page .fc-button-active,
.dashboard-page .fc-state-active {
	background: #2563eb !important;
	color: #ffffff !important;
	border-color: #2563eb !important;
}

html, body, .body, .inner-wrapper, .content-body, .dashboard-page {
	max-width: 100vw !important;
	overflow-x: hidden !important;
}

@media (max-width: 768px) {
	.dashboard-page .row {
		margin-left: -5px !important;
		margin-right: -5px !important;
	}
	.dashboard-page [class*="col-"] {
		padding-left: 5px !important;
		padding-right: 5px !important;
	}
	.dashboard-page .counter {
		font-size: 20px !important;
	}
	.dashboard-page div[style*="font-size:22px"], 
	.dashboard-page div[style*="font-size:24px"] {
		font-size: 16px !important;
		word-break: break-word;
	}
}
</style>


<div class="dashboard-page">

<?php if (is_superadmin_loggedin() || is_admin_loggedin() || get_permission('fees_summary_widget', 'is_view') || get_permission('invoice', 'is_view')) { ?>
<!-- Executive Overview Banner (White Theme) -->
<div class="row mb-md">
	<div class="col-md-12">
		<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; color: #0f172a; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);">
			<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px;">
				<div>
					<h3 style="font-family:'Outfit',sans-serif; font-size:22px; font-weight:800; color:#0f172a; margin:0 0 4px 0;">
						<i class="fas fa-layer-group text-primary me-2"></i> <?php echo (is_superadmin_loggedin() && empty($school_id)) ? translate('all_branches_executive_overview') : translate('executive_overview'); ?>
					</h3>
					<p style="font-size:13px; color:#64748b; margin:0;">Real-time collection & dues metrics</p>
				</div>
				<span style="background:#eff6ff; border:1px solid #bfdbfe; color:#2563eb; font-size:12px; font-weight:700; padding:6px 14px; border-radius:9999px; text-transform:uppercase;">
					<?php echo (is_superadmin_loggedin() && empty($school_id)) ? 'Super Admin Access' : 'Branch Overview'; ?>
				</span>
			</div>
			
			<div class="row">
				<!-- Today's Collection -->
				<div class="col-md-4 col-sm-4 col-xs-12 mb-xs">
					<div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:12px; padding:16px;">
						<div style="font-size:11px; color:#0369a1; font-weight:700; text-transform:uppercase; margin-bottom:6px;">Today's Total Collection</div>
						<div style="font-family:'Outfit',sans-serif; font-size:22px; font-weight:800; color:#0284c7;">
							<?php echo $global_config['currency_symbol'] . ' ' . number_format($superadmin_todays_fees, 2); ?>
						</div>
						<div style="font-size:11px; color:#0284c7; margin-top:4px;"><i class="fas fa-coins me-1"></i> Today Received</div>
					</div>
				</div>

				<!-- Total Fees Collected -->
				<div class="col-md-4 col-sm-4 col-xs-12 mb-xs">
					<div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px;">
						<div style="font-size:11px; color:#15803d; font-weight:700; text-transform:uppercase; margin-bottom:6px;">Total Fees Collected</div>
						<div style="font-family:'Outfit',sans-serif; font-size:22px; font-weight:800; color:#16a34a;">
							<?php echo $global_config['currency_symbol'] . ' ' . number_format($superadmin_total_collected, 2); ?>
						</div>
						<div style="font-size:11px; color:#16a34a; margin-top:4px;"><i class="fas fa-wallet me-1"></i> Total Collections</div>
					</div>
				</div>

				<!-- Pending Fees -->
				<div class="col-md-4 col-sm-4 col-xs-12 mb-xs">
					<div style="background:#fff1f2; border:1px solid #fecdd3; border-radius:12px; padding:16px;">
						<div style="font-size:11px; color:#be123c; font-weight:700; text-transform:uppercase; margin-bottom:6px;">Pending Fees</div>
						<div style="font-family:'Outfit',sans-serif; font-size:22px; font-weight:800; color:#e11d48;">
							<?php echo $global_config['currency_symbol'] . ' ' . number_format($superadmin_pending_fees, 2); ?>
						</div>
						<div style="font-size:11px; color:#e11d48; margin-top:4px;"><i class="fas fa-clock me-1"></i> Outstanding Dues</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>


	<div class="row">
<?php if (get_permission('monthly_income_vs_expense_chart', 'is_view')) { ?>
		<!-- monthly cash book transaction -->
		<div class="<?php echo get_permission('annual_student_fees_summary_chart', 'is_view') ? 'col-md-12 col-lg-4 col-xl-3' : 'col-md-12'; ?>">
			<section class="panel pg-fw">
				<div class="panel-body">
					<h4 class="chart-title mb-xs"><?=translate('income_vs_expense_of') . " " . translate(strtolower(date('F')))?></h4>
					<div id="cash_book_transaction"></div>
					<div class="round-overlap"><i class="fab fa-sellcast"></i></div>
					<div class="text-center">
						<ul class="list-inline">
							<li>
								<h6 class="text-muted"><i class="fa fa-circle" style="color: #10b981;"></i> <?=translate('income')?></h6>
							</li>
							<li>
								<h6 class="text-muted"><i class="fa fa-circle" style="color: #ef4444;"></i> <?=translate('expense')?></h6>
							</li>
						</ul>

					</div>
				</div>
			</section>
		</div>
<?php } ?>
<?php if (get_permission('annual_student_fees_summary_chart', 'is_view')) { ?>
		<!-- student fees summary graph -->
		<div class="<?php echo get_permission('monthly_income_vs_expense_chart', 'is_view') ? 'col-md-12 col-lg-8 col-xl-9' : 'col-md-12'; ?>">
			<section class="panel">
				<div class="panel-body">
					<h4 class="chart-title mb-md"><?=translate('annual_fee_summary')?></h4>
					<div class="pe-chart">
						<canvas id="fees_graph" style="height: 322px;"></canvas>
					</div>
				</div>
			</section>
		</div>
<?php } ?>
	</div>
<?php if ($widget1 > 0) { ?>
	<div class="row widget-1">
		<div class="col-md-12 col-lg-12 col-sm-12">
			<div class="panel">
				<div class="row widget-row-in">
				<?php if (get_permission('employee_count_widget', 'is_view')) { ?>
					<div class="col-lg-<?php echo $widget1; ?> col-sm-6 ">
						<div class="panel-body">
							<div class="widget-col-in row">
								<div class="col-md-6 col-sm-6 col-xs-6"> <i class="fas fa-users"></i>
									<h5><?php echo translate('employee'); ?></h5>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-6">
									<h3 class="counter text-right mt-md text-primary"><?php
									$staff = $this->dashboard_model->getstaffcounter('', $school_id);
									echo $staff['snumber'];
									?></h3>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="box-top-line line-color-primary">
										<span class="text-uppercase"><?php echo translate('total_strength'); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if (get_permission('student_count_widget', 'is_view')) { ?>
					<div class="col-lg-<?php echo $widget1; ?> col-sm-6">
						<div class="panel-body">
							<div class="widget-col-in row">
								<div class="col-md-6 col-sm-6 col-xs-6"> <i class="fas fa-user-graduate"></i>
									<h5><?php echo translate('students'); ?></h5> </div>
								<div class="col-md-6 col-sm-6 col-xs-6">
									<h3 class="counter text-right mt-md text-primary"><?=$get_total_student?></h3>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="box-top-line line-color-primary">
											<span class="text-uppercase"><?php echo translate('total_strength'); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if (get_permission('parent_count_widget', 'is_view')) { ?>
					<div class="col-lg-<?php echo $widget1; ?> col-sm-6 ">
						<div class="panel-body">
							<div class="widget-col-in row">
								<div class="col-md-6 col-sm-6 col-xs-6"> <i class="fas fa-user-tie" ></i>
									<h5><?php echo translate('parents'); ?></h5></div>
								<div class="col-md-6 col-sm-6 col-xs-6">
									<h3 class="counter text-right mt-md text-primary"><?php
										if (!empty($school_id))
											$this->db->where('branch_id', $school_id);
										echo $this->db->select('id')->get('parent')->num_rows();
									?></h3>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="box-top-line line-color-primary">
										<span class="text-uppercase"><?php echo translate('total_strength'); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if (get_permission('teacher_count_widget', 'is_view')) { ?>
					<div class="col-lg-<?php echo $widget1; ?> col-sm-6 ">
						<div class="panel-body">
							<div class="widget-col-in row">
								<div class="col-md-6 col-sm-6 col-xs-6"> <i class="fas fa-chalkboard-teacher" ></i>
									<h5><?php echo translate('teachers'); ?></h5></div>
								<div class="col-md-6 col-sm-6 col-xs-6">
									<h3 class="counter text-right mt-md text-primary"><?php
									$staff = $this->dashboard_model->getstaffcounter(3, $school_id);
									echo $staff['snumber'];
									?></h3>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="box-top-line line-color-primary">
										<span class="text-uppercase"><?=translate('total_strength')?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
	<!-- student quantity chart -->
	<div class="row">
<?php if (get_permission('student_quantity_pie_chart', 'is_view')) { ?>
		<div class="<?php echo get_permission('weekend_attendance_inspection_chart', 'is_view') ? 'col-md-12 col-lg-4 col-xl-3' : 'col-md-12'; ?>">
			<section class="panel pg-fw">
				<div class="panel-body">
					<h4 class="chart-title mb-xs"><?=translate('student_quantity')?></h4>
					<div id="student_strength"></div>
					<div class="round-overlap"><i class="fas fa-school"></i></div>
				</div>
			</section>
		</div>
<?php } ?>
<?php if (get_permission('weekend_attendance_inspection_chart', 'is_view')) { ?>
		<div class="<?php echo get_permission('student_quantity_pie_chart', 'is_view') ? 'col-md-12 col-lg-8 col-xl-9' : 'col-md-12'; ?>">
			<section class="panel">
				<div class="panel-body">
					<h4 class="chart-title mb-md"><?=translate('weekend_attendance_inspection')?></h4>
					<div class="pg-fw">
						<canvas id="weekend_attendance" style="height: 340px;"></canvas>
					</div>
				</div>
			</section>
		</div>
<?php } ?>
	</div>
<?php if ($widget2 > 0) { ?>
	<div class="row widget-2">
		<div class="col-md-12 col-lg-12 col-sm-12">
			<div class="panel">
				<div class="row widget-row-in">
				<?php if (get_permission('admission_count_widget', 'is_view')) { ?>
					<div class="col-lg-<?php echo $widget2; ?> col-sm-6 ">
						<div class="panel-body">
							<div class="widget-col-in row">
								<div class="col-md-6 col-sm-6 col-xs-6"> <i class="far fa-address-card"></i>
									<h5><?php echo translate('admission'); ?></h5>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-6">
									<h3 class="counter text-right mt-md text-primary"><?=$get_monthly_admission;?></h3>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="box-top-line line-color-primary">
										<span class="text-uppercase"><?php echo translate('interval_month'); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if (get_permission('voucher_count_widget', 'is_view')) { ?>
					<div class="col-lg-<?php echo $widget2; ?> col-sm-6">
						<div class="panel-body">
							<div class="widget-col-in row">
								<div class="col-md-6 col-sm-6 col-xs-6"> <i class="fas fa-money-check-alt"></i>
									<h5><?php echo translate('voucher'); ?></h5> </div>
								<div class="col-md-6 col-sm-6 col-xs-6">
									<h3 class="counter text-right mt-md text-primary"><?=$get_voucher?></h3>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="box-top-line line-color-primary">
											<span class="text-uppercase"><?php echo translate('total_number'); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if (get_permission('transport_count_widget', 'is_view') && moduleIsEnabled('transport')) { ?>
					<div class="col-lg-<?php echo $widget2; ?> col-sm-6 ">
						<div class="panel-body">
							<div class="widget-col-in row">
								<div class="col-md-6 col-sm-6 col-xs-6"> <i class="fas fa-road" ></i>
									<h5><?php echo translate('transport'); ?></h5></div>
								<div class="col-md-6 col-sm-6 col-xs-6">
									<h3 class="counter text-right mt-md text-primary"><?=$get_transport_route?></h3>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="box-top-line line-color-primary">
										<span class="text-uppercase"><?php echo translate('total_route'); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if (get_permission('hostel_count_widget', 'is_view') && moduleIsEnabled('hostel')) { ?>
					<div class="col-lg-<?php echo $widget2; ?> col-sm-6 ">
						<div class="panel-body">
							<div class="widget-col-in row">
								<div class="col-md-6 col-sm-6 col-xs-6"> <i class="fas fa-warehouse" ></i>
									<h5><?php echo translate('hostel'); ?></h5></div>
								<div class="col-md-6 col-sm-6 col-xs-6">
									<h3 class="counter text-right mt-md text-primary"><?php
										if (!empty($school_id))
											$this->db->where('branch_id', $school_id);
										$hostel_room = $this->db->select('id')->get('hostel_room')->num_rows();
										echo $hostel_room;
										?></h3>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="box-top-line line-color-primary">
										<span class="text-uppercase"><?=translate('total_room')?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
	<div class="row">
	    <!-- event calendar -->
		<div class="col-md-<?php echo $div3 ?>">
			<section class="panel">
				<div class="panel-body">
					<div id="event_calendar"></div>
				</div>
			</section>
		</div>
	<?php if ($div3 == 9) { ?>
		<div class="col-md-3">
			<div class="panel">
				<div class="row widget-row-in">
				<?php if (get_permission('student_birthday_widget', 'is_view')) { ?>
					<div class="col-xs-12">
						<div class="panel-body">
							<div class="widget-col-in row">
								<div class="col-md-6 col-sm-6 col-xs-6"> <a href="<?php echo base_url('birthday/student') ?>" data-toggle="tooltip" data-original-title="<?=translate('view') . " " . translate('list')?>"><i class="fas fa-birthday-cake" ></i></a>
									<h5 class="text-muted"><?=translate('student')?></h5></div>
								<div class="col-md-6 col-sm-6 col-xs-6">
									<h3 class="counter text-right mt-md text-primary"><?php
										$this->db->select('student.id');
										$this->db->from('student');
										$this->db->join('enroll', 'enroll.student_id = student.id', 'inner');
										$this->db->where("enroll.session_id", get_session_id());
										if (!empty($school_id))
											$this->db->where('branch_id', $school_id);
										$this->db->where("MONTH(student.birthday)", date('m'));
										$this->db->where("DAY(student.birthday)", date('d'));
										$this->db->group_by('student.id'); 
										$stuTodayBirthday = $this->db->get()->result();
										echo(count($stuTodayBirthday));
										?></h3>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="box-top-line line-color-primary">
										<span class="text-muted text-uppercase"><?=translate('today_birthday')?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } if (get_permission('staff_birthday_widget', 'is_view')) { ?>
					<div class="col-xs-12">
						<div class="panel-body">
							<div class="widget-col-in row">
								<div class="col-md-6 col-sm-6 col-xs-6"> <a href="<?php echo base_url('birthday/staff') ?>" data-toggle="tooltip" data-original-title="<?=translate('view') . " " . translate('list')?>"><i class="fas fa-birthday-cake" ></i></a>
									<h5 class="text-muted"><?=translate('employee')?></h5></div>
								<div class="col-md-6 col-sm-6 col-xs-6">
									<h3 class="counter text-right mt-md text-primary"><?php
										$this->db->select('id');
										if (!empty($school_id))
											$this->db->where('branch_id', $school_id);
										$this->db->where("MONTH(birthday)", date('m'));
										$this->db->where("DAY(birthday)", date('d'));
										$emyTodayBirthday = $this->db->get('staff')->result();
										echo(count($emyTodayBirthday));
										?></h3>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="box-top-line line-color-primary">
										<span class="text-muted text-uppercase"><?=translate('today_birthday')?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				</div>
			</div>
		</div>
	<?php } ?>
	</div>
</div>

<div class="zoom-anim-dialog modal-block modal-block-primary mfp-hide" id="modal">
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-btn">
				<button onclick="fn_printElem('printResult')" class="btn btn-default btn-circle icon" ><i class="fas fa-print"></i></button>
			</div>
			<h4 class="panel-title"><i class="fas fa-info-circle"></i> <?=translate('event_details')?></h4>
		</header>
		<div class="panel-body">
			<div id="printResult" class=" pt-sm pb-sm">
				<div class="table-responsive">						
					<table class="table table-bordered table-condensed text-dark tbr-top" id="ev_table">
						
					</table>
				</div>
			</div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button class="btn btn-default modal-dismiss">
						<?=translate('close')?>
					</button>
				</div>
			</div>
		</footer>
	</section>
</div>

<script type="application/javascript">
(function($) {
	$('#event_calendar').fullCalendar({
		header: {
		left: 'prev,next,today',
		center: 'title',
			right: 'month,agendaWeek,agendaDay,listWeek'
		},
		firstDay: 1,
		height: 720,
		droppable: false,
		editable: true,
		timezone: 'UTC',
		lang: '<?php echo $language ?>',
		events: {
			url: "<?=base_url('event/get_events_list/'. $school_id)?>"
		},
		
		eventRender: function(event, element) {
			$(element).on("click", function() {
				viewEvent(event.id);
			});
			if(event.icon){          
				element.find(".fc-title").prepend("<i class='fas fa-"+event.icon+"'></i> ");
			}
		}
	});

	// Annual Fee Summary JS - Modern SaaS Colors & Smooth Curved Lines
	var total_fees = <?php echo json_encode($fees_summary["total_fee"]);?>;
	var total_paid = <?php echo json_encode($fees_summary["total_paid"]);?>;
	var total_due = <?php echo json_encode($fees_summary["total_due"]);?>;
	var feesGraph = {
		type: 'line',
		data: {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			datasets: [{
				label: '<?php echo translate("total");?>',
				data: total_fees,
				backgroundColor: 'rgba(37, 99, 235, 0.12)',
				borderColor: '#2563eb',
				borderWidth: 3,
				pointBackgroundColor: '#2563eb',
				pointBorderColor: '#ffffff',
				pointHoverRadius: 6,
				pointRadius: 4,
				lineTension: 0.35
			},{
				label: '<?php echo translate("collected");?>',
				data: total_paid,
				backgroundColor: 'rgba(16, 185, 129, 0.12)',
				borderColor: '#10b981',
				borderWidth: 3,
				pointBackgroundColor: '#10b981',
				pointBorderColor: '#ffffff',
				pointHoverRadius: 6,
				pointRadius: 4,
				lineTension: 0.35
			},{
				label: '<?php echo translate("remaining");?>',
				data: total_due,
				backgroundColor: 'rgba(244, 63, 94, 0.12)',
				borderColor: '#f43f5e',
				borderWidth: 3,
				pointBackgroundColor: '#f43f5e',
				pointBorderColor: '#ffffff',
				pointHoverRadius: 6,
				pointRadius: 4,
				lineTension: 0.35
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			tooltips: {
				mode: 'index',
				intersect: false,
				backgroundColor: '#0f172a',
				titleFontColor: '#ffffff',
				bodyFontColor: '#cbd5e1',
				cornerRadius: 8,
				padding: 12
			},
			legend: {
				position: 'top',
				labels: {
					boxWidth: 14,
					fontFamily: 'Plus Jakarta Sans',
					fontSize: 13,
					fontColor: '#475569',
					padding: 20
				}
			},
			scales: {
				xAxes: [{
					gridLines: {
						color: 'rgba(226, 232, 240, 0.6)',
						zeroLineColor: 'rgba(226, 232, 240, 0.6)'
					},
					ticks: {
						fontFamily: 'Plus Jakarta Sans',
						fontColor: '#64748b'
					}
				}],
				yAxes: [{
					gridLines: {
						color: 'rgba(226, 232, 240, 0.6)',
						zeroLineColor: 'rgba(226, 232, 240, 0.6)'
					},
					ticks: {
						fontFamily: 'Plus Jakarta Sans',
						fontColor: '#64748b'
					}
				}]
			}
		}
	}

	var days = <?php echo json_encode($weekend_attendance["days"]);?>;
	var employees_att = <?php echo json_encode($weekend_attendance["employee_att"]);?>;
	var student_att = <?php echo json_encode($weekend_attendance["student_att"]);?>;
	var weekendAttendanceChart = {
		type: 'bar',
		data: {
			labels: days,
			datasets: [{
				label: '<?php echo translate("employee");?>',
				data: employees_att,
				backgroundColor: '#2563eb',
				borderColor: '#1d4ed8',
				borderWidth: 1,
				borderRadius: 6
			},{
				label: '<?php echo translate("student");?>',
				data: student_att,
				backgroundColor: '#38bdf8',
				borderColor: '#0284c7',
				borderWidth: 1,
				borderRadius: 6
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			tooltips: {
				mode: 'index',
				backgroundColor: '#0f172a',
				cornerRadius: 8
			},
			legend: {
				position: 'top',
				labels: {
					boxWidth: 14,
					fontFamily: 'Plus Jakarta Sans',
					fontSize: 13,
					fontColor: '#475569'
				}
			},
			scales: {
				xAxes: [{
					gridLines: { color: 'rgba(226, 232, 240, 0.6)' },
					ticks: { fontColor: '#64748b' }
				}],
				yAxes: [{
					gridLines: { color: 'rgba(226, 232, 240, 0.6)' },
					ticks: { fontColor: '#64748b' }
				}]
			}
		}
	};


<?php if (get_permission('annual_student_fees_summary_chart', 'is_view')) { ?>
	var ctx = document.getElementById('fees_graph').getContext('2d');
	window.myLine =new Chart(ctx, feesGraph);
<?php } ?>
<?php if (get_permission('weekend_attendance_inspection_chart', 'is_view')) { ?>
	var ctx2 = document.getElementById('weekend_attendance').getContext('2d');
	window.myLine =new Chart(ctx2, weekendAttendanceChart);
<?php } ?>
<?php if (get_permission('monthly_income_vs_expense_chart', 'is_view')) { ?>
	// monthly income vs expense chart
	var cash_book_transaction = document.getElementById("cash_book_transaction");
	var cashbookchart = echarts.init(cash_book_transaction);
	cashbookchart.setOption({
		tooltip: {
			trigger: 'item',
			formatter: "{a} <br/>{b} : <?php echo $global_config["currency_symbol"];?> {c} ({d}%)"
		}, 
		legend: {
			show: false
		},
		color: ["#ef4444", "#10b981"],

		series: [{
			name: 'Transaction',
			type: 'pie',
			radius: ['75%', '90%'],
			itemStyle: {
				normal: {
					label: {
						show: false
					},
					labelLine: {
						show: false
					}
				},
				emphasis: {
					label: {
						show: false
					}
				}
			},
			data: <?=json_encode($income_vs_expense)?>
		}]
	});
<?php } ?>
<?php if (get_permission('student_quantity_pie_chart', 'is_view')) { ?>
	// Student Strength Doughnut Chart - Modern SaaS Palette
	var color = ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ec4899', '#3b82f6', '#14b8a6'];
	var strength_data = <?php echo json_encode($student_by_class);?>;
	var student_strength = document.getElementById("student_strength");
	var studentchart = echarts.init(student_strength);
	studentchart.setOption( {
		tooltip: {
			trigger: 'item',
			formatter: "{a} <br/>{b} : {c} ({d}%)"
		}, 

		legend: {
			type: 'scroll',
			x: 'center',
			y: 'bottom',
			itemWidth: 14,
<?php if($theme_config["dark_skin"] == "true"): ?>
			inactiveColor: '#4b4b4b',
			textStyle: {
				color: '#6b6b6c'
			}
<?php endif; ?>
		},
		series: [{
			name: 'Strength',
			type: 'pie',
			color: color,
			radius: ['70%', '85%'],
			center: ['50%', '46%'],
			itemStyle: {
				normal: {
					label: {
						show: false
					},
					labelLine: {
						show: false
					}
				},
				emphasis: {
					label: {
						show: false
					}
				}
			},
			data: strength_data
		}]
	});
<?php } ?>
	// charts resize
	$(".sidebar-toggle").on("click",function(event){
		echartsresize();
	});

	$(window).on("resize", echartsresize);

	function echartsresize() {
		setTimeout(function () {
			if ($("#student_strength").length) {
				studentchart.resize();
			}
			if ($("#cash_book_transaction").length) {
				cashbookchart.resize();
			}
		}, 350);
	}
})(jQuery);
</script>