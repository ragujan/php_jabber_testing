var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var dialog = ($.fn.dialog !== undefined),
			$dialogDeleteTicket = $("#dialogDeleteTicket"),
			tabs = ($.fn.tabs !== undefined),
			tipsy = ($.fn.tipsy !== undefined),
			$tabs = $("#tabs"),
			tOpt = {
				select: function (event, ui) {
					$(":input[name='tab_id']").val(ui.panel.id);
				}
			};
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs(tOpt);
		}
		$(".field-int").spinner({
			min: 0
		});
		if (tipsy) {
			$(".center-langbar-tip").tipsy({
				offset: 1,
				opacity: 1,
				html: true,
				className: "tipsy-listing-center"
			});
		}
		function reDrawCode() {
			var code = $("#hidden_code").text(),
				locale = $("select[name='install_locale']").find("option:selected").val(),
				hide = $("input[name='install_hide']").is(":checked") ? "&hide=1" : "",
				theme = $('.inused').data('theme');
			locale = locale && parseInt(locale.length, 10) > 0 ? "&locale=" + locale : "";
			theme = theme ? '&layout=' + theme : '';
			
			$("#install_code").val(code.replace(/&action=pjActionLoadJS/g, function(match) {
	            return ["&action=pjActionLoad", locale, hide, theme].join("");
	        }).replace(/&action=pjActionLoadCss/g, function (match) {
	        	return ["&action=pjActionLoadCss", theme].join("");
	        }));
			
			$('.pjBrsPreviewUrl').each(function(){
				var href = $(this).attr('data-href');
				href = href.replace("{LOCALE}", locale);
				href = href.replace("{HIDE}", hide);
				$(this).attr('href', href);
			});
		}
		if($('#frmNotification').length > 0)
		{
			var value = $('#client_email_notify').val();
			$('.boxClient' + value).show();
			
			var value = $('#admin_email_notify').val();
			$('.boxAdmin' + value).show();
			
			var value = $('#admin_sms').val();
			$('.boxAdminSms' + value).show();
			
			tinymce.init({
			    selector: "textarea.mceEditor",
			    theme: "modern",
			    relative_urls : false,
				remove_script_host : false,
				convert_urls : true,
			    width: 500,
			    plugins: [
			         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
			         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			         "save table contextmenu directionality emoticons template paste textcolor"
				],
				toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons"
			});
		}
		if ($dialogDeleteTicket.length > 0 && dialog) 
		{
			$dialogDeleteTicket.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 400,
				buttons: (function () {
					var buttons = {};
					buttons[tbApp.locale.button.delete] = function () {
						$.ajax({
							type: "GET",
							dataType: "json",
							url: $dialogDeleteTicket.data('href'),
							success: function (res) {
								if(res.code == 200){
									$('#ticket_container').remove();
									$dialogDeleteTicket.dialog('close');
								}
							}
						});
					};
					buttons[tbApp.locale.button.cancel] = function () {
						$dialogDeleteTicket.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		
		$("#content").on("focusin", ".textarea_install", function (e) {
			$(this).select();
		}).on("change", "select[name='value-enum-o_send_email']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'mail|smtp::mail':
				$(".boxSmtp").hide();
				break;
			case 'mail|smtp::smtp':
				$(".boxSmtp").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_paypal']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxPaypal").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxPaypal").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_authorize']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxAuthorize").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxAuthorize").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_bank']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxBankAccount").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxBankAccount").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_email_confirmation']", function (e) {
			switch ($("option:selected", this).val()) {
			case '0|1::0':
				$(".boxClientConfirmation").hide();
				break;
			case '0|1::1':
				$(".boxClientConfirmation").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_email_payment']", function (e) {
			switch ($("option:selected", this).val()) {
			case '0|1::0':
				$(".boxClientPayment").hide();
				break;
			case '0|1::1':
				$(".boxClientPayment").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_email_cancel']", function (e) {
			switch ($("option:selected", this).val()) {
			case '0|1::0':
				$(".boxClientCancel").hide();
				break;
			case '0|1::1':
				$(".boxClientCancel").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_admin_email_confirmation']", function (e) {
			switch ($("option:selected", this).val()) {
			case '0|1::0':
				$(".boxAdminConfirmation").hide();
				break;
			case '0|1::1':
				$(".boxAdminConfirmation").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_admin_email_payment']", function (e) {
			switch ($("option:selected", this).val()) {
			case '0|1::0':
				$(".boxAdminPayment").hide();
				break;
			case '0|1::1':
				$(".boxAdminPayment").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_admin_email_cancel']", function (e) {
			switch ($("option:selected", this).val()) {
			case '0|1::0':
				$(".boxAdminCancel").hide();
				break;
			case '0|1::1':
				$(".boxAdminCancel").show();
				break;
			}
		}).on("change", "select[name='install_locale']", function(e) {
            reDrawCode.call(null);
		}).on("change", "input[name='install_hide']", function (e) {
			reDrawCode.call(null);
		}).on("click", ".pj-delete-ticket", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogDeleteTicket.data('href', $(this).data('href')).dialog("open");
		}).on("click", ".pj-use-theme", function (e) {
			var theme = $(this).attr('data-theme'),
				href = $('#pj_preview_install').attr('href');
			$('.pj-loader').css('display', 'block');
			$.ajax({
				type: "GET",
				async: false,
				url: 'index.php?controller=pjAdminOptions&action=pjActionUpdateTheme&theme=' + theme,
				success: function (data) {
					$('.theme-holder').html(data);
					$('.pj-loader').css('display', 'none');
					reDrawCode.call(null);
				}
			});
		}).on("change", "#client_email_notify", function (e) {
			var value = $(this).val();
			$('.boxClient').hide();
			$('.boxClient' + value).show();
		}).on("change", "#admin_email_notify", function (e) {
			var value = $(this).val();
			$('.boxAdmin').hide();
			$('.boxAdmin' + value).show();
		}).on("change", "#admin_sms", function (e) {
			var value = $(this).val();
			$('.boxAdminSms').hide();
			$('.boxAdminSms' + value).show();
		});
	});
})(jQuery_1_8_2);