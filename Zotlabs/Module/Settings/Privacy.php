<?php

namespace Zotlabs\Module\Settings;

use App;
use Zotlabs\Access\PermissionLimits;
use Zotlabs\Access\Permissions;
use Zotlabs\Daemon\Master;
use Zotlabs\Lib\Group;
use Zotlabs\Lib\Libsync;

class Privacy {

	function post() {

		check_form_security_token_redirectOnErr('/settings/privacy', 'settings');
		call_hooks('settings_post', $_POST);

		$role = get_pconfig(local_channel(), 'system', 'permissions_role');
		if ($role === 'custom') {
			$global_perms = Permissions::Perms();

			foreach ($global_perms as $k => $v) {
				PermissionLimits::Set(local_channel(), $k, intval($_POST[$k]));
			}
		}

		$publish = null;

		$default_acl   = ((x($_POST, 'default_acl')) ? '<' . notags(trim($_POST['default_acl'])) . '>' : '');
		$show_presence = (((x($_POST, 'show_presence')) && (intval($_POST['show_presence']) == 1)) ? 1 : 0);
		$publish       = (((x($_POST, 'profile_in_directory')) && (intval($_POST['profile_in_directory']) == 1)) ? 1 : 0);
		$def_group     = ((x($_POST, 'group-selection')) ? notags(trim($_POST['group-selection'])) : '');

		$expire    = ((x($_POST, 'expire')) ? intval($_POST['expire']) : 0);
		$suggestme = ((x($_POST, 'suggestme')) ? intval($_POST['suggestme']) : 0);

		set_pconfig(local_channel(), 'system', 'suggestme', $suggestme);
		set_pconfig(local_channel(), 'system', 'show_online_status', $show_presence);

		$r = q("update channel set channel_expire_days = %d, channel_default_group = '%s', channel_allow_gid = '%s'
				where channel_id = %d",
			intval($expire),
			dbesc($def_group),
			dbesc($default_acl),
			intval(local_channel())
		);
		if ($r)
			info(t('Privacy settings updated.') . EOL);

		if (!is_null($publish)) {
			q("UPDATE profile SET publish = %d WHERE is_default = 1 AND uid = %d",
				intval($publish),
				intval(local_channel())
			);
		}

		Master::Summon(['Directory', local_channel()]);
		Libsync::build_sync_packet();

		goaway(z_root() . '/settings/privacy');
		return; // NOTREACHED
	}

	function get() {

		load_pconfig(local_channel());

		$channel      = App::get_channel();
		$global_perms = Permissions::Perms();
		$permiss      = [];

		$perm_opts = [
			[t('Nobody except yourself'), 0],
			[t('Only those you specifically allow'), PERMS_SPECIFIC],
			[t('Approved connections'), PERMS_CONTACTS],
			[t('Any connections'), PERMS_PENDING],
			[t('Anybody on this website'), PERMS_SITE],
			[t('Anybody in this network'), PERMS_NETWORK],
			[t('Anybody authenticated'), PERMS_AUTHED],
			[t('Anybody on the internet'), PERMS_PUBLIC]
		];

		$recommend_public = [
			'view_stream',
			'view_wiki',
			'view_pages',
			'view_storage'
		];

		$help_txt      = t('Advise: set to "Anybody on the internet" and use privacy groups to restrict access');
		$limits        = PermissionLimits::Get(local_channel());
		$anon_comments = get_config('system', 'anonymous_comments', true);

		foreach ($global_perms as $k => $perm) {
			$options       = [];
			$can_be_public = (strstr($k, 'view') || ($k === 'post_comments' && $anon_comments));

			foreach ($perm_opts as $opt) {
				if ($opt[1] == PERMS_PUBLIC && (!$can_be_public))
					continue;
				$options[$opt[1]] = $opt[0];
			}

			$permiss[] = [
				$k,
				$perm,
				$limits[$k],
				((in_array($k, $recommend_public)) ? $help_txt : ''),
				$options
			];
		}

		//logger('permiss: ' . print_r($permiss,true));

		$expire        = $channel['channel_expire_days'];
		$sys_expire    = get_config('system', 'default_expire_days');
		$show_presence = intval(get_pconfig(local_channel(), 'system', 'show_online_status'));
		$suggestme     = get_pconfig(local_channel(), 'system', 'suggestme');
		$suggestme     = (($suggestme === false) ? '0' : $suggestme); // default if not set: 0
		$yes_no        = [t('No'), t('Yes')];

		$p = q("SELECT * FROM profile WHERE is_default = 1 AND uid = %d LIMIT 1",
			intval(local_channel())
		);

		if (!$p) {
			notice('Error: no default profile found' . EOL);
			return;
		}

		$profile = $p[0];

		$opt_tpl = get_markup_template("field_checkbox.tpl");
		if (get_config('system', 'publish_all')) {
			$profile_in_dir = '<input type="hidden" name="profile_in_directory" value="1" />';
		}
		else {
			$profile_in_dir = replace_macros($opt_tpl, [
				'$field' => ['profile_in_directory', t('Publish my default profile in the network directory'), $profile['publish'], '', $yes_no],
			]);
		}

		$suggestme = replace_macros($opt_tpl, [
			'$field' => ['suggestme', t('Suggest me as a potential contact to new members'), $suggestme, '', $yes_no],
		]);

		$group_select_options = [
			'selected' => $channel['channel_default_group'],
			'form_id'  => 'group-selection',
			'label'    => t('Add approved contacts to this privacy group')
		];

		$group_select = Group::select(local_channel(), $group_select_options);

		$default_acl_select_options = [
			'selected' => trim($channel['channel_allow_gid'], '<>'),
			'form_id'  => 'default_acl',
			'label'    => t('Default privacy group for new content')
		];

		$default_acl_select = Group::select(local_channel(), $default_acl_select_options);
		$permissions_role   = get_pconfig(local_channel(), 'system', 'permissions_role', 'custom');
		$permission_limits  = ($permissions_role === 'custom');

		$stpl = get_markup_template('settings_privacy.tpl');

		$o = replace_macros($stpl, [
			'$ptitle'                    => t('Privacy Settings'),
			'$submit'                    => t('Submit'),
			'$form_security_token'       => get_form_security_token("settings"),
			'$permission_limits'         => $permission_limits,
			'$show_presence'             => ['show_presence', t('Reveal my online status'), $show_presence, '', $yes_no],
			'$permiss_arr'               => $permiss,
			'$permission_limits_label'   => t('Channel permission limits'),
			'$permission_limits_warning' => [
				t('Proceed with caution'),
				t('Changing advanced configuration preferences can impact your channels functionality and security.'),
				t('Accept the risk and continue')
			],
			'$expire'                    => ['expire', t('Expire other channel content after this many days'), $expire, t('0 or blank to use the website limit.') . ' ' . ((intval($sys_expire)) ? sprintf(t('This website expires after %d days.'), intval($sys_expire)) : t('This website does not expire imported content.')) . ' ' . t('The website limit takes precedence if lower than your limit.')],
			'$suggestme'                 => $suggestme,
			'$group_select'              => $group_select,
			'$default_acl_select'        => $default_acl_select,
			'$profile_in_dir'            => $profile_in_dir,
		]);

		return $o;
	}
}
