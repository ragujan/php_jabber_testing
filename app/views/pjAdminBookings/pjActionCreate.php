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
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionBarcode"><?php __('tabBarcodeReader'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjInvoice&amp;action=pjActionInvoices"><?php __('plugin_invoice_menu_invoices'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoAddBookingTitle', true, false), __('infoAddBookingDesc', true, false)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate" method="post" class="form pj-form" id="frmCreateBooking">
		<input type="hidden" name="booking_create" value="1" />
		<input type="hidden" id="venue_id"name="venue_id" value="<?php echo isset($tpl['venue_id']) && !empty($tpl['venue_id']) ? $tpl['venue_id'] : NULL;?>" />
		<input type="hidden" id="reload_map" name="reload_map" value="1" />
		<input type="hidden" name="tab_id" value="<?php echo isset($_GET['tab_id']) && !empty($_GET['tab_id']) ? $_GET['tab_id'] : 'tabs-1'; ?>" />
		
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('lblBookingDetails');?></a></li>
				<li><a href="#tabs-2"><?php __('lblClientDetails');?></a></li>
			</ul>
			<div id="tabs-1" class="pj-loader-outer">
				<div class="pj-loader"></div>
				<p>
					<label class="title"><?php __('lblEvent'); ?></label>
					<span class="inline-block">
						<select name="event_id" id="event_id" class="pj-form-field w300 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach ($tpl['event_arr'] as $v)
							{
								?><option value="<?php echo $v['id']; ?>"<?php echo isset($_GET['event_id']) ? ($_GET['event_id'] == $v['id'] ? ' selected="selected"' : null) : null;?>><?php echo stripslashes($v['title']); ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblShow'); ?></label>
					<span id="boxShow" class="inline-block">
						<?php
						if(!isset($_GET['event_id']))
						{ 
							?>
							<select name="date_time" id="date_time" class="pj-form-field w300 required">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
							</select>
							<?php
						}else{
							?>
							<select name="date_time" id="date_time" class="pj-form-field w300 required">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
								<?php
								foreach($tpl['show_arr'] as $v)
								{
									?><option value="<?php echo $v['date_time'];?>"<?php echo $v['date_time'] == $tpl['date_time'] ? ' selected="selected"' : null;?>><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['date_time'])); ?>, <?php echo date($tpl['option_arr']['o_time_format'], strtotime($v['date_time'])); ?></option><?php
								} 
								?>
							</select>
							<?php
						} 
						?>
					</span>
				</p>
				<div id="ticketBox">
					<?php
					if(isset($_GET['event_id']))
					{
						$ticket_name_arr = array();
						if(isset($tpl['ticket_arr']) && count($tpl['ticket_arr']) > 0)
						{
							?>
							<p>
								<label class="title"><?php __('lblTickets');?></label>
								<span class="block overflow">
									<?php
									foreach($tpl['ticket_arr'] as $v)
									{
										if($v['cnt_tickets'] > 0)
										{
											?>
											<span class="block b5 overflow">
												<label class="block float_left r5 t5 w150"><?php echo pjSanitize::html($v['ticket']);?></label>
												<select id="tbTicket_<?php echo $v['price_id'];?>" name="tickets[<?php echo $v['id'];?>][<?php echo $v['price_id'];?>]" class="pj-form-field w60 r3 float_left tbTicketSelector" data-id="<?php echo $v['price_id'];?>" data-ticket="<?php echo pjSanitize::html($v['ticket']);?>" data-price="<?php echo $v['price'];?>">
													<?php
													for($i = 0; $i <= $v['cnt_tickets']; $i++)
													{
														?><option value="<?php echo $i;?>"><?php echo $i?></option><?php
													} 
													?>
												</select>
												<label class="block float_left r5 t5">x</label>
												<label class="block float_left t5"><?php echo pjUtil::formatCurrencySign( $v['price'], $tpl['option_arr']['o_currency']);?></label>
											</span>
											<?php
										}
										$ticket_name_arr[$v['price_id']] = pjSanitize::html($v['ticket']);
									} 
									?>
								</span>
							</p>
							<?php
						}
						if($tpl['has_map'] == 0)
						{ 
							?>
							<div id="tbMapHolder">
								<?php
								foreach ($tpl['seat_arr'] as $seat)
								{
									$is_selected = false;
									$is_available = true;
									$_arr = explode("~:~", $seat['price_id']);
									?><span class="tbSeatRect<?php echo $seat['seats'] - $seat['cnt_booked'] <= 0 ? ' tbSeatBlocked' : ($is_available == true ? ' tbSeatAvailable' : null); ?><?php echo $is_selected == true ? ' tbSeatSelected' : null;?>" data-id="<?php echo $seat['id']; ?>" data-price-id="<?php echo $seat['price_id']; ?>" data-name="<?php echo $seat['name']; ?>" data-count="<?php echo $seat['seats']; ?>" style="display: none;"><?php echo stripslashes($seat['name']); ?></span><?php
								}
								?>
							</div>
							<?php
						}
					}
					?>
				</div>
				<div id="seatsBox" style="display: none;">
					<p>
						<label class="title"><?php __('lblSeats'); ?></label>
						<span class="inline-block">
							<label class="content">
								<a class="tb-select-seats" href="#"><?php __('lblSelectSeats');?></a>
								<span id="tbCopiedSeats" class="copied"></span>
							</label>
							<label class="tbSeatValidation"><?php __('lblSelectMoreSeats');?></label>
						</span>
					</p>
				</div>
				<p>
					<label class="title"><?php __('lblSubTotal'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="sub_total" name="sub_total" class="pj-form-field number w108 required" readonly="readonly"/>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblTax'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="tax" name="tax" class="pj-form-field number w108" readonly="readonly" data-tax="<?php echo $tpl['option_arr']['o_tax_payment'];?>"/>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblTotal'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="total" name="total" class="pj-form-field number w108" readonly="readonly"/>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblDeposit'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="deposit" name="deposit" class="pj-form-field number w108" readonly="readonly" data-deposit="<?php echo $tpl['option_arr']['o_deposit_payment'];?>"/>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblPaymentMethod');?></label>
					<span class="inline-block">
						<select name="payment_method" id="payment_method" class="pj-form-field w150 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach (__('payment_methods', true, false) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p class="boxCC" style="display: none;">
					<label class="title"><?php __('lblCCType'); ?></label>
					<span class="inline-block">
						<select name="cc_type" class="pj-form-field w150">
							<option value="">---</option>
							<?php
							foreach (__('cc_types', true, false) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p class="boxCC" style="display: none;">
					<label class="title"><?php __('lblCCNum'); ?></label>
					<span class="inline-block">
						<input type="text" name="cc_num" id="cc_num" class="pj-form-field w136" />
					</span>
				</p>
				<p class="boxCC" style="display: none;">
					<label class="title"><?php __('lblCCExp'); ?></label>
					<span class="inline-block">
						<select name="cc_exp_month" class="pj-form-field">
							<option value="">---</option>
							<?php
							$month_arr = __('months', true, false);
							ksort($month_arr);
							foreach ($month_arr as $key => $val)
							{
								?><option value="<?php echo $key;?>"><?php echo $val;?></option><?php
							}
							?>
						</select>
						<select name="cc_exp_year" class="pj-form-field">
							<option value="">---</option>
							<?php
							$y = (int) date('Y');
							for ($i = $y; $i <= $y + 10; $i++)
							{
								?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p class="boxCC" style="display: none">
					<label class="title"><?php __('lblCCCode'); ?></label>
					<span class="inline-block">
						<input type="text" name="cc_code" id="cc_code" class="pj-form-field w100" />
					</span>
				</p>
				<div class="p">
					<label class="title"><?php __('lblStatus'); ?></label>
					<span class="inline-block">
						<select name="status" id="status" class="pj-form-field w150 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach (__('booking_statuses', true, false) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</div>
				<p>
					<label class="title">&nbsp;</label>
					<span id="tbSeatsForm" style="display: none;"></span>
					<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
				</p>
			</div>
			
			<div id="tabs-2">
				<?php
				if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
				{
					?>
					<p>
						<label class="title"><?php __('lblBookingTitle'); ?></label>
						<span class="inline-block">
							<select name="c_title" id="c_title" class="pj-form-field w150<?php echo $tpl['option_arr']['o_bf_include_title'] == 3 ? ' required' : NULL; ?>">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
								<?php
								foreach ( __('personal_titles', true, false) as $k => $v)
								{
									?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
								}
								?>
							</select>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_name'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblBookingName'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_name" id="c_name" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_name'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_email'], array(2,3)))
				{
					?>
					<p>
						<label class="title"><?php __('lblBookingEmail'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_email" id="c_email" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_email'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_phone'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblBookingPhone'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_phone" id="c_phone" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_phone'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_notes'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblBookingNotes'); ?></label>
						<span class="inline-block">
							<textarea name="c_notes" id="c_notes" class="pj-form-field w500 h120<?php echo $tpl['option_arr']['o_bf_include_notes'] == 3 ? ' required' : NULL; ?>"></textarea>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_company'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblBookingCompany'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_company" id="c_company" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_company'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_address'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblBookingAddress'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_address" id="c_address" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_address'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_city'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblBookingCity'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_city" id="c_city" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_city'] == 3 ? ' required' : NULL; ?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_state'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblBookingState'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_state" id="c_state" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_state'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_zip'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblBookingZip'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_zip" id="c_zip" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_zip'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_country'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblBookingCountry'); ?></label>
						<span class="inline-block">
							<select name="c_country" id="c_country" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_country'] == 3 ? ' required' : NULL; ?>">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
								<?php
								foreach ($tpl['country_arr'] as $v)
								{
									?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['country_title']); ?></option><?php
								}
								?>
							</select>
						</span>
					</p>
					<?php
				}
				?>
				
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
				</p>
			</div>
		</div>
	</form>
	<div id="dialogSelect" title="<?php __('lblSelectSeats'); ?>" style="display:none"><img src="<?php echo PJ_IMG_PATH . 'backend/pj-preloader.gif'?>" /></div>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.guide_msg = <?php echo pjAppController::jsonEncode(__('front_guide', true)); ?>;
	</script>
	<?php
	if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
	{		
		$tab_id = $_GET['tab_id'];
		$tab_id = $tab_id < 0 ? 0 : $tab_id;
		?>
		<script type="text/javascript">
		(function ($) {
			$(function () {
				$("#tabs").tabs("option", "selected", <?php echo $tab_id; ?>);
			});
		})(jQuery);
		</script>
		<?php
	}
}
?>