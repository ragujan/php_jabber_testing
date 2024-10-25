<br/>
<div class="container-fluid">
	<div class="panel panel-default pjCbMain">
		<div class="panel-heading tbPanelHeading pjCbHeading">
			<form action="#" method="post" class="form-horizontal pjCbForm pjCbFormDate">
				<div class="form-group clearfix">
                    <?php
                    include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/locale.php';
                    ?>
					<div class="pull-left pjCbWeekPanel">
						<?php
						if(date('Y-m-d', $tpl['from_ts']) > date('Y-m-d'))
						{ 
							?>
							<a href="#" class="pjCbDaysNav pjCbNavArrowLeft" data-from_date="<?php echo date($tpl['option_arr']['o_date_format'], $tpl['from_ts'] - (86400 * 7));?>" data-date="<?php echo date($tpl['option_arr']['o_date_format'], $tpl['from_ts'] - (86400 * 7));?>"><i class="fa fa-angle-double-left"></i></a>
							<?php
						}
						for($i = $tpl['from_ts']; $i < $tpl['end_ts']; $i+=86400)
						{
							$date_format = date($tpl['option_arr']['o_date_format'], $i);
							?><a href="#" class="pjCbDaysNav<?php echo date('Y-m-d', $i) == $tpl['hash_date'] ? ' active' : NULL;?>" data-from_date="<?php echo date($tpl['option_arr']['o_date_format'], $tpl['from_ts']);?>" data-date="<?php echo $date_format;?>"><?php echo $date_format;?></a><?php
						} 
						?>
						<a href="#" class="pjCbDaysNav pjCbNavArrowRight" data-from_date="<?php echo date($tpl['option_arr']['o_date_format'], $tpl['end_ts']);?>" data-date="<?php echo date($tpl['option_arr']['o_date_format'], $tpl['end_ts']);?>"><i class="fa fa-angle-double-right"></i></a>
					</div>
					<?php
					$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
					$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
					$months = __('months', true);
					$short_months = __('short_months', true);
					ksort($months);
					ksort($short_months);
					$days = __('days', true);
					$short_days = __('short_days', true);
					?>
					<div class="pull-left pjCbWeekPanelDatePicker">
						<label for="" class="control-label"><?php __('front_select_date')?>:</label>
						<div class="pjCbFormControls">
							<div class="input-group date">
								<input type="text" name="selected_date" readonly="readonly" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date'])); ?>" class="form-control tbSelectorDatepick" data-id="<?php echo $tpl['arr']['id'];?>" data-list="1" data-dformat="<?php echo $jqDateFormat; ?>" data-fday="<?php echo $week_start; ?>" data-months="<?php echo join(',', $months);?>" data-shortmonths="<?php echo join(',', $short_months);?>" data-day="<?php echo join(',', $days);?>" data-daymin="<?php echo join(',', $short_days);?>">
								<span class="input-group-addon tbSelectorDatepickIcon">
									<i class="fa fa-calendar"></i>
								</span>
							</div><!-- /.input-group date -->
						</div><!-- /.pjCbFormControls -->
					</div><!-- /.pull-left pjCbWeekPanelDatePicker -->
				</div><!-- /.form-group -->
			</form><!-- /.form-horizontal -->
		</div><!-- /.panel-heading tbPanelHeading pjCbHeading -->
		<div class="panel-body pjCbBody pjCbEvents">
			<?php
			
			if(count($tpl['arr']) > 0)
			{ 
				?>
				<div class="pjCbEventList">
					<?php
					foreach($tpl['arr'] as $v)
					{
						$src = 'https://placehold.it/220x320';
						if(!empty($v['event_img']) && is_file(PJ_INSTALL_PATH . $v['event_img']))
						{
							$src = PJ_INSTALL_URL . $v['event_img'];
						}
						?>
						<div class="pjCbEventRow">
							<div class="pjCbMovieIntroImage">
								<a href="#" class="tbMovieLink pjCbMovie" data-id="<?php echo $v['id'];?>" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date']));?>" data-from_date="<?php echo date($tpl['option_arr']['o_date_format'], $tpl['from_ts']);?>"><img src="<?php echo $src;?>" class="img-responsive" alt="" /></a>
							</div>
							<div class="pjCbMovieIntroContent">
								<div class="pjCbMovieTitle"><a href="#" class="tbMovieLink pjCbMovie" data-id="<?php echo $v['id'];?>" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date']));?>" data-from_date="<?php echo date($tpl['option_arr']['o_date_format'], $tpl['from_ts']);?>"><?php echo pjSanitize::html($v['title']);?></a></div>
								<div class="pjCbMovieDuration"><?php echo $v['duration'];?> <?php __('front_minutes');?></div>
								<div class="pjCbMovieDesc"><?php echo stripslashes(pjUtil::truncateDescription(pjUtil::html2txt($v['description']), 300, ' '));?></div>
								<div class="pjCbMovieTime">
									<label><?php __('front_select_time');?>:</label>
									<div class="pjCbMovieTimesWrapper">
										<?php
										if(isset($tpl['show_arr'][$v['id']]))
										{
											foreach($tpl['show_arr'][$v['id']] as $k => $time)
											{
												$date_time_iso = $tpl['hash_date'] . ' ' . $time . ':00';
												$date_time_ts = strtotime($tpl['hash_date'] . ' ' . $time . ':00');
												
												$show_time = date($tpl['option_arr']['o_time_format'], strtotime($date_time_iso));
												
												if($date_time_ts <= (strtotime(date('Y-m-d H:00')) + $tpl['option_arr']['o_booking_earlier'] * 60 ))
												{
													?>
													<a href="#" class="tbSelectorSeats pjCbTimePassed"><?php echo $show_time;?></a>
													<?php
												}else{
													?>
													<a href="#" class="tbSelectorSeats" data-id="<?php echo $v['id'];?>" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date']));?>" data-time="<?php echo $time; ?>" data-from_date="<?php echo date($tpl['option_arr']['o_date_format'], $tpl['from_ts']);?>"><?php echo $show_time;?></a>
													<?php
												}	
											}
										} 
										?>
									</div><!-- /.pjCbMovieTimesWrapper -->
								</div>
							</div>
						</div>
						<?php
					} 
					?>
				</div>
				<?php
			} else {
				__('front_no_events_found');
			}
			?>
		</div>
	</div>
</div>