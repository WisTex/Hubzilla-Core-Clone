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

		$index_opt_out = (((x($_POST, 'index_opt_out')) && (intval($_POST['index_opt_out']) == 1)) ? 1 : 0);
		set_pconfig(local_channel(), 'system', 'index_opt_out', $index_opt_out);

		$autoperms = (((x($_POST, 'autoperms')) && (intval($_POST['autoperms']) == 1)) ? 1 : 0);
		set_pconfig(local_channel(), 'system', 'autoperms', $autoperms);

		$role = get_pconfig(local_channel(), 'system', 'permissions_role');
		if ($role === 'custom') {
			$global_perms = Permissions::Perms();

			foreach ($global_perms as $k => $v) {
				PermissionLimits::Set(local_channel(), $k, intval($_POST[$k]));
			}
		}

		$default_acl   = ((x($_POST, 'default_acl')) ? '<' . notags(trim($_POST['default_acl'])) . '>' : '');
		$def_group     = ((x($_POST, 'group-selection')) ? notags(trim($_POST['group-selection'])) : '');

		$r = q("update channel set channel_default_group = '%s', channel_allow_gid = '%s'
				where channel_id = %d",
			dbesc($def_group),
			dbesc($default_acl),
			intval(local_channel())
		);
		if ($r)
			info(t('Privacy settings updated.') . EOL);

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

		$autoperms = get_pconfig(local_channel(), 'system', 'autoperms');
		$index_opt_out   = get_pconfig(local_channel(), 'system', 'index_opt_out');

		$permissions_role   = get_pconfig(local_channel(), 'system', 'permissions_role', 'custom');
		$permission_limits  = ($permissions_role === 'custom');

		$stpl = get_markup_template('settings_privacy.tpl');

		$o = replace_macros($stpl, [
			'$ptitle'                    => t('Privacy Settings'),
			'$submit'                    => t('Submit'),
			'$form_security_token'       => get_form_security_token("settings"),
			'$permission_limits'         => $permission_limits,
			'$permiss_arr'               => $permiss,
			'$permission_limits_label'   => t('Channel permission limits'),
			'$permission_limits_warning' => [
				t('Proceed with caution'),
				t('Changing advanced configuration preferences can impact your channels functionality and security.'),
				t('Accept the risk and continue')
			],
			'$autoperms' => ['autoperms', t('Automatically approve new contacts'), $autoperms, '', [t('No'), t('Yes')]],
			'$index_opt_out' => ['index_opt_out', t('Opt-out of search engine indexing'), $index_opt_out, '', [t('No'), t('Yes')]]
		]);

		return $o;
	}
}
