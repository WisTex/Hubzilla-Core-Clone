<?php

namespace Zotlabs\Widget;

use App;
use Zotlabs\Lib\IConfig;

class Direct_messages {

	function widget($arr) {
		if(! local_channel())
			return EMPTY_STR;

		$page = self::get_dm_page();

		$tpl = get_markup_template('direct_messages_widget.tpl');

		$o .= replace_macros($tpl, [
			'$banner' => t('Direct Messages'),
			'$loading' => t('Loading'),
			'$entries' => $page['entries'],
			'$last_id' => $page['last_id']
		]);

		return $o;
	}

	public static function get_dm_page($last_id = 0) {

		if ($last_id == -1) {
			return;
		}

		$channel = App::get_channel();
		$item_normal = item_normal();
		$entries = [];
		$limit = 10;
		$id_sql = '';

		if ($last_id) {
			$id_sql = " AND id < " . intval($last_id);
		}

		$items = q("SELECT * FROM item WHERE uid = %d
			$id_sql
			AND item_private = 2
			AND item_thread_top = 1
			$item_normal
			ORDER BY created DESC
			LIMIT $limit",
			intval(local_channel())
		);

		xchan_query($items, false);

		$i = 0;

		foreach($items as $item) {

			$owner = $item['owner'];
			$recipients = '';


			if($channel['channel_hash'] === $owner['xchan_hash']) {
				// we are the owner, get the recipients from the item
				$recips = expand_acl($item['allow_cid']);
				$column = 'xchan_hash';
			}
			else {
				$recips = IConfig::Get($item, 'activitypub', 'recips')['to'];
				$column = 'xchan_url';
			}

			if(is_array($recips)) {
				stringify_array_elms($recips, true);

				$query_str = implode(',', $recips);
				$xchans = dbq("SELECT xchan_name FROM xchan WHERE $column IN ($query_str)");

				$recipients = $owner['xchan_name'] . ', ';
				foreach($xchans as $xchan) {
					$recipients .= $xchan['xchan_name'] . ', ';
				}
			}

			$summary = $item['summary'];
			if(!$summary) {
				$summary = htmlentities(html2plain(bbcode($item['body']), 75, true), ENT_QUOTES, 'UTF-8', false);
				if(strlen($summary) > 30)
					$summary = trim(substr($summary, 0, 41)) . '...';

				if(!$summary)
					$summary = t('Nothing to preview');
			}

			$entries[$i]['recipients'] = trim($recipients, ', ');
			$entries[$i]['created'] = datetime_convert('UTC', date_default_timezone_get(), $item['created']);
			$entries[$i]['subject'] = (($item['title']) ? $item['title'] : t('No subject'));
			$entries[$i]['summary'] = $summary;
			$entries[$i]['b64mid'] = gen_link_id($item['mid']);

			$last_id = $item['id'];

			$i++;

		}

		$result = [
			'last_id' => ((count($entries) < $limit) ? -1 : $last_id),
			'entries' => $entries
		];

		return $result;
	}
}
