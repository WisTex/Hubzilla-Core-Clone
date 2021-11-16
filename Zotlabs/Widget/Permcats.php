<?php

namespace Zotlabs\Widget;

use Zotlabs\Lib\Permcat;
use Zotlabs\Access\PermissionLimits;

class Permcats {

	function widget($arr) {
		$pcat = new Permcat(local_channel());
		$pcatlist = $pcat->listing();

		$list = '<b>Roles:</b><br>';
		$active = '';
		if($pcatlist) {
			$i = 0;
			foreach($pcatlist as $pc) {
				if(argc() > 1) {
					if($pc['name'] == (($pc['system']) ? argv(1) : hex2bin(argv(1))) )
						$active = $i;
				}

				$list .= '<a href="permcats/' . (($pc['system']) ? $pc['name'] : bin2hex($pc['name'])) . '">' . $pc['localname'] . '</a><br>';
				$i++;
			}
		}

		if(argc() > 1) {
			$test = $pcatlist[$active]['perms'];

			hz_syslog(print_r($test,true));




			$role_sql = '';
			$count = 0;
			foreach ($test as $t) {
				$checkinherited = PermissionLimits::Get(local_channel(),$t['name']);
				hz_syslog($t['name'] . ': ' . $checkinherited);

				if($checkinherited & PERMS_SPECIFIC) {
					$role_sql .= "( k = '" . dbesc($t['name']) . "' AND v = '" . intval($t['value']) . "' ) OR ";
					$count++;
				}
			}

			$role_sql = rtrim($role_sql, ' OR ');

			// get all xchans belonging to a permission role
			$q = q("SELECT abconfig.xchan, xchan.xchan_name FROM abconfig LEFT JOIN xchan on xchan = xchan_hash WHERE chan = %d AND cat = 'my_perms' AND ( $role_sql ) GROUP BY xchan HAVING count(xchan) = %d",
				intval(local_channel()),
				intval($count)
			);

			$members = '<b>Role members:</b><br>';

			foreach ($q as $qq)
				$members .= $qq['xchan_name'] . '<br>';


			//hz_syslog(print_r($q,true));
		}
		return $list . '<br>' . $members;

	}
}
