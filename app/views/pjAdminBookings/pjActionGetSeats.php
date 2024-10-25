<?php
$map = $tpl['venue_arr']['map_path'];
if (is_file($map))
{
	$size = getimagesize($map);
	?>
	<div style="display: block" class="b10">
		<div class="tbSelectSeatGuide"></div>
		<div class="tbLabelSeats"><?php __('lblSelectedSeats');?>:</div>
		<div class="tbAskToSelectSeats" style="display: block"><?php __('lblSelectAvailableSeats');?></div>
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
							?><span class="<?php echo $class;?> tbAssignedSeats_<?php echo $price_id;?>" data_seat_id="<?php echo $seat_id;?>" data_price_id="<?php echo $price_id;?>"><?php echo $tpl['ticket_name_arr'][$price_id]; ?> #<?php echo $tpl['seat_name_arr'][$seat_id];?></span><?php
						}	
					}
				}
			} 
			?>
		</div>
		<div class="tbTipToRemoveSeats" style="display: none"><?php __('lblRemoveSeats');?><br/></div>
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