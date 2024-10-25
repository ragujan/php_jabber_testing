<?php
$STORE = @$_SESSION[$controller->defaultStore];
$FORM = @$_SESSION[$controller->defaultForm];
if($tpl['status'] == 'OK')
{
	$validate = str_replace(array('"', "'"), array('\"', "\'"), __('validate', true, true));
	?>
	<br/>
	<div class="container-fluid">
		<div class="panel panel-default pjCbMain">
			<div class="panel-heading clearfix pjCbHeading">
				<?php include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/locale.php';?>

				<a href="#" class="btn btn-link text-muted tbBackToSeats pjCbBtnBack" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date'])); ?>">
					<span class="text-muted">
						<i class="fa fa-arrow-left"></i>
						<?php __('front_back');?>
					</span>
				</a>
			</div><!-- /.panel-heading clearfix pjCbHeading -->
			
			<form id="frmCheckoutForm_<?php echo $_GET['index']?>" class="pjCbFormCheckOut" action="#" method="post">
				<input type="hidden" name="tb_checkout" value="1" />
				<div class="panel-body pjCbBody pjCbSeats">
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="well">
								<h1 class="pjCbSeatsTitle text-center"><?php echo pjSanitize::html($tpl['arr']['title']);?></h1>
								
								<div class="row">
									<p class="col-md-6 col-sm-6 col-xs-12 text-right text-muted pjCbSeatsInfo"><?php __('front_date')?>:</p>
																		
									<p class="col-md-6 col-sm-6 col-xs-12 lead pjCbSeatsInfo"><strong><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['selected_date'])); ?></strong></p>
									<p class="col-md-6 col-sm-6 col-xs-12 text-right text-muted pjCbSeatsInfo"><?php __('front_time')?>:</p>
																		
									<p class="col-md-6 col-sm-6 col-xs-12 lead pjCbSeatsInfo"><strong><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['selected_date'] . ' ' . $STORE['selected_time'])); ?></strong></p>
									
									<?php
									$total = 0;
									if(isset($tpl['ticket_arr']) && count($tpl['ticket_arr']) > 0)
									{
										foreach($tpl['ticket_arr'] as $v)
										{
											if(isset($STORE['tickets'][$v['id']][$v['price_id']]) && $STORE['tickets'][$v['id']][$v['price_id']] > 0)
											{
												?>
													<p class="col-md-6 col-sm-6 col-xs-12 text-right text-muted pjCbSeatsInfo"><?php echo pjSanitize::html($v['ticket']);?>:</p>
													<p class="col-md-6 col-sm-6 col-xs-12 lead pjCbSeatsInfo"><strong><?php echo $STORE['tickets'][$v['id']][$v['price_id']]; ?> x <?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']);?></strong></p>
												<?php
												$total += $STORE['tickets'][$v['id']][$v['price_id']] * $v['price'];
											}
										}
									} 
									?>
									<p class="col-md-6 col-sm-6 col-xs-12 text-right text-muted pjCbSeatsInfo"><?php __('front_selected_seats');?>:</p>
								
									<p class="col-md-6 col-sm-6 col-xs-12 lead pjCbSeatsInfo">
										<strong>
										<?php
										if(isset($STORE['seat_id']))
										{
											$seat_label_arr = $STORE['seat_id'];
											$_seat_arr = array();
											foreach($seat_label_arr as $price_id => $seat_arr)
											{
												foreach($seat_arr as $seat_id => $cnt)
												{
													if($cnt == 1)
													{
														$_seat_arr[] = $tpl['seat_name_arr'][$seat_id];
													}else{
														$_seat_arr[] = $tpl['seat_name_arr'][$seat_id] . "(".$cnt.")";
													}
												}
											}
											echo join(", ", $_seat_arr);
										} 
										?>
										</strong>
									</p>
								</div><!-- /.row -->
							</div><!-- /.well -->
						</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-12 -->

						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="well">
								<h2 class="pjCbSeatsTitle text-center"><?php __('front_payment_information');?></h2>
								<div class="row">
									<p class="col-md-6 col-sm-6 col-xs-12 text-right text-muted pjCbSeatsInfo"><?php __('front_sub_total');?>:</p>
									<p class="col-md-6 col-sm-6 col-xs-12 lead pjCbSeatsInfo"><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['sub_total'], 2), $tpl['option_arr']['o_currency']);?></strong></p>
								</div>
								<div class="row">
									<p class="col-md-6 col-sm-6 col-xs-12 text-right text-muted pjCbSeatsInfo"><?php __('front_tax');?>:</p>
									<p class="col-md-6 col-sm-6 col-xs-12 lead pjCbSeatsInfo"><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['tax'], 2), $tpl['option_arr']['o_currency']);?></strong></p>
								</div>
								<div class="row">
									<p class="col-md-6 col-sm-6 col-xs-12 text-right text-muted pjCbSeatsInfo"><?php __('front_total');?>:</p>
									<p class="col-md-6 col-sm-6 col-xs-12 lead pjCbSeatsInfo"><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['total'], 2), $tpl['option_arr']['o_currency']);?></strong></p>
								</div>
								<div class="row">
									<p class="col-md-6 col-sm-6 col-xs-12 text-right text-muted pjCbSeatsInfo"><?php __('front_deposit');?>:</p>
									<p class="col-md-6 col-sm-6 col-xs-12 lead pjCbSeatsInfo"><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['deposit'], 2), $tpl['option_arr']['o_currency']);?></strong></p>
								</div>
							</div><!-- /.well -->
						</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-12 -->
					</div><!-- /.row -->

					<h2 class="pjCbFormTitle"><?php __('front_fill_details');?></h2>
					<?php
					ob_start();
					$number_of_cols = 0;
					if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
					{
						?>
						<div class="col-md-6 col-sm-6 col-xs-12">
						  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_include_title'] === 3 ? ' required' : null;?>">
						    	<label for="" class="control-label"><?php __('front_customer_title'); ?></label>
						    	<select name="c_title" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_title'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $validate['title'];?>">
									<option value="">-- <?php __('front_dropdown_choose');?> --</option>
									<?php
									foreach(__('personal_titles', true) as $k => $v) 
									{
										?><option value="<?php echo $k;?>"<?php echo isset($FORM['c_title']) ? ($FORM['c_title'] == $k ? ' selected="selected"' : null) : null;?>><?php  echo $v;?></option><?php
									}
									?>
								</select>
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_name'], array(2,3)))
					{
						?>
						<div class="col-md-6 col-sm-6 col-xs-12">
						  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_include_name'] === 3 ? ' required' : null;?>">
						    	<label class="control-label"><?php __('front_customer_name'); ?></label>
						    	<input type="text" name="c_name" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_name'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_name', false, true); ?>" value="<?php echo pjSanitize::html(@$FORM['c_name']); ?>" data-msg-required="<?php echo $validate['name'];?>">
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						?>
						<div class="row"><?php echo $ob_fields; ?></div>
						<?php
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_email'], array(2,3)))
					{
						?>
						<div class="col-md-6 col-sm-6 col-xs-12">
						  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_include_email'] === 3 ? ' required' : null;?>">
						    	<label class="control-label"><?php __('front_customer_email'); ?></label>
						    	<input type="text" name="c_email" class="form-control email<?php echo (int) $tpl['option_arr']['o_bf_include_email'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_email', false, true); ?>" value="<?php echo pjSanitize::html(@$FORM['c_email']); ?>" data-msg-required="<?php echo $validate['email'];?>" data-msg-email="<?php echo $validate['email_invalid'];?>">
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						?>
						<div class="row"><?php echo $ob_fields; ?></div>
						<?php
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_phone'], array(2,3)))
					{
						?>
						<div class="col-md-6 col-sm-6 col-xs-12">
						  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_include_phone'] === 3 ? ' required' : null;?>">
						    	<label class="control-label"><?php __('front_customer_phone'); ?></label>
						    	<input type="text" name="c_phone" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_phone'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_phone', false, true); ?>" value="<?php echo pjSanitize::html(@$FORM['c_phone']); ?>" data-msg-required="<?php echo $validate['phone'];?>">
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						?>
						<div class="row"><?php echo $ob_fields; ?></div>
						<?php
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_company'], array(2,3)))
					{
						?>
						<div class="col-md-6 col-sm-6 col-xs-12">
						  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_include_company'] === 3 ? ' required' : null;?>">
						    	<label class="control-label"><?php __('front_customer_company'); ?></label>
						    	<input type="text" name="c_company" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_company'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_company', false, true); ?>" value="<?php echo pjSanitize::html(@$FORM['c_company']); ?>" data-msg-required="<?php echo $validate['company'];?>">
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						?>
						<div class="row"><?php echo $ob_fields; ?></div>
						<?php
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_address'], array(2,3)))
					{
						?>
						<div class="col-md-6 col-sm-6 col-xs-12">
						  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_include_address'] === 3 ? ' required' : null;?>">
						    	<label class="control-label"><?php __('front_customer_address'); ?></label>
						    	<input type="text" name="c_address" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_address'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_address', false, true); ?>" value="<?php echo pjSanitize::html(@$FORM['c_address']); ?>" data-msg-required="<?php echo $validate['address'];?>">
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						?>
						<div class="row"><?php echo $ob_fields; ?></div>
						<?php
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_country'], array(2,3)))
					{
						?>
						<div class="col-md-6 col-sm-6 col-xs-12">
						  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_include_country'] === 3 ? ' required' : null;?>">
						    	<label class="control-label"><?php __('front_customer_country'); ?></label>
						    	<select name="c_country" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_country'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $validate['country'];?>">
									<option value="">-- <?php __('front_dropdown_choose');?> --</option>
									<?php
									foreach($tpl['country_arr'] as $k => $v) 
									{
										?><option value="<?php echo $v['id'];?>"<?php echo isset($FORM['c_country']) ? ($FORM['c_country'] == $v['id'] ? ' selected="selected"' : null) : null;?>><?php  echo $v['name'];?></option><?php
									}
									?>
								</select>
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						?>
						<div class="row"><?php echo $ob_fields; ?></div>
						<?php
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_state'], array(2,3)))
					{
						?>
						<div class="col-md-6 col-sm-6 col-xs-12">
						  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_include_state'] === 3 ? ' required' : null;?>">
						    	<label class="control-label"><?php __('front_customer_state'); ?></label>
						    	<input type="text" name="c_state" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_state'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_state', false, true); ?>" value="<?php echo pjSanitize::html(@$FORM['c_state']); ?>" data-msg-required="<?php echo $validate['state'];?>">
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						?>
						<div class="row"><?php echo $ob_fields; ?></div>
						<?php
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_city'], array(2,3)))
					{
						?>
						<div class="col-md-6 col-sm-6 col-xs-12">
						  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_include_city'] === 3 ? ' required' : null;?>">
						    	<label class="control-label"><?php __('front_customer_city'); ?></label>
						    	<input type="text" name="c_city" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_city'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_city', false, true); ?>" value="<?php echo pjSanitize::html(@$FORM['c_city']); ?>" data-msg-required="<?php echo $validate['city'];?>">
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						?>
						<div class="row"><?php echo $ob_fields; ?></div>
						<?php
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_zip'], array(2,3)))
					{
						?>
						<div class="col-md-6 col-sm-6 col-xs-12">
						  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_include_zip'] === 3 ? ' required' : null;?>">
						    	<label class="control-label"><?php __('front_customer_zip'); ?></label>
						    	<input type="text" name="c_zip" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_zip'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_zip', false, true); ?>" value="<?php echo pjSanitize::html(@$FORM['c_zip']); ?>" data-msg-required="<?php echo $validate['zip'];?>">
						  	</div>
						</div>
						<?php
						$number_of_cols++;
					}
					if($number_of_cols == 2 || $number_of_cols == 1)
					{
						$ob_fields = ob_get_contents();
						ob_end_clean();
						?>
						<div class="row"><?php echo $ob_fields; ?></div>
						<?php
						ob_start();
						$number_of_cols = 0;
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_notes'], array(2,3)))
					{
						?>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_include_notes'] === 3 ? ' required' : null;?>">
							    	<label class="control-label"><?php __('front_customer_notes'); ?></label>
							    	<textarea name="c_notes" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_notes'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_notes', false, true); ?>" rows="6" data-msg-required="<?php echo $validate['notes'];?>"><?php echo pjSanitize::html(@$FORM['c_notes']); ?></textarea>
							  	</div>
							</div>
						</div>
						<?php
					}
					if ($tpl['option_arr']['o_payment_disable'] == 'No')
					{
						?>
						
						<div class="row">
							<div class="col-sm-6">
							  	<div class="form-group required">
							    	<label class="control-label"><?php __('front_payment_method'); ?></label>
							    	<select name="payment_method" class="form-control required" data-msg-required="<?php echo $validate['payment_method'];?>">
							    		<option value=""><?php __('front_dropdown_choose'); ?></option>
							    		<?php
										foreach (__('payment_methods', true) as $k => $v)
										{
											if ($tpl['option_arr']['o_allow_'.$k] == 'No')
											{
												continue;
											}
											?><option value="<?php echo $k; ?>"<?php echo @$FORM['payment_method'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
										}
										?>
							    	</select>
							  	</div>
							</div>
							<div class="col-sm-6 tbBankWrap" style="display: <?php echo @$FORM['payment_method'] != 'bank' ? 'none' : NULL; ?>">
								<div class="form-group">
							    	<label><?php __('front_bank_account'); ?></label>
							    	<div class="text-muted"><?php echo stripslashes(nl2br($tpl['option_arr']['o_bank_account'])); ?></div>
							  	</div>
							</div>
						</div>
						<div class="row tbCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
							<div class="col-sm-6">
							  	<div class="form-group required">
							    	<label class="control-label"><?php __('front_cc_type'); ?></label>
							    	<select name="cc_type" class="form-control required" data-msg-required="<?php echo $validate['cc_type'];?>">
							    		<option value="">---</option>
							    		<?php
										foreach (__('cc_types', true) as $k => $v)
										{
											?><option value="<?php echo $k; ?>"<?php echo @$FORM['cc_type'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
										}
										?>
							    	</select>
							  	</div>
							</div>
							<div class="col-sm-6">
							  	<div class="form-group required">
							    	<label class="control-label"><?php __('front_cc_num'); ?></label>
							    	<input type="text" name="cc_num" class="form-control required" value="<?php echo pjSanitize::html(@$FORM['cc_num']); ?>"  autocomplete="off" placeholder="<?php __('front_placeholder_cc_number', false, true); ?>" data-msg-required="<?php echo $validate['cc_number'];?>"/>
							  	</div>
							</div>
						</div>
						<div class="row tbCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
							<div class="col-sm-6">
							  	<div class="form-group required">
							    	<label class="control-label"><?php __('front_cc_sec'); ?></label>
							    	<input type="text" name="cc_code" class="form-control required" value="<?php echo pjSanitize::html(@$FORM['cc_code']); ?>"  autocomplete="off" placeholder="<?php __('front_placeholder_cc_code', false, true); ?>" data-msg-required="<?php echo $validate['cc_code'];?>"/>
							  	</div>
							</div>
							<div class="col-sm-3">
							  	<div class="form-group required">
							    	<label class="control-label"><?php __('front_cc_exp'); ?></label>
							    	<?php
									$rand = rand(1, 99999);
									$time = pjTime::factory()
										->attr('name', 'cc_exp_month')
										->attr('id', 'cc_exp_month_' . $rand)
										->attr('class', 'form-control required')
										->prop('format', 'F');
									if (isset($FORM['cc_exp_month']) && !is_null($FORM['cc_exp_month']))
									{
										$time->prop('selected', $FORM['cc_exp_month']);
									}
									echo $time->month();
									?>
							  	</div>
							</div>
							<div class="col-sm-3">
							  	<div class="form-group">
							    	<label>&nbsp;</label>
							    	<?php
									$time = pjTime::factory()
										->attr('name', 'cc_exp_year')
										->attr('id', 'cc_exp_year_' . $rand)
										->attr('class', 'form-control required')
										->prop('left', 0)
										->prop('right', 10);
									if (isset($FORM['cc_exp_year']) && !is_null($FORM['cc_exp_year']))
									{
										$time->prop('selected', $FORM['cc_exp_year']);
									}
									echo $time->year();
									?>
							  	</div>
							</div>
						</div>
						<?php
					}
										
					if (in_array((int) $tpl['option_arr']['o_bf_include_captcha'], array(3)))
					{
						?>
						<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_include_captcha'] === 3 ? ' required' : null;?>">
						  	<label class="control-label"><?php __('front_captcha'); ?></label>
							<div class="row">
							  	<div class="col-xs-6 col-md-3">
							    	<input type="text" id="pjCbsCaptchaField" name="captcha" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_captcha'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $validate['captcha'];?>" data-msg-remote="<?php echo $validate['captcha_wrong'];?>" maxlength="6" autocomplete="off">
							  	</div>
							  	<div class="col-xs-6">
							    	<img id="pjCbsCaptchaImage" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?><?php echo isset($_GET['session_id']) ? '&session_id=' . $_GET['session_id'] : NULL;?>" alt="Captcha" style="vertical-align: middle;cursor: pointer;" />
							  	</div>
							</div>
						</div>
						<?php
					}
					
					?>
					<div class="checkbox form-group required">
					    <label style="display: block;">
					      	<input type="checkbox" name="terms" value="1" class="required" data-msg-required="<?php echo $validate['terms'];?>">
					      	<a href="#" class="pjCbModalTrigger" data-pj-toggle="modal" data-pj-target="#scTermModal" data-title="<?php __('front_terms_title', false, true); ?>"><?php __('front_terms'); ?></a>
					    </label>
					</div>
					
					<div class="alert tdSelectorNoticeMsg" role="alert" style="display:none;"></div>
					
				</div><!-- /.panel-body pjCbBody -->
				<div class="panel-footer text-center pjCbFoot">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<br />
							
							<button type="submit" class="btn btn-default pull-right tbSelectorButton l pjCbBtn pjCbBtnPrimary"><?php __('front_button_confirm_booking')?></button>
		
							<button class="btn btn-default pull-left tbSelectorButton tbCancelToSeats pjCbBtn pjCbBtnSecondary" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date'])); ?>"><?php __('front_button_cancel')?></button>
						</div><!-- /.col-md-12 col-sm-12 col-xs-12 -->
					</div>
				</div><!-- /.panel-footer text-center pjCbFoot -->
			</form>
		</div><!-- panel panel-default pjCbMain -->
	</div><!-- /.container-fluid -->
	
	<?php
}else{
	?>
	<br/>
	<div class="container-fluid">
		<div class="panel panel-default pjCbMain">
			<div class="panel-heading clearfix">
				<?php include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/locale.php';?>
			</div><!-- /.panel-heading -->
			<div class="panel-body pjCbBody">
				<p class="text-warning"><?php __('front_start_over_message');?></p>
			</div>
			<div class="panel-footer text-center pjCbFoot">
				<div class="row">
					<div class="col-md-7 col-sm-12 col-xs-12">&nbsp;</div>
					<div class="col-md-5 col-sm-12 col-xs-12 text-right">
						<br />
						<button class="btn btn-default tbSelectorButton tbStartOverButton pjCbBtn pjCbBtnSecondary"><?php __('front_button_start_over')?></button>
					</div><!-- /.col-md-5 col-sm-12 col-xs-12 -->
				</div>
			</div>
		</div>
	</div>
	<?php
}
?>