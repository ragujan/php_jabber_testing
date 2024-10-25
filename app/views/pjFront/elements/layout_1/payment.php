<?php
$status = __('front_booking_statuses', true);
?>
<br/>
<div class="container-fluid">
	<div class="panel panel-default pjCbMain">
		<div class="panel-heading clearfix pjCbHeading">
			<?php include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/locale.php';?>
		</div><!-- /.panel-heading clearfix pjCbHeading -->
		<div class="panel-body pjCbBody">
			<?php
			if (isset($tpl['get']['payment_method']))
			{
				switch ($tpl['get']['payment_method'])
				{
					case 'paypal':
						?><p class="text-success text-center pjCbMessage pjCbMessageSucces"><?php echo $status[11]; ?></p><?php
						if (pjObject::getPlugin('pjPaypal') !== NULL)
						{
							$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
						}
						break;
					case 'authorize':
						?><p class="text-success text-center pjCbMessage pjCbMessageSucces"><?php echo $status[11]; ?></p><?php
						if (pjObject::getPlugin('pjAuthorize') !== NULL)
						{
							$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
						}
						break;
					case 'bank':
						?><p class="text-success text-center pjCbMessage pjCbMessageSucces"><?php echo $status[1]; ?></p><p><?php echo stripslashes(nl2br($tpl['option_arr']['o_bank_account'])); ?></p><?php
						break;
					case 'creditcard':
					case 'cash':
					default:
						?><p class="text-success text-center pjCbMessage pjCbMessageSucces"><?php echo $status[1]; ?></p><?php
				}
			}
			?>
		</div>
		<?php
		if($tpl['get']['payment_method'] == 'bank' || $tpl['get']['payment_method'] == 'creditcard' || $tpl['get']['payment_method'] == 'cash' || $tpl['option_arr']['o_payment_disable'] == 'Yes') 
		{
			?>
			<div class="panel-footer text-center pjCbFoot">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12 text-center">
						<br />
						<button class="btn btn-default tbSelectorButton tbStartOverButton pjCbBtn pjCbBtnSecondary"><?php __('front_button_start_over')?></button>
					</div><!-- /.col-md-12 col-sm-12 col-xs-12 -->
				</div>
			</div>
			<?php
		} 
		?>
	</div>
</div>