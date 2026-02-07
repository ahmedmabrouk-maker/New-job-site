<?php

/**
 * Fired during plugin deactivation
 */
class Jobs_Deactivator {

	public static function deactivate() {
		// Do not delete pages on deactivation, only on uninstall.
        // Flush rewrite rules if necessary.
        flush_rewrite_rules();
	}

}
