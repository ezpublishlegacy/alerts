(function($){$(function(){

	if(typeof($.cookie)=='undefined'){
		var o=$('script[src*="jquery.cookie.js"]');
		$('<script type="text/javascript" src="/extension/alerts/design/standard/javascript/jquery.cookie.js" charset="utf-8"></script>').insertBefore(o);
	}

	if($("#alerts").length > 0){
		var alertID = $("input[name='AlertID']").attr('value');
		if(!($.cookie(alertID))){
			$("#alerts").css({'display':'block'});
			$("body").addClass("has-alerts");
		}
		$("#alerts .close").click(function(){
			$("#alerts").remove();
			$("body").removeClass("has-alerts");
			$.cookie(alertID, "close", { path: '/' });
		});
	}

});})(jQuery);
