<!doctype html>
<html>
	<head>
		<title>Cinema Booking System by PHPJabbers.com</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<link type="text/css" rel="stylesheet" href="<?php echo PJ_INSTALL_URL . PJ_CSS_PATH; ?>print.css"/>
	</head>
	<body style="background-image: none; background-color: #fff;">
		<div id="container" style="padding: 10px;">
			<?php
			if(isset($_GET['date']) && $_GET['date'] != '')
			{ 
				?>
				<div style="font-weight: bold;margin-bottom: 5px;"><?php __('lblDate');?>:&nbsp;<?php echo date($tpl['option_arr']['o_date_format'], strtotime($_GET['date']));?></div>
				<?php
			}
			if(isset($_GET['time']) && $_GET['time'] != '')
			{
				?>
				<div style="font-weight: bold;margin-bottom: 5px;"><?php __('lblShowtime');?>:&nbsp;<?php echo date($tpl['option_arr']['o_time_format'], strtotime($_GET['time']));?></div>
				<?php
			} 
			?>
			<div style="float:left; width: 350px">
				<div style="font-weight: bold;margin-bottom: 5px;"><?php __('lblEvent');?>:&nbsp;<?php echo $tpl['arr']['title'];?></div>
				<div style="font-weight: bold;margin-bottom: 5px;"><?php __('lblDuration');?>:&nbsp;<?php echo pjSanitize::html($tpl['arr']['duration']);?> <?php __('lblMinutes')?></div>
			</div>
			<div style="float:left; width: 250px">
				<div style="font-weight: bold;margin-bottom: 5px;"><?php __('lblTotalBookings');?>:&nbsp;<?php echo $tpl['cnt_bookings'];?></div>
				<div style="font-weight: bold;margin-bottom: 5px;"><?php __('lblTotalSeats');?>:&nbsp;<?php echo $tpl['total_seats'];?></div>
			</div>
			<table class="table" cellpadding="0" cellspacing="0" style="width: 100%;">
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
								<td><?php echo $v['uuid']; ?></td>
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
		</div>
	</body>
</html>
<script type="text/javascript">
if (window.print) {
	window.print();
}
</script>