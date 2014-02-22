jQuery(function ($) {
	$forceDownloadLink = $("body a.forceDownload");
	$forceDownloadLink.each(function(index){
		var oldHref = $(this).attr("href");
		$(this).attr("href", location.protocol + '//' + location.host + "/?fileToDownload=" + oldHref);
	});
});
