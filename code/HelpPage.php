<?php

class HelpPage extends Page {

	function getCMSFields() {
		return SiteTree::getCMSFields();
	}

}

class HelpPage_Controller extends Page_Controller {

	function init() {
		return ContentController::init();
	}

}
