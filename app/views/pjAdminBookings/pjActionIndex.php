<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('menuBookings'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionBarcode"><?php __('tabBarcodeReader'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjInvoice&amp;action=pjActionInvoices"><?php __('plugin_invoice_menu_invoices'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoBookingsListTitle', true, false), __('infoBookingsListDesc', true, false)); 
	?>
	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left r5">
			<input type="hidden" name="controller" value="pjAdminBookings" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnAddBooking'); ?>" />
		</form>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
			<button type="button" class="pj-button pj-button-detailed"><span class="pj-button-detailed-arrow"></span></button>
		</form>
		<?php
		$bs = __('booking_statuses', true);
		?>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-filter btn-all"><?php __('lblAll')?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="confirmed"><?php echo $bs['confirmed']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="pending"><?php echo $bs['pending']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="cancelled"><?php echo $bs['cancelled']; ?></a>
		</div>
		<br class="clear_both" />
	</div>
	
	<div class="pj-form-filter-advanced" style="display: none">
		<span class="pj-menu-list-arrow"></span>
		<form action="" method="get" class="form pj-form pj-form-search frm-filter-advanced">
			<div class="overflow float_left w340">
				<p>
					<label class="title"><?php __('lblID'); ?></label>
					<input type="text" name="uuid" id="uuid" class="pj-form-field w150" />
				</p>
				<p>
					<label class="title"><?php __('lblBookingName'); ?></label>
					<input type="text" name="c_name" id="c_name" class="pj-form-field w150" />
				</p>
			</div>
			<div class="overflow">
				<p class="w340">
					<label class="title"><?php __('lblTotalPrice'); ?></label>
					<label class="float_left block r5 t5"><?php __('lblFrom');?></label><input type="text" name="from_price" id="from_price" class="pj-form-field w50 r10 float_left" />
					<label class="float_left block r5 t5"><?php __('lblTo');?></label><input type="text" name="to_price" id="to_price" class="pj-form-field w50 float_left" />
				</p>
				<p class="w340">
					<label class="title"><?php __('lblBookingEmail'); ?></label>
					<input type="text" name="c_email" id="c_email" class="pj-form-field w170" />
				</p>
			</div>
			<div class="overflow float_left w680">
				<p style="overflow: visible">
					<label class="title"><?php __('lblEvent'); ?></label>
					<span class="inline_block">
						<select name="event_id" id="filter_event_id" class="pj-form-field w400">
							<option value="">-- <?php __('lblChoose'); ?> --</option>
							<?php
							if (isset($tpl['event_arr']) && count($tpl['event_arr']) > 0)
							{
								foreach ($tpl['event_arr'] as $v)
								{
									?><option value="<?php echo $v['id']; ?>"<?php echo isset($_GET['event_id']) ? ($_GET['event_id'] == $v['id'] ? ' selected="selected"' : null) : null;?>><?php echo pjSanitize::html($v['title']); ?></option><?php
									
								}
							}
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSearch'); ?>" class="pj-button" />
					<input type="reset" value="<?php __('btnCancel'); ?>" class="pj-button" />
				</p>
			</div>
		</form>
	</div>
	
	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['event_id']) && (int) $_GET['event_id'] > 0)
	{
		?>pjGrid.queryString += "&event_id=<?php echo (int) $_GET['event_id']; ?>";<?php
	}
	if (isset($_GET['time']) && $_GET['time'] != '')
	{
		?>pjGrid.queryString += "&time=<?php echo $_GET['time']; ?>";<?php
	}
	if (isset($_GET['dt']) && $_GET['dt'] != '')
	{
		?>pjGrid.queryString += "&dt=<?php echo $_GET['dt']; ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.event = "<?php __('lblEvent'); ?>";
	myLabel.name = "<?php __('lblName'); ?>";
	myLabel.tickets = "<?php __('lblTickets'); ?>";
	myLabel.date_time = "<?php __('lblDateTime'); ?>";
	myLabel.exported = "<?php __('lblExport'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.pending = "<?php echo $bs['pending']; ?>";
	myLabel.confirmed = "<?php echo $bs['confirmed']; ?>";
	myLabel.cancelled = "<?php echo $bs['cancelled']; ?>";
	</script>
	<?php
}
?>