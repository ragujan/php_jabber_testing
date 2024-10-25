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
			if(isset($tpl['date']))
			{
				$date = isset($_GET['date']) ? $_GET['date'] : date($tpl['option_arr']['o_date_format']);
				?>
				<div style="font-weight: bold;margin-bottom: 5px;"><?php __('lblDate');?>:&nbsp;<?php echo $date;?></div>
				<table class="table" cellpadding="0" cellspacing="0" style="width: 100%;">
					<thead>
						<tr>
							<th><?php __('lblEvent');?></th>
							<?php
							foreach($tpl['time_arr'] as $v)
							{
								?><th><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['date'] . ' ' . $v)); ?></th><?php
							}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($tpl['arr'] as $k => $v)
						{ 
							?>
							<tr>
								<td><strong><?php echo pjSanitize::html($v['title']);?></strong></td>
								<?php
								foreach($tpl['time_arr'] as $time)
								{
									$content_arr = array();
									if(isset($tpl['show_arr'][$v['id']]))
									{
										$show_arr = $tpl['show_arr'][$v['id']];
										foreach($show_arr as $show)
										{
											$_time = $tpl['date'] . ' ' . $show . ':00';
											if(date('H:00', strtotime($_time)) == $time)
											{
												$cnt_bookings = 0;
												if(isset($tpl['booking_cnt_arr'][$v['id'] . '-' . strtotime($_time)]))
												{
													$cnt_bookings = $tpl['booking_cnt_arr'][$v['id'] . '-' . strtotime($_time)];
												}
												$show_time = date($tpl['option_arr']['o_time_format'], strtotime($tpl['date'] . ' ' . $show));
												$show_time .= isset($tpl['venue_arr'][$_time][$v['id']]) ? '<br/>' . $tpl['venue_arr'][$_time][$v['id']] : null;
												$show_time .= '<br/>' . $cnt_bookings . ' ' . ($cnt_bookings != 1 ? __('lblPluralTickets', true) : __('lblSigularTicket', true));
												$content_arr[] = $show_time;
											}
										}
									}
									?>
									<td>
										<?php echo !empty($content_arr) ? join("<br/><br/>", $content_arr) : null; ?>
									</td>
									<?php
								} 
								?>
							</tr>
							<?php
						} 
						?>
					</tbody>
				</table>
				<?php
			}else{
				__('lblInvalidDate');
			} 
			?>
		</div>
	</body>
</html>
<?php
if(isset($tpl['date']))
{ 
	?>
	<script type="text/javascript">
	if (window.print) {
		window.print();
	}
	</script>
	<?php
} 
?>