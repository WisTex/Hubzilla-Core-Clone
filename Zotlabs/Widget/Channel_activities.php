<?php

/**
 *   * Name: Channel Activity
 *   * Description: A widget that shows you a greeting and info about your last login and other channel activities
 */

namespace Zotlabs\Widget;

use App;

class Channel_activities {

	public static $activities = [];
	public static $uid = null;
	public static $limit = 3;
	public static $channel = [];

	public static function widget($arr) {
		if (!local_channel()) {
			return EMPTY_STR;
		}

		self::$uid = local_channel();
		self::$channel = App::get_channel();

		$o = '<div id="channel-activities">';


		$o .= '<h2>Welcome ' . self::$channel['channel_name'] . '!</h2>';
		//$o .= 'Last login date: ' . get_pconfig(self::$uid, 'system', 'stored_login_date') . ' from ' . get_pconfig(self::$uid, 'system', 'stored_login_addr');

		self::get_photos_activity();

		$hookdata = [
			'channel' => self::$channel,
			'activities' => self::$activities,
			'limit' => self::$limit
		];

		call_hooks('channel_activities_widget', $hookdata);

		$keys = array_column($hookdata['activities'], 'date');

		array_multisort($keys, SORT_DESC, $hookdata['activities']);

		hz_syslog('activities: ' . print_r($hookdata['activities'], true));

		$o .= '<br>';

		foreach($hookdata['activities'] as $a) {
			$o .= '<h3>' . $a['label'] . '</h3>';

			foreach($a['items'] as $i) {
				$o .= $i;
			}

			$o .= '<br><br>';
		}

		$o .= '</div>';


		return $o;
	}

	private static function get_photos_activity() {

		$r = q("SELECT * FROM photo WHERE uid = %d AND photo_usage = 0 AND imgscale = 3 ORDER BY edited DESC LIMIT %d",
			intval(self::$uid),
			intval(self::$limit)
		);

		if (!$r) {
			return;
		}

		$p[] = '<div id="photo-album">';

		foreach($r as $rr) {
			$url = z_root() . '/photos/' . self::$channel['channel_address'] . '/image/' . $rr['resource_id'];

			$src = z_root() . '/photo/' . $rr['resource_id'] . '-' . $rr['imgscale'];
			$w = $rr['width'];
			$h= $rr['height'];
			$p[]  = "<a href='$url'><img src='$src' width='$w' height='$h'></a>";
		}

		$p[] = '</div>';
		$p[] = <<<EOF
		<script>
		$('#photo-album').justifiedGallery({
			captions: false,
			border: 0,
			margins: 3,
			maxRowsCount: 1
		});
		</script>
EOF;


		self::$activities['photos'] = [
			'label' => t('Last active photos'),
			'date' => $r[0]['edited'],
			'items' => $p
		];

	}


}

