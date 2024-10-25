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
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionResend&id=<?php echo $_GET['id'];?>"><?php __('lblResendTickets'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoResendTitle', true), __('infoResendDesc', true)); 
	?>
	
	<input type="button" value="<?php __('btnBack'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionUpdate&id=<?php echo $_GET['id'];?>';" />
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionResend" method="post" id="frmResendConfirm" class="form pj-form">
		<input type="hidden" name="resend_email" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
		<input type="hidden" name="to" value="<?php echo $tpl['arr']['c_email'];?>" />
		<fieldset class="fieldset overflow b20 t20">
			<legend><?php __('lblConfirmationEmail'); ?></legend>
			<p>
				<label class="title"><?php __('lblSubject'); ?></label>
				<span class="inline_block">
					<input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['confirm_subject'])); ?>" class="pj-form-field w400 required" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblMessage'); ?></label>
				<span class="inline_block">
					<textarea name="message" class="pj-form-field mceEditor" style="width: 500px; height: 300px;"><?php echo stripslashes($tpl['arr']['confirm_message']); ?></textarea>
				</span>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSend'); ?>" class="pj-button" />
			</p>
		</fieldset>
	</form>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionResend" method="post" id="frmResendPayment" class="form pj-form">
		<input type="hidden" name="resend_email" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
		<input type="hidden" name="to" value="<?php echo $tpl['arr']['c_email'];?>" />
		<fieldset class="fieldset overflow b10">
			<legend><?php __('lblPaymentEmail'); ?></legend>
			<p>
				<label class="title"><?php __('lblSubject'); ?></label>
				<span class="inline_block">
					<input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['payment_subject'])); ?>" class="pj-form-field w400 required" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblMessage'); ?></label>
				<span class="inline_block">
					<textarea name="message" class="pj-form-field mceEditor" style="width: 500px; height: 300px;"><?php echo stripslashes($tpl['arr']['payment_message']); ?></textarea>
				</span>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSend'); ?>" class="pj-button" />
			</p>
		</fieldset>
	</form>
	<?php
}
?>