<?php
namespace Zotlabs\Module;

use App;
use Zotlabs\Web\Controller;
use Zotlabs\Widget\Direct_messages;


class Dm extends Controller {

	function get() {
		$mod = new Hq();
		return $mod->get();
	}

	function post() {
		if (!local_channel())
			return;

		$ret = Direct_messages::get_dm_page($_REQUEST['last_id']);
		json_return_and_die($ret);

	}

}
