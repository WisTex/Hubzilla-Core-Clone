<?php

namespace Zotlabs\Update;

class _1249 {

	function run() {

		q("START TRANSACTION");

		if(ACTIVE_DBTYPE == DBTYPE_POSTGRES) {
			$r1 = q("ALTER TABLE abook ADD abook_role TEXT NOT NULL DEFAULT ''");
			$r2 = q("CREATE INDEX \"abook_role\" ON abook (\"abook_role\")");
			$r = ($r1 && $r2);
		}
		else {
			$r = q("ALTER TABLE `abook` ADD `abook_role` CHAR(191) NOT NULL DEFAULT '' ,
				ADD INDEX `abook_role` (`abook_role`)");
		}

		if($r) {
			q("COMMIT");
			return UPDATE_SUCCESS;
		}

		q("ROLLBACK");
		return UPDATE_FAILED;

	}

}
