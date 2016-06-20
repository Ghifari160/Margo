(function ( $ )
{
	$(document).ready(function()
	{
		$('div').each(function()
		{
			if($(this).data('italicize') == "margo")
			{
				$(this).html($(this).html().replace(/a/g, "<i>a</i>"));
				$(this).html($(this).html().replace(/e/g, "<i>e</i>"));
				$(this).html($(this).html().replace(/o/g, "<i>o</i>"));
			}
		});
	});
})( jQuery );
