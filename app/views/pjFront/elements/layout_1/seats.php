<?php
$STORE = @$_SESSION[$controller->defaultStore];
if($tpl['status'] == 'OK')
{
	?>
	<br/>
	<div class="container-fluid">
		<div class="panel panel-default pjCbMain">
			<div class="panel-heading clearfix pjCbHeading">
				<?php include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/locale.php';?>

				<a href="#" class="btn btn-link text-muted pjCbBtnBack<?php echo $STORE['back_to'] == 'details' ? ' tbBackToDetails' : ' tbBackToEvents';?>" data-id="<?php echo $tpl['arr']['id']; ?>" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date'])); ?>">
					<span class="text-muted">
						<i class="fa fa-arrow-left"></i>
						<?php __('front_back');?>
					</span>
				</a>
			</div><!-- /.panel-heading clearfix pjCbHeading -->
			
			<div class="panel-body pjCbBody pjCbSeats">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="well">
							<h1 class="text-center pjCbSeatsTitle"><?php echo pjSanitize::html($tpl['arr']['title']);?></h1>
							
							<div class="row">
								<p class="text-muted pjCbSeatsInfo text-right col-lg-6 col-md-6 col-sm-6 col-xs-12"><?php __('front_date')?>:</p>
																
								<p class="lead pjCbSeatsInfo col-lg-6 col-md-6 col-sm-6 col-xs-12"><strong><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['selected_date'])); ?></strong></p><!-- /.col-md-12 col-sm-12 col-xs-12 -->
							</div><!-- /.row -->

							<div class="row">								
								<p class="text-muted pjCbSeatsInfo text-right col-lg-6 col-md-6 col-sm-6 col-xs-12"><?php __('front_time')?>:</p>
																
								<p class="lead pjCbSeatsInfo col-lg-6 col-md-6 col-sm-6 col-xs-12"><strong><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['selected_date'] . ' ' . $STORE['selected_time'])); ?></strong></p><!-- /.col-md-12 col-sm-12 col-xs-12 -->
							</div><!-- /.row -->

							<div class="row">
								<p class="text-muted pjCbSeatsInfo text-right col-lg-6 col-md-6 col-sm-6 col-xs-12"><?php __('front_running_time')?>:</p>
																
								<p class="lead pjCbSeatsInfo col-lg-6 col-md-6 col-sm-6 col-xs-12"><strong><?php echo $tpl['arr']['duration']?> <?php __('front_minutes')?></strong></p><!-- /.col-md-12 col-sm-12 col-xs-12 -->
							</div><!-- /.row -->
							<?php
							if(count($tpl['hall_arr']) > 1)
							{ 
								?>
								<div class="row">
									<p class="text-muted pjCbSeatsInfo text-right col-lg-6 col-md-6 col-sm-6 col-xs-12"><?php __('front_hall');?>:</p>
																	
									<p class="lead pjCbSeatsInfo col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<select id="venue_id_<?php echo $_GET['index'];?>" name="venue_id" class="form-control pjCbSeatVenue">
											<?php
											foreach($tpl['hall_arr'] as $hall)
											{
												?><option value="<?php echo $hall['venue_id'];?>"<?php echo isset($STORE['venue_id']) ? ($STORE['venue_id'] == $hall['venue_id'] ? ' selected="selected"' : NULL) : NULL;?>><?php echo pjSanitize::html($hall['venue_name']);?></option><?php
											} 
											?>
										</select>
									</p><!-- /.col-md-12 col-sm-12 col-xs-12 -->
								</div><!-- /.row -->
								<?php
							} 
							?>
						</div><!-- /.well -->
					</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-12 -->

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="well">
							<div class="row">
								<div class="col-xs-12"><p class="text-muted"><?php __('front_available_seats');?>: <?php echo (int)@$tpl['total_remaining_avaliable_seats'];?></p></div>
								<?php
								$class = 'tbAssignedNoMap';
								if(isset($tpl['venue_arr']))
								{
								    if (!empty($tpl['venue_arr']['map_path']) && is_file($tpl['venue_arr']['map_path']))
									{
										$class = 'tbAssignedSeats';
									}
								} 
								$ticket_name_arr = array();
								$ticket_tooltip_arr = array();
								if(isset($tpl['ticket_arr']) && count($tpl['ticket_arr']) > 0)
								{
									foreach($tpl['ticket_arr'] as $v)
									{
										$ticket_name_arr[$v['price_id']] = pjSanitize::html($v['ticket']);
										$ticket_tooltip_arr[$v['price_id']] = pjSanitize::html($v['ticket']) . ', ' .  pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']);
									}
								}
								?>
								<div class="col-xs-12">
									<div class="tbAskToSelectTickets alert alert-info" role="alert" style="display: <?php echo isset($STORE['tickets']) ? 'none': 'block';?>"><?php $tpl['seats_available'] == true ? __('front_select_ticket_types_above') : __('front_no_seats_available');?></div>
									<div style="display: <?php echo isset($STORE['tickets']) ? 'block': 'none';?>">
										<div class="tbSelectSeatGuide alert alert-info" role="alert"></div>
										<label for="" class="tbSelectedSeatsLabel"><?php __('front_selected_seats');?>:</label>
										<?php
										if($class == 'tbAssignedSeats')
										{ 
											?>
											<div class="tbAskToSelectSeats pjCbSeatsMessage" style="display: <?php echo isset($STORE['seat_id']) ? 'none': 'block';?>"><?php __('front_select_available_seats');?></div>
											<?php
										} 
										?>
										<div id="tbSelectedSeats_<?php echo $_GET['index'];?>">
											<?php
											if(isset($STORE['seat_id']))
											{
												$seat_label_arr = $STORE['seat_id'];
												foreach($seat_label_arr as $price_id => $seat_arr)
												{
													foreach($seat_arr as $seat_id => $cnt)
													{
														for($i = 1; $i <= $cnt; $i++)
														{
															?><span class="<?php echo $class;?> tbAssignedSeats_<?php echo $price_id;?>" data_seat_id="<?php echo $seat_id;?>" data_price_id="<?php echo $price_id;?>"><?php echo $ticket_name_arr[$price_id]; ?> #<?php echo $tpl['seat_name_arr'][$seat_id];?></span><?php
														}	
													}
												}
											} 
											?>
										</div>
										<?php
										if($class == 'tbAssignedSeats')
										{ 
											?>
											<div class="tbTipToRemoveSeats pjCbSeatsMessage" style="display: <?php echo isset($STORE['seat_id']) ? 'block': 'none';?>"><?php __('front_how_to_remove_seats');?><br/></div>
											<?php
										} 
										?>
									</div>
								</div><!-- /.col-lg-8 col-md-7 col-sm-6 col-xs-12 -->
							</div><!-- /.row -->
							<div class="row">
								<?php
								if(isset($tpl['ticket_arr']) && count($tpl['ticket_arr']) > 0)
								{
									foreach($tpl['ticket_arr'] as $v)
									{
										if($v['cnt_tickets'] > 0 && $tpl['seats_available'] == true)
										{
											?>
											<div class="col-md-12 col-sm-12 col-xs-12">
												<p class="text-muted"><?php echo pjSanitize::html($v['ticket']);?>:</p>
												<div class="form-horizontal pjCbFormSeats">
													<div class="form-group">
														<div class="col-sm-6">
															<select id="tbTicket_<?php echo $v['price_id'];?>" name="tickets[<?php echo $v['id'];?>][<?php echo $v['price_id'];?>]" class="form-control tbTicketSelector" data-id="<?php echo $v['price_id'];?>" data-ticket="<?php echo pjSanitize::html($v['ticket']);?>" data-price="<?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']);?>">
																<?php
																for($i = 0; $i <= $v['cnt_tickets']; $i++)
																{
																	?><option value="<?php echo $i;?>"<?php echo isset($STORE['tickets'][$v['id']][$v['price_id']]) ? ($STORE['tickets'][$v['id']][$v['price_id']] == $i ? ' selected="selected"' : null) : null;?>><?php echo $i?></option><?php
																} 
																?>
															</select>
														</div><!-- /.col-sm-6 -->
														
														<label for="" class="control-label">x<?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']);?></label>
													</div><!-- /.form-group -->
												</div><!-- /.form-horizontal pjCbFormSeats -->
											</div>
											<?php
										}else{
											?>
											<div class="col-md-12 col-sm-12 col-xs-12">
												<p class="text-muted"><?php echo pjSanitize::html($v['ticket']);?>:</p>
												<p class="lead"><strong><?php __('front_na');?></strong></p>
											</div>
											<?php
										}
									}
								} 
								?>
							</div><!-- /.row -->
						</div><!-- /.well -->
					</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-12 -->
				</div><!-- /.row -->
				
				<div class="row">
					<div class="col-xs-12 tbGuideMessage" data-type=""></div>
				</div><!-- /.row -->
				<?php
				if(isset($tpl['venue_arr']))
				{
					$map = PJ_INSTALL_PATH . $tpl['venue_arr']['map_path'];
					if (is_file($map))
					{ 
						$size = getimagesize($map);
						?>
						<div class="row">
							<div class="col-xs-12">
								<div id="tbMapHolder_<?php echo $_GET['index'];?>" class="tbMapHolder pjCbCinema" style="height: <?php echo $size[1];?>px;">
									<div style="height: <?php echo $size[1];?>px;width:<?php echo $size[0];?>px;margin-left: 0px;margin:0 auto;position: relative;">
										<img id="tbMap_<?php echo $_GET['index'];?>" src="<?php echo PJ_INSTALL_URL . $tpl['venue_arr']['map_path']; ?>" alt="" style="margin: 0; border: none; position: absolute; top: 0; left: 0; z-index: 500;" />
										<?php
										foreach ($tpl['seat_arr'] as $seat)
										{
											$is_selected = false;
											$is_available = true;
											$_arr = explode("~:~", $seat['price_id']);
											$tooltip = array();
											foreach($_arr as $pid)
											{
												if(isset($STORE['seat_id'][$pid][$seat['id']]))
												{
													$is_selected = true;
													if($seat['seats'] == $STORE['seat_id'][$pid][$seat['id']])
													{
														$is_available = false;
													}
												}
												$tooltip[] = $ticket_tooltip_arr[$pid];
											}
											$avail_seats = $seat['seats'] - $seat['cnt_booked'];
											?><span class="tbSeatRect<?php echo $avail_seats <= 0 ? ' tbSeatBlocked' : ($is_available == true ? ' tbSeatAvailable' : null); ?><?php echo $is_selected == true ? ' tbSeatSelected' : null;?>" data-id="<?php echo $seat['id']; ?>" data-price-id="<?php echo $seat['price_id']; ?>" data-name="<?php echo $seat['name']; ?>" data-count="<?php echo $avail_seats; ?>" style="width: <?php echo $seat['width']; ?>px; height: <?php echo $seat['height']; ?>px; left: <?php echo $seat['left']; ?>px; top: <?php echo $seat['top']; ?>px; line-height: <?php echo $seat['height']; ?>px" data-pj-toggle="tooltip" data-placement="top" data-html="true" title="<?php echo join('<br/>', $tooltip);?>"><?php echo stripslashes($seat['name']); ?></span><?php
										}
										?>
									</div>
								</div>
							</div>
						</div><!-- /.row -->
						<?php
					}else{
						?>
						<div id="tbMapHolder_<?php echo $_GET['index'];?>">
							<?php
							foreach ($tpl['seat_arr'] as $seat)
							{
								$is_selected = false;
								$is_available = true;
								$_arr = explode("~:~", $seat['price_id']);
								foreach($_arr as $pid)
								{
									if(isset($STORE['seat_id'][$pid][$seat['id']]))
									{
										$is_selected = true;
										if($seat['seats'] == $STORE['seat_id'][$pid][$seat['id']])
										{
											$is_available = false;
										}
									}
								}
								?><span class="tbSeatRect<?php echo $seat['seats'] - $seat['cnt_booked'] <= 0 ? ' tbSeatBlocked' : ($is_available == true ? ' tbSeatAvailable' : null); ?><?php echo $is_selected == true ? ' tbSeatSelected' : null;?>" data-id="<?php echo $seat['id']; ?>" data-price-id="<?php echo $seat['price_id']; ?>" data-name="<?php echo $seat['name']; ?>" data-count="<?php echo $seat['seats']; ?>" style="display: none;"><?php echo stripslashes($seat['name']); ?></span><?php
							}
							?>
						</div>
						<?php
					}
				} 
				?>
				<div class="row">
					<?php
					if(isset($tpl['venue_arr']))
					{
					    if (!empty($tpl['venue_arr']['map_path']) && is_file($tpl['venue_arr']['map_path']))
						{
							?>
							<br/>
							<div class="col-xs-12 text-left">
								<ul class="list-inline pjCbSeatsColors">
									<li>
										<button class="btn btn-success btn-xs">&nbsp;&nbsp;&nbsp;&nbsp;</button>
										<label class="tbLegendLabel" for=""><?php __('front_available');?></label>
									</li>
			
									<li>
										<button class="btn btn-danger btn-xs" disabled="disabled">&nbsp;&nbsp;&nbsp;&nbsp;</button>
										<label class="tbLegendLabel" for=""><?php __('front_blocked');?></label>
									</li>
			
									<li>
										<button class="btn btn-primary btn-xs" disabled="disabled">&nbsp;&nbsp;&nbsp;&nbsp;</button>
										<label class="tbLegendLabel" for=""><?php __('front_selected');?></label>
									</li>
								</ul><!-- /.list-inline -->
							</div><!-- /.col-lg-4 col-md-5 col-sm-6 col-xs-12 -->
							<?php
						}
					} 
					?>
				</div><!-- /.row -->
				<br />
	
			</div><!-- /.panel-body pjCbBody -->
			<?php
			if($tpl['seats_available'] == true)
			{ 
				?>
				<div class="panel-footer text-center pjCbFoot">
					<form id="tbSeatsForm_<?php echo $_GET['index'];?>" action="#" method="post" class="form-inline" style="display: none;">
						<?php
						if(isset($STORE['seat_id']))
						{
							$seat_label_arr = $STORE['seat_id'];
							foreach($seat_label_arr as $price_id => $seat_arr)
							{
								foreach($seat_arr as $seat_id => $cnt)
								{
									?><input class="tbHiddenSeat_<?php echo $price_id;?>" type="hidden" name="seat_id[<?php echo $price_id;?>][<?php echo $seat_id;?>]" data_seat_id="<?php echo $seat_id;?>" value="<?php echo $cnt;?>"><?php
								}
							}
						} 
						?>
					</form>
					
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<br />
							<div class="col-xs-12 tbErrorMessage pjCbSeatsMessage"></div>
					
							<button class="btn btn-default pull-left tbSelectorButton pjCbBtnBack pjCbBtn pjCbBtnSecondary<?php echo $STORE['back_to'] == 'details' ? ' tbBackToDetails' : ' tbBackToEvents';?>" data-id="<?php echo $tpl['arr']['id']; ?>" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date'])); ?>"><?php __('front_button_cancel')?></button>
							
							<button class="btn btn-default pull-right tbSelectorButton tbContinueButton pjCbBtn pjCbBtnPrimary" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date'])); ?>"><?php __('front_button_continue')?></button>
		
						</div><!-- /.col-md-12 col-sm-12 col-xs-12 -->
					</div>
					
						
				</div><!-- /.panel-footer text-center pjCbFoot -->
				<?php
			} 
			?>
		</div><!-- panel panel-default pjCbMain -->
	</div><!-- /.container-fluid -->
	<?php
}else{
	?>
	<div class="container-fluid">
		<div class="panel panel-default pjCbMain">
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
			</div><!-- /.panel-footer text-center pjCbFoot -->
		</div>
	</div>
	<?php
}
?>