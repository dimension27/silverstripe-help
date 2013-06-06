<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
	<head> 
		<% base_tag %>
		<title>$Title &raquo; $SiteConfig.Title</title>
		$MetaTags(false)
		
		<link rel="shortcut icon" href="/favicon.ico" />
		
		<% require css(themes/ss-help/css/ss.screen.min.css, screen) %>
		<% require css(themes/ss-help/css/ss.print.min.css, print) %>
		
		<% require css(themes/ss-help/css/userhelp.css) %>
		
		<% require javascript(sapphire/thirdparty/jquery/jquery.js) %>
		<% require javascript(toolbar/javascript/toolbar.js?site=userhelp&searchShow=false) %>
		<% require css(toolbar/css/toolbar.css) %>
	</head>
<body>
	<div id="container">
		<div id="header">

			<h1>
				<a href="" title="$SiteConfig.Title"><span>$SiteConfig.Title</span></a>
			</h1>
		
			<div id="breadcrumbs">
				<% include Breadcrumbs %>
			</div>
		
			<div class="clear"></div>
		</div>
	
		<div id="layout">
			<div id="search-bar">
				<div id="search">
					$SearchForm
				</div>
			</div>
			
			<div id="content" class="typography">
				$Layout
			</div>
		</div>
	</div>		
		
	<div id="footer" class="clear">
		<p>Powered by <a href="http://www.silverstripe.org">SilverStripe</a>. <a href="http://open.silverstripe.org/newticket/?component=Documentation">Raise a bug or enhancement ticket</a> to help improve the documentation.
		<br />Except where otherwise noted, content on this wiki is licensed under <a class="urlextern" rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">CC Attribution-Noncommercial-Share Alike 3.0 Unported</a><a href="http://creativecommons.org/licenses/by/3.0/nz/" rel="license"><img class="cc-logo" src="http://i.creativecommons.org/l/by/3.0/nz/80x15.png" style="border-width: 0pt;" alt="Creative Commons License"></a></p>
	</div>

	<% if PastMember %>
		$SilverStripeNavigator
	<% end_if %>

</body>
</html>