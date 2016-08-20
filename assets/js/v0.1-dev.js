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
			}
		});
	}

	$.fn.initializeCanvas = function( options )
	{
		var settings = $.extend({
			width: "100%",
			height: "100%"
		}, options);

		if(settings.width === "100%")
			$(this).attr("width", $(this).parent().width());
		else
			$(this).attr("width", settings.width);

		if(settings.height === "100%")
			$(this).attr("height", $(this).parent().height());
		else
			$(this).attr("height", settings.height);
	}

	function canvasAnimate_scaleTranslate(w, h, x, y, tw, th, canvas, duration, callback = "na")
	{
		var rectW, rectH, rectX, rectY, animation, context, mw = 0, mh = 0,
			divident, interval;

		context = canvas.getContext('2d');

		if(w == "100%")
			rectW = canvas.width;
		else
			rectW = w;

		if(h == "100%")
			rectH = canvas.height;
		else
			rectH = h;

		if(x == "center")
			rectX = (canvas.width - rectW) / 2;
		else
			rectX = x;

		if(y == "center")
			rectY = (canvas.height - rectH) / 2;
		else
			rectY = y;

		if(tw == "100%")
			tw = canvas.width;

		if(th == "100%")
			th = canvas.height;

		if(rectW > tw)
			mw = 1;

		if(rectH > th)
			mh = 1;

		if(tw < rectW)
			tpw = rectW;
		else
			tpw = tw;

		if(th < rectH)
			tph = rectH;
		else
			tph = th;

		if(tpw > tph)
			divident = tpw;
		else
			divident = tph;

		interval = duration / divident;

		context.fillStyle = "rgb(125,125,250)";
		context.fillRect(rectX, rectY, rectW, rectH);
		context.save();

		console.log(interval);

		animation = setInterval(function()
		{
			context.clearRect(0, 0, canvas.width, canvas.height);

			if(mw)
			{
				if(rectW > tw)
					rectW--;
				else
				{
					if(rectW > rectH)
					{
						clearInterval(animation);
						if(callback != "na")
							callback(callback);
					}
				}
			}
			else
			{
				if(rectW < tw)
					rectW++;
				else
				{
					if(tw > th)
					{
						clearInterval(animation);
						if(callback != "na")
							callback(callback);
					}				}
			}

			if(mh)
			{
				if(rectH > th)
					rectH--;
				else
				{
					if(rectH > rectW)
					{
						clearInterval(animation);
						if(callback != "na")
							callback(callback);
					}
				}
			}
			else
			{
				if(rectH < th)
					rectH++;
				else
				{
					if(th > tw)
					{
						clearInterval(animation);
						if(callback != "na")
							callback(callback);
					}
				}
			}

			if(x == "center")
				rectX = (canvas.width - rectW) / 2;
			else
				rectX = x;

			if(y == "center")
				rectY = (canvas.height - rectH) / 2;
			else
				rectY = y;

			context.fillRect(rectX, rectY, rectW, rectH);
		}, interval);

		console.log("Animation Completed!");
	}

	function loadingToTitle()
	{
		var $title = $('.m-title');

		$title.text("Margo: The Game").data("italicize", "margo");
		$.refreshText();
	}

	function titleToLoading()
	{
		var counter = 0,
			$title = $('.m-title'),
			animation;

		$title.removeAttr('data-italicize').text("Loading");

		animation = setInterval(function()
		{
			if(counter < 3)
			{
				$title.text($title.text() + ".");
				counter++;
			}
			else
			{
				clearInterval(animation);
				loadingToTitle()
			}
		}, 1500);
	}

	$(document).ready(function()
	{
		$.refreshText();

		$('#ribbon-bg').initializeCanvas({ width: "100%" });

		var headerCanvas = $('#ribbon-bg')[0],
			header = headerCanvas.getContext('2d'),
			counter = 0;

		canvasAnimate_scaleTranslate(0, "100%",
			"center", "center",
			"100%", "100%",
			headerCanvas, 500, titleToLoading);

		console.log("Done!");
	});
})( jQuery );
