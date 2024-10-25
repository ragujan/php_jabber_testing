(function (window, undefined){
	"use strict";
	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	var document = window.document,
		validate = (pjQ.$.fn.validate !== undefined),
		datepicker = (pjQ.$.fn.datepicker !== undefined),
		dialog = (pjQ.$.fn.dialog !== undefined),
		routes = [
		          	{pattern: /^#!\/Events$/, eventName: "loadEvents"},
		          	{pattern: /^#!\/Events\/date:([\d\-\.\/]+)?$/, eventName: "loadEvents"},
		          	{pattern: /^#!\/Events\/from_date:([\d\-\.\/]+)\/date:([\d\-\.\/]+)?$/, eventName: "loadEvents"},
		          	{pattern: /^#!\/EventDetails\/id:(\d+)\/date:([\d\-\.\/]+)$/, eventName: "loadEventDetails"},
		          	{pattern: /^#!\/Seats\/date:([\d\-\.\/]+)?$/, eventName: "loadSeats"},
		          	{pattern: /^#!\/Checkout\/date:([\d\-\.\/]+)?$/, eventName: "loadCheckout"},
		          	{pattern: /^#!\/Preview\/date:([\d\-\.\/]+)?$/, eventName: "loadPreview"}
		          ];
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function hashBang(value) {
		if (value !== undefined && value.match(/^#!\//) !== null) {
			if (window.location.hash == value) {
				return false;
			}
			window.location.hash = value;
			return true;
		}
		
		return false;
	}
	
	function onHashChange() {
		var i, iCnt, m;
		for (i = 0, iCnt = routes.length; i < iCnt; i++) {
			m = window.location.hash.match(routes[i].pattern);
			if (m !== null) {
				pjQ.$(window).trigger(routes[i].eventName, m.slice(1));
				break;
			}
		}
		if (m === null) {
			pjQ.$(window).trigger("loadEvents");
		}
	}
	pjQ.$(window).on("hashchange", function (e) {
    	onHashChange.call(null);
    });
	
	function TicketBooking(opts) {
		if (!(this instanceof TicketBooking)) {
			return new TicketBooking(opts);
		}
				
		this.reset.call(this);
		this.init.call(this, opts);
		
		return this;
	}
	
	TicketBooking.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};
	
	TicketBooking.size = function(obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};
	
	TicketBooking.prototype = {
		reset: function () {
			this.$container = null;
			this.container = null;
			this.event_id = null;
			this.date = null;
			this.from_date = null;
			this.current_ticket = null;
			this.opts = {};
			
			return this;
		},
		disableButtons: function () {
			var $el;
			this.$container.find(".tbSelectorButton").each(function (i, el) {
				$el = pjQ.$(el).attr("disabled", "disabled");
			});
		},
		enableButtons: function () {
			this.$container.find(".tbSelectorButton").removeAttr("disabled").removeClass("tbButtonBlackDisabled tbButtonRedDisabled tbButtonBlueDisabled");
		},
		checkHasPriceId: function(id, arr){
			for (var index in arr) 
			{
				if(arr[index] == id)
				{
					return true
				}	
			}
			return false;
		},
		adviseToSelectSeats: function(){
			var self = this,
				total_ticket = 0,
				ticket_arr = {},
				$seatContainer = pjQ.$('#tbSelectedSeats_' + self.opts.index),
				$mapHolder = pjQ.$('#tbMapHolder_' + self.opts.index);
			
			pjQ.$(".tbTicketSelector").each(function (i, el) {
				var price_id = pjQ.$(el).attr('data-id'),
					value = parseInt(pjQ.$(el).val(), 10),
					ticket_name = pjQ.$(el).attr('data-ticket');
				if(value > 0)
				{
					ticket_arr[price_id] = {'cnt': value, 'name': ticket_name};
				}
				total_ticket += value;
			});
			pjQ.$.each(ticket_arr, function (price_id, pair) {
				if($seatContainer.find(".tbAssignedSeats_" + price_id).length < pair.cnt)
				{
					self.current_ticket = price_id;
					var guide_message = '';
					if(pair.cnt > 1)
					{
						guide_message = self.opts.guide_msg.select_seats_for;
					}else{
						guide_message = self.opts.guide_msg.select_seat_for;
					}
					if($mapHolder.hasClass('tbMapHolder'))
					{
						guide_message = guide_message.replace(/\{tickets\}/g, pair.cnt + ' ' + pair.name);
						pjQ.$('.tbSelectSeatGuide').removeClass('alert-success').addClass('alert-info').html(guide_message).show();
					}
					return false;
				}
			});
			if(total_ticket == 0)
			{
				self.current_ticket = null;
				pjQ.$('.tbSelectSeatGuide').html('').hide();
				
				pjQ.$('.tbAskToSelectTickets').show();
				pjQ.$('.tbAskToSelectTickets').siblings().hide();
			}else if(total_ticket > 0){
				pjQ.$('.tbAskToSelectTickets').hide();
				pjQ.$('.tbAskToSelectTickets').siblings().show();
				if(total_ticket == $seatContainer.find(".tbAssignedSeats").length){
					self.current_ticket = null;
					var msg = self.opts.guide_msg.continue; 
					msg = msg.replace(/\{STAG\}/g, '<a href="#" class="alert-link tbContinueLink"><strong>'); 
					msg = msg.replace(/\{ETAG\}/g, '</strong></a>');
					pjQ.$('.tbSelectSeatGuide').removeClass('alert-info').addClass('alert-success').html(msg);
				}
			}
		},
		checkAssignedSeats: function()
		{
			var self = this,
				$seatContainer = pjQ.$('#tbSelectedSeats_' + self.opts.index);
			if($seatContainer.find('.tbAssignedSeats').length > 0)
			{
				pjQ.$('.tbAskToSelectSeats').hide();
				pjQ.$('.tbTipToRemoveSeats').show();
			}else{
				pjQ.$('.tbAskToSelectSeats').show();
				pjQ.$('.tbTipToRemoveSeats').hide();
			}
		},
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.container = document.getElementById("tbContainer_" + this.opts.index);
			this.$container = pjQ.$(this.container);
			
			this.$container.on("click.tb", ".tbSelectorLocale", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var locale = pjQ.$(this).data("id");
				self.opts.locale = locale;
				pjQ.$(this).addClass("tbLocaleFocus").parent().parent().find("a.tbSelectorLocale").not(this).removeClass("tbLocaleFocus");
				
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionLocale"].join(""), {
					"session_id": self.opts.session_id,
					"locale_id": locale
				}).done(function (data) {					
					if(!hashBang("#!/Events"))
					{
						self.loadEvents.call(self);
					}
				}).fail(function () {
					log("Deferred is rejected");
				});
				return false;
			}).on("click.tb", ".tbSelectorDatepickIcon", function (e) {
				var $dp = pjQ.$(this).siblings("input[type='text']");
				if ($dp.hasClass("hasDatepicker")) {
					$dp.datepicker("show");
				} else {
					if(!$dp.is('[disabled=disabled]'))
					{
						$dp.trigger("focusin").datepicker("show");
					}
				}
			}).on("focusin.tb", ".tbSelectorDatepick", function (e) {
				if (datepicker) {
					var $this = pjQ.$(this),
						dOpts = {
							dateFormat: $this.data("dformat"),
							firstDay: $this.data("fday"),
							dayNames: ($this.data("day")).split(","),
						    monthNames: ($this.data("months")).split(","),
						    monthNamesShort: ($this.data("shortmonths")).split(","),
						    dayNamesMin: ($this.data("daymin")).split(","),
							minDate: 0,
							beforeShow: function(input, inst) {
								pjQ.$('#ui-datepicker-div').addClass("pjCbjQueryUI");
							},
							onClose: function(dateText)
							{
								if(pjQ.$(this).attr('data-list') == '1')
								{
									hashBang("#!/Events/from_date:" + dateText + "/date:" + dateText);
								}else{
									self.disableButtons.call(self);
									pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionGetTime"].join(""), {"session_id": self.opts.session_id, "id" : pjQ.$(this).attr('data-id'), "date": dateText}).done(function (data) {
										pjQ.$('#tbTimeContainer_' + self.opts.index).html(data);
										if(data.indexOf("<select") > -1)
										{
											pjQ.$('.tbSelectorButtonPurchase').show();
										}else{
											pjQ.$('.tbSelectorButtonPurchase').hide();
										}
										self.enableButtons.call(self);
									}).fail(function () {
										self.enableButtons.call(self);
									});
								}
							}
						};
					$this.datepicker(dOpts);
				}
			}).on("click.tb", ".pjCbDaysNav", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Events/from_date:" + pjQ.$(this).attr('data-from_date') + "/date:" + pjQ.$(this).attr('data-date'));
			}).on("click.tb", ".tbMovieLink", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.from_date = pjQ.$(this).attr('data-from_date');
				self.date = pjQ.$(this).attr('data-date');
				hashBang("#!/EventDetails/id:" + pjQ.$(this).attr('data-id') + "/date:"+ pjQ.$(this).attr('data-date'));
			}).on("click.tb", ".tbBackToEvents", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Events/from_date:" + self.from_date + "/date:" + self.date);
			}).on("click.tb", ".tbBackToDetails", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/EventDetails/id:" + pjQ.$(this).attr('data-id') + "/date:"+ pjQ.$(this).attr('data-date'));
			}).on("click.tb", ".tbBackToSeats", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Seats/date:" + pjQ.$(this).attr('data-date'));
			}).on("click.tb", ".tbBackToCheckout", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Checkout/date:" + pjQ.$(this).attr('data-date'));
			}).on("click.tb", ".tbSelectorButtonPurchase", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $frm = pjQ.$('#tbDetailsForm_' + self.opts.index),
					date = pjQ.$(this).attr('data-date');
				if($frm.find('select[name=selected_time]').length > 0)
				{
					self.disableButtons.call(self);
					pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDateTime", "&session_id=", self.opts.session_id].join(""), $frm.serialize()).done(function (data) {
						if(data.code == '200')
						{
							hashBang("#!/Seats/date:" + date);
						}
					}).fail(function () {
						self.enableButtons.call(self);
					});
				}
			}).on("click.tb", ".tbSelectorSeats", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(!pjQ.$(this).hasClass('pjCbTimePassed'))
				{
					var date = pjQ.$(this).attr('data-date'),
						params = {
							"id": pjQ.$(this).attr('data-id'),
							"selected_date": pjQ.$(this).attr('data-date'),
							"selected_time": pjQ.$(this).attr('data-time'),
							"back_to": "events"
						};
					self.from_date = pjQ.$(this).attr('data-from_date');
					pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDateTime", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
						if(data.code == '200')
						{
							hashBang("#!/Seats/date:" + date);
						}
					}).fail(function () {
						
					});
				}
				
			}).on("click.tb", ".tbSeatAvailable", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				
				var $frm = pjQ.$('#tbSeatsForm_' + self.opts.index),
					$seatContainer = pjQ.$('#tbSelectedSeats_' + self.opts.index),
					price_id_arr = (pjQ.$(this).attr('data-price-id')).split('~:~'),
					seat_id = pjQ.$(this).attr('data-id'),
					seat_name = pjQ.$(this).attr('data-name'),
					cnt = parseInt(pjQ.$(this).attr('data-count'), 10);
				
				if(self.current_ticket != null && self.checkHasPriceId.call(self, self.current_ticket, price_id_arr) == true)
				{
					var price_id = self.current_ticket,
						$ticket = pjQ.$('#tbTicket_' + price_id),
						chosen_ticket = parseInt($ticket.val(), 10),
						ticket_name = $ticket.attr('data-ticket');
					
					if(chosen_ticket > 0)
					{
						var cnt_selected = 0;
						var total_cnt_selected = 0;
						var price_indx = 0;
						var price_id_i = 0;

						$frm.find(".tbHiddenSeat_" + price_id).each(function (i, el) {
							cnt_selected += parseInt(pjQ.$(el).val(), 10);
						});
						
						for(price_indx = 0; price_indx < price_id_arr.length; price_indx++){
							price_id_i = price_id_arr[price_indx];
							$frm.find(".tbHiddenSeat_" + price_id_i).each(function (i, el) {
								total_cnt_selected += parseInt(pjQ.$(el).val(), 10);
							});
						}	
						
						if(cnt_selected < chosen_ticket)
						{
							var $el = pjQ.$("#tbSeatsForm_" + self.opts.index + " input[name='seat_id\\["+price_id+"\\]\\["+seat_id+"\\]']"),
								seatClass = 'tbAssignedSeats';
							if(!pjQ.$('#tbMapHolder_' + self.opts.index).hasClass('tbMapHolder'))
							{
								seatClass = 'tbAssignedNoMap';
							}
							if($el.length > 0)
							{
								var cnt_seats = parseInt($el.val(), 10);
								if(cnt_seats < cnt)
								{
									$el.val(cnt_seats + 1);
									$seatContainer.append('<span class="'+seatClass+' tbAssignedSeats_'+price_id+'" data_price_id="'+price_id+'" data_seat_id="'+seat_id+'">'+$ticket.attr('data-ticket') + ' #'+ seat_name +'</span>');
									if(total_cnt_selected + 1 >= cnt) 
									{
										pjQ.$(this).removeClass('tbSeatAvailable');
									}
								}
							}else{
								pjQ.$('<input>').attr({
								    type: 'hidden',
								    name: 'seat_id['+price_id+'][' + seat_id + ']',
								    class: 'tbHiddenSeat_' + price_id,
								    data_seat_id: seat_id,
								    value: '1'
								}).appendTo($frm);
								$seatContainer.append('<span class="'+seatClass+' tbAssignedSeats_'+price_id+'" data_price_id="'+price_id+'" data_seat_id="'+seat_id+'">'+$ticket.attr('data-ticket') + ' #'+ seat_name +'</span>');
								if(cnt <= total_cnt_selected + 1)
								{
									pjQ.$(this).removeClass('tbSeatAvailable');
								}
							}
							self.adviseToSelectSeats.call(self);
							self.checkAssignedSeats.call(self);
							pjQ.$(this).addClass('tbSeatSelected');
						}else{
							pjQ.$('.tbGuideMessage').attr('data-type', '').html((self.opts.error_msg.enough).replace("[TICKET]", ticket_name)).fadeIn('slow').delay(1000).fadeOut('slow');
						}
					}else{
						pjQ.$('.tbGuideMessage').attr('data-type', 'ticket' + price_id).html((self.opts.error_msg.no_tickets).replace("[TICKET]", ticket_name)).show();
					}
				}
								
			}).on("change.tb", ".tbTicketSelector", function (e) {
				var $frm = pjQ.$('#tbSeatsForm_' + self.opts.index),
					$mapHolder = pjQ.$('#tbMapHolder_' + self.opts.index),
					$seatContainer = pjQ.$('#tbSelectedSeats_' + self.opts.index),
					price_id = pjQ.$(this).attr('data-id'),
					$guide = pjQ.$('.tbGuideMessage'),
					value = parseInt(pjQ.$(this).val(), 10),
					el_arr = [],
					cnt_seats = 0;
				
				self.adviseToSelectSeats.call(self);
				
				if(!$mapHolder.hasClass('tbMapHolder'))
				{
					$mapHolder.find(".tbSeatSelected").each(function (i, el) {
						var price_id_arr = (pjQ.$(el).attr('data-price-id')).split('~:~'),
							seat_id = pjQ.$(el).attr('data-id');
						if(self.checkHasPriceId.call(self, price_id, price_id_arr))
						{
							var can_removed = false;
							$seatContainer.find(".tbAssignedSeats_" + price_id).each(function (indx, element) {
								if(pjQ.$(element).attr('data_seat_id') == seat_id && pjQ.$(element).attr('data_price_id') == price_id)
								{
									can_removed = true;
								}
							});
							if(can_removed == true)
							{
								if(!pjQ.$(el).hasClass('tbSeatAvailable'))
								{
									pjQ.$(el).addClass('tbSeatAvailable');
								}
								
								pjQ.$(el).removeClass('tbSeatSelected');
							}
						}
					});
					$seatContainer.find(".tbAssignedSeats_" + price_id).remove();
					$frm.find(".tbHiddenSeat_" + price_id).remove();
				}
				
				if($guide.is(":visible") && $guide.attr('data-type') == ('ticket' + price_id) )
				{
					$guide.css('display', 'none');
				}
				
				$frm.find(".tbHiddenSeat_" + price_id).each(function (i, el) {
					el_arr.push(pjQ.$(el));
					cnt_seats += parseInt(pjQ.$(el).val(), 10);
				});
				if(cnt_seats > value){
					
					while(cnt_seats > value)
					{
						var $removal = el_arr.pop(),
							seat_id = $removal.attr('data_seat_id'),
							val = $removal.val();
						
						if((cnt_seats - value) >= val)
						{
							cnt_seats -= val;
							$mapHolder.find(".tbSeatSelected").each(function (i, el) {
								var price_id_arr = (pjQ.$(el).attr('data-price-id')).split('~:~');
								if(self.checkHasPriceId.call(self, price_id, price_id_arr) == true && $seatContainer.find(".tbAssignedSeats_" + price_id).length > 0 && pjQ.$(el).attr('data-id') == seat_id)
								{
									if(!pjQ.$(el).hasClass('tbSeatAvailable'))
									{
										pjQ.$(el).addClass('tbSeatAvailable');
									}
									pjQ.$(el).removeClass('tbSeatSelected');
								}
							});
							$seatContainer.find(".tbAssignedSeats").each(function (i, el) {
								if(pjQ.$(el).attr('data_seat_id') == seat_id && pjQ.$(el).attr('data_price_id') == price_id)
								{
									pjQ.$(el).remove();
								}
							});
							$removal.remove();
						}else{
							var tmp = cnt_seats - value
							cnt_seats -= tmp;
							$removal.val(val - tmp);
							$mapHolder.find(".tbSeatSelected").each(function (i, el) {
								var price_id_arr = (pjQ.$(el).attr('data-price-id')).split('~:~');
								if(self.checkHasPriceId.call(self, price_id, price_id_arr) == true && $seatContainer.find(".tbAssignedSeats_" + price_id).length > 0  && pjQ.$(el).attr('data-id') == seat_id)
								{
									if(!pjQ.$(el).hasClass('tbSeatAvailable'))
									{
										pjQ.$(el).addClass('tbSeatAvailable');
									}
								}
							});
							
							$seatContainer.find(".tbAssignedSeats").each(function (i, el) {
								if(tmp > 0 && pjQ.$(el).attr('data_seat_id') == seat_id && pjQ.$(el).attr('data_price_id') == price_id)
								{
									pjQ.$(el).remove();
									tmp--;
								}
							});
						}
					}
				}else{
					if(!$mapHolder.hasClass('tbMapHolder'))
					{
						self.current_ticket = price_id;
						$mapHolder.find(".tbSeatAvailable").each(function (i, el) {
							var price_id_arr = (pjQ.$(el).attr('data-price-id')).split('~:~');
							if(self.checkHasPriceId.call(self, price_id, price_id_arr) == true)
							{
								if(value > 0)
								{
									var cnt = parseInt(pjQ.$(el).attr('data-count'), 10),
										tmp = value - cnt;
									
									if(cnt == 1)
									{
										pjQ.$(el).trigger('click');
										value--;
									}else if(cnt > 1){
										if(tmp >= 0)
										{
											for(var i = 1; i<= cnt; i++)
											{
												pjQ.$(el).trigger('click');
												value--;
											}
										}else{
											var number_of_tickers = value;
											for(var i = 1; i<= number_of_tickers; i++)
											{
												pjQ.$(el).trigger('click');
												value--;
											}
										}
									}
								}
							}
						});
					}
				}
			}).on("click.tb", ".tbAssignedSeats", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $mapHolder = pjQ.$('#tbMapHolder_' + self.opts.index),
					seat_id = pjQ.$(this).attr('data_seat_id'),
					seat_id_arr = [],
					price_id = pjQ.$(this).attr('data_price_id'),
					$hidden = pjQ.$("#tbSeatsForm_" + self.opts.index + " input[name='seat_id\\["+price_id+"\\]\\["+seat_id+"\\]']");
				if($hidden.length > 0)
				{
					var val = $hidden.val(),
						tmp =parseInt(val, 10) - 1;
					if(tmp == 0)
					{
						$hidden.remove();
						pjQ.$("#tbSeatsForm_" + self.opts.index + " :input").each(function(i, el){
							seat_id_arr.push(pjQ.$(el).attr('data_seat_id'));
						});
						$mapHolder.find(".tbSeatSelected").each(function (i, el) {
							var price_id_arr = (pjQ.$(el).attr('data-price-id')).split('~:~');
							if(self.checkHasPriceId.call(self, price_id, price_id_arr) == true && pjQ.$(el).attr('data-id') == seat_id && self.checkHasPriceId.call(self, seat_id, seat_id_arr) == false)
							{
								if(!pjQ.$(el).hasClass('tbSeatAvailable'))
								{
									pjQ.$(el).addClass('tbSeatAvailable');
								}
								pjQ.$(el).removeClass('tbSeatSelected');
							}
						});
					}else if(tmp > 0){
						$hidden.val(tmp);
					}
					pjQ.$(this).remove();
					self.adviseToSelectSeats.call(self);
					self.checkAssignedSeats.call(self);
				}
			}).on("click.tb", ".tbContinueButton", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				
				var total_tickets = 0,
					total_seats = 0,
					$seatContainer = pjQ.$('#tbSelectedSeats_' + self.opts.index),
					date = pjQ.$(this).attr('data-date');
				pjQ.$('.tbTicketSelector').each(function(i, el){
					total_tickets += parseInt(pjQ.$(el).val(), 10);
				});
				total_seats	= $seatContainer.find('.tbAssignedSeats').length;
				if(total_seats == 0)
				{
					total_seats	= $seatContainer.find('.tbAssignedNoMap').length;
				}
				if(total_seats == 0)
				{
					pjQ.$('.tbErrorMessage').html(self.opts.error_msg.empty).fadeIn('slow').delay(2000).fadeOut('slow');
				}else if(total_tickets > total_seats){
					pjQ.$('.tbErrorMessage').html(self.opts.error_msg.not_enough).fadeIn('slow').delay(2000).fadeOut('slow');
				}else{
					var params = pjQ.$('#tbSeatsForm_'+self.opts.index+', .tbTicketSelector').serialize();
					self.disableButtons.call(self);
					pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveSeats", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
						if(data.code == '200')
						{
							hashBang("#!/Checkout/date:" + date);
						}
					}).fail(function () {
						self.enableButtons.call(self);
					});
				}
			}).on("click.tb", ".tbContinueLink", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.tbContinueButton').trigger('click');
			}).on("change.tb", "select[name='payment_method']", function () {
				self.$container.find(".tbCcWrap").hide();
				self.$container.find(".tbBankWrap").hide();
				switch (pjQ.$("option:selected", this).val()) {
				case 'creditcard':
					self.$container.find(".tbCcWrap").show();
					break;
				case 'bank':
					self.$container.find(".tbBankWrap").show();
					break;
				}
			}).on("click.tb", ".tbCancelToSeats", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.disableButtons.call(self);
				hashBang("#!/Seats/date:" + pjQ.$(this).attr('data-date'));
			}).on("click.tb", ".tbCancelToCheckout", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.disableButtons.call(self);
				hashBang("#!/Checkout/date:" + pjQ.$(this).attr('data-date'));
			}).on("click.tb", ".tbStartOverButton", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.date = null;
				self.disableButtons.call(self);
				hashBang("#!/Events");
			}).on("change.tb", ".pjCbSeatVenue", function (e) {
				
				self.disableButtons.call(self);
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionSetVenue", "&session_id=", self.opts.session_id, "&venue_id=", pjQ.$(this).val()].join("")).done(function (data) {
					self.loadSeats.call(self);
				}).fail(function () {
					self.enableButtons.call(self);
				});
			}).on("click.tb", "#pjCbsCaptchaImage", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $captchaImg = pjQ.$(this);
				if($captchaImg.length > 0){
					var rand = Math.floor((Math.random()*999999)+1); 
					$captchaImg.attr("src", self.opts.folder + 'index.php?controller=pjFront&action=pjActionCaptcha&rand=' + rand);
					pjQ.$('#pjCbsCaptchaField').val("").removeData("previousValue");
				}
			});
			
			pjQ.$(window).on("loadEvents", this.container, function (e) {
				if(arguments.length == 3)
				{
					self.from_date = arguments[1];
					self.date = arguments[2];
				}
				self.loadEvents.call(self);
			}).on("loadEventDetails", this.container, function (e) {
				if(arguments.length == 3)
				{
					self.event_id = arguments[1];
					self.date = arguments[2];
				}
				self.loadEventDetails.call(self);
			}).on("loadSeats", this.container, function (e) {
				if(arguments.length == 2)
				{
					self.date = arguments[1];
				}
				self.loadSeats.call(self);
			}).on("loadCheckout", this.container, function (e) {
				if(arguments.length == 2)
				{
					self.date = arguments[1];
				}
				self.loadCheckout.call(self);
			}).on("loadPreview", this.container, function (e) {
				if(arguments.length == 2)
				{
					self.date = arguments[1];
				}
				self.loadPreview.call(self);
			});
			
			if (window.location.hash.length === 0) {
				this.loadEvents.call(this);
			} else {
				onHashChange.call(null);
			}
			
			pjQ.$(document).on("click.tb", 'button[data-dismiss="modal"]', function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $modal = pjQ.$(this).closest('.modal');
				if ($modal !== undefined && $modal.length > 0) {
					$modal.modal('hide');
					pjQ.$('body').removeClass('modal-open');
				}
				return false;
			});
		},
		
		loadEvents: function () {
			var self = this,
				index = this.opts.index,
				params = 	{
								"locale": this.opts.locale,
								"layout": this.opts.layout,
								"hide": this.opts.hide,
								"index": this.opts.index,
								"from_date": this.from_date,
								"date": this.date
							};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionEvents", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('html, body').animate({
			        scrollTop: self.$container.offset().top
			    }, 500);
				var fnReplaceDatePickers = function(container, elementLarge, elementSmall, benchmark) {
					var $containerWidth = container.outerWidth();

					if ($containerWidth <= benchmark) {
						elementLarge.hide();
						elementSmall.show();
					} else {
						elementLarge.show();
						elementSmall.hide();
					};
				};

				if (self.$container.find('.pjCbWeekPanelDatePicker').length) {
					var $scriptHeader = self.$container.find('.pjCbHeading');
					var $weekPanel = self.$container.find('.pjCbWeekPanel');
					var $weekPanelPicker = self.$container.find('.pjCbWeekPanelDatePicker');
					var $allWeekLinks = $weekPanel.find('.pjCbDaysNav');
					
					var allWeekLinksWidth = 130;
					
					$allWeekLinks.each(function() {
						allWeekLinksWidth += pjQ.$(this).outerWidth();
					});

					fnReplaceDatePickers($scriptHeader, $weekPanel, $weekPanelPicker, allWeekLinksWidth);

					pjQ.$(window).off('.tb').on('resize.tb', function() {
						fnReplaceDatePickers($scriptHeader, $weekPanel, $weekPanelPicker, allWeekLinksWidth);
					});
				};
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		loadEventDetails: function () {
			var self = this,
				index = this.opts.index,
				params = 	{
								"locale": this.opts.locale,
								"layout": this.opts.layout,
								"hide": this.opts.hide,
								"index": this.opts.index,
								"id": self.event_id,
								"date": this.date
							};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionDetails", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('html, body').animate({
			        scrollTop: self.$container.offset().top
			    }, 500);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		loadSeats: function () {
			var self = this,
				index = this.opts.index,
				params = 	{
								"locale": this.opts.locale,
								"layout": this.opts.layout,
								"hide": this.opts.hide,
								"index": this.opts.index,
								"date": this.date
							};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionSeats", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('html, body').animate({
			        scrollTop: self.$container.offset().top
			    }, 500);
				if(pjQ.$('#tbMap_' + self.opts.index).length > 0)
				{
					pjQ.$('[data-pj-toggle="tooltip"]').tooltip({container:'#pjWrapperTicketBooking_' + self.opts.layout});
				}
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		loadCheckout: function () {
			var self = this,
				index = this.opts.index,
				params = 	{
							"locale": this.opts.locale,
							"layout": this.opts.layout,
							"hide": this.opts.hide,
							"index": this.opts.index,
							"date": this.date
						};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionCheckout", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('html, body').animate({
			        scrollTop: self.$container.offset().top
			    }, 500);
				pjQ.$('.modal-dialog').css("z-index", "9999"); 
				if (validate) 
				{
					pjQ.$('#frmCheckoutForm_'+ self.opts.index).validate({
						rules: {
							"captcha" : {
								remote: self.opts.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha&session_id=" + self.opts.session_id,
								required: true,
								minlength: 6,
								maxlength: 6
							}
						},
						onkeyup: false,
						errorPlacement: function (error, element) {
							var $parent = element.parent(),
								$input_group = $parent.parent();
							if(element.attr('name') == 'terms')
							{
								error.insertAfter(element.parent());
							}else{
								error.insertAfter(element);
							}
							if(element.attr('name') == 'captcha')
							{
								$input_group.parent().addClass('has-error');
							}else{
								$parent.addClass('has-error');
							}
						},
						success: function (label) {
							var $parent = pjQ.$(label).parent(),
								$sibling = pjQ.$(label).siblings();
							if($sibling.attr('name') == 'captcha')
							{
								$parent.parent().parent().removeClass('has-error').addClass('has-success');
							}else{
								$parent.removeClass('has-error').addClass('has-success');
							}
							pjQ.$(label).remove();
						},
						submitHandler: function (form) {
							self.disableButtons.call(self);
							var $form = pjQ.$(form);
							pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionCheckout", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
								if (data.status == "OK") {
									hashBang("#!/Preview/date:" + self.date);
								} else if (data.status == "ERR" && data.code == '110') {
									pjQ.$('#pjCbsCaptchaMessage').html(data.text);
									pjQ.$('#pjCbsCaptchaModal').modal('show');
									pjQ.$('#pjCbsCaptchaImage').trigger('click');
									self.enableButtons.call(self);
								}else if (data.status == "ERR") {
										$form
										.find(".tdSelectorNoticeMsg")
										.html(data.text)
										.removeClass("alert-success")
										.addClass("alert-warning")
										.show();
									self.enableButtons.call(self);
								}
							}).fail(function () {
								self.enableButtons.call(self);
							});
							return false;
						}
					});
				}
			});
		},
		loadPreview: function () {
			var self = this,
				index = this.opts.index,
				params = 	{
					"locale": this.opts.locale,
					"layout": this.opts.layout,
					"hide": this.opts.hide,
					"index": this.opts.index,
					"date": this.date
				};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionPreview", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('html, body').animate({
			        scrollTop: self.$container.offset().top
			    }, 500);
				if (validate) {
					pjQ.$('#frmPreviewForm_'+ self.opts.index).validate({
						rules: {},
						onkeyup: false,
						onclick: false,
						onfocusout: false,
						submitHandler: function (form) {
							self.disableButtons.call(self);
							var $form = pjQ.$(form);
							pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveBooking", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
								if (data.code == "200") {
									self.getPaymentForm.call(self, data);
								} else {
									$form
										.find(".tdSelectorNoticeMsg")
										.html(data.text)
										.removeClass("alert-success")
										.addClass("alert-warning")
										.show();
									self.enableButtons.call(self);
								}
							}).fail(function () {
								self.enableButtons.call(self);
							});
							return false;
						}
					});
				}
			});
		},
		getPaymentForm: function(obj){
			var self = this,
				index = this.opts.index;
			var qs = {
					"cid": this.opts.cid,
					"locale": this.opts.locale,
					"hide": this.opts.hide,
					"index": this.opts.index,
					"booking_id": obj.booking_id, 
					"payment_method": obj.payment,
					"layout": this.opts.layout,
				};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetPaymentForm", "&session_id=", self.opts.session_id].join(""), qs).done(function (data) {
				self.$container.html(data);
				pjQ.$('html, body').animate({
			        scrollTop: self.$container.offset().top
			    }, 500);
				switch (obj.payment) {
					case 'paypal':
						self.$container.find("form[name='tbPaypal']").trigger('submit');
						break;
					case 'authorize':
						self.$container.find("form[name='tbAuthorize']").trigger('submit');
						break;
					case 'creditcard':
					case 'bank':
					case 'cash':
						break;
				}
			}).fail(function () {
				log("Deferred is rejected");
			});
		}
	};
	
	window.TicketBooking = TicketBooking;	
})(window);