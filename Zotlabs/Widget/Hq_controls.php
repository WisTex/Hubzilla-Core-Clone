<?php

namespace Zotlabs\Widget;

class Hq_controls {

	function widget($arr) {

		if (! local_channel())
			return;

		return replace_macros(get_markup_template('hq_controls.tpl'),
			[
				'$menu' => [
					'create' => [
						'label' => t('Create a new post'),
						'id' => 'jot-toggle',
						'href' => '#',
						'class' => 'btn btn-success',
						'type' => 'button',
						'icon' => 'pencil-square-o'
					]
				]
			]
		);
	}
}
