<?php

namespace Zotlabs\Widget;

use App;
use Zotlabs\Lib\IConfig;

class Messages {

	public static function widget($arr) {
		if (! local_channel())
			return EMPTY_STR;

		$options = [
			'last_id' => 0,
		];

		if (intval($arr['dm']) === 1) {
			$options['dm'] = true;
		}

		$o = '';
		$page = self::get_messages_page($options);

		if (!$page['entries'])
			return $o;

		$tpl = get_markup_template('messages_widget.tpl');
		$o .= replace_macros($tpl, [
			'$banner' => t('Direct Messages'),
			'$loading' => t('Loading'),
			'$entries' => $page['entries'],
			'$last_id' => $page['last_id']
		]);

		return $o;
	}

	public static function get_messages_page($options) {

		if ($options['last_id'] == -1) {
			return;
		}

		$channel = App::get_channel();
		$item_normal = item_normal();
		$entries = [];
		$limit = 10;
		$dm_mode = false;

		$id_sql = '';
		if ($options['last_id']) {
			$id_sql = " AND id < " . intval($options['last_id']);
		}

		$dm_sql = ' AND item_private IN (0, 1) ';
		if ($options['dm']) {
			$dm_mode = true;
			$dm_sql = ' AND item_private = 2 ';
		}

		$items = q("SELECT * FROM item WHERE uid = %d
			$id_sql
			$dm_sql
			AND item_thread_top = 1
			$item_normal
			ORDER BY created DESC
			LIMIT $limit",
			intval(local_channel())
		);

		xchan_query($items, false);

		$i = 0;

		foreach($items as $item) {

			$recipients = '';
			if ($dm_mode) {
				$recipients = self::get_dm_recipients($channel, $item);
			}

			$summary = $item['summary'];
			if (!$summary) {
				$summary = htmlentities(html2plain(bbcode($item['body']), 75, true), ENT_QUOTES, 'UTF-8', false);
				if (strlen($summary) > 68) {
					$summary = trim(substr($summary, 0, 68)) . '...';
				}
			}

			switch(intval($item['item_private'])) {
				case 1:
					$icon = '<i class="fa fa-lock"></i>';
					break;
				case 2:
					$icon = '<i class="fa fa-envelope-o"></i>';
					break;
				default:
					$icon = '';
			}

			$owner = $item['owner'];

			$entries[$i]['owner_name'] = $owner['xchan_name'];
			$entries[$i]['owner_addr'] = (($owner['xchan_addr']) ? $owner['xchan_addr'] : $owner['xchan_url']);
			$entries[$i]['recipients'] = $recipients;
			$entries[$i]['created'] = datetime_convert('UTC', date_default_timezone_get(), $item['created']);
			$entries[$i]['subject'] = $item['title'];
			$entries[$i]['summary'] = $summary;
			$entries[$i]['b64mid'] = gen_link_id($item['mid']);
			$entries[$i]['href'] = z_root() . '/' . (($dm_mode) ? 'dm' : 'hq') . '/' . gen_link_id($item['mid']);
			$entries[$i]['icon'] = $icon;

			$last_id = $item['id'];

			$i++;
		}

		$result = [
			'last_id' => ((count($entries) < $limit) ? -1 : $last_id),
			'entries' => $entries
		];

		return $result;
	}

	public static function get_dm_recipients($channel, $item) {

		if($channel['channel_hash'] === $item['owner']['xchan_hash']) {
			// we are the owner, get the recipients from the item
			$recips = expand_acl($item['allow_cid']);
			if (is_array($recips)) {
				array_unshift($recips, $owner['xchan_hash']);
				$column = 'xchan_hash';
			}
		}
		else {
			$recips = IConfig::Get($item, 'activitypub', 'recips')['to'];
			if (is_array($recips)) {
				array_unshift($recips, $item['owner']['xchan_url']);
				$column = 'xchan_url';
			}
			else {
				$hookinfo = [
					'item' => $item,
					'recips' => null,
					'column' => ''
				];

				call_hooks('direct_message_recipients', $hookinfo);

				$recips = $hookinfo['recips'];
				$column = $hookinfo['column'];
			}
		}

		if(is_array($recips)) {
			stringify_array_elms($recips, true);

			$query_str = implode(',', $recips);

			//fixme: when query by xchan_addr or xchan_url we might get duplicate entries (zot6+zot xchan)
			$xchans = dbq("SELECT xchan_name FROM xchan WHERE $column IN ($query_str)");

			foreach($xchans as $xchan) {
				$recipients .= $xchan['xchan_name'] . ', ';
			}
		}

		return trim($recipients, ', ');
	}

}
