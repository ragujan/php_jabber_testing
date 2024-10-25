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
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionIndex"><?php __('menuEvents'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionExport"><?php __('lblExport'); ?></a></li>
		</ul>
	</div>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblDetails'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionShow&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblShows'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionBooking&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblBookings'); ?></a></li>
		</ul>
	</div>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionUpdate" method="post" id="frmUpdateEvent" class="pj-form form" enctype="multipart/form-data">
		<input type="hidden" name="event_update" value="1" />
		<input type="hidden" id="index_arr" name="index_arr" value="" />
		<input type="hidden" id="remove_arr" name="remove_arr" value="" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']?>" />
		<?php
		pjUtil::printNotice(__('infoUpdateEventTitle', true), __('infoUpdateEventDesc', true)); 
		?>
		<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
		<div class="multilang"></div>
		<?php endif; ?>
		<div class="clear_both">
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblTitle'); ?></label>
					<span class="inline_block">
						<input type="text" id="i18n_title_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][title]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['title'])); ?>"/>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			}
			foreach ($tpl['lp_arr'] as $v)
			{
			?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblDescription'); ?></label>
					<span class="inline_block">
						<textarea id="i18n_description_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][description]" class="pj-form-field w500 h200<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>"><?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['description'])); ?></textarea>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			} 
			?>
			<p>
				<label class="title"><?php __('lblImage'); ?></label>
				<span class="inline_block">
					<input type="file" name="event_img" id="event_img" class="pj-form-field w250" />
				</span>
			</p>
			<?php
			if(!empty($tpl['arr']['event_img']))
			{
				$image_url = PJ_INSTALL_URL . $tpl['arr']['event_img'];
				?>
				<p id="image_container">
					<label class="title">&nbsp;</label>
					<span class="inline_block">
						<img class="td-image" src="<?php echo $image_url; ?>" />
						<a href="javascript:void(0);" class="pj-delete-image" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionDeleteImage&id=<?php echo $tpl['arr']['id'];?>"><?php __('btnDelete');?></a>
					</span>
				</p>
				<?php
			} 
			?>
			<p>
				<label class="title"><?php __('lblDuration'); ?></label>
				<span class="inline_block">
					<input type="text" name="duration" id="duration" class="pj-form-field field-int w80" value="<?php echo pjSanitize::html($tpl['arr']['duration']);?>"/>&nbsp;<?php __('lblMinutes'); ?>
				</span>
			</p>
		</div>
		<?php
		pjUtil::printNotice(__('infoEventPriceTitle', true), __('infoEventPriceDesc', true)); 
		?>
		<div id="multiple_prices">
			<div id="fd_size_list" class="fd-size-list">
				<?php
				if(isset($tpl['price_arr']))
				{
					foreach($tpl['price_arr'] as $k => $price)
					{
						?>
						<div class="fd-size-row" data-index="<?php echo $price['id'];?>">
							<?php
							foreach ($tpl['lp_arr'] as $v)
							{
								?>
								<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
									<label class="title"><?php echo $k == 0 ? __('lblTicket') : null;?></label>
									<span class="inline_block">
										<input type="text" name="i18n[<?php echo $v['id']; ?>][price_name][<?php echo $price['id'];?>]" class="pj-form-field float_left r3 w200<?php echo (int) $v['is_default'] === 0 ? NULL : ' fdRequired'; ?>" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$price['i18n'][$v['id']]['price_name'])); ?>"/>
										<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
										<span class="pj-multilang-input float_left r10"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
										<?php endif; ?>
									</span>
								</p>
								<?php
							}
							if($k > 0)
							{ 
								?>
								<div class="size-icons">
									<input type="button" value="<?php __('btnRemove'); ?>" class="pj-button pj-remove-size" />
								</div>
								<?php
							} 
							?>
						</div>
						<?php
					}
				} else{
					$index = 'fd_' . rand(1, 999999); 
					?>
					<div class="fd-size-row" data-index="<?php echo $index;?>">
						<?php
						foreach ($tpl['lp_arr'] as $v)
						{
							?>
							<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
								<label class="title fd-title-<?php echo $index;?>"><?php __('lblTicket'); ?></label>
								<span class="inline_block">
									<input type="text" name="i18n[<?php echo $v['id']; ?>][price_name][<?php echo $index;?>]" class="pj-form-field float_left r3 w200<?php echo (int) $v['is_default'] === 0 ? NULL : ' fdRequired'; ?>" lang="<?php echo $v['id']; ?>"/>
									<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
									<span class="pj-multilang-input float_left r10"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
									<?php endif; ?>
								</span>
							</p>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
			<p>
				<label class="title">&nbsp;</label>
				<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button pj-add-size" />
			</p>
		</div>
		
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminEvents&action=pjActionIndex';" />
		</p>
	</form>
	
	<div id="dialogDeleteImage" style="display: none" title="<?php __('lblDeleteImage');?>"><?php __('lblDeleteImageConfirm');?></div>
	
	<div id="fd_size_clone" style="display: none;">
		<div class="fd-size-row" data-index="{INDEX}">
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title fd-title-{INDEX}">&nbsp;</label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][price_name][{INDEX}]" class="pj-form-field float_left r3 w200<?php echo (int) $v['is_default'] === 0 ? NULL : ' fdRequired'; ?>" lang="<?php echo $v['id']; ?>"/>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input float_left r10"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			}
			?>
			<div class="size-icons">
				<input type="button" value="<?php __('btnRemove'); ?>" class="pj-button pj-remove-size" />
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
	var locale_array = new Array(); 
	var myLabel = myLabel || {};
	myLabel.field_required = "<?php __('tb_field_required'); ?>";
	myLabel.duration_greater_zero = "<?php __('lblDurationGreaterThanZero');?>";
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