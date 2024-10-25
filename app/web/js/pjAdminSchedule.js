var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $boxSchedule = $("#boxSchedule");
		
		function showSchedule()
		{
			var number_rows = $('.dTable tr').length - 1;
			for(var i = 0; i < number_rows; i ++)
			{
				var headCol_height = $('#dHeadCol_' + i).height();
				var max_height = headCol_height;
				$('.dSlot_' + i).each(function( index ) {
					var cell_height = $( this ).height();
					if(cell_height > max_height)
					{
						max_height = cell_height;
					}
				});
				$('#dHeadCol_' + i).height(max_height + 1);
				$('.dSlot_' + i).height(max_height);
			}
		}
		
		function getSchedule(date) {
			$('#pj_schedule_loader').css('display', 'block');
			$.get("index.php?controller=pjAdminSchedule&action=pjActionGetSchedule", {
				"date": date
			}).done(function (data) {
				$("#boxSchedule").html(data);
				showSchedule();
				$('#pj_schedule_loader').css('display', 'none');
			});
		}
		
		getSchedule($('#schedule_date').val());
		
		$(document).on("focusin", ".datepick", function (e) {
			var $this = $(this);
			$this.datepicker({
				firstDay: $this.attr("rel"),
				dateFormat: $this.attr("rev"),
				onSelect: function (dateText, inst) {
					getSchedule(dateText);
					var print_href = $this.attr('data-href');
					print_href = print_href.replace("[DATE]", dateText);
					$('.btnPrint').attr('href', print_href);
				}
			});
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
		}).on("click", ".btnFilter", function (e) {
			var dateText = $(this).attr('rev'),
				print_href = $(this).attr('data-href');
			$('#schedule_date').val(dateText);
			print_href = print_href.replace("[DATE]", dateText);
			$('.btnPrint').attr('href', print_href);
			getSchedule(dateText);
		});
	});
})(jQuery_1_8_2);