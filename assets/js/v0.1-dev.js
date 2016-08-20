(function ( $ )
{
	$.refreshText = function()
	{
		$('div').each(function()
		{
			if($(this).data('italicize') == "margo")
			{
				var txt = $(this).text();

				$(this).html(txt.replace(/a|i|u|e|o|A|I|U|E|O/g, function f(x)
				{
						return "<i>" + x + "</i>";
				}));

				console.log("Refresh: Text");
			}
		});
	}

	$(document).ready(function()
	{
		$.refreshText();
	});
})( jQuery );
