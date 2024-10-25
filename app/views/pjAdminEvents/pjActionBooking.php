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
	
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionIndex"><?php __('menuEvents'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionExport"><?php __('lblExport'); ?></a></li>
		</ul>
	</div>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblDetails'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionShow&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblShows'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionBooking&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblBookings'); ?></a></li>
		</ul>
	</div>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionBooking" method="get" id="frmEventBooking" class="pj-form form">
		<input type="hidden" name="controller" value="pjAdminEvents" />
		<input type="hidden" name="action" value="pjActionBooking" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']?>" />
		<?php
		pjUtil::printNotice(__('infoEventBookingsTitle', true), __('infoEventBookingsDesc', true)); 
		?>
		<p>
			<label class="title"><?php __('lblDate'); ?></label>
			<span class="inline_block">
				<select id="date" name="date" class="pj-form-field w200">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
				 	foreach($tpl['date_arr'] as $v)
				 	{
				 		?><option value="<?php echo $v['date']?>"<?php echo isset($_GET['date']) && $_GET['date'] != '' ? ($_GET['date'] == $v['date'] ? ' selected="selected"' : null) : null;?>><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['date']));?></option><?php
				 	}
					?>
				</select>
			</span>
		</p>
		<?php
		if(isset($_GET['date']) && $_GET['date'] != '')
		{
			?>
			<p>
				<label class="title"><?php __('lblShowtime'); ?></label>
				<span class="inline_block">
					<select id="time" name="time" class="pj-form-field w200">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
					 	foreach($tpl['time_arr'] as $v)
					 	{
					 		?><option value="<?php echo $v['time']?>"<?php echo isset($_GET['time']) && $_GET['time'] != '' ? ($_GET['time'] == $v['time'] ? ' selected="selected"' : null) : null;?>><?php echo date($tpl['option_arr']['o_time_format'], strtotime($v['time']));?></option><?php
					 	}
						?>
					</select>
				</span>
			</p>
			<?php
		} 
		?>
		<div class="float_left w350">
			<p>
				<label class="title"><?php __('lblEvent'); ?></label>
				<span class="inline_block">
					<label class="content"><?php echo pjSanitize::html($tpl['arr']['title']);?></label>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblDuration'); ?></label>
				<span class="inline_block">
					<label class="content"><?php echo pjSanitize::html($tpl['arr']['duration']);?> <?php __('lblMinutes')?></label>
				</span>
			</p>
		</div>
		<div class="float_left w350">
			<p>
				<label class="title"><?php __('lblTotalBookings'); ?></label>
				<span class="inline_block">
					<label class="content"><?php echo $tpl['cnt_bookings'];?></label>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblTotalSeats'); ?></label>
				<span class="inline_block">
					<label class="content"><?php echo $tpl['total_seats'];?></label>
				</span>
			</p>
		</div>
		<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
			<thead>
				<tr>
					<th><?php __('lblID'); ?></th>
					<th><?php __('lblTickets'); ?></th>
					<th><?php isset($_GET['date']) && $_GET['date'] != '' ? __('lblHour') : __('lblDateTime') ; ?></th>
					<th><?php __('lblName'); ?></th>
					<th><?php __('lblEmail'); ?></th>
					<th><?php __('lblStatus'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$booking_statuses = __('booking_statuses', true);
				if(!empty($tpl['booking_arr']))
				{ 
					foreach($tpl['booking_arr'] as $v)
					{
						?>
						<tr>
							<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id'];?>"><?php echo $v['uuid']; ?></a></td>
							<td><?php echo $v['tickets'];?></td>
							<?php
							if(isset($_GET['date'])  && $_GET['date'] != '')
							{
								?><td><?php echo date($tpl['option_arr']['o_time_format'], strtotime($v['date_time']));?></td><?php
							}else{
								?><td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['date_time'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['date_time']));?></td><?php
							} 
							?>
							<td><?php echo pjSanitize::html($v['c_name']);?></td>
							<td><?php echo pjSanitize::html($v['c_email']);?></td>
							<td><?php echo $booking_statuses[$v['status']];?></td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<td colspan="6"><?php __('lblBookingsNotFound');?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<br/>
		<?php
		if(!empty($tpl['booking_arr']))
		{ 
			?>
			<p>
				<label class="title">&nbsp;</label>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionPrint&amp;id=<?php echo $tpl['arr']['id'];?>&date=<?php echo isset($_GET['date']) && $_GET['date'] != '' ? $_GET['date'] : null;?>&time=<?php echo isset($_GET['time']) && $_GET['time'] != '' ? $_GET['time'] : null;?>" class="pj-button tbPrintBookings" target="_blank"><?php __('btnPrint'); ?></a>
			</p>
			<?php
		} 
		?>
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.choose = "-- <?php __('lblChoose'); ?> --";
	</script>
	<?php
}
?>