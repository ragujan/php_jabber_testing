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
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('menuBookings'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionBarcode"><?php __('tabBarcodeReader'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjInvoice&amp;action=pjActionInvoices"><?php __('plugin_invoice_menu_invoices'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoBarcodeReaderTitle', true), __('infoBarcodeReaderDesc', true)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionBarcode" method="post" id="frmReadBarcode" class="form pj-form">
		<input type="hidden" name="read_barcode" value="1" autocomplete="off"/>
		
		<div class="barcode-container">
			<label class="block b10"><?php __('lblBarcodeDetails')?></label>
			<span class="block b10 overflow"><input type="text" name="barcode_label" id="barcode_label" value="" class="pj-form-field barcode-field required b10" /></span>
			<span class="block b10 overflow"><input type="submit" value="<?php __('btnCheck'); ?>" class="pj-button" /></span>
			<?php
			if(isset($tpl['ticket_status']))
			{
				$ticket_statuses = __('ticket_statuses', true);
				if($tpl['ticket_status'] == 1)
				{
					?>
					<label class="check-ticket-message"><?php echo $ticket_statuses[$tpl['ticket_status']]; ?></label>
					<?php
				}else{
					?>
					<label class="check-ticket-error"><?php echo $ticket_statuses[$tpl['ticket_status']]; ?></label>
					<?php
				}
			} 
			?>
		</div>
		<br/><br/>
		<?php
		if(isset($tpl['arr']))
		{
			$booking_statuses = __('booking_statuses', true);
			
			?>
			<p>
				<label class="title"><?php echo ucfirst(__('lblTicket', true)); ?></label>
				<span class="block r20 overflow float_left">
					<label class="content"><?php echo stripslashes($_POST['barcode_label']);?></label>
				</span>
				<?php
				if($tpl['ticket_status'] == 1)
				{ 
					?>
					<span class="block t5 pointer overflow">
						<input type="checkbox" <?php echo $tpl['arr']['is_used'] == 'T' ? 'disabled="disabled" checked="checked"' : null; ?> class="r5 pointer" id="use_ticket" lang="<?php echo $tpl['arr']['ticket_id'];?>" /><label class="pointer" for="use_ticket"><?php echo __('lblUseTicket');?></label>
					</span>
					<?php
				} 
				?>
			</p>
			<p>
				<label class="title"><?php __('lblEvent'); ?></label>
				<span class="inline_block">
					<label class="content"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionUpdate&id=<?php echo $tpl['arr']['event_id'];?>"><?php echo stripslashes($tpl['arr']['event_title'])?></a></label>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblStatus'); ?></label>
				<span class="inline_block">
					<label class="content"><?php echo $booking_statuses[$tpl['arr']['status']]; ?></label>
				</span>
			</p>
			<?php
			if (in_array((int) $tpl['option_arr']['o_bf_include_name'], array(2,3)))
			{ 
				?>
				<p>
					<label class="title"><?php __('lblName'); ?></label>
					<span class="inline_block">
						<label class="content"><?php echo pjSanitize::html($tpl['arr']['c_name']); ?></label>
					</span>
				</p>
				<?php
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_email'], array(2,3)))
			{ 
				?>
				<p>
					<label class="title"><?php __('lblEmail'); ?></label>
					<span class="inline_block">
						<label class="content"><?php echo pjSanitize::html($tpl['arr']['c_email']); ?></label>
					</span>
				</p>
				<?php
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_phone'], array(2,3)))
			{ 
				?>
				<p>
					<label class="title"><?php __('lblBookingPhone'); ?></label>
					<span class="inline_block">
						<label class="content"><?php echo pjSanitize::html($tpl['arr']['c_phone']); ?></label>
					</span>
				</p>
				<?php
			} 
			?>
			<p>
				<label class="title"><?php __('lblTickets'); ?></label>
				<span class="inline_block">
					<label class="content overflow">
						<?php
						echo $tpl['arr']['tickets']; 
						?>
					</label>
				</span>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<label class="content"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&id=<?php echo $tpl['arr']['id'];?>"><?php __('lblEditBooking'); ?></a></label>
				</span>
			</p>
			<?php
		}
		?>
	</form>
	<div id="dialogTicketConfirmation" title="<?php __('lblTicketConfirmationTitle'); ?>" style="display:none">
		<p><?php __('lblTicketConfirmationDesc'); ?></p>
	</div>
	<?php
}
?>