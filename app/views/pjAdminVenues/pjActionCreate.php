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
	
	<?php
	$desc = str_replace("{SIZE}", ini_get('post_max_size'), __('infoAddVenueDesc', true, false));
	pjUtil::printNotice(__('infoAddVenueTitle', true, false), $desc);
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVenues&amp;action=pjActionCreate" method="post" id="frmCreateVenue" class="pj-form form" enctype="multipart/form-data">
		<input type="hidden" name="venue_create" value="1" />
		<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
		<div class="multilang"></div>
		<?php endif;?>
		<div class="clear_both">
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
			?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblName'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" />
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			}
			$_yesno_arr = __('_yesno', true, false);
			?>
			<p>
				<label class="title"><?php __('lblUserSeatsMap'); ?></label>
				<span class="inline_block">
					<span class="content">
						<input type="radio" id="tbUseMap_Yes" name="use_seats_map" value="T" checked="checked"/><label for="tbUseMap_Yes"><?php echo $_yesno_arr['T'];?></label>
						&nbsp;&nbsp;
						<input type="radio" id="tbUseMap_No" name="use_seats_map" value="F"/><label for="tbUseMap_No"><?php echo $_yesno_arr['F'];?></label>
					</span>
				</span>
			</p>
			<div class="tbUseMapYes">
				<p>
					<label class="title"><?php __('lblSeatsMap'); ?></label>
					<span class="inline_block">
						<input type="file" name="seats_map" id="seats_map" class="pj-form-field w250 required"/>
					</span>
				</p>
			</div>
			<div class="tbUseMapNo" style="display:none;">
				
				<p>
					<label class="title"><?php __('lblSeatsCount'); ?></label>
					<span class="inline_block">
						<input type="text" name="seats_count" id="seats_count" class="pj-form-field w80"/>
					</span>
				</p>
				<div class="pj-loader-outer">
					<div class="pj-loader"></div>
					<p>
						<label class="title"><?php __('lblSeatNumbers'); ?></label>
						<span class="block overflow ">
							<span class="content b5"><?php __('lblSeatNumbersText1'); ?></span>
							<span id="tbSeatNumber" class="tbSeatNumber"></span>
							<input type="hidden" name="seat_number" id="seat_number"/>
						</span>
					</p>
				</div>
			</div>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminVenues&action=pjActionIndex';" />
			</p>
		</div>
	</form>
	
	<script type="text/javascript">
	var locale_array = new Array(); 
	var myLabel = myLabel || {};
	myLabel.field_required = "<?php __('tb_field_required'); ?>";
	myLabel.seat_numbers_1 = "<?php __('lblSeatNumbersText1'); ?>";
	myLabel.seat_numbers_2 = "<?php __('lblSeatNumbersText2'); ?>";
	myLabel.seat_numbers = "<?php __('lblSeatNumbers'); ?>";
	myLabel.seat_numbers_required = "<?php __('lblSeatNumbersRequired'); ?>";
	myLabel.seat_count_greater_zero = "<?php __('lblSeatCountGreaterThanZero');?>";
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