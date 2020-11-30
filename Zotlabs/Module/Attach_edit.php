<?php
namespace Zotlabs\Module;
/**
 * @file Zotlabs/Module/Attach_edit.php
 *
 */

use App;
use Zotlabs\Web\Controller;
use Zotlabs\Lib\Libsync;
use Zotlabs\Access\AccessList;

class Attach_edit extends Controller {

	function post() {

		if (! local_channel()) {
			notice( t('Permission denied.') . EOL);
			return;
		}

		$attach_id = ((x($_POST, 'attach_id')) ? intval($_POST['attach_id']) : '');
		$resource = ((x($_POST, 'resource')) ? notags($_POST['resource']) : '');
		$folder  = ((x($_POST, 'folder'))  ? notags($_POST['folder'])  : '');
		$newfolder  = ((x($_POST, 'newfolder_' . $attach_id))  ? notags($_POST['newfolder_' . $attach_id])  : '');
		$filename = ((x($_POST, 'filename')) ? notags($_POST['filename']) : '');
 		$newfilename = ((x($_POST, 'newfilename_' . $attach_id)) ? notags($_POST['newfilename_' . $attach_id]) : '');
		$recurse = ((x($_POST, 'recurse_' . $attach_id)) ? intval($_POST['recurse_' . $attach_id]) : 0);
		$notify = ((x($_POST, 'notify_edit_' . $attach_id)) ? intval($_POST['notify_edit_' . $attach_id]) : 0);
		$copy = ((x($_POST, 'copy_' . $attach_id)) ? intval($_POST['copy_' . $attach_id]) : 0);

		$channel = App::get_channel();

		if ($copy) {
			attach_copy($channel['channel_id'], $resource, $newfolder, $newfilename);		
		}
 		elseif ($folder !== $newfolder || $filename !== $newfilename) {
			attach_move($channel['channel_id'], $resource, $newfolder, $newfilename);			
		}

		$acl = new AccessList($channel);
		$acl->set_from_array($_POST);
		$x = $acl->get();

		$url = get_cloud_url($channel['channel_id'], $channel['channel_address'], $resource);

		attach_change_permissions($channel['channel_id'], $resource, $x['allow_cid'], $x['allow_gid'], $x['deny_cid'], $x['deny_gid'], $recurse, true);

		$sync = attach_export_data($channel, $resource, false);

		if ($sync) {
			Libsync::build_sync_packet($channel['channel_id'], ['file' => [$sync]]);
		}

		if ($notify) {
			$observer = App::get_observer();
			attach_store_item($channel, $observer, $resource);
		}

		goaway(dirname($url));
		
	}

}
