<?php
ob_start();
if(isset($tpl['ticket_arr']) && count($tpl['ticket_arr']) > 0)
{ 
	?>
	<p>
		<label class="title"><?php __('lblTickets');?></label>
		<span class="block overflow">
			<?php
			foreach($tpl['ticket_arr'] as $v)
			{
				if($v['cnt_tickets'] > 0)
				{
					?>
					<span class="block b5 overflow">
						<label class="block float_left r5 t5 w150"><?php echo pjSanitize::html($v['ticket']);?></label>
						<select id="tbTicket_<?php echo $v['price_id'];?>" name="tickets[<?php echo $v['id'];?>][<?php echo $v['price_id'];?>]" class="pj-form-field w60 r3 float_left tbTicketSelector" data-id="<?php echo $v['price_id'];?>" data-ticket="<?php echo pjSanitize::html($v['ticket']);?>" data-price="<?php echo $v['price'];?>">
							<?php
							for($i = 0; $i <= $v['cnt_tickets']; $i++)
							{
								?><option value="<?php echo $i;?>" data-map="<?php echo isset($tpl['seat_arr']) ? 0 : 1;?>"><?php echo $i?></option><?php
							} 
							?>
						</select>
						<label class="block float_left r5 t5">x</label>
						<label class="block float_left t5"><?php echo pjUtil::formatCurrencySign( $v['price'], $tpl['option_arr']['o_currency']);?></label>
					</span>
					<?php
				}
			} 
			?>
		</span>
	</p>
	<?php
	$has_map = 1;
	if(isset($tpl['seat_arr']))
	{
		$has_map = 0;
		?>
		<div id="tbMapHolder">
			<?php
			foreach ($tpl['seat_arr'] as $seat)
			{
				$is_selected = false;
				$is_available = true;
				?><span class="tbSeatRect<?php echo $seat['seats'] - $seat['cnt_booked'] <= 0 ? ' tbSeatBlocked' : ($is_available == true ? ' tbSeatAvailable' : null); ?><?php echo $is_selected == true ? ' tbSeatSelected' : null;?>" data-id="<?php echo $seat['id']; ?>" data-price-id="<?php echo $seat['price_id']; ?>" data-name="<?php echo $seat['name']; ?>" data-count="<?php echo $seat['seats']; ?>" style="display: none;"><?php echo stripslashes($seat['name']); ?></span><?php
			}
			?>
		</div>
		<?php
	}
}
$venue_id = isset($tpl['venue_id']) ? $tpl['venue_id'] : null;
$ticket = ob_get_contents();
ob_end_clean();
pjAppController::jsonResponse(compact('ticket', 'has_map', 'venue_id'));
?>