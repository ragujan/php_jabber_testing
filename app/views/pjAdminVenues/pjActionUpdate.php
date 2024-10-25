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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true, false);
		$bodies = __('error_bodies', true, false);
		
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVenues&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblDetails'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVenues&amp;action=pjActionSector&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblSectors'); ?></a></li>
		</ul>
	</div>
	<?php
	$desc = str_replace("{SIZE}", ini_get('post_max_size'), __('infoUpdateVenueDesc', true, false));
	pjUtil::printNotice(__('infoUpdateVenueTitle', true, false), $desc); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVenues&amp;action=pjActionUpdate" method="post" id="frmUpdateVenue" class="pj-form form pj-loader-outer" enctype="multipart/form-data">
		<div class="pj-loader"></div>
		<input type="hidden" name="venue_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']?>" />
		<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
		<div class="multilang"></div>
		<?php endif; ?>
		<div class="clear_both">
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
			?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblName'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['name'])); ?>"/>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			}
			$_yesno_arr = __('_yesno', true, false);
			$map = $tpl['arr']['map_path'];
			?>
			<p>
				<label class="title"><?php __('lblUserSeatsMap'); ?></label>
				<span class="inline_block">
					<span class="content float_left r20">
						<input type="radio" id="tbUseMap_Yes" name="use_seats_map" value="T"<?php echo !empty($map) && is_file($map) ? ' checked="checked"' : null;?>/><label for="tbUseMap_Yes"><?php echo $_yesno_arr['T'];?></label>
						&nbsp;&nbsp;
						<input type="radio" id="tbUseMap_No" name="use_seats_map" value="F"<?php echo empty($map) || !is_file($map) ? ' checked="checked"' : null;?>/><label for="tbUseMap_No"><?php echo $_yesno_arr['F'];?></label>
					</span>
					<span class="content float_left">
						<a href="#" class="tbHotpotSize" style="display: <?php echo is_file($map) ? 'block' : 'none';?>;"><?php __('lblSetHotspotSize');?></a>
					</span>
				</span>
			</p>
			<?php
			if (!empty($map) && is_file($map))
			{
				$size = getimagesize($map);
				?>
				<div id="boxMap" class="tbUseMapYes">
					<p>
						<label class="title"><?php __('lblSeatsMap'); ?></label>
						<span class="inline_block">
							<input type="button" value="<?php __('btnDeleteMap'); ?>" class="pj-button pj-delete-map" lang="<?php echo $tpl['arr']['id']?>"/>
						</span>
					</p>
					<div class="bsMapHolder">
						<div id="mapHolder" style="position: relative; overflow: hidden; width: <?php echo $size[0]; ?>px; height: <?php echo $size[1]; ?>px; margin: 0 auto;">
							<img id="map" src="<?php echo $map; ?>" alt="" style="margin: 0; border: none; position: absolute; top: 0; left: 0; z-index: 500" />
							<?php
							foreach ($tpl['seat_arr'] as $seat)
							{
								?><span rel="hi_<?php echo $seat['id']; ?>" title="<?php echo $seat['name']; ?>" class="rect empty" style="width: <?php echo $seat['width']; ?>px; height: <?php echo $seat['height']; ?>px; left: <?php echo $seat['left']; ?>px; top: <?php echo $seat['top']; ?>px; line-height: <?php echo $seat['height']; ?>px"><span class="bsInnerRect" data-name="hi_<?php echo $seat['id']; ?>"><?php echo stripslashes($seat['name']); ?></span></span><?php
							}
							?>
						</div>
						<input type="hidden" id="number_of_seats" name="number_of_seats" value=""/>
					</div>
					<div id="hiddenHolder">
						<?php
						foreach ($tpl['seat_arr'] as $seat)
						{
							?><input id="hi_<?php echo $seat['id']; ?>" type="hidden" name="seats[]" value="<?php echo join("|", array($seat['id'], $seat['width'], $seat['height'], $seat['left'], $seat['top'], $seat['name'], $seat['seats'])); ?>" /><?php
						}
						?>
					</div>
					<div id="dialogDelete" title="<?php __('btnDeleteMap'); ?>" style="display:none">
						<p><?php __('lblDeleteMapConfirm'); ?></p>
					</div>
					<div id="dialogUpdate" title="<?php __('lblUpdateMapTitle'); ?>" style="display:none">
						<p><?php __('lblUpdateMapDesc'); ?></p>
						<br/>
						<div class="form pj-form">
							<p>
								<label class="title"><?php __('lblName', false, true); ?></label>
								<input type="text" name="seat_name" id="seat_name" class="pj-form-field w220" />
							</p>
							<p>
								<label class="title"><?php __('lblSeats', false, true); ?></label>
								<input type="text" name="seat_seats" id="seat_seats" class="pj-form-field w50" />
							</p>
						</div>
					</div>
					<div id="dialogHotspot" title="<?php __('lblSetHotspotSize'); ?>" style="display:none">
						<div class="form pj-form">
							<p>
								<label class="title"><?php __('lblWidth', false, true); ?></label>
								<input type="text" name="hotspot_width" id="hotspot_width" value="25" class="pj-form-field w50" />
							</p>
							<p>
								<label class="title"><?php __('lblHeight', false, true); ?></label>
								<input type="text" name="hotspot_height" id="hotspot_height" value="25" class="pj-form-field w50" />
							</p>
						</div>
					</div>
				</div>
				<?php
			}else{
				?>
				<div class="tbUseMapYes" style="display:none;">
					<p>
						<label class="title"><?php __('lblSeatsMap'); ?></label>
						<span class="inline_block">
							<input type="file" name="seats_map" id="seats_map" class="pj-form-field w250"/>
						</span>
					</p>
				</div>
				<?php
			} 
			?>
			<div class="tbUseMapNo" style="display:<?php echo (is_file($map)) ? 'none' : 'block';?>">
				<p>
					<label class="title"><?php __('lblSeatsCount'); ?></label>
					<span class="inline_block">
						<input type="text" name="seats_count" id="seats_count" class="pj-form-field w80" value="<?php echo count($tpl['seat_arr']) > 0 ? count($tpl['seat_arr']) : null; ?>"/>
					</span>
				</p>
				<div class="pj-loader-outer">
					<div class="pj-loader"></div>
					<p>
						<label class="title"><?php __('lblSeatNumbers'); ?></label>
						<span class="block overflow">
							<span class="content b5"><?php __('lblSeatNumbersText1'); ?></span>
							<span id="tbSeatNumber" class="tbSeatNumber">
								<?php
								if(count($tpl['seat_arr']) > 0)
								{
									foreach($tpl['seat_arr'] as $k => $v)
									{
										?><input type="text" name="number[<?php echo $v['id'];?>]" value="<?php echo $v['name']?>" class="pj-form-field w80 number-field" data-index="<?php echo $k + 1;?>" /><?php
									}
								} 
								?>
							</span>
							<input type="hidden" name="seat_number" id="seat_number"/>
						</span>
					</p>
				</div>
			</div>		
			<div style="clear:both;"></div>
			<p>
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button float_left r5" />
					<input type="button" id="pj_delete_seat" value="" class="pj-button float_left" style="display: none;"/>
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminVenues&action=pjActionIndex';" />
				</span>
			</p>
		</div>
	</form>
	
	<script type="text/javascript">
	var locale_array = new Array(); 
	var myLabel = myLabel || {};
	myLabel.field_required = "<?php __('tb_field_required'); ?>";
	myLabel.seats_required = "<?php __('tb_seats_required'); ?>";
	myLabel.seat_numbers_1 = "<?php __('lblSeatNumbersText1'); ?>";
	myLabel.seat_numbers_2 = "<?php __('lblSeatNumbersText2'); ?>";
	myLabel.seat_numbers = "<?php __('lblSeatNumbers'); ?>";
	myLabel.seat_numbers_required = "<?php __('lblSeatNumbersRequired'); ?>";
	myLabel.seat_count_greater_zero = "<?php __('lblSeatCountGreaterThanZero');?>";
	myLabel.delete = "<?php __('lblDelete'); ?>";
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>locale_array.push(<?php echo $v['id'];?>);<?php
	} 
	?>
	myLabel.locale_array = locale_array;
	myLabel.localeId = "<?php echo $controller->getLocaleId(); ?>";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				select: function (event, ui) {
					
				}
			});
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>