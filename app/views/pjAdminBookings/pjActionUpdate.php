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
	pjUtil::printNotice(__('infoUpdateBookingTitle', true, false), __('infoUpdateBookingDesc', true, false)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate" method="post" class="form pj-form" id="frmUpdateBooking">
		<input type="hidden" name="booking_update" value="1" />
		<input type="hidden" id="id"name="id" value="<?php echo $tpl['arr']['id'];?>" />
		<input type="hidden" id="venue_id"name="venue_id" value="<?php echo $tpl['venue_id'];?>" />
		<input type="hidden" id="has_map" name="has_map" value="<?php echo $tpl['has_map'];?>"/>
		<input type="hidden" id="reload_map" name="reload_map" value="0"/>
		<input type="hidden" name="tab_id" value="<?php echo isset($_GET['tab_id']) && !empty($_GET['tab_id']) ? $_GET['tab_id'] : 'tabs-1'; ?>" />
		
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('lblBookingDetails');?></a></li>
				<li><a href="#tabs-2"><?php __('lblClientDetails');?></a></li>
				<li><a href="#tabs-3"><?php __('lblTabInvoices'); ?></a></li>
			</ul>
			<div id="tabs-1" class="pj-loader-outer">
				<div class="pj-loader"></div>
				<p>
					<label class="title">&nbsp;</label>
					<span class="inline_block">
						<label class="content"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionResend&id=<?php echo $tpl['arr']['id'];?>"><?php echo __('lblResendTickets', true); ?></a></label>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblCreatedOn'); ?></label>
					<span class="inline_block">
						<label class="content"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['created'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['created']));?></label>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblID'); ?></label>
					<span class="inline-block">
						<input type="text" name="uuid" id="uuid" class="pj-form-field w1500 required" value="<?php echo pjSanitize::html($tpl['arr']['uuid']); ?>"/>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblEvent'); ?></label>
					<span class="inline-block">
						<select name="event_id" id="event_id" class="pj-form-field w300 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach ($tpl['event_arr'] as $v)
							{
							    ?><option value="<?php echo $v['id']; ?>"<?php echo $tpl['arr']['event_id'] == $v['id'] ? ' selected="selected"' : null;?>><?php echo pjSanitize::html($v['title']); ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				
				<p>
					<label class="title"><?php __('lblShow'); ?></label>
					<span id="boxShow" class="inline-block">
						<select name="date_time" id="date_time" class="pj-form-field w300 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach($tpl['show_arr'] as $v)
							{
								?><option value="<?php echo $v['date_time'];?>"<?php echo ($v['date_time'] == $tpl['arr']['booking_date_time'] && $v['venue_id'] == $tpl['venue_id']) ? ' selected="selected"' : null;?> data-venue_id="<?php echo $v['venue_id']?>"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['date_time'])); ?>, <?php echo date($tpl['option_arr']['o_time_format'], strtotime($v['date_time'])); ?>, <?php echo pjSanitize::html($v['venue_name']);?></option><?php
							} 
							?>
						</select>
					</span>
				</p>
				<div id="ticketBox">
					<?php
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
													?><option value="<?php echo $i;?>"<?php echo $i == $v['cnt'] ? ' selected="selected"' : null;?>><?php echo $i?></option><?php
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
								foreach($_arr as $pid)
								{
									if(isset($tpl['seat_id_arr'][$pid][$seat['id']]))
									{
										$is_selected = true;
										if($seat['seats'] == $tpl['seat_id_arr'][$pid][$seat['id']])
										{
											$is_available = false;
										}
									}
								}
								?><span class="tbSeatRect<?php echo $seat['seats'] - $seat['cnt_booked'] <= 0 ? ' tbSeatBlocked' : ($is_available == true ? ' tbSeatAvailable' : null); ?><?php echo $is_selected == true ? ' tbSeatSelected' : null;?>" data-id="<?php echo $seat['id']; ?>" data-price-id="<?php echo $seat['price_id']; ?>" data-name="<?php echo $seat['name']; ?>" data-count="<?php echo $seat['seats']; ?>" style="display: none;"><?php echo pjSanitize::html($seat['name']); ?></span><?php
							}
							?>
						</div>
						<?php
					}
					?>
				</div>
				<div id="seatsBox" style="display: block;">
					<p>
						<label class="title"><?php __('lblSeats'); ?></label>
						<span class="inline-block">
							<label class="content">
								<a class="tb-select-seats" href="#" style="display:<?php echo $tpl['has_map'] == 1 ? ' block' : ' none;'?>"><?php __('lblSelectSeats');?></a>
								<span id="tbCopiedSeats" class="copied">
									<?php
									if(isset($tpl['ticket_arr']) && count($tpl['ticket_arr']) > 0)
									{
										$class = 'tbAssignedNoMap';
										if($tpl['has_map'] == 1)
										{
											$class = 'tbAssignedSeats';
										}
										if(isset($tpl['seat_id_arr']) && count($tpl['seat_id_arr']) > 0)
										{
											$seat_label_arr = $tpl['seat_id_arr'];
											foreach($seat_label_arr as $price_id => $seat_arr)
											{
												foreach($seat_arr as $seat_id => $cnt)
												{
													for($i = 1; $i <= $cnt; $i++)
													{
														?><span class="<?php echo $class;?> tbAssignedSeats_<?php echo $price_id;?>" data_seat_id="<?php echo $seat_id;?>" data_price_id="<?php echo $price_id;?>"><?php echo $ticket_name_arr[$price_id]; ?> #<?php echo @$tpl['seat_name_arr'][$seat_id];?></span><?php
													}	
												}
											}
										}
									} 
									?>
								</span>
								<?php
								$pdf_file = PJ_UPLOAD_PATH . 'tickets/pdfs/p_' . $tpl['arr']['uuid'] . '.pdf';
								if(is_file(PJ_INSTALL_PATH . $pdf_file))
								{ 
									?><a class="" href="<?php echo PJ_INSTALL_URL . $pdf_file;?>" target="_blank" style="display:block"><?php __('lblPrintTickets');?></a><?php
								} 
								?>
							</label>
							<label class="tbSeatValidation"><?php __('lblSelectMoreSeats');?></label>
						</span>
					</p>
				</div>
				<p>
					<label class="title"><?php __('lblSubTotal'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="sub_total" name="sub_total" value="<?php echo pjSanitize::clean($tpl['arr']['sub_total']); ?>" class="pj-form-field number w108 required" readonly="readonly"/>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblTax'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="tax" name="tax" value="<?php echo pjSanitize::clean($tpl['arr']['tax']); ?>" class="pj-form-field number w108" readonly="readonly" data-tax="<?php echo $tpl['option_arr']['o_tax_payment'];?>"/>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblTotal'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="total" name="total" value="<?php echo pjSanitize::clean($tpl['arr']['total']); ?>" class="pj-form-field number w108" readonly="readonly"/>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblDeposit'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="deposit" name="deposit" value="<?php echo pjSanitize::clean($tpl['arr']['deposit']); ?>" class="pj-form-field number w108" readonly="readonly" data-deposit="<?php echo $tpl['option_arr']['o_deposit_payment'];?>"/>
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
								?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['payment_method'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p class="boxCC" style="display: <?php echo $tpl['arr']['payment_method'] != 'creditcard' ? 'none' : 'block'; ?>">
					<label class="title"><?php __('lblCCType'); ?></label>
					<span class="inline-block">
						<select name="cc_type" class="pj-form-field w150">
							<option value="">---</option>
							<?php
							foreach (__('cc_types', true, false) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['cc_type'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p class="boxCC" style="display: <?php echo $tpl['arr']['payment_method'] != 'creditcard' ? 'none' : 'block'; ?>">
					<label class="title"><?php __('lblCCNum'); ?></label>
					<span class="inline-block">
						<input type="text" name="cc_num" id="cc_num" value="<?php echo pjSanitize::clean($tpl['arr']['cc_num']); ?>" class="pj-form-field w136" />
					</span>
				</p>
				<p class="boxCC" style="display: <?php echo $tpl['arr']['payment_method'] != 'creditcard' ? 'none' : 'block'; ?>">
					<label class="title"><?php __('lblCCExp'); ?></label>
					<span class="inline-block">
						<select name="cc_exp_month" class="pj-form-field">
							<option value="">---</option>
							<?php
							$month_arr = __('months', true, false);
							ksort($month_arr);
							foreach ($month_arr as $key => $val)
							{
								?><option value="<?php echo $key;?>"<?php echo $key == $tpl['arr']['cc_exp_month'] ? ' selected="selected"' : NULL; ?>><?php echo $val;?></option><?php
							}
							?>
						</select>
						<select name="cc_exp_year" class="pj-form-field">
							<option value="">---</option>
							<?php
							$y = (int) date('Y');
							for ($i = $y; $i <= $y + 10; $i++)
							{
								?><option value="<?php echo $i; ?>"<?php echo $i == $tpl['arr']['cc_exp_year'] ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p class="boxCC" style="display: <?php echo $tpl['arr']['payment_method'] != 'creditcard' ? 'none' : 'block'; ?>">
					<label class="title"><?php __('lblCCCode'); ?></label>
					<span class="inline-block">
						<input type="text" name="cc_code" id="cc_code" value="<?php echo pjSanitize::clean($tpl['arr']['cc_code']); ?>" class="pj-form-field w100" />
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
								?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['status'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</div>
				<p>
					<label class="title">&nbsp;</label>
					<span id="tbSeatsForm" style="display: none;">
						<?php
						if(isset($tpl['seat_id_arr']) && count($tpl['seat_id_arr']) > 0)
						{
							$seat_label_arr = $tpl['seat_id_arr'];
							foreach($seat_label_arr as $price_id => $seat_arr)
							{
								foreach($seat_arr as $seat_id => $cnt)
								{
									?><input class="tbHiddenSeat_<?php echo $price_id;?>" type="hidden" name="seat_id[<?php echo $price_id;?>][<?php echo $seat_id;?>]" data_seat_id="<?php echo $seat_id;?>" value="<?php echo $cnt;?>"><?php
								}
							}
						} 
						?>
					</span>
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
									?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['c_title'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
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
							<input type="text" name="c_name" id="c_name" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_name'] == 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html($tpl['arr']['c_name']); ?>"/>
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
							<input type="text" name="c_email" id="c_email" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_email'] == 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html($tpl['arr']['c_email']); ?>"/>
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
							<input type="text" name="c_phone" id="c_phone" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_phone'] == 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html($tpl['arr']['c_phone']); ?>"/>
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
							<textarea name="c_notes" id="c_notes" class="pj-form-field w500 h120<?php echo $tpl['option_arr']['o_bf_include_notes'] == 3 ? ' required' : NULL; ?>"><?php echo pjSanitize::html($tpl['arr']['c_notes']); ?></textarea>
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
							<input type="text" name="c_company" id="c_company" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_company'] == 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html($tpl['arr']['c_company']); ?>"/>
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
							<input type="text" name="c_address" id="c_address" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_address'] == 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html($tpl['arr']['c_address']); ?>"/>
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
							<input type="text" name="c_city" id="c_city" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_city'] == 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html($tpl['arr']['c_city']); ?>"/>
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
							<input type="text" name="c_state" id="c_state" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_state'] == 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html($tpl['arr']['c_state']); ?>"/>
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
							<input type="text" name="c_zip" id="c_zip" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_zip'] == 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html($tpl['arr']['c_zip']); ?>"/>
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
								    ?><option value="<?php echo $v['id']; ?>"<?php echo $tpl['arr']['c_country'] == $v['id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($v['country_title']); ?></option><?php
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
			<div id="tabs-3">
				<?php
				if (pjObject::getPlugin('pjInvoice') !== NULL)
				{
					?>
					
					<input type="button" class="pj-button btnCreateInvoice" value="<?php __('btnCreateInvoice'); ?>" />
					
					<div id="grid_invoices" class="t10 b10"></div>
				
					<?php
				}
				?>
			</div><!-- #tabs-3 -->
		</div>
	</form>
	<?php
	$statuses = __('plugin_invoice_statuses', true);
	?>
	<div id="dialogSelect" title="<?php __('lblSelectSeats'); ?>" style="display:none">
		<?php
		$map = $tpl['venue_arr']['map_path'];
		if (is_file($map))
		{
			$size = getimagesize($map);
			?>
			<div style="display: block" class="b10">
				<div class="tbSelectSeatGuide"></div>
				<div class="tbLabelSeats"><?php __('lblSelectedSeats');?>:</div>
				<div class="tbAskToSelectSeats" style="display: none"><?php __('lblSelectAvailableSeats');?></div>
				<div id="tbSelectedSeats">
					<?php
					$class = 'tbAssignedSeats';
					if(isset($tpl['seat_id_arr']) && count($tpl['seat_id_arr']) > 0)
					{
						$seat_label_arr = $tpl['seat_id_arr'];
						foreach($seat_label_arr as $price_id => $seat_arr)
						{
							foreach($seat_arr as $seat_id => $cnt)
							{
								for($i = 1; $i <= $cnt; $i++)
								{
									?><span class="<?php echo $class;?> tbAssignedSeats_<?php echo $price_id;?>" data_seat_id="<?php echo $seat_id;?>" data_price_id="<?php echo $price_id;?>"><?php echo $tpl['ticket_name_arr'][$price_id]; ?> #<?php echo @$tpl['seat_name_arr'][$seat_id];?></span><?php
								}	
							}
						}
					} 
					?>
				</div>
				<div class="tbTipToRemoveSeats" style="display: block"><?php __('lblRemoveSeats');?><br/></div>
			</div>
			<div class="tb-seats-legend b10">
				<label><span class="tb-available-seats"></span><?php __('lblAvailableSeats');?></label>
				<label><span class="tb-selected-seats"></span><?php __('lblSelectedSeats');?></label>
				<label><span class="tb-booked-seats"></span><?php __('lblBookedSeats');?></label>
			</div>
			<div class="clear_both"></div>
			<div id="boxMap">
				<div id="tbMapHolder" class="tbMapHolder" style="position: relative; overflow: hidden; width: <?php echo $size[0]; ?>px; height: <?php echo $size[1]; ?>px; margin: 0 auto;">
					<img id="map" src="<?php echo $map; ?>" alt="" style="margin: 0; border: none; position: absolute; top: 0; left: 0; z-index: 500" />
					<?php
					foreach ($tpl['seat_arr'] as $seat)
					{
						$is_selected = false;
						$is_available = true;
						$_arr = explode("~:~", $seat['price_id']);
						$tooltip = array();
						foreach($_arr as $pid)
						{
							if(isset($tpl['seat_id_arr'][$pid][$seat['id']]))
							{
								$is_selected = true;
								if($seat['seats'] == $tpl['seat_id_arr'][$pid][$seat['id']])
								{
									$is_available = false;
								}
							}
						}
						?><span class="tbSeatRect<?php echo $seat['seats'] - $seat['cnt_booked'] <= 0 ? ' tbSeatBlocked' : ($is_available == true ? ' tbSeatAvailable' : null); ?><?php echo $is_selected == true ? ' tbSeatSelected' : null;?>" data-id="<?php echo $seat['id']; ?>" data-price-id="<?php echo $seat['price_id']; ?>" data-name="<?php echo $seat['name']; ?>" data-count="<?php echo $seat['seats']; ?>" style="width: <?php echo $seat['width']; ?>px; height: <?php echo $seat['height']; ?>px; left: <?php echo $seat['left']; ?>px; top: <?php echo $seat['top']; ?>px; line-height: <?php echo $seat['height']; ?>px"><?php echo stripslashes($seat['name']); ?></span><?php
					}
					?>
				</div>
			</div>
			<?php
		} 
		?>
	</div>
	<?php
	if (pjObject::getPlugin('pjInvoice') !== NULL)
	{
		$map = array(
			'confirmed' => 'paid',
			'pending' => 'not_paid',
			'cancelled' => 'cancelled'
		);
		?>
		<form action="<?php echo PJ_INSTALL_URL; ?>index.php" method="get" target="_blank" style="display: inline" id="frmCreateInvoice">
			<input type="hidden" name="controller" value="pjInvoice" />
			<input type="hidden" name="action" value="pjActionCreateInvoice" />
			<input type="hidden" name="tmp" value="<?php echo md5(uniqid(rand(), true)); ?>" />
			<input type="hidden" name="uuid" value="<?php echo pjUtil::uuid(); ?>" />
			<input type="hidden" name="order_id" value="<?php echo pjSanitize::html($tpl['arr']['uuid']); ?>" />
			<input type="hidden" name="issue_date" value="<?php echo date('Y-m-d'); ?>" />
			<input type="hidden" name="due_date" value="<?php echo date('Y-m-d'); ?>" />
			<input type="hidden" name="status" value="<?php echo @$map[$tpl['arr']['status']]; ?>" />
			<input type="hidden" name="subtotal" value="<?php echo $tpl['arr']['sub_total']; ?>" />
			<input type="hidden" name="discount" value="0.00" />
			<input type="hidden" name="tax" value="<?php echo $tpl['arr']['tax']; ?>" />
			<input type="hidden" name="shipping" value="0.00" />
			<input type="hidden" name="total" value="<?php echo $tpl['arr']['total']; ?>" />
			<input type="hidden" name="paid_deposit" value="<?php echo $tpl['arr']['deposit']; ?>" />
			<input type="hidden" name="amount_due" value="<?php echo  number_format($tpl['arr']['total'] - $tpl['arr']['deposit'], 2, '.', ''); ?>" />
			<input type="hidden" name="currency" value="<?php echo pjSanitize::html($tpl['option_arr']['o_currency']); ?>" />
			<input type="hidden" name="notes" value="<?php echo pjSanitize::html($tpl['arr']['c_notes']); ?>" />
			<input type="hidden" name="b_company" value="<?php echo pjSanitize::html($tpl['arr']['c_company']); ?>" />
			<input type="hidden" name="b_billing_address" value="<?php echo pjSanitize::html($tpl['arr']['c_address']); ?>" />
			<input type="hidden" name="b_name" value="<?php echo pjSanitize::html($tpl['arr']['c_name']); ?>" />
			<input type="hidden" name="b_address" value="<?php echo pjSanitize::html($tpl['arr']['c_address']); ?>" />
			<input type="hidden" name="b_street_address" value="<?php echo pjSanitize::html($tpl['arr']['c_address']); ?>" />
			<input type="hidden" name="b_city" value="<?php echo pjSanitize::html($tpl['arr']['c_city']); ?>" />
			<input type="hidden" name="b_state" value="<?php echo pjSanitize::html($tpl['arr']['c_state']); ?>" />
			<input type="hidden" name="b_zip" value="<?php echo pjSanitize::html($tpl['arr']['c_zip']); ?>" />
			<input type="hidden" name="b_phone" value="<?php echo pjSanitize::html($tpl['arr']['c_phone']); ?>" />
			<input type="hidden" name="b_email" value="<?php echo pjSanitize::html($tpl['arr']['c_email']); ?>" />
			<input type="hidden" name="items[0][name]" value="<?php echo pjSanitize::html($tpl['arr']['event_title']);?>" />
			<input type="hidden" name="items[0][description]" value="<?php echo stripslashes($tpl['arr']['tickets']); ?>" />
			<input type="hidden" name="items[0][qty]" value="1" />
			<input type="hidden" name="items[0][unit_price]" value="<?php echo $tpl['arr']['total']; ?>" />
			<input type="hidden" name="items[0][amount]" value="<?php echo $tpl['arr']['total']; ?>" />
		</form>
		<?php
	}
	?>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.guide_msg = <?php echo pjAppController::jsonEncode(__('front_guide', true)); ?>;
	myLabel.existing_id = "<?php __('lblBookingIDExists')?>";
	myLabel.num = "<?php __('plugin_invoice_i_num'); ?>";
	myLabel.order_id = "<?php __('plugin_invoice_i_order_id'); ?>";
	myLabel.issue_date = "<?php __('plugin_invoice_i_issue_date'); ?>";
	myLabel.due_date = "<?php __('plugin_invoice_i_due_date'); ?>";
	myLabel.created = "<?php __('plugin_invoice_i_created'); ?>";
	myLabel.status = "<?php __('plugin_invoice_i_status'); ?>";
	myLabel.total = "<?php __('plugin_invoice_i_total'); ?>";
	myLabel.delete_title = "<?php __('plugin_invoice_i_delete_title'); ?>";
	myLabel.delete_body = "<?php __('plugin_invoice_i_delete_body'); ?>";
	myLabel.paid = "<?php echo $statuses['paid']; ?>";
	myLabel.not_paid = "<?php echo $statuses['not_paid']; ?>";
	myLabel.cancelled = "<?php echo $statuses['cancelled']; ?>";
	myLabel.btnContinue = "<?php __('btnContinue'); ?>";
	myLabel.btnCancel = "<?php __('btnCancel'); ?>";
	myLabel.invoice_total = <?php echo isset($tpl['invoice_arr']) && count($tpl['invoice_arr']) === 1 ? (float) $tpl['invoice_arr'][0]['total'] : 0; ?>;
	myLabel.empty_date = "<?php __('gridEmptyDate'); ?>";
	myLabel.invalid_date = "<?php __('gridInvalidDate'); ?>";
	myLabel.empty_datetime = "<?php __('gridEmptyDatetime'); ?>";
	myLabel.invalid_datetime = "<?php __('gridInvalidDatetime'); ?>";
	</script>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jqDateFormat = "<?php echo pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
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