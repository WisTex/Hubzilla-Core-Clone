<?php
namespace Zotlabs\Module;

use Zotlabs\Web\Controller;

class Dm extends Controller {

	function get($update = 0, $load = false) {
		$mod = new Hq();
		return $mod->get($update, $load);
	}

}
