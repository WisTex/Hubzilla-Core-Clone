<?php

namespace Zotlabs\Module\Settings;

use Zotlabs\Lib\Apps;
use Zotlabs\Lib\Libsync;

require_once('include/selectors.php');


class Channel {


	function post() {

		$channel = \App::get_channel();

		check_form_security_token_redirectOnErr('/settings', 'settings');

		call_hooks('settings_post', $_POST);

		$role             = ((x($_POST,'permissions_role')) ? notags(trim($_POST['permissions_role'])) : '');

		//$username         = ((x($_POST,'username'))   ? notags(trim($_POST['username']))     : '');
		$timezone         = ((x($_POST,'timezone_select'))   ? notags(trim($_POST['timezone_select']))     : '');
		$defloc           = ((x($_POST,'defloc'))     ? notags(trim($_POST['defloc']))       : '');
		$openid           = ((x($_POST,'openid_url')) ? notags(trim($_POST['openid_url']))   : '');
		$maxreq           = ((x($_POST,'maxreq'))     ? intval($_POST['maxreq'])             : 0);
		$expire           = ((x($_POST,'expire'))     ? intval($_POST['expire'])             : 0);
		$evdays           = ((x($_POST,'evdays'))     ? intval($_POST['evdays'])             : 3);
		$photo_path       = ((x($_POST,'photo_path')) ? escape_tags(trim($_POST['photo_path'])) : '');
		$attach_path      = ((x($_POST,'attach_path')) ? escape_tags(trim($_POST['attach_path'])) : '');

		$expire_items     = ((x($_POST,'expire_items')) ? intval($_POST['expire_items'])	 : 0);
		$expire_starred   = ((x($_POST,'expire_starred')) ? intval($_POST['expire_starred']) : 0);
		$expire_photos    = ((x($_POST,'expire_photos'))? intval($_POST['expire_photos'])	 : 0);
		$expire_network_only    = ((x($_POST,'expire_network_only'))? intval($_POST['expire_network_only'])	 : 0);

		$allow_location   = (((x($_POST,'allow_location')) && (intval($_POST['allow_location']) == 1)) ? 1: 0);

		//$blocktags        = (((x($_POST,'blocktags')) && (intval($_POST['blocktags']) == 1)) ? 0: 1); // this setting is inverted!
		$unkmail          = (((x($_POST,'unkmail')) && (intval($_POST['unkmail']) == 1)) ? 1: 0);
		$cntunkmail       = ((x($_POST,'cntunkmail')) ? intval($_POST['cntunkmail']) : 0);
		$suggestme        = ((x($_POST,'suggestme')) ? intval($_POST['suggestme'])  : 0);
		$autoperms        = ((x($_POST,'autoperms')) ? intval($_POST['autoperms'])  : 0);

		$post_newfriend   = (($_POST['post_newfriend'] == 1) ? 1: 0);
		$post_joingroup   = (($_POST['post_joingroup'] == 1) ? 1: 0);
		$post_profilechange   = (($_POST['post_profilechange'] == 1) ? 1: 0);
		$adult            = (($_POST['adult'] == 1) ? 1 : 0);

		$mailhost        = ((array_key_exists('mailhost',$_POST)) ? notags(trim($_POST['mailhost'])) : '');

		$pageflags = $channel['channel_pageflags'];
		$existing_adult = (($pageflags & PAGE_ADULT) ? 1 : 0);
		if($adult != $existing_adult)
			$pageflags = ($pageflags ^ PAGE_ADULT);


		$notify = 0;

		if(x($_POST,'notify1'))
			$notify += intval($_POST['notify1']);
		if(x($_POST,'notify2'))
			$notify += intval($_POST['notify2']);
		if(x($_POST,'notify3'))
			$notify += intval($_POST['notify3']);
		if(x($_POST,'notify4'))
			$notify += intval($_POST['notify4']);
		if(x($_POST,'notify5'))
			$notify += intval($_POST['notify5']);
		if(x($_POST,'notify6'))
			$notify += intval($_POST['notify6']);
		if(x($_POST,'notify7'))
			$notify += intval($_POST['notify7']);
		if(x($_POST,'notify8'))
			$notify += intval($_POST['notify8']);


		$vnotify = 0;

		if(x($_POST,'vnotify1'))
			$vnotify += intval($_POST['vnotify1']);
		if(x($_POST,'vnotify2'))
			$vnotify += intval($_POST['vnotify2']);
		if(x($_POST,'vnotify3'))
			$vnotify += intval($_POST['vnotify3']);
		if(x($_POST,'vnotify4'))
			$vnotify += intval($_POST['vnotify4']);
		if(x($_POST,'vnotify5'))
			$vnotify += intval($_POST['vnotify5']);
		if(x($_POST,'vnotify6'))
			$vnotify += intval($_POST['vnotify6']);
		if(x($_POST,'vnotify7'))
			$vnotify += intval($_POST['vnotify7']);
		if(x($_POST,'vnotify8'))
			$vnotify += intval($_POST['vnotify8']);
		if(x($_POST,'vnotify9'))
			$vnotify += intval($_POST['vnotify9']);
		if(x($_POST,'vnotify10'))
			$vnotify += intval($_POST['vnotify10']);
		if(x($_POST,'vnotify11') && is_site_admin())
			$vnotify += intval($_POST['vnotify11']);
		if(x($_POST,'vnotify12'))
			$vnotify += intval($_POST['vnotify12']);
		if(x($_POST,'vnotify13'))
			$vnotify += intval($_POST['vnotify13']);
		if(x($_POST,'vnotify14'))
			$vnotify += intval($_POST['vnotify14']);
		if(x($_POST,'vnotify15'))
			$vnotify += intval($_POST['vnotify15']);

		$always_show_in_notices = x($_POST, 'always_show_in_notices') ? 1 : 0;
		$update_notices_per_parent = x($_POST, 'update_notices_per_parent') ? 1 : 0;

		//$err = '';

		//$name_change = false;

		//if($username != $channel['channel_name']) {
			//$name_change = true;
			//require_once('include/channel.php');
			//$err = validate_channelname($username);
			//if($err) {
				//notice($err);
				//return;
			//}
		//}

		if($timezone !== $channel['channel_timezone']) {
			if(strlen($timezone))
				date_default_timezone_set($timezone);
		}

		if($role !== get_pconfig(local_channel(),'system','permissions_role')) {
			$role_permissions = \Zotlabs\Access\PermissionRoles::role_perms($_POST['permissions_role']);
			if(isset($role_permissions['limits'])) {
				foreach($role_permissions['limits'] as $k => $v) {
					\Zotlabs\Access\PermissionLimits::Set(local_channel(),$k,$v);
				}
			}
		}

		set_pconfig(local_channel(),'system','permissions_role',$role);

		set_pconfig(local_channel(),'system','use_browser_location',$allow_location);
		set_pconfig(local_channel(),'system','post_newfriend', $post_newfriend);
		set_pconfig(local_channel(),'system','post_joingroup', $post_joingroup);
		set_pconfig(local_channel(),'system','post_profilechange', $post_profilechange);
		//set_pconfig(local_channel(),'system','blocktags',$blocktags);
		set_pconfig(local_channel(),'system','vnotify',$vnotify);
		set_pconfig(local_channel(),'system','always_show_in_notices',$always_show_in_notices);
		set_pconfig(local_channel(),'system','update_notices_per_parent',$update_notices_per_parent);
		set_pconfig(local_channel(),'system','evdays',$evdays);
		set_pconfig(local_channel(),'system','photo_path',$photo_path);
		set_pconfig(local_channel(),'system','attach_path',$attach_path);
		set_pconfig(local_channel(),'system','email_notify_host',$mailhost);

		$r = q("update channel set channel_pageflags = %d, channel_timezone = '%s',
				channel_location = '%s', channel_notifyflags = %d, channel_max_anon_mail = %d,
				channel_max_friend_req = %d
				where channel_id = %d",
			intval($pageflags),
			dbesc($timezone),
			dbesc($defloc),
			intval($notify),
			intval($unkmail),
			intval($maxreq),
			intval(local_channel())
		);
		if($r)
			info( t('Settings updated.') . EOL);

		//if($name_change) {
			//// change name on all associated xchans by matching the url
			//$r = q("update xchan set xchan_name = '%s', xchan_name_date = '%s' where xchan_url = '%s'",
				//dbesc($username),
				//dbesc(datetime_convert()),
				//dbesc(z_root() . '/channel/' . $channel['channel_address'])
			//);
			//$r = q("update profile set fullname = '%s' where uid = %d and is_default = 1",
				//dbesc($username),
				//intval($channel['channel_id'])
			//);
		//}

		\Zotlabs\Daemon\Master::Summon(array('Directory',local_channel()));

		Libsync::build_sync_packet();


		if($email_changed && \App::$config['system']['register_policy'] == REGISTER_VERIFY) {

			// FIXME - set to un-verified, blocked and redirect to logout
			// Q: Why? Are we verifying people or email addresses?
			// A: the policy is to verify email addresses
		}

		goaway(z_root() . '/settings' );
		return; // NOTREACHED
	}

	function get() {

		$yes_no = array(t('No'),t('Yes'));


		$p = q("SELECT * FROM profile WHERE is_default = 1 AND uid = %d LIMIT 1",
			intval(local_channel())
		);
		if(count($p))
			$profile = $p[0];

		load_pconfig(local_channel(),'expire');

		$channel = \App::get_channel();

		//$username   = $channel['channel_name'];
		$nickname   = $channel['channel_address'];
		$timezone   = $channel['channel_timezone'];
		$notify     = $channel['channel_notifyflags'];
		$defloc     = $channel['channel_location'];

		$maxreq     = $channel['channel_max_friend_req'];
		$expire     = $channel['channel_expire_days'];
		$adult_flag = intval($channel['channel_pageflags'] & PAGE_ADULT);

		$expire_items = get_pconfig(local_channel(), 'expire','items');
		$expire_items = (($expire_items===false)? '1' : $expire_items); // default if not set: 1

		$expire_notes = get_pconfig(local_channel(), 'expire','notes');
		$expire_notes = (($expire_notes===false)? '1' : $expire_notes); // default if not set: 1

		$expire_starred = get_pconfig(local_channel(), 'expire','starred');
		$expire_starred = (($expire_starred===false)? '1' : $expire_starred); // default if not set: 1

		$expire_photos = get_pconfig(local_channel(), 'expire','photos');
		$expire_photos = (($expire_photos===false)? '0' : $expire_photos); // default if not set: 0

		$expire_network_only = get_pconfig(local_channel(), 'expire','network_only');
		$expire_network_only = (($expire_network_only===false)? '0' : $expire_network_only); // default if not set: 0

		$post_newfriend = get_pconfig(local_channel(), 'system','post_newfriend');
		$post_newfriend = (($post_newfriend===false)? '0': $post_newfriend); // default if not set: 0

		$post_joingroup = get_pconfig(local_channel(), 'system','post_joingroup');
		$post_joingroup = (($post_joingroup===false)? '0': $post_joingroup); // default if not set: 0

		$post_profilechange = get_pconfig(local_channel(), 'system','post_profilechange');
		$post_profilechange = (($post_profilechange===false)? '0': $post_profilechange); // default if not set: 0

		//$blocktags  = get_pconfig(local_channel(),'system','blocktags');
		//$blocktags = (($blocktags===false) ? '0' : $blocktags);

		$timezone = date_default_timezone_get();


		$subdir = ((strlen(\App::get_path())) ? '<br />' . t('or') . ' ' . z_root() . '/channel/' . $nickname : '');

		$webbie = $nickname . '@' . \App::get_hostname();
		$intl_nickname = unpunify($nickname) . '@' . unpunify(\App::get_hostname());


		$tpl_addr = get_markup_template("settings_nick_set.tpl");

		$prof_addr = replace_macros($tpl_addr,array(
			'$desc' => t('Your channel address is'),
			'$nickname' => (($intl_nickname === $webbie) ? $webbie : $intl_nickname . '&nbsp;(' . $webbie . ')'),
			'$subdir' => $subdir,
			'$davdesc' => t('Your files/photos are accessible via WebDAV at'),
			'$davpath' => z_root() . '/dav/' . $nickname,
			'$basepath' => \App::get_hostname()
		));


		$evdays = get_pconfig(local_channel(),'system','evdays');
		if(! $evdays)
			$evdays = 3;

		$always_show_in_notices = get_pconfig(local_channel(),'system','always_show_in_notices');
		$update_notices_per_parent = get_pconfig(local_channel(), 'system', 'update_notices_per_parent', 1);
		$vnotify = get_pconfig(local_channel(),'system','vnotify');

		if($vnotify === false)
			$vnotify = (-1);

		$disable_discover_tab = intval(get_config('system','disable_discover_tab',1)) == 1;
		$site_firehose = intval(get_config('system','site_firehose',0)) == 1;

		$permissions_role = get_pconfig(local_channel(),'system','permissions_role');
		if(! $permissions_role)
			$permissions_role = 'custom';

		$perm_roles = \Zotlabs\Access\PermissionRoles::channel_roles();


		$plugin = [ 'basic' => '', 'notify' => '' ];
		call_hooks('channel_settings',$plugin);

		$stpl = get_markup_template('settings.tpl');
		$o .= replace_macros($stpl,array(
			'$ptitle' 	=> t('Channel Settings'),

			'$submit' 	=> t('Submit'),
			'$baseurl' => z_root(),
			'$uid' => local_channel(),
			'$form_security_token' => get_form_security_token("settings"),
			'$role' => array('permissions_role' , t('Channel role'), $permissions_role, '', $perm_roles),

			'$nickname_block' => $prof_addr,
			'$h_basic' 	=> t('Basic Settings'),
			//'$username' => array('username',  t('Full Name:'), $username,''),
			'$email' 	=> array('email', t('Email Address:'), $email, ''),
			'$timezone' => array('timezone_select' , t('Your Timezone:'), $timezone, '', get_timezones()),
			'$defloc'	=> array('defloc', t('Default Post Location:'), $defloc, t('Geographical location to display on your posts')),
			'$allowloc' => array('allow_location', t('Use Browser Location:'), ((get_pconfig(local_channel(),'system','use_browser_location')) ? 1 : ''), '', $yes_no),

			'$adult'    => array('adult', t('Adult Content'), $adult_flag, t('This channel frequently or regularly publishes adult content. (Please tag any adult material and/or nudity with #NSFW)'), $yes_no),

			//'$blocktags' => array('blocktags',t('Allow others to tag your posts'), 1-$blocktags, t('Often used by the community to retro-actively flag inappropriate content'), $yes_no),
			'$maxreq' 	=> array('maxreq', t('Maximum Friend Requests/Day:'), intval($channel['channel_max_friend_req']) , t('May reduce spam activity')),

			'$h_not' 	=> t('Notification Settings'),
			'$activity_options' => t('By default post a status message when:'),
			'$post_newfriend' => array('post_newfriend',  t('accepting a friend request'), $post_newfriend, '', $yes_no),
			'$post_joingroup' => array('post_joingroup',  t('joining a forum/community'), $post_joingroup, '', $yes_no),
			'$post_profilechange' => array('post_profilechange',  t('making an <em>interesting</em> profile change'), $post_profilechange, '', $yes_no),
			'$lbl_not' 	=> t('Send a notification email when:'),
			'$notify1'	=> array('notify1', t('You receive a connection request'), ($notify & NOTIFY_INTRO), NOTIFY_INTRO, '', $yes_no),
			'$notify2'	=> array('notify2', t('Your connections are confirmed'), ($notify & NOTIFY_CONFIRM), NOTIFY_CONFIRM, '', $yes_no),
			'$notify3'	=> array('notify3', t('Someone writes on your profile wall'), ($notify & NOTIFY_WALL), NOTIFY_WALL, '', $yes_no),
			'$notify4'	=> array('notify4', t('Someone writes a followup comment'), ($notify & NOTIFY_COMMENT), NOTIFY_COMMENT, '', $yes_no),
			'$notify5'	=> array('notify5', t('You receive a private message'), ($notify & NOTIFY_MAIL), NOTIFY_MAIL, '', $yes_no),
			'$notify6'  => array('notify6', t('You receive a friend suggestion'), ($notify & NOTIFY_SUGGEST), NOTIFY_SUGGEST, '', $yes_no),
			'$notify7'  => array('notify7', t('You are tagged in a post'), ($notify & NOTIFY_TAGSELF), NOTIFY_TAGSELF, '', $yes_no),
			'$notify8'  => array('notify8', t('You are poked/prodded/etc. in a post'), ($notify & NOTIFY_POKE), NOTIFY_POKE, '', $yes_no),

			'$notify9'  => array('notify9', t('Someone likes your post/comment'), ($notify & NOTIFY_LIKE), NOTIFY_LIKE, '', $yes_no),


			'$lbl_vnot' 	=> t('Show visual notifications including:'),

			'$vnotify1'	=> array('vnotify1', t('Unseen stream activity'), ($vnotify & VNOTIFY_NETWORK), VNOTIFY_NETWORK, '', $yes_no),
			'$vnotify2'	=> array('vnotify2', t('Unseen channel activity'), ($vnotify & VNOTIFY_CHANNEL), VNOTIFY_CHANNEL, '', $yes_no),
			'$vnotify3'	=> array('vnotify3', t('Unseen private messages'), ($vnotify & VNOTIFY_MAIL), VNOTIFY_MAIL, t('Recommended'), $yes_no),
			'$vnotify4'	=> array('vnotify4', t('Upcoming events'), ($vnotify & VNOTIFY_EVENT), VNOTIFY_EVENT, '', $yes_no),
			'$vnotify5'	=> array('vnotify5', t('Events today'), ($vnotify & VNOTIFY_EVENTTODAY), VNOTIFY_EVENTTODAY, '', $yes_no),
			'$vnotify6'  => array('vnotify6', t('Upcoming birthdays'), ($vnotify & VNOTIFY_BIRTHDAY), VNOTIFY_BIRTHDAY, t('Not available in all themes'), $yes_no),
			'$vnotify7'  => array('vnotify7', t('System (personal) notifications'), ($vnotify & VNOTIFY_SYSTEM), VNOTIFY_SYSTEM, '', $yes_no),
			'$vnotify8'  => array('vnotify8', t('System info messages'), ($vnotify & VNOTIFY_INFO), VNOTIFY_INFO, t('Recommended'), $yes_no),
			'$vnotify9'  => array('vnotify9', t('System critical alerts'), ($vnotify & VNOTIFY_ALERT), VNOTIFY_ALERT, t('Recommended'), $yes_no),
			'$vnotify10'  => array('vnotify10', t('New connections'), ($vnotify & VNOTIFY_INTRO), VNOTIFY_INTRO, t('Recommended'), $yes_no),
			'$vnotify11'  => ((is_site_admin()) ? array('vnotify11', t('System Registrations'), ($vnotify & VNOTIFY_REGISTER), VNOTIFY_REGISTER, '', $yes_no) : array()),
			'$vnotify12'  => array('vnotify12', t('Unseen shared files'), ($vnotify & VNOTIFY_FILES), VNOTIFY_FILES, '', $yes_no),
			'$vnotify13'  => ((($disable_discover_tab && !$site_firehose) || !Apps::system_app_installed(local_channel(), 'Public Stream')) ? array() : array('vnotify13', t('Unseen public stream activity'), ($vnotify & VNOTIFY_PUBS), VNOTIFY_PUBS, '', $yes_no)),
			'$vnotify14'	=> array('vnotify14', t('Unseen likes and dislikes'), ($vnotify & VNOTIFY_LIKE), VNOTIFY_LIKE, '', $yes_no),
			'$vnotify15'	=> array('vnotify15', t('Unseen forum posts'), ($vnotify & VNOTIFY_FORUMS), VNOTIFY_FORUMS, '', $yes_no),
			'$mailhost' => [ 'mailhost', t('Email notification hub (hostname)'), get_pconfig(local_channel(),'system','email_notify_host',\App::get_hostname()), sprintf( t('If your channel is mirrored to multiple hubs, set this to your preferred location. This will prevent duplicate email notifications. Example: %s'),\App::get_hostname()) ],
			'$always_show_in_notices'  => array('always_show_in_notices', t('Show new wall posts, private messages and connections under Notices'), $always_show_in_notices, 1, '', $yes_no),
			'$update_notices_per_parent'  => array('update_notices_per_parent', t('Mark all notices of the thread read if a notice is clicked'), $update_notices_per_parent, 1, t('If no, only the clicked notice will be marked read'), $yes_no),
			'$desktop_notifications_info' => t('Desktop notifications are unavailable because the required browser permission has not been granted'),
			'$desktop_notifications_request' => t('Grant permission'),
			'$evdays' => array('evdays', t('Notify me of events this many days in advance'), $evdays, t('Must be greater than 0')),
			'$basic_addon' => $plugin['basic'],
			'$notify_addon' => $plugin['notify'],

			'$photo_path' => array('photo_path', t('Default photo upload folder'), get_pconfig(local_channel(),'system','photo_path'), t('%Y - current year, %m -  current month')),
			'$attach_path' => array('attach_path', t('Default file upload folder'), get_pconfig(local_channel(),'system','attach_path'), t('%Y - current year, %m -  current month')),
			'$removeme' => t('Remove Channel'),
			'$removechannel' => t('Remove this channel.'),
		));

		call_hooks('settings_form',$o);

		return $o;
	}
}
