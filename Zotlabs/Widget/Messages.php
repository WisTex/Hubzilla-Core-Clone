<?php

namespace Zotlabs\Widget;

use App;
use Zotlabs\Lib\IConfig;

class Messages {

	public static function widget($arr) {
		if (! local_channel())
			return EMPTY_STR;

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
			'$offset' => $page['offset']
		]);

		return $o;
	}

	public static function get_messages_page($options) {

		if ($options['offset'] == -1) {
			return;
		}

		$channel = App::get_channel();
		$item_normal = item_normal();
		$entries = [];
		$limit = 10;
		$dm_mode = false;

		$offset = 0;
		if ($options['offset']) {
			$offset = intval($options['offset']);
		}

		$dm_sql = ' AND item_private IN (0, 1) ';
		if ($options['dm']) {
			$dm_mode = true;
			$dm_sql = ' AND item_private = 2 ';
		}

		$items = q("SELECT * FROM item WHERE uid = %d
			AND created <= '%s'
			$dm_sql
			AND item_thread_top = 1
			$item_normal
			ORDER BY created DESC
			LIMIT $limit OFFSET $offset",
			intval(local_channel()),
			dbescdate($_SESSION['page_loadtime'])

		);

		xchan_query($items, false);

		$i = 0;

		foreach($items as $item) {

			$info = '';
			if ($dm_mode) {
				$info .= self::get_dm_recipients($channel, $item);
			}

			if($item['owner_xchan'] !== $item['author_xchan']) {
				$info .= t('via') . ' ' . $item['owner']['xchan_name'];
			}

			$summary = $item['title'];
			if (!$summary) {
				$summary = $item['summary'];
			}
			if (!$summary) {
				$summary = htmlentities(html2plain(bbcode($item['body']), 75, true), ENT_QUOTES, 'UTF-8', false);
			}
			if (!$summary) {
				$summary = t('Sorry, there is no text preview available for this post');
			}
			if (strlen($summary) > 68) {
				$summary = trim(substr($summary, 0, 68)) . '...';
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

			$entries[$i]['author_name'] = $item['author']['xchan_name'];
			$entries[$i]['author_addr'] = (($item['author']['xchan_addr']) ? $item['author']['xchan_addr'] : $item['author']['xchan_url']);
			$entries[$i]['info'] = $info;
			$entries[$i]['created'] = datetime_convert('UTC', date_default_timezone_get(), $item['created']);
			$entries[$i]['summary'] = $summary;
			$entries[$i]['b64mid'] = gen_link_id($item['mid']);
			$entries[$i]['href'] = z_root() . '/' . (($dm_mode) ? 'dm' : 'hq') . '/' . gen_link_id($item['mid']);
			$entries[$i]['icon'] = $icon;

			$i++;
		}

		$result = [
			'offset' => ((count($entries) < $limit) ? -1 : intval($offset + $limit)),
			'entries' => $entries
		];

		return $result;
	}

	public static function get_dm_recipients($channel, $item) {

		if($channel['channel_hash'] === $item['owner']['xchan_hash']) {
			// we are the owner, get the recipients from the item
			$recips = expand_acl($item['allow_cid']);
			if (is_array($recips)) {
				array_unshift($recips, $item['owner']['xchan_hash']);
				$column = 'xchan_hash';
			}
		}
		else {
			$recips = IConfig::Get($item, 'activitypub', 'recips');
			if (isset($recips['to']) && is_array($recips['to'])) {
				$recips = $recips['to'];
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
			$xchans = dbq("SELECT DISTINCT xchan_name FROM xchan WHERE $column IN ($query_str)");

			foreach($xchans as $xchan) {
				$recipients .= $xchan['xchan_name'] . ', ';
			}
		}

		return trim($recipients, ', ');
	}

}
