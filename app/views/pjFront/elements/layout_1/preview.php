<?php
$STORE = @$_SESSION[$controller->defaultStore];
$FORM = @$_SESSION[$controller->defaultForm];

if($tpl['status'] == 'OK')
{
	?>
	<br/>
	<div class="container-fluid">
		<div class="panel panel-default pjCbMain">
			<div class="panel-heading clearfix pjCbHeading">
				<?php include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/locale.php';?>
				
				<a href="#" class="btn btn-link text-muted tbBackToCheckout pjCbBtnBack" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date'])); ?>">
					<span class="text-muted">
						<i class="fa fa-arrow-left"></i>
						<?php __('front_back');?>
					</span>
				</a>
			</div><!-- /.panel-heading clearfix pjCbHeading -->
			
			<form id="frmPreviewForm_<?php echo $_GET['index']?>" action="#" method="post">
				<input type="hidden" name="tb_preview" value="1" />
				<div class="panel-body pjCbBody pjCbPayment">
					<h2 class="text-center pjCbPaymentTitle"><?php echo pjSanitize::html($tpl['arr']['title']);?></h2>
					
					<div class="form-horizontal">
						<div class="form-group">
							<label class="text-muted text-right col-lg-6 col-md-6 col-sm-6 col-xs-12"><?php __('front_date')?>:</label>
												
							<label class="col-lg-6 col-md-6 col-sm-6 col-xs-12"><strong><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['selected_date'])); ?></strong></label>
						</div><!-- /.form-group -->
					</div><!-- /.form-horizontal -->
					<div class="form-horizontal">
						<div class="form-group">
							<label class="text-muted text-right col-lg-6 col-md-6 col-sm-6 col-xs-12"><?php __('front_time')?>:</label>
												
							<label class="col-lg-6 col-md-6 col-sm-6 col-xs-12"><strong><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['selected_date'] . ' ' . $STORE['selected_time'])); ?></strong></label>
						</div><!-- /.form-group -->
					</div><!-- /.form-horizontal -->
					
					<?php
					$total = 0;
					if(isset($tpl['ticket_arr']) && count($tpl['ticket_arr']) > 0)
					{
						foreach($tpl['ticket_arr'] as $v)
						{
							if(isset($STORE['tickets'][$v['id']][$v['price_id']]) && $STORE['tickets'][$v['id']][$v['price_id']] > 0)
							{
								?>
								<div class="form-horizontal">
									<div class="form-group">
										<label class="text-muted text-right col-lg-6 col-md-6 col-sm-6 col-xs-12"><?php echo pjSanitize::html($v['ticket']);?>:</label>
										<label class="col-lg-6 col-md-6 col-sm-6 col-xs-12"><strong><?php echo $STORE['tickets'][$v['id']][$v['price_id']]; ?> x <?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']);?></strong></label>
									</div><!-- /.form-group -->
								</div><!-- /.form-horizontal -->
								<?php
								$total += $STORE['tickets'][$v['id']][$v['price_id']] * $v['price'];
							}
						}
					} 
					?>
					<div class="form-horizontal">
						<div class="form-group">
							<label class="text-muted text-right col-lg-6 col-md-6 col-sm-6 col-xs-12"><?php __('front_selected_seats');?>:</label>
													
							<label class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
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
							</label>
						</div><!-- /.form-group -->
					</div><!-- /.form-horizontal -->

					<h2 class="text-center pjCbPaymentTitle"><?php __('front_payment_information');?></h2>
					<div class="form-horizontal">
						<div class="form-group">
						    <label class="col-sm-6 text-muted text-right"><?php __('front_sub_total');?></label>
						    <label class="col-sm-6 text-left"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['sub_total'], 2), $tpl['option_arr']['o_currency']);?></label>
					  	</div>
					</div>
					<div class="form-horizontal">
						<div class="form-group">
						    <label class="col-sm-6 text-muted text-right"><?php __('front_tax');?></label>
						    <label class="col-sm-6 text-left"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['tax'], 2), $tpl['option_arr']['o_currency']);?></label>
					  	</div>
					</div>
					<div class="form-horizontal">
						<div class="form-group">
						    <label class="col-sm-6 text-muted text-right"><?php __('front_total');?></label>
						    <label class="col-sm-6 text-left"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['total'], 2), $tpl['option_arr']['o_currency']);?></label>
					  	</div>
					</div>
					<div class="form-horizontal">
						<div class="form-group">
						    <label class="col-sm-6 text-muted text-right"><?php __('front_deposit');?></label>
						    <label class="col-sm-6 text-left"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['deposit'], 2), $tpl['option_arr']['o_currency']);?></label>
					  	</div>
					</div>
					<hr class="divider">
					<h2 class="text-center pjCbPaymentTitle"><?php __('front_your_details');?></h2>
					<div class="row pjCbPaymentDetails">
						<?php
							if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
							{
								$personal_titles = __('personal_titles', true);
								?>
								<div class="col-md-6 col-sm-6 col-xs-12">
								  	<div class="form-group">
								    	<label for="" class="control-label"><?php __('front_customer_title'); ?></label><br/>
								    	<span class="text-muted"><?php echo isset($FORM['c_title']) && !empty($FORM['c_title']) ? $personal_titles[$FORM['c_title']] : null;?></span>
								  	</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_name'], array(2,3)))
							{
								?>
								<div class="col-md-6 col-sm-6 col-xs-12">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('front_customer_name'); ?></label><br/>
								    	<span class="text-muted"><?php echo pjSanitize::html(@$FORM['c_name']); ?></span>
								  	</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_email'], array(2,3)))
							{
								?>
								<div class="col-md-6 col-sm-6 col-xs-12">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('front_customer_email'); ?></label><br/>
								    	<span class="text-muted"><?php echo pjSanitize::html(@$FORM['c_email']); ?></span>
								  	</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_phone'], array(2,3)))
							{
								?>
								<div class="col-md-6 col-sm-6 col-xs-12">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('front_customer_phone'); ?></label><br/>
								    	<span class="text-muted"><?php echo pjSanitize::html(@$FORM['c_phone']); ?></span>
								  	</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_company'], array(2,3)))
							{
								?>
								<div class="col-md-6 col-sm-6 col-xs-12">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('front_customer_company'); ?></label><br/>
								    	<span class="text-muted"><?php echo pjSanitize::html(@$FORM['c_company']); ?></span>
								  	</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_address'], array(2,3)))
							{
								?>
								<div class="col-md-6 col-sm-6 col-xs-12">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('front_customer_address'); ?></label><br/>
								    	<span class="text-muted"><?php echo pjSanitize::html(@$FORM['c_address']); ?></span>
								  	</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_country'], array(2,3)))
							{
								?>
								<div class="col-md-6 col-sm-6 col-xs-12">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('front_customer_country'); ?></label><br/>
								    	<?php
								    	if(!empty($tpl['country_arr']))
									    { 
									    	?>
									    	<span class="text-muted"><?php echo pjSanitize::html($tpl['country_arr']['country_title']); ?></span>
									    	<?php
									    } 
									    ?>
								  	</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_state'], array(2,3)))
							{
								?>
								<div class="col-md-6 col-sm-6 col-xs-12">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('front_customer_state'); ?></label><br/>
								    	<span class="text-muted"><?php echo pjSanitize::html(@$FORM['c_state']); ?></span>
								  	</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_city'], array(2,3)))
							{
								?>
								<div class="col-md-6 col-sm-6 col-xs-12">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('front_customer_city'); ?></label><br/>
								    	<span class="text-muted"><?php echo pjSanitize::html(@$FORM['c_city']); ?></span>
								  	</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_zip'], array(2,3)))
							{
								?>
								<div class="col-md-6 col-sm-6 col-xs-12">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('front_customer_zip'); ?></label><br/>
								    	<span class="text-muted"><?php echo pjSanitize::html(@$FORM['c_zip']); ?></span>
								  	</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_notes'], array(2,3)))
							{
								?>
								<div class="row">
									<div class="col-sm-12 text-center">
										<div class="form-group">
									    	<label class="control-label"><?php __('front_customer_notes'); ?></label><br/>
									    	<span class="text-muted"><?php echo pjSanitize::html(nl2br(@$FORM['c_notes'])); ?></span>
									  	</div>
									</div>
								</div>
								<?php
							}
						?>
					</div><!-- /.row pjCbPaymentDetails -->

					<?php 
					if ($tpl['option_arr']['o_payment_disable'] == 'No')
					{
						$payment_methods = __('payment_methods', true);
						$cc_types = __('cc_types', true);
						?>
						<div class="row">
							<div class="col-sm-12 text-center">
							  	<div class="form-group">
							    	<label class="control-label"><?php __('front_payment_method'); ?></label><br/>
							    	<span class="text-muted"><?php echo @$payment_methods[$FORM['payment_method']]; ?></span>
							  	</div>
							</div>
							<div class="col-sm-12 text-center tbBankWrap" style="display: <?php echo @$FORM['payment_method'] != 'bank' ? 'none' : NULL; ?>">
								<div class="form-group">
							    	<label><?php __('front_bank_account'); ?></label><br/>
							    	<div class="text-muted"><?php echo stripslashes(nl2br($tpl['option_arr']['o_bank_account'])); ?></div>
							  	</div>
							</div>
						</div>
						<div class="row tbCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
							<div class="col-sm-6 text-right">
							  	<div class="form-group">
							    	<label class="control-label"><?php __('front_cc_type'); ?></label><br/>
							    	<span class="text-muted"><?php echo @$cc_types[@$FORM['cc_type']]; ?></span>
							  	</div>
							</div>
							<div class="col-sm-6">
							  	<div class="form-group">
							    	<label class="control-label"><?php __('front_cc_num'); ?></label><br/>
							    	<span class="text-muted"><?php echo pjSanitize::html(@$FORM['cc_num']); ?></span>
							  	</div>
							</div>
						</div>
						<div class="row tbCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
							<div class="col-sm-6 text-right">
							  	<div class="form-group">
							    	<label class="control-label"><?php __('front_cc_sec'); ?></label><br/>
							    	<span class="text-muted"><?php echo pjSanitize::html(@$FORM['cc_code']); ?></span>
							  	</div>
							</div>
							<div class="col-sm-6">
							  	<div class="form-group">
							    	<label class="control-label"><?php __('front_cc_exp'); ?></label><br/>
							    	<span class="text-muted"><?php printf("%s/%s", @$FORM['cc_exp_month'], @$FORM['cc_exp_year']); ?></span>
							  	</div>
							</div>
						</div>
						<?php
					}
					?>
					
					<div class="alert tdSelectorNoticeMsg" role="alert" style="display:none;"></div>
					
				</div><!-- /.panel-body pjCbBody -->
				<div class="panel-footer text-center pjCbFoot">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<br />
							
							<button type="submit" class="btn btn-default pull-right tbSelectorButton pjCbBtn pjCbBtnPrimary"><?php __('front_button_confirm_booking')?></button>
		
							<button class="btn btn-default pull-left tbSelectorButton tbCancelToCheckout pjCbBtn pjCbBtnSecondary" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date'])); ?>"><?php __('front_button_cancel')?></button>
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
			<div class="panel-heading clearfix pjCbHeading">
				<?php include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/locale.php';?>
			</div><!-- /.panel-heading clearfix pjCbHeading -->
			<div class="panel-body pjCbBody">
				<p class="text-warning text-center pjCbMessage"><?php __('front_start_over_message');?></p>
			</div>
			<div class="panel-footer text-center pjCbFoot">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12 text-center">
						<br />
						<button class="btn btn-default tbSelectorButton tbStartOverButton pjCbBtn pjCbBtnSecondary"><?php __('front_button_start_over')?></button>
					</div><!-- /.col-md-12 col-sm-12 col-xs-12 -->
				</div>
			</div>
		</div>
	</div>
	<?php
}
?>