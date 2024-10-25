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
}else{
	?>
	<div class="dashboard_header">
		<div class="item">
			<div class="stat movies">
				<div class="info">
					<abbr><?php echo $tpl['cnt_movies_today']?></abbr>
					<?php $tpl['cnt_movies_today'] != 1 ? __('lblMoviesShowingToday') : __('lblMovieShowingToday');?>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat bookings">
				<div class="info">
					<abbr><?php echo $tpl['cnt_bookings_today']?></abbr>
					<?php $tpl['cnt_bookings_today'] != 1 ? __('lblBookingsMadeToday') : __('lblBookingMadeToday');?>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat halls">
				<div class="info">
					<abbr><?php echo $tpl['cnt_halls'];?></abbr>
					<?php $tpl['cnt_halls'] != 1 ? __('lblDashboardHalls') : __('lblDashboardHall');?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="dashboard_box">
		<div class="dashboard_top">
			<div class="dashboard_column_top"><?php __('lblNextMovies')?></div>
			<div class="dashboard_column_top"><?php __('lblLatestBookings')?></div>
			<div class="dashboard_column_top"><?php __('lblNowShowing')?></div>
		</div>
		<div class="dashboard_middle">
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					if(count($tpl['next_movies']) > 0)
					{
						foreach($tpl['next_movies'] as $v)
						{
							?>
							<div class="dashboard_row">							
								<label><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionUpdate&amp;id=<?php echo $v['event_id'];?>"><?php echo pjSanitize::html($v['title']);?></a></label>
								<label><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['date_time'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['date_time']));?></label>
								<label><?php echo (!empty($v['cnt_tickets']) ? $v['cnt_tickets'] : 0) . ' ' . ($v['cnt_tickets'] != 1 ? __('lblPluralTickets', true) : __('lblSingularTicket', true)); ?></label>
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row"><label><?php __('lblMoviesNotFound');?></label></div>
						<?php
					} 
					?>
				</div>
			</div>
			
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					if(count($tpl['latest_bookings']) > 0)
					{
						foreach($tpl['latest_bookings'] as $v)
						{
							?>
							<div class="dashboard_row">							
								<label><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id'];?>"><?php echo pjSanitize::html($v['c_name']);?></a></label>
								<label><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['created'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['created']));?></label>
								<label><?php echo (!empty($v['cnt_tickets']) ? $v['cnt_tickets'] : 0) . ' ' . ($v['cnt_tickets'] != 1 ? __('lblPluralTickets', true) : __('lblSingularTicket', true)); ?></label>
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row"><label><?php __('lblBookingsNotFound');?></label></div>
						<?php
					} 
					?>
				</div>
			</div>
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					if(count($tpl['now_showing']) > 0)
					{
						foreach($tpl['now_showing'] as $v)
						{
							?>
							<div class="dashboard_row">	
								<label><?php echo pjSanitize::html($v['hall']);?></label>						
								<label><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionUpdate&amp;id=<?php echo $v['event_id'];?>"><?php echo pjSanitize::html($v['title']);?></a></label>
								<label><?php echo (!empty($v['cnt_people']) ? $v['cnt_people'] : 0) . ' ' . ($v['cnt_people'] != 1 ? __('lblPeopleWatching', true) : __('lblPersonWatching', true)); ?></label>
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row"><label><?php __('lblShowNotFound');?></label></div>
						<?php
					} 
					?>
				</div>
			</div>
		</div>
		<div class="dashboard_bottom"></div>
	</div>
	
	<div class="clear_left t20 overflow">
		<div class="float_left black t30 t20"><span class="gray"><?php echo ucfirst(__('lblDashLastLogin', true)); ?>:</span> <?php echo pjUtil::formatDate(date('Y-m-d', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ', ' . pjUtil::formatTime(date('H:i:s', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'H:i:s', $tpl['option_arr']['o_time_format']); ?></div>
		<div class="float_right overflow">
		<?php
		$days = __('days', true, false);
		?>
			<div class="dashboard_date">
				<abbr><?php echo $days[date('w')]; ?></abbr>
				<?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>
			</div>
			<div class="dashboard_hour"><?php echo date($tpl['option_arr']['o_time_format']); ?></div>
		</div>
	</div>
	<?php
}
?>