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
						'label' => t('Toggle post editor'),
						'id' => 'jot-toggle',
						'href' => '#',
						'class' => 'btn btn-link',
						'type' => 'button',
						'icon' => ''
					],
					'notes' => [
						'label' => t('Toggle personal notes'),
						'id' => 'notes-toggle',
						'href' => '#',
						'class' => 'btn btn-link',
						'type' => 'button',
						'icon' => ''
					],
				]
			]
		);
	}
}
