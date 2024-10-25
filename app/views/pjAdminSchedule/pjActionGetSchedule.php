<?php
if (count($tpl['arr']) > 0)
{
	?>
	<div class="dContainer">
		<div class="dWrapper">
			<table class="pj-table dTable" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
						<td class="dHeadcol"><strong><?php __('lblEvent');?></strong></td>
						<?php
						foreach($tpl['time_arr'] as $v)
						{
							?><td class="dHead"><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['date'] . ' ' . $v)); ?></td><?php
						}
						?>
					</tr>
					<?php
					foreach($tpl['arr'] as $k => $v)
					{
						?>
						<tr>
							<td id="dHeadCol_<?php echo $k;?>" class="dHeadcol"><strong><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']?>"><?php echo pjSanitize::html($v['title']);?></a></strong></td>
							<?php
							foreach($tpl['time_arr'] as $time)
							{
								$content_arr = array();
								$date_time = '';
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
											$date_time = $tpl['date'] . ' ' . $show;
											$show_time = date($tpl['option_arr']['o_time_format'], strtotime($date_time));
											$show_time .= isset($tpl['venue_arr'][$_time][$v['id']]) ? '<br/>' . $tpl['venue_arr'][$_time][$v['id']] : null;
											$show_time .= '<br/><a href="'.$_SERVER['PHP_SELF'].'?controller=pjAdminBookings&amp;action=pjActionIndex&amp;event_id='.$v['id'].'&amp;dt='.strtotime($_time).'">' . $cnt_bookings . ' ' . ($cnt_bookings != 1 ? __('lblPluralTickets', true) : __('lblSigularTicket', true)) . '</a>';
											$content_arr[] = $show_time;
										}
									}
								}
								?>
								<td class="dSlot tdCenter dSlot_<?php echo $k; ?><?php echo !empty($content_arr) ? ' available' : null; ?>">
									<?php 
									echo !empty($content_arr) ? join("<br/><br/>", $content_arr) : null;
									if(!empty($content_arr))
									{
										?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate&event_id=<?php echo $v['id']; ?>&amp;ts=<?php echo strtotime($date_time);?>" class="pj-table-icon-add"></a><?php
									} 
									?>
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
		</div><!-- dWrapper -->
	</div><!-- dContainer -->
	<?php
}else{
	?><br/><?php __('lblNoEventFound');?><br/><br/><?php
}
?>