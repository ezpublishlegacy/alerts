/********************************************************************************
* Alerts
********************************************************************************/
var $WebsiteAlerts = (function($){

	var CookieName = 'Alerts';

	var PageID = 0;

	var _this = false,
		Cookie = false,
		Cache = {},
		Utils = {
			fetch:function(options){
				if(!options.items){
					if(options.source){
						$.ajax({
							async:false,
							url:options.source,
							success:function(data){
								options.items = data.content[0];
							}
						});
					}
				}
				return options.items;
			},
			getCookie:function(){
				var Cookie = false;
				if(Cookie = $.cookie(CookieName)){
					return $.evalJSON(Cookie);
				}
				return false;
			},
			hasCookie:function(){
				return !!this.getCookie();
			},
			setCookie:function(data){
				$.cookie(CookieName, $.toJSON(data), {
					expires:7,
					path:'/'
				});
			}
		}
	;


	$.fn.alerts = function(o){
		var _this = this,
			Options = $.extend({
			items:false,
			class_id:false
		}, o);


		Options.source = '/ezjscore/call/moduletools::View::'+Options.class_id+'|'+PageID+'::alerts::items?ContentType=json';
		// ensure that there is only one attachment item, and if so, attempt to fetch the images if not provided
		if(this.length!=1 || !Utils.fetch(Options)){
			return this;
		}

		if(Options.items.count){
			this.find('.items').append(Options.items.content);
			if(Options.items.count > 1){
				this.addClass('scrollable').scrollable({
					circular:true,
					prev:false,
					next:false,
					keyboard:false
				}).autoscroll(5000);
			}

			// check if cookie is set
			if(Utils.hasCookie()){
				Cookie = Utils.getCookie();
				if(Cookie.collapsed){
					_this.css({
						top:'-24px'
					}).addClass('collapsed');
				}
			}else{
				Utils.setCookie({
					collapsed:false
				});
			}

			this.show().find('.close').on('click', function(){
				_this.animate({
					top:'-24px'
				}, 1000, function(){
					_this.addClass('collapsed');
					Utils.setCookie({
						collapsed:true
					});
				});
				return false;
			}).end().on('click', function(){
				if(_this.hasClass('collapsed')){
					_this.animate({
						top:'0px'
					}, 1000, function(){
						Utils.setCookie({
							collapsed:false
						});
					}).removeClass('collapsed');
				}
			});
		}

		return this;
	};

	return {
		setPageID:function(id){
			PageID = id;
		}
	};

})(typeof(jQuery)!=='undefined' ? jQuery : null);
