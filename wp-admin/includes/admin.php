<?php
/**
 * Core Administration API
 *
 * @package CMS
 * @subpackage Administration
 * @since 2.3.0
 */

if ( ! defined('WP_ADMIN') ) {
	/*
	 * This file is being included from a file other than wp-admin/admin.php, so
	 * some setup was skipped. Make sure the admin message catalog is loaded since
	 * load_default_textdomain() will not have done so in this context.
	 */
	load_textdomain( 'default', WP_LANG_DIR . '/admin-' . get_locale() . '.mo' );
}

/** CMS Administration Hooks */
require_once(ABSPATH . 'wp-admin/includes/admin-filters.php');

/** CMS Bookmark Administration API */
require_once(ABSPATH . 'wp-admin/includes/bookmark.php');

/** CMS Comment Administration API */
require_once(ABSPATH . 'wp-admin/includes/comment.php');

/** CMS Administration File API */
require_once(ABSPATH . 'wp-admin/includes/file.php');

/** CMS Image Administration API */
require_once(ABSPATH . 'wp-admin/includes/image.php');

/** CMS Media Administration API */
require_once(ABSPATH . 'wp-admin/includes/media.php');

/** CMS Import Administration API */
require_once(ABSPATH . 'wp-admin/includes/import.php');

/** CMS Misc Administration API */
require_once(ABSPATH . 'wp-admin/includes/misc.php');

/** CMS Options Administration API */
require_once(ABSPATH . 'wp-admin/includes/options.php');

/** CMS Plugin Administration API */
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

/** CMS Post Administration API */
require_once(ABSPATH . 'wp-admin/includes/post.php');

/** CMS Administration Screen API */
require_once(ABSPATH . 'wp-admin/includes/class-wp-screen.php');
require_once(ABSPATH . 'wp-admin/includes/screen.php');

/** CMS Taxonomy Administration API */
require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');

/** CMS Template Administration API */
require_once(ABSPATH . 'wp-admin/includes/template.php');

/** CMS List Table Administration API and base class */
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table-compat.php');
require_once(ABSPATH . 'wp-admin/includes/list-table.php');

/** CMS Theme Administration API */
require_once(ABSPATH . 'wp-admin/includes/theme.php');

/** CMS User Administration API */
require_once(ABSPATH . 'wp-admin/includes/user.php');

/** CMS Site Icon API */
require_once(ABSPATH . 'wp-admin/includes/class-wp-site-icon.php');

/** CMS Update Administration API */
require_once(ABSPATH . 'wp-admin/includes/update.php');

/** CMS Deprecated Administration API */
require_once(ABSPATH . 'wp-admin/includes/deprecated.php');

/** CMS Multisite support API */
if ( is_multisite() ) {
	require_once(ABSPATH . 'wp-admin/includes/ms-admin-filters.php');
	require_once(ABSPATH . 'wp-admin/includes/ms.php');
	require_once(ABSPATH . 'wp-admin/includes/ms-deprecated.php');
}
