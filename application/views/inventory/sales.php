<?php 
$currency_symbol = $global_config['currency_symbol']; 
$branch_id = !empty($branch_id) ? $branch_id : $this->application_model->get_branch_id();
$productlist = !empty($productlist) ? $productlist : array();
?>

<style>
/* Modern POS Styles */
.pos-container {
    background: #fdfdfd;
    border-radius: 8px;
    padding: 10px 0;
}
.pos-customer-bar {
    background: #ffffff;
    border: 1px solid #e7ecf1;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}
.pos-catalog-panel {
    background: #ffffff;
    border: 1px solid #e7ecf1;
    border-radius: 10px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    min-height: 580px;
}
.pos-search-box {
    position: relative;
    margin-bottom: 14px;
}
.pos-search-box i {
    position: absolute;
    left: 14px;
    top: 12px;
    color: #9aa0ac;
    font-size: 15px;
}
.pos-search-input {
    padding-left: 38px !important;
    border-radius: 25px !important;
    height: 40px !important;
    border: 1.5px solid #dce2e6 !important;
    font-size: 13.5px !important;
    transition: all 0.2s;
}
.pos-search-input:focus {
    border-color: #5b69bc !important;
    box-shadow: 0 0 0 3px rgba(91, 105, 188, 0.15) !important;
}
.pos-category-scroll {
    display: flex;
    overflow-x: auto;
    gap: 8px;
    padding-bottom: 10px;
    margin-bottom: 14px;
    scrollbar-width: thin;
}
.pos-category-scroll::-webkit-scrollbar {
    height: 4px;
}
.pos-category-scroll::-webkit-scrollbar-thumb {
    background: #d4d8dd;
    border-radius: 4px;
}
.pos-cat-pill {
    padding: 6px 14px;
    border-radius: 20px;
    background: #f1f3f7;
    color: #4c5667;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    border: 1px solid transparent;
    transition: all 0.15s ease-in-out;
}
.pos-cat-pill:hover {
    background: #e4e7ee;
    color: #2c384e;
}
.pos-cat-pill.active {
    background: #2a3042;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(42, 48, 66, 0.25);
}
.pos-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
    max-height: 520px;
    overflow-y: auto;
    padding: 2px 4px 10px 2px;
}
.pos-product-card {
    background: #ffffff;
    border: 1.5px solid #edf0f5;
    border-radius: 10px;
    padding: 12px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative;
    user-select: none;
}
.pos-product-card:hover {
    border-color: #5b69bc;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(91, 105, 188, 0.12);
}
.pos-product-card:active {
    transform: scale(0.98);
}
.pos-card-badge-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}
.pos-card-code {
    font-size: 10.5px;
    background: #eff2f7;
    color: #6c757d;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
}
.pos-card-stock {
    font-size: 10.5px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
}
.pos-card-name {
    font-size: 13px;
    font-weight: 600;
    color: #2b3344;
    margin: 4px 0 6px 0;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 35px;
}
.pos-card-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 6px;
    border-top: 1px dashed #f0f2f5;
    padding-top: 6px;
}
.pos-card-price {
    font-size: 14.5px;
    font-weight: 700;
    color: #0088cc;
}
.pos-card-add-btn {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f0f4fd;
    color: #5b69bc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.15s;
}
.pos-product-card:hover .pos-card-add-btn {
    background: #5b69bc;
    color: #ffffff;
}

/* POS Terminal / Cart Styles */
.pos-cart-panel {
    background: #ffffff;
    border: 1px solid #e7ecf1;
    border-radius: 10px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    display: flex;
    flex-direction: column;
    height: 100%;
}
.pos-cart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 12px;
    border-bottom: 1.5px solid #edf0f5;
    margin-bottom: 12px;
}
.pos-cart-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #2d3548;
}
.pos-cart-items-wrapper {
    flex: 1;
    max-height: 320px;
    overflow-y: auto;
    padding-right: 4px;
    margin-bottom: 14px;
    scrollbar-width: thin;
}
.pos-cart-table {
    width: 100%;
    margin-bottom: 0;
}
.pos-cart-table th {
    font-size: 11px;
    text-transform: uppercase;
    color: #7b8190;
    font-weight: 600;
    padding: 6px 4px !important;
    border-bottom: 1px solid #eef1f5 !important;
}
.pos-cart-table td {
    padding: 8px 4px !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #f6f8fb !important;
}
.pos-item-title {
    font-size: 12.5px;
    font-weight: 600;
    color: #2a3140;
    line-height: 1.25;
}
.pos-item-unit-price {
    font-size: 11px;
    color: #8c93a0;
}
.pos-qty-stepper {
    display: inline-flex;
    align-items: center;
    border: 1px solid #dcdfe6;
    border-radius: 6px;
    overflow: hidden;
    background: #ffffff;
}
.pos-qty-stepper button {
    background: #f7f9fa;
    border: none;
    width: 24px;
    height: 26px;
    font-size: 11px;
    color: #4b5262;
    cursor: pointer;
    transition: background 0.15s;
    padding: 0;
}
.pos-qty-stepper button:hover {
    background: #ebeef5;
    color: #111;
}
.pos-qty-stepper input {
    width: 34px;
    height: 26px;
    border: none;
    text-align: center;
    font-size: 12px;
    font-weight: 600;
    padding: 0;
}
.pos-qty-stepper input:focus {
    outline: none;
}
.pos-item-discount-input {
    width: 52px;
    height: 26px;
    border: 1px solid #dcdfe6;
    border-radius: 4px;
    font-size: 11.5px;
    text-align: center;
    padding: 2px;
}
.pos-item-subtotal {
    font-size: 13px;
    font-weight: 700;
    color: #2f364a;
}
.pos-item-remove-btn {
    color: #e55353;
    cursor: pointer;
    font-size: 13px;
    padding: 3px 6px;
    border-radius: 4px;
    transition: all 0.15s;
}
.pos-item-remove-btn:hover {
    background: #ffebeb;
}
.pos-empty-cart {
    text-align: center;
    padding: 40px 10px;
    color: #a4abb8;
}
.pos-empty-cart i {
    font-size: 42px;
    color: #cfd5e0;
    margin-bottom: 10px;
}
.pos-billing-summary {
    background: #f9fbfd;
    border: 1px solid #e7ecf2;
    border-radius: 8px;
    padding: 12px 14px;
    margin-bottom: 12px;
}
.pos-summary-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    color: #555f71;
    margin-bottom: 6px;
}
.pos-summary-payable {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #1f2937, #111827);
    color: #ffffff;
    border-radius: 8px;
    padding: 10px 14px;
    margin-top: 8px;
}
.pos-summary-payable span {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.pos-summary-payable h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    color: #22c55e;
}
.pos-quick-cash {
    display: flex;
    gap: 5px;
    margin-top: 6px;
    flex-wrap: wrap;
}
.pos-cash-chip {
    background: #edf2f7;
    border: 1px solid #d2d8e0;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    color: #3f4756;
    padding: 3px 7px;
    cursor: pointer;
    transition: all 0.15s;
}
.pos-cash-chip:hover {
    background: #dfe5ed;
}
.pos-change-indicator {
    margin-top: 6px;
    font-size: 12px;
    font-weight: 700;
    display: none;
    padding: 4px 8px;
    border-radius: 4px;
}
.pos-checkout-btn {
    background: linear-gradient(135deg, #22c55e, #16a34a) !important;
    border: none !important;
    color: #ffffff !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    padding: 11px !important;
    border-radius: 8px !important;
    letter-spacing: 0.3px;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
    transition: all 0.2s;
}
.pos-checkout-btn:hover {
    background: linear-gradient(135deg, #16a34a, #15803d) !important;
    box-shadow: 0 6px 16px rgba(22, 163, 74, 0.35);
    transform: translateY(-1px);
}
</style>

<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#productlist" data-toggle="tab"><i class="fas fa-list-ul"></i> <?php echo translate('sales') . ' ' . translate('list'); ?></a>
			</li>
<?php if (get_permission('product_sales', 'is_add')): ?>
			<li>
				<a href="#create" data-toggle="tab"><i class="fas fa-cash-register"></i> POS Sales</a>
			</li>
<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div id="productlist" class="tab-pane active mb-md">
				<div class="export_title"><?php echo translate('sales') . " " . translate('report'); ?></div>
				<table class="table table-bordered table-hover table-condensed nowrap" id="invSalesList" cellpadding="0" cellspacing="0" width="100%">
					<thead>
						<tr>
<?php if (is_superadmin_loggedin()): ?>
							<th><?=translate('branch')?></th>
<?php endif; ?>
							<th><?php echo translate('bill_no'); ?></th>
							<th><?php echo translate('role'); ?></th>
							<th><?php echo translate('sale_to'); ?></th>
							<th><?php echo translate('payment') . " " . translate('status'); ?></th>
							<th><?php echo translate('date'); ?></th>
							<th><?php echo translate('net') . " " . translate('payable'); ?></th>
							<th><?php echo translate('paid'); ?></th>
							<th><?php echo translate('due'); ?></th>
							<th class="no-sort"><?php echo translate('remarks'); ?></th>
							<th><?php echo translate('action'); ?></th>
						</tr>
					</thead>
				</table>
			</div>

<?php if (get_permission('product_sales', 'is_add')){ ?>
			<div id="create" class="tab-pane">
				<?php echo form_open('inventory/sales_save', array('id' => 'frmSubmit')); ?>
					<div class="pos-container">
						<!-- Top Customer Details Bar -->
						<div class="pos-customer-bar">
							<div class="row">
							<?php if (is_superadmin_loggedin()): ?>
								<div class="col-md-3 mb-xs">
									<label class="control-label" style="font-size:12px; font-weight:600;"><?=translate('branch')?> <span class="text-danger">*</span></label>
									<?php
										$arrayBranch = $this->app_lib->getSelectList('branch');
										echo form_dropdown("branch_id", $arrayBranch, $branch_id, "class='form-control input-sm' id='branchID' data-plugin-selectTwo data-width='100%'");
									?>
									<span class="error" id="branchID_error"></span>
								</div>
							<?php else: ?>
								<input type="hidden" name="branch_id" id="branchID" value="<?php echo $branch_id; ?>" />
							<?php endif; ?>

								<div class="<?php echo is_superadmin_loggedin() ? 'col-md-2' : 'col-md-3'; ?> mb-xs">
									<label class="control-label" style="font-size:12px; font-weight:600;"><?=translate('role')?> <span class="text-danger">*</span></label>
									<?php
										$role_list = array("0" => "Walk-in Customer") + $this->app_lib->getRoles(1);
										echo form_dropdown("role_id", $role_list, set_value('role_id', '0'), "class='form-control input-sm' data-plugin-selectTwo id='roleID' data-width='100%' data-minimum-results-for-search='Infinity'");
									?>
									<span class="error" id="roleID_error"></span>
								</div>

								<div class="<?php echo is_superadmin_loggedin() ? 'col-md-2' : 'col-md-2'; ?> mb-xs class_div" <?php if(empty($class_id)) { ?> style="display: none;" <?php } ?>>
									<label class="control-label" style="font-size:12px; font-weight:600;"><?=translate('class')?> <span class="text-danger">*</span></label>
									<?php
										$arrayClass = $this->app_lib->getClass($branch_id);
										echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control input-sm' id='class_id' data-plugin-selectTwo data-width='100%'");
									?>
									<span class="error" id="class_id_error"></span>
								</div>

								<div class="<?php echo is_superadmin_loggedin() ? 'col-md-3' : 'col-md-4'; ?> mb-xs">
									<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2px;">
										<label class="control-label" style="font-size:12px; font-weight:600; margin-bottom:0;"><?=translate('sale_to')?> <span class="text-danger">*</span></label>
										<span id="custModeToggleWrap" style="font-size:11px;">
											<a href="javascript:void(0);" id="btnToggleCustMode" class="text-primary" style="text-decoration:none; font-weight:600;">
												<i class="fas fa-users"></i> <span id="custModeLabel">Registered User</span>
											</a>
										</span>
									</div>

									<!-- Instant Customer Input (Default) -->
									<div id="instantCustDiv">
										<div class="input-group">
											<input type="text" name="customer_name" id="customer_name" class="form-control input-sm" placeholder="Customer Name (e.g. Ramesh Sharma)" value="Walk-in Customer" autocomplete="off" />
											<span class="input-group-btn">
												<button type="button" class="btn btn-default btn-sm" id="btnOpenCustModal" data-toggle="modal" data-target="#quickAddCustModal" title="Add Customer Mobile / Info" style="border-left:none;">
													<i class="fas fa-phone-alt text-muted" id="custPhoneBadge"></i>
												</button>
											</span>
										</div>
										<input type="hidden" name="customer_phone" id="customer_phone" value="" />
										<span class="error" id="customer_name_error"></span>
									</div>

									<!-- Registered User Dropdown (When Role is Student, Parent, Staff, etc.) -->
									<div id="registeredCustDiv" style="display: none;">
										<?php
											$arrayUser = array("" => translate('select_user'));
											echo form_dropdown("sale_to", $arrayUser, set_value('sale_to'), "class='form-control input-sm' id='receiverID' data-plugin-selectTwo data-width='100%'");
										?>
										<span class="error" id="receiverID_error"></span>
									</div>
								</div>

								<div class="<?php echo is_superadmin_loggedin() ? 'col-md-1' : 'col-md-2'; ?> mb-xs">
									<label class="control-label" style="font-size:12px; font-weight:600;"><?php echo translate('bill_no'); ?></label>
									<input type="text" class="form-control input-sm" name="bill_no" value="<?php echo $this->app_lib->get_bill_no('sales_bill'); ?>" id="bill_no" readonly />
									<span class="error" id="bill_no_error"></span>
								</div>

								<div class="<?php echo is_superadmin_loggedin() ? 'col-md-1' : 'col-md-1'; ?> mb-xs">
									<label class="control-label" style="font-size:12px; font-weight:600;"><?php echo translate('date'); ?></label>
									<input type="text" class="form-control input-sm" name="date" value="<?php echo date('Y-m-d'); ?>" data-plugin-datepicker data-plugin-options='{ "todayHighlight" : true }' id="date" />
									<span class="error" id="date_error"></span>
								</div>
							</div>
						</div>

						<!-- POS Split Layout -->
						<div class="row">
							<!-- Left Column: Product Showcase & Search -->
							<div class="col-md-7 col-lg-7">
								<div class="pos-catalog-panel">
									<!-- Instant Search Box -->
									<div class="pos-search-box">
										<i class="fas fa-search"></i>
										<input type="text" id="posSearchInput" class="form-control pos-search-input" placeholder="Search product by name, item code..." autocomplete="off" />
									</div>

									<!-- Category Filter Pills -->
									<div class="pos-category-scroll" id="posCategoryChips">
										<div class="pos-cat-pill active" data-category="all">
											<i class="fas fa-th-large mr-xs"></i> All Products
										</div>
										<?php if(!empty($categorylist)): foreach($categorylist as $cat_id => $cat_name): if(!empty($cat_id)): ?>
										<div class="pos-cat-pill" data-category="<?php echo $cat_id; ?>">
											<?php echo html_escape($cat_name); ?>
										</div>
										<?php endif; endforeach; endif; ?>
									</div>

									<!-- Product Cards Grid -->
									<div class="pos-products-grid" id="posProductGrid">
										<!-- Populated via JavaScript dynamically -->
									</div>
								</div>
							</div>

							<!-- Right Column: Live POS Billing Terminal & Cart -->
							<div class="col-md-5 col-lg-5">
								<div class="pos-cart-panel">
									<div class="pos-cart-header">
										<h4>
											<i class="fas fa-shopping-cart text-primary mr-xs"></i> Order Items
											<span id="posCartBadge" class="badge" style="background:#5b69bc; font-size:11px; margin-left:4px;">0</span>
										</h4>
										<button type="button" class="btn btn-xs btn-default text-danger" id="btnClearCart" title="Clear Cart">
											<i class="fas fa-trash-alt"></i> Clear
										</button>
									</div>

									<!-- Cart Items Table / List -->
									<div class="pos-cart-items-wrapper">
										<table class="table pos-cart-table" id="posCartTable">
											<thead>
												<tr>
													<th style="width: 42%;">Item</th>
													<th style="width: 25%; text-align:center;">Qty</th>
													<th style="width: 15%; text-align:center;">Disc</th>
													<th style="width: 15%; text-align:right;">Total</th>
													<th style="width: 3%;"></th>
												</tr>
											</thead>
											<tbody id="posCartBody">
												<!-- Populated via renderCart() -->
											</tbody>
										</table>
										<div class="pos-empty-cart" id="posEmptyCart">
											<i class="fas fa-shopping-basket"></i>
											<p style="font-weight:600; margin-bottom:2px;">Your Cart is Empty</p>
											<p style="font-size:11.5px;">Click on any product from the left catalog to add.</p>
										</div>
									</div>

									<!-- Billing Summary -->
									<div class="pos-billing-summary">
										<div class="pos-summary-line">
											<span>Subtotal:</span>
											<span style="font-weight:700;"><span class="curr-sym"><?php echo $currency_symbol; ?></span><span id="posDisplaySubTotal">0.00</span></span>
										</div>
										<div class="pos-summary-line">
											<span>Discount ( - ):</span>
											<span style="font-weight:700; color:#e55353;"><span class="curr-sym"><?php echo $currency_symbol; ?></span><span id="posDisplayDiscount">0.00</span></span>
										</div>
										<div class="pos-summary-payable">
											<span>Net Payable:</span>
											<h3><span class="curr-sym"><?php echo $currency_symbol; ?></span><span id="posDisplayNetPayable">0.00</span></h3>
										</div>
									</div>

									<!-- Payment Options -->
									<div class="row">
										<div class="col-xs-7 mb-sm">
											<label style="font-size:11px; font-weight:600; margin-bottom:2px;">Received Amount</label>
											<div class="input-group input-group-sm">
												<span class="input-group-addon"><?php echo $currency_symbol; ?></span>
												<input type="number" step="any" class="form-control" name="payment_amount" id="payment_amount" placeholder="0.00" autocomplete="off" />
											</div>
											<div class="pos-quick-cash">
												<span class="pos-cash-chip" onclick="setQuickCash('exact')">Exact</span>
												<span class="pos-cash-chip" onclick="addQuickCash(50)">+50</span>
												<span class="pos-cash-chip" onclick="addQuickCash(100)">+100</span>
												<span class="pos-cash-chip" onclick="addQuickCash(500)">+500</span>
												<span class="pos-cash-chip" onclick="addQuickCash(1000)">+1000</span>
											</div>
											<div id="posChangeIndicator" class="pos-change-indicator"></div>
											<span class="error" id="payment_amount_error"></span>
										</div>

										<div class="col-xs-5 mb-sm">
											<label style="font-size:11px; font-weight:600; margin-bottom:2px;">Pay Via</label>
											<?php
												echo form_dropdown("pay_via", $payvia_list, set_value('pay_via', 1), "class='form-control input-sm' id='pay_via' data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
											?>
											<span class="error" id="pay_via_error"></span>
										</div>
									</div>

									<div class="row">
										<div class="col-xs-12 mb-sm">
											<input type="text" class="form-control input-sm" name="payment_remarks" id="payment_remarks" placeholder="Optional notes / remarks..." autocomplete="off" />
										</div>
									</div>

									<!-- Hidden Fields mapped to backend inventory/sales_save -->
									<input type="hidden" id="sub_total_amount" name="sub_total_amount" value="0.00" />
									<input type="hidden" id="grandTotal" name="grand_total" value="0.00" />
									<input type="hidden" id="total_discount" name="total_discount" value="0.00" />
									<input type="hidden" id="totalDiscount" name="total_discount" value="0.00" />
									<input type="hidden" id="netPayable" name="net_payable_amount" value="0.00" />
									<input type="hidden" id="netGrandTotal" name="net_amount" value="0.00" />
									<div id="posHiddenItems"></div>

									<!-- Big Checkout Button -->
									<button type="submit" id="savebtn" class="btn btn-block pos-checkout-btn mt-xs" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing Sale...">
										<i class="fas fa-check-circle mr-xs"></i> Complete Sale & Bill
									</button>
								</div>
							</div>
						</div>
					</div>
				<?php echo form_close(); ?>
			</div>
<?php } ?>
		</div>
	</div>
</section>

<!-- Success Modal for Invoice -->
<div class="modal fade" id="posSuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm" style="margin-top:12%;">
		<div class="modal-content" style="border-radius:12px; text-align:center; padding:20px;">
			<div style="width:55px; height:55px; border-radius:50%; background:#dcfce7; color:#16a34a; font-size:26px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
				<i class="fas fa-check"></i>
			</div>
			<h4 style="font-weight:700; margin-bottom:6px;">Sale Completed!</h4>
			<p style="color:#64748b; font-size:13px; margin-bottom:18px;">Bill generated successfully.</p>
			<div style="display:flex; gap:8px;">
				<button type="button" id="posModalInvoiceBtn" class="btn btn-primary btn-block" style="border-radius:6px; font-weight:600;" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Printing...">
					<i class="fas fa-print mr-xs"></i> Print Bill
				</button>
				<button type="button" class="btn btn-default btn-block" data-dismiss="modal" style="border-radius:6px; font-weight:600;">
					Next Sale
				</button>
			</div>
			<div style="margin-top: 10px;">
				<a href="#" id="posModalViewInvoiceLink" target="_blank" style="font-size:11.5px; color:#64748b; text-decoration:none;">
					<i class="fas fa-external-link-alt mr-xs"></i> View Full Bill Page
				</a>
			</div>
		</div>
	</div>
</div>

<!-- Quick Customer Information Modal -->
<div class="modal fade" id="quickAddCustModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm" style="margin-top:10%;">
		<div class="modal-content" style="border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.15); border:none; overflow:hidden;">
			<div class="modal-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:12px 18px;">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" style="font-size:14px; font-weight:700; color:#1e293b;">
					<i class="fas fa-user-edit text-primary mr-xs"></i> Customer Information
				</h4>
			</div>
			<div class="modal-body" style="padding:18px;">
				<div class="form-group mb-sm">
					<label class="control-label" style="font-size:12px; font-weight:600; color:#334155;">Customer Name <span class="text-danger">*</span></label>
					<input type="text" id="modalCustName" class="form-control input-sm" placeholder="e.g. Ramesh Kumar" autocomplete="off" />
				</div>
				<div class="form-group mb-xs">
					<label class="control-label" style="font-size:12px; font-weight:600; color:#334155;">Mobile / Phone No.</label>
					<input type="text" id="modalCustPhone" class="form-control input-sm" placeholder="e.g. 9876543210" autocomplete="off" />
				</div>
			</div>
			<div class="modal-footer" style="padding:10px 18px; background:#f8fafc; border-top:1px solid #e2e8f0;">
				<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary btn-sm" id="btnApplyCustModal" style="font-weight:600;">
					<i class="fas fa-check mr-xs"></i> Save & Apply
				</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	// Data init
	var curSymbol = "<?php echo $currency_symbol; ?>";
	var posProducts = <?php echo json_encode($productlist); ?> || [];
	var posCart = []; // [{ id, name, code, category_id, category_name, price, stock, unit, quantity, discount }]
	var activeCategory = "all";
	var lastSalesBillId = null;

	// Direct Print Sales Invoice (without redirecting to another page)
	function printSalesInvoice(billId, btn) {
		var $btn = btn ? $(btn) : null;
		if ($btn && typeof $btn.button === 'function') {
			$btn.button('loading');
		}
		$.ajax({
			url: base_url + "inventory/invoicePrint",
			type: "POST",
			data: { id: billId },
			dataType: "html",
			success: function (response) {
				fn_printElem(response, true);
			},
			error: function () {
				alert("Unable to load invoice for printing. Please try again.");
			},
			complete: function () {
				if ($btn && typeof $btn.button === 'function') {
					$btn.button('reset');
				}
			}
		});
	}

	// Customer Mode Helper
	function setCustomerMode(mode) {
		if (mode === 'instant') {
			if ($('#roleID').val() !== '0') {
				$('#roleID').val('0').trigger('change.select2');
			}
			$('#instantCustDiv').show();
			$('#registeredCustDiv').hide();
			$(".class_div").hide(300);
			if ($('#customer_name').val().trim() === '') {
				$('#customer_name').val('Walk-in Customer');
			}
			$('#custModeLabel').text('Registered User');
			$('#receiverID').val('').trigger('change.select2');
		} else {
			if ($('#roleID').val() === '0' || !$('#roleID').val()) {
				$('#roleID').val('7').trigger('change.select2').trigger('change');
			}
			$('#instantCustDiv').hide();
			$('#registeredCustDiv').show();
			$('#custModeLabel').text('Walk-in / Custom');
			$('#customer_name').val('');
		}
	}

	$(document).ready(function () {
		// Init Sales List Datatable
		initDatatable('#invSalesList', 'inventory/getSaleslistDT');

		// Render initial products
		renderProducts();

		// Customer Mode Toggle
		$('#btnToggleCustMode').on('click', function (e) {
			e.preventDefault();
			if ($('#instantCustDiv').is(':visible')) {
				setCustomerMode('registered');
			} else {
				setCustomerMode('instant');
			}
		});

		// Clear Customer Name error on input
		$('#customer_name').on('input', function() {
			$('#customer_name_error').empty();
		});

		// Quick Customer Modal
		$('#quickAddCustModal').on('show.bs.modal', function () {
			$('#modalCustName').val($('#customer_name').val());
			$('#modalCustPhone').val($('#customer_phone').val());
		});

		$('#btnApplyCustModal').on('click', function () {
			var name = $('#modalCustName').val().trim();
			var phone = $('#modalCustPhone').val().trim();
			if (!name) {
				name = 'Walk-in Customer';
			}
			$('#customer_name').val(name);
			$('#customer_phone').val(phone);
			if (phone) {
				$('#custPhoneBadge').removeClass('text-muted').addClass('text-success');
				$('#btnOpenCustModal').attr('title', 'Mobile: ' + phone);
			} else {
				$('#custPhoneBadge').removeClass('text-success').addClass('text-muted');
				$('#btnOpenCustModal').attr('title', 'Add Customer Mobile / Info');
			}
			setCustomerMode('instant');
			$('#quickAddCustModal').modal('hide');
		});

		// Direct Print Bill button in Success Modal (no page redirect)
		$(document).on('click', '#posModalInvoiceBtn', function (e) {
			e.preventDefault();
			var billId = $(this).data('bill-id') || lastSalesBillId;
			if (billId) {
				printSalesInvoice(billId, this);
			}
		});

		// Live Search Input
		$('#posSearchInput').on('keyup input', function () {
			renderProducts();
		});

		// Category Chip Click
		$(document).on('click', '.pos-cat-pill', function () {
			$('.pos-cat-pill').removeClass('active');
			$(this).addClass('active');
			activeCategory = $(this).data('category');
			renderProducts();
		});

		// Clear Cart Button
		$('#btnClearCart').on('click', function () {
			if (posCart.length > 0) {
				posCart = [];
				renderCart();
			}
		});

		// Payment amount change for realtime due/change calculation
		$('#payment_amount').on('keyup input change', function () {
			updateChangeCalculation();
		});

		// Superadmin Branch Change
		$(document).on('change', '#branchID', function() {
			var branchID = $(this).val();
			getClassByBranch(branchID);
			$('#roleID').val('0').trigger('change.select2');
			setCustomerMode('instant');
			$('#receiverID').empty().html("<option value=''><?=translate('select_user')?></option>");

			// Fetch POS Products for newly selected branch
			$.ajax({
				url: "<?=base_url('inventory/getPosProducts')?>",
				type: 'POST',
				data: { branch_id: branchID },
				dataType: 'json',
				success: function (data) {
					if (data.status === 'success') {
						posProducts = data.products || [];
						// Update Category Pills
						var catHtml = '<div class="pos-cat-pill active" data-category="all"><i class="fas fa-th-large mr-xs"></i> All Products</div>';
						if (data.categories) {
							$.each(data.categories, function(id, name) {
								if (id) {
									catHtml += '<div class="pos-cat-pill" data-category="' + id + '">' + name + '</div>';
								}
							});
						}
						$('#posCategoryChips').html(catHtml);
						activeCategory = "all";
						renderProducts();
					}
				}
			});
		});

		// Role Change
		$(document).on('change', '#roleID', function() {
			var roleID = $(this).val();
			var branchID = $('#branchID').val();

			if (roleID === '0' || roleID === '') {
				$('#instantCustDiv').show();
				$('#registeredCustDiv').hide();
				$(".class_div").hide(400);
				if ($('#customer_name').val().trim() === '') {
					$('#customer_name').val('Walk-in Customer');
				}
				$('#custModeLabel').text('Registered User');
				$('#receiverID').val('').trigger('change.select2');
			} else {
				$('#instantCustDiv').hide();
				$('#registeredCustDiv').show();
				$('#custModeLabel').text('Walk-in / Custom');
				$('#customer_name').val('');

				if(roleID == 6){
					$.ajax({
						url: base_url + "communication/getParentListBranch",
						type: 'POST',
						data: { branch_id: branchID },
						success: function (data) {
							$('#receiverID').html(data);
						}
					});
					$(".class_div").hide(400);
				} else if(roleID == 7) {
					$(".class_div").show(400);
					$('#receiverID').empty().html("<option value=''><?=translate('select_user')?></option>");
				} else {
					$(".class_div").hide(400);
					$.ajax({
						url: base_url + "communication/getStafflistRole",
						type: 'POST',
						data: { branch_id: branchID, role_id: roleID },
						success: function (data) {
							$('#receiverID').html(data);
						}
					});	
				}
			}
		});

		// Class Change
		$(document).on('change', '#class_id', function() {
			var classID = $(this).val();
			var branchID = $('#branchID').val();
	        $.ajax({
	            url: base_url + "communication/getStudentByClass",
	            type: 'POST',
	            data: { branch_id: branchID, class_id: classID },
	            success: function (data) {
	                $('#receiverID').html(data);
	            }
	        });
		});

		// Form Submit
		$("#frmSubmit").on('submit', function (e) {
		    e.preventDefault();
		    $('.error').html('');

		    if (posCart.length === 0) {
		        alert("Please add at least one product to the cart before checkout.");
		        return false;
		    }

		    var btn = $("#savebtn");
		    btn.button('loading');
		    $.ajax({
		        url: $(this).attr('action'),
		        type: "POST",
		        data: $(this).serialize(),
		        dataType: 'json',
		        success: function (data) {
		            btn.button('reset');
		            if (data.status == "fail") {
		                $.each(data.error, function (index, value) {
		                	$('#' + index + '_error').html(value);
		                	$('#' + index).parents('.form-group').find('.error').html(value);
		                });
		            } else {
		            	if (data.bill_id || data.invoice_url) {
		            		lastSalesBillId = data.bill_id;
		            		$('#posModalInvoiceBtn').data('bill-id', data.bill_id);
		            		if (data.invoice_url) {
		            			$('#posModalViewInvoiceLink').attr('href', data.invoice_url);
		            		}
		            		$('#posSuccessModal').modal('show');
		            		// Reset Cart & Inputs
		            		posCart = [];
		            		renderCart();
		            		$('#payment_amount').val('');
		            		$('#payment_remarks').val('');
		            		$('#customer_name').val('Walk-in Customer');
		            		$('#customer_phone').val('');
		            		$('#custPhoneBadge').removeClass('text-success').addClass('text-muted');
		            		$('#btnOpenCustModal').attr('title', 'Add Customer Mobile / Info');
		            		setCustomerMode('instant');
		            		// Reload datatable in background
		            		if (typeof cusDataTable !== 'undefined') {
		            			cusDataTable.ajax.reload();
		            		}
		            	} else {
		            		window.location.href = data.url;
		            	}
		            }
		        },
		        error: function () {
		            btn.button('reset');
		            alert("An error occurred during transaction. Please try again.");
		        }
		    });
		});
	});

	// Render Product Catalog
	function renderProducts() {
		var keyword = ($('#posSearchInput').val() || '').toLowerCase().trim();
		var container = $('#posProductGrid');
		container.empty();

		var filtered = posProducts.filter(function (p) {
			// Category filter
			if (activeCategory !== 'all' && String(p.category_id) !== String(activeCategory)) {
				return false;
			}
			// Search keyword filter
			if (keyword) {
				var nameMatch = (p.name || '').toLowerCase().indexOf(keyword) > -1;
				var codeMatch = (p.code || '').toLowerCase().indexOf(keyword) > -1;
				var catMatch = (p.category_name || '').toLowerCase().indexOf(keyword) > -1;
				return nameMatch || codeMatch || catMatch;
			}
			return true;
		});

		if (filtered.length === 0) {
			container.html('<div style="grid-column: 1 / -1; text-align:center; padding:50px 10px; color:#9aa0ac;">' +
				'<i class="fas fa-box-open fa-3x" style="margin-bottom:10px; color:#d1d5db;"></i>' +
				'<p style="font-weight:600; margin:0;">No products found</p>' +
				'<p style="font-size:12px;">Try a different keyword or category.</p></div>');
			return;
		}

		$.each(filtered, function (idx, item) {
			var price = parseFloat(item.sales_price || 0).toFixed(2);
			var stock = parseInt(item.available_stock || 0);
			var stockBadge = '';
			if (stock > 5) {
				stockBadge = '<span class="pos-card-stock" style="background:#e6f7ec; color:#16a34a;">Stock: ' + stock + '</span>';
			} else if (stock > 0) {
				stockBadge = '<span class="pos-card-stock" style="background:#fef3c7; color:#d97706;">Low: ' + stock + '</span>';
			} else {
				stockBadge = '<span class="pos-card-stock" style="background:#fee2e2; color:#dc2626;">Out of Stock</span>';
			}

			var codeBadge = item.code ? '<span class="pos-card-code">' + escapeHtml(item.code) + '</span>' : '<span class="pos-card-code">' + escapeHtml(item.category_name || 'Item') + '</span>';

			var cardHtml = '<div class="pos-product-card" onclick="addToCart(' + item.id + ')">' +
				'<div>' +
					'<div class="pos-card-badge-row">' +
						codeBadge +
						stockBadge +
					'</div>' +
					'<div class="pos-card-name" title="' + escapeHtml(item.name) + '">' + escapeHtml(item.name) + '</div>' +
				'</div>' +
				'<div class="pos-card-bottom">' +
					'<div class="pos-card-price">' + curSymbol + price + '</div>' +
					'<div class="pos-card-add-btn"><i class="fas fa-plus"></i></div>' +
				'</div>' +
			'</div>';

			container.append(cardHtml);
		});
	}

	// Add Product to Cart
	function addToCart(productId) {
		var product = posProducts.find(function(p) { return p.id == productId; });
		if (!product) return;

		var existingIndex = posCart.findIndex(function(item) { return item.id == productId; });
		if (existingIndex > -1) {
			posCart[existingIndex].quantity += 1;
		} else {
			posCart.push({
				id: product.id,
				name: product.name,
				code: product.code || '',
				category_id: product.category_id || 0,
				category_name: product.category_name || '',
				price: parseFloat(product.sales_price || 0),
				stock: parseInt(product.available_stock || 0),
				unit: product.unit_name || '',
				quantity: 1,
				discount: 0
			});
		}
		renderCart();
	}

	// Update Qty
	function updateQty(productId, delta) {
		var index = posCart.findIndex(function(item) { return item.id == productId; });
		if (index > -1) {
			posCart[index].quantity += delta;
			if (posCart[index].quantity <= 0) {
				posCart.splice(index, 1);
			}
			renderCart();
		}
	}

	function setQty(productId, newQty) {
		var qty = parseInt(newQty) || 1;
		if (qty < 1) qty = 1;
		var index = posCart.findIndex(function(item) { return item.id == productId; });
		if (index > -1) {
			posCart[index].quantity = qty;
			renderCart();
		}
	}

	// Update Discount
	function updateDiscount(productId, discountVal) {
		var disc = parseFloat(discountVal) || 0;
		if (disc < 0) disc = 0;
		var index = posCart.findIndex(function(item) { return item.id == productId; });
		if (index > -1) {
			posCart[index].discount = disc;
			renderCart();
		}
	}

	// Remove from Cart
	function removeFromCart(productId) {
		posCart = posCart.filter(function(item) { return item.id != productId; });
		renderCart();
	}

	// Render Cart Items and Calculations
	function renderCart() {
		var tbody = $('#posCartBody');
		var hiddenContainer = $('#posHiddenItems');
		tbody.empty();
		hiddenContainer.empty();

		var subTotal = 0;
		var totalDiscount = 0;
		var totalCount = 0;

		if (posCart.length === 0) {
			$('#posEmptyCart').show();
			$('#posCartTable').hide();
		} else {
			$('#posEmptyCart').hide();
			$('#posCartTable').show();

			$.each(posCart, function (i, item) {
				var linePrice = item.price * item.quantity;
				var lineNet = Math.max(0, linePrice - item.discount);
				subTotal += linePrice;
				totalDiscount += item.discount;
				totalCount += item.quantity;

				var rowHtml = '<tr>' +
					'<td>' +
						'<div class="pos-item-title">' + escapeHtml(item.name) + '</div>' +
						'<div class="pos-item-unit-price">' + curSymbol + item.price.toFixed(2) + ' ' + (item.unit ? '/ ' + escapeHtml(item.unit) : '') + '</div>' +
					'</td>' +
					'<td style="text-align:center;">' +
						'<div class="pos-qty-stepper">' +
							'<button type="button" onclick="updateQty(' + item.id + ', -1)">-</button>' +
							'<input type="text" value="' + item.quantity + '" onchange="setQty(' + item.id + ', this.value)" />' +
							'<button type="button" onclick="updateQty(' + item.id + ', 1)">+</button>' +
						'</div>' +
					'</td>' +
					'<td style="text-align:center;">' +
						'<input type="number" step="any" class="pos-item-discount-input" value="' + item.discount + '" onchange="updateDiscount(' + item.id + ', this.value)" />' +
					'</td>' +
					'<td style="text-align:right;">' +
						'<span class="pos-item-subtotal">' + curSymbol + lineNet.toFixed(2) + '</span>' +
					'</td>' +
					'<td style="text-align:center;">' +
						'<span class="pos-item-remove-btn" onclick="removeFromCart(' + item.id + ')"><i class="fas fa-times"></i></span>' +
					'</td>' +
				'</tr>';
				tbody.append(rowHtml);

				// Generate hidden form inputs for backend inventory/sales_save
				var hiddenInputs = 
					'<input type="hidden" name="sales[' + i + '][category]" value="' + item.category_id + '">' +
					'<input type="hidden" name="sales[' + i + '][product]" value="' + item.id + '">' +
					'<input type="hidden" name="sales[' + i + '][unit_price]" value="' + item.price.toFixed(2) + '">' +
					'<input type="hidden" name="sales[' + i + '][quantity]" value="' + item.quantity + '">' +
					'<input type="hidden" name="sales[' + i + '][discount]" value="' + item.discount + '">' +
					'<input type="hidden" name="sales[' + i + '][sub_total]" value="' + linePrice.toFixed(2) + '">' +
					'<input type="hidden" name="sales[' + i + '][net_sub_total]" value="' + lineNet.toFixed(2) + '">';
				hiddenContainer.append(hiddenInputs);
			});
		}

		var netPayable = Math.max(0, subTotal - totalDiscount);

		$('#posCartBadge').text(totalCount);
		$('#posDisplaySubTotal').text(subTotal.toFixed(2));
		$('#posDisplayDiscount').text(totalDiscount.toFixed(2));
		$('#posDisplayNetPayable').text(netPayable.toFixed(2));

		$('#sub_total_amount').val(subTotal.toFixed(2));
		$('#grandTotal').val(subTotal.toFixed(2));
		$('#total_discount').val(totalDiscount.toFixed(2));
		$('#totalDiscount').val(totalDiscount.toFixed(2));
		$('#netPayable').val(netPayable.toFixed(2));
		$('#netGrandTotal').val(netPayable.toFixed(2));

		updateChangeCalculation();
	}

	// Realtime Change / Due calculation
	function updateChangeCalculation() {
		var netPayable = parseFloat($('#netPayable').val()) || 0;
		var paid = parseFloat($('#payment_amount').val()) || 0;
		var indicator = $('#posChangeIndicator');

		if (paid > 0 && netPayable > 0) {
			if (paid >= netPayable) {
				var change = paid - netPayable;
				indicator.show().css({ 'background': '#dcfce7', 'color': '#15803d' }).html('<i class="fas fa-arrow-down mr-xs"></i> Change to Return: ' + curSymbol + change.toFixed(2));
			} else {
				var due = netPayable - paid;
				indicator.show().css({ 'background': '#fef3c7', 'color': '#b45309' }).html('<i class="fas fa-exclamation-circle mr-xs"></i> Balance Due: ' + curSymbol + due.toFixed(2));
			}
		} else {
			indicator.hide();
		}
	}

	// Quick Cash Buttons
	function setQuickCash(type) {
		if (type === 'exact') {
			var netPayable = parseFloat($('#netPayable').val()) || 0;
			$('#payment_amount').val(netPayable.toFixed(2));
			updateChangeCalculation();
		}
	}

	function addQuickCash(amount) {
		var current = parseFloat($('#payment_amount').val()) || 0;
		$('#payment_amount').val((current + amount).toFixed(2));
		updateChangeCalculation();
	}

	function escapeHtml(text) {
		if (!text) return '';
		return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
	}
</script>