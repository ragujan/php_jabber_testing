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
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
		
	pjUtil::printNotice(__('infoBookingsTitle', true), __('infoBookingsDesc', true));
	
	if (isset($tpl['arr']))
	{
		if (is_array($tpl['arr']))
		{
			$count = count($tpl['arr']);
			if ($count > 0)
			{
				?>
				<form id="frmUpdateOptions" action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form">
					<input type="hidden" name="options_update" value="1" />
					<input type="hidden" name="next_action" value="pjActionBooking" />
					<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
						<thead>
							<tr>
								<th><?php __('lblOption'); ?></th>
								<th><?php __('lblValue'); ?></th>
							</tr>
						</thead>
						<tbody>
	
				<?php
				for ($i = 0; $i < $count; $i++)
				{
					if ($tpl['arr'][$i]['tab_id'] != 2 || (int) $tpl['arr'][$i]['is_visible'] === 0) continue;
					
					$rowClass = NULL;
					$rowStyle = NULL;
					if (in_array($tpl['arr'][$i]['key'], array('o_paypal_address','o_paypal_client_id','o_paypal_client_secret','o_paypal_cancel_url')))
					{
						$rowClass = " boxPaypal";
						$rowStyle = "display: none";
						if ($tpl['option_arr']['o_allow_paypal'] == 'Yes')
						{
							$rowStyle = NULL;
						}
					}
					if (in_array($tpl['arr'][$i]['key'], array('o_authorize_merchant_id', 'o_authorize_transkey', 'o_authorize_timezone', 'o_authorize_hash')))
					{
						$rowClass = " boxAuthorize";
						$rowStyle = "display: none";
						if ($tpl['option_arr']['o_allow_authorize'] == 'Yes')
						{
							$rowStyle = NULL;
						}
					}
					if (in_array($tpl['arr'][$i]['key'], array('o_bank_account')))
					{
						$rowClass = " boxBankAccount";
						$rowStyle = "display: none";
						if ($tpl['option_arr']['o_allow_bank'] == 'Yes')
						{
							$rowStyle = NULL;
						}
					}
					
					?>
					<tr class="pj-table-row-odd<?php echo $rowClass; ?>" style="<?php echo $rowStyle; ?>">
						<td width="50%">
							<span class="block"><?php __('opt_' . $tpl['arr'][$i]['key']); ?></span>
						</td>
						<td>
							<?php
							switch ($tpl['arr'][$i]['type'])
							{
								case 'string':
								    ?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w400" value="<?php echo isset($tpl['arr'][$i]['value']) && !empty($tpl['arr'][$i]['value']) ? htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])) : ''; ?>" /><?php
									break;
								case 'text':
								    ?><textarea name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field" style="width: 400px; height: 80px;"><?php echo isset($tpl['arr'][$i]['value']) && !empty($tpl['arr'][$i]['value']) ? htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])) : ''; ?></textarea><?php
									break;
								case 'int':
									if($tpl['arr'][$i]['key'] == 'o_deposit_payment' || $tpl['arr'][$i]['key'] == 'o_tax_payment')
									{
										?>
										<span class="pj-form-field-custom pj-form-field-custom-after">
											<input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w60 align_right" value="<?php echo isset($tpl['arr'][$i]['value']) && !empty($tpl['arr'][$i]['value']) ? htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])) : ''; ?>" />
											<span class="pj-form-field-after"><abbr class="pj-form-field-icon-text">%</abbr></span>
										</span>
										<?php
									}else{
										?>
										<input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-int w60" value="<?php echo isset($tpl['arr'][$i]['value']) && !empty($tpl['arr'][$i]['value']) ? htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])) : ''; ?>" />
										<?php
									}
									break;
								case 'float':
								    ?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-float w60" value="<?php echo isset($tpl['arr'][$i]['value']) && !empty($tpl['arr'][$i]['value']) ? htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])) : ''; ?>" /><?php
									break;
								case 'enum':
									?><select name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field">
									<?php
									$default = explode("::", $tpl['arr'][$i]['value']);
									$enum = explode("|", $default[0]);
									
									$enumLabels = array();
									if (!empty($tpl['arr'][$i]['label']) && strpos($tpl['arr'][$i]['label'], "|") !== false)
									{
										$enumLabels = explode("|", $tpl['arr'][$i]['label']);
									}
									if (in_array($tpl['arr'][$i]['key'], array('o_booking_status', 'o_payment_status')))
									{
									    $enumLabels = array();
									    $enumLabels = __('booking_statuses', true);
									}
									if (in_array($tpl['arr'][$i]['key'], array('o_payment_disable', 'o_allow_paypal', 'o_allow_authorize', 'o_allow_cash', 'o_allow_creditcard', 'o_allow_bank')))
									{
									    $enumLabels = array();
									    $enumLabels['Yes'] = __('_yesno_ARRAY_T', true);
									    $enumLabels['No'] = __('_yesno_ARRAY_F', true);
									}
									foreach ($enum as $k => $el)
									{
										if ($default[1] == $el)
										{
										    ?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : (array_key_exists($el, $enumLabels) ? $enumLabels[$el] : stripslashes($el)); ?></option><?php
										} else {
										    ?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : (array_key_exists($el, $enumLabels) ? $enumLabels[$el] : stripslashes($el)); ?></option><?php
										}
									}
									?>
									</select>
									<?php
									break;
							}
							?>
						</td>
					</tr>
					<?php
					if ($tpl['arr'][$i]['key'] == 'o_authorize_hash') {
					    ?>
					 	<tr class="pj-table-row-odd<?php echo $rowClass; ?>" style="<?php echo $rowStyle; ?>">
    						<td width="50%">
    							<span class="block"><?php __('opt_o_authorize_silent_post_url'); ?></span>
    						</td>
    						<td><?php echo PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmAuthorize';?></td>
    					</tr>
					    <?php 
					}
				}
				?>
						</tbody>
					</table>
					
					<p><input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" /></p>
				</form>
				<script type="text/javascript">
				var myLabel = myLabel || {};
				myLabel.positive_number = "<?php __('lblPositiveNumber', false, true); ?>";
				</script>
				<?php
			}
		}
	}
}
?>