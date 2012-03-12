<?php
/*
 * 404 Manager Global Functions
 *
*/

function wbz404_load_translations() {
	$trans_path = WBZ404_PATH . '/translations';
	load_plugin_textdomain(WBZ404_TRANS, '', $trans_path);
}

function wbz404_trans($text = '') {
	return __($text, WBZ404_TRANS);
}

function wbz404_getOptions() {
	$options = get_option('wbz404_settings');

	if ($options == "") {
		add_option('wbz404_settings','','','no');
	}

	//Check to make sure we aren't missing any new options
	$defaults = wbz404_getDefaultOptions();
	$missing = 0;
	$keys = array_keys($defaults);
	for ($i=0; $i < count($keys); $i++) {
		$key = $keys[$i];
		if ($options[$key] == "") {
			$options[$key] = $defaults[$key];
			$missing++;
		}
	}

	if ($missing != 0) {
		if (function_exists('update_option')) {
			update_option('wbz404_settings', $options);
		}
	}

	return $options;
}

function wbz404_getDefaultOptions() {
	$options = array(
		'default_redirect' => '301',
		'capture_404' => '1',
		'capture_deletion' => 1095,
		'manual_deletion' => '0',
		'admin_notification' => '200',
		'remove_matches' => '1',
		'display_suggest' => '1',
		'suggest_minscore' => '25',
		'suggest_max' => '5',
		'suggest_title' => '<h3>Suggested Alternatives</h3>',
		'suggest_before' => '<ol>',
		'suggest_after' => '</ol>',
		'suggest_entrybefore' => '<li>',
		'suggest_entryafter' => '</li>',
		'suggest_noresults' => '<p>No Results To Display.</p>',
		'suggest_cats' => '1',
		'suggest_tags' => '1',
		'auto_redirects' => '1',
		'auto_score' => '90',
		'auto_deletion' => '1095',
		'auto_cats' => '1',
		'auto_tags' => '1',
		'force_permalinks' => '1',
		'404_promote' => '1'
	);
	return $options;
}

function wbz404_pluginActivation() {
	global $wpdb;
	add_option('wbz404_settings','','','no');

        $charset_collate = '';
        if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') ) {
                if (!empty($wpdb->charset)) {
                        $charset_collate .= " DEFAULT CHARACTER SET $wpdb->charset";
                }
                if (!empty($wpdb->collate)) {
                        $charset_collate .= " COLLATE $wpdb->collate";
                }
        }

	$query = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "wbz404_redirects` (
	  `id` bigint(30) NOT NULL auto_increment,
	  `url` varchar(512) NOT NULL,
	  `status` bigint(20) NOT NULL,
	  `type` bigint(20) NOT NULL,
	  `final_dest` varchar(512) NOT NULL,
	  `code` bigint(20) NOT NULL,
	  `disabled` int(10) NOT NULL default '0',
	  `timestamp` bigint(30) NOT NULL,
	  PRIMARY KEY  (`id`),
	  KEY `status` (`status`),
	  KEY `type` (`type`),
	  KEY `code` (`code`),
	  KEY `timestamp` (`timestamp`),
	  KEY `disabled` (`disabled`),
	  FULLTEXT KEY `url` (`url`),
	  FULLTEXT KEY `final_dest` (`final_dest`)
	) ENGINE=MyISAM " . $charset_collate . " COMMENT='404 Redirected Plugin Redirects Table' AUTO_INCREMENT=1";
	$wpdb->query($query);

	$query = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "wbz404_logs` (
	  `id` bigint(40) NOT NULL auto_increment,
	  `redirect_id` bigint(40) NOT NULL,
	  `timestamp` bigint(40) NOT NULL,
	  `remote_host` varchar(512) NOT NULL,
	  `referrer` varchar(512) NOT NULL,
	  `action` varchar(512) NOT NULL,
	  PRIMARY KEY  (`id`),
	  KEY `redirect_id` (`redirect_id`),
	  KEY `timestamp` (`timestamp`)
	) ENGINE=MyISAM " . $charset_collate . " COMMENT='404 Redirected Plugin Logs Table' AUTO_INCREMENT=1";
	$wpdb->query($query);

	$now = time();
	wp_schedule_event($now + 86400, 'daily', 'wbz404_cleaningCron');
	wp_schedule_event($now + 3600, 'hourly', 'wbz404_removeDuplicatesCron');
}

function wbz404_pluginRemove() {
	delete_option('wbz404_settings');
	wp_clear_scheduled_hook('wbz404_cleaningCron');
	wp_clear_scheduled_hook('wbz404_removeDuplicatesCron');
}

function wbz404_rankPermalinks($url, $includeCats = '1', $includeTags = '1') {
        global $wpdb;
        $permalinks = array();

        $query = "select id from $wpdb->posts where post_status='publish' and (post_type='page' or post_type='post')";
        $rows = $wpdb->get_results($query);
        foreach ($rows as $row) {
                $id = $row->id;
                $the_permalink = get_permalink($id);
                $urlParts = parse_url($the_permalink);
                $scoreBasis = strlen($urlParts['path']);
                $levscore = levenshtein($url, $urlParts['path'],1,1,1);
                $score = 100 - (($levscore / $scoreBasis)*100);
                $permalinks[$id . "|POST"] = number_format($score,4,'.','');
        }

        if ($includeTags == "1") {
                $query = "select " . $wpdb->terms . ".term_id from " . $wpdb->terms . " ";
                $query .= "left outer join " . $wpdb->term_taxonomy . " on " . $wpdb->terms . ".term_id = " . $wpdb->term_taxonomy . ".term_id ";
                $query .= "where " . $wpdb->term_taxonomy . ".taxonomy='post_tag' and " . $wpdb->term_taxonomy . ".count >= 1";
                $rows = $wpdb->get_results($query);
                foreach ($rows as $row) {
                        $id = $row->term_id;
                        $the_permalink = get_tag_link($id);
                        $urlParts = parse_url($the_permalink);
                        $scoreBasis = strlen($urlParts['path']);
                        $levscore = levenshtein($url, $urlParts['path'],1,1,1);
                        $score = 100 - (($levscore / $scoreBasis)*100);
                        $permalinks[$id . "|TAG"] = number_format($score,4,'.','');
                }
        }

        if ($includeCats == "1") {
                $query = "select " . $wpdb->terms . ".term_id from " . $wpdb->terms . " ";
                $query .= "left outer join " . $wpdb->term_taxonomy . " on " . $wpdb->terms . ".term_id = " . $wpdb->term_taxonomy . ".term_id ";
                $query .= "where " . $wpdb->term_taxonomy . ".taxonomy='category' and " . $wpdb->term_taxonomy . ".count >= 1";
                $rows = $wpdb->get_results($query);
                foreach ($rows as $row) {
                        $id = $row->term_id;
                        $the_permalink = get_category_link($id);
                        $urlParts = parse_url($the_permalink);
                        $scoreBasis = strlen($urlParts['path']);
                        $levscore = levenshtein($url, $urlParts['path'],1,1,1);
                        $score = 100 - (($levscore / $scoreBasis)*100);
                        $permalinks[$id . "|CAT"] = number_format($score,4,'.','');
                }
        }

        arsort($permalinks);
        return $permalinks;
}

function wbz404_permalinkInfo($k, $v) {
	$permalink = array();

	$meta = $k;
        $meta = explode("|",$meta);

        $permalink['id'] = $meta[0];
        $permalink['type'] = $meta[1];
        $permalink['score'] = $v;

	if ($permalink['type'] == "POST") {
		$permalink['link'] = get_permalink($permalink['id']);
		$permalink['title'] = get_the_title($permalink['id']);
	} else if ($permalink['type'] == "TAG") {
		$permalink['link'] = get_tag_link($permalink['id']);
		$tag = get_term($permalink['id'], 'post_tag');
		$permalink['title'] = $tag->name;
	} else if ($permalink['type'] == "CAT") {
		$permalink['link'] = get_category_link($permalink['id']);
		$cat = get_term($permalink['id'], 'category');
		$permalink['title'] = $cat->name;
	}

	return $permalink;
}


function wbz404_loadRedirectData($url) {
        global $wpdb;
        $redirect = array();

        $query="select * from " . $wpdb->prefix . "wbz404_redirects where url = '" . $wpdb->escape($url) . "'";
        $row = $wpdb->get_row($query, ARRAY_A);
        if ($row == NULL) {
                $redirect[id]=0;
        } else {
                $redirect['id'] = $row['id'];
                $redirect['url'] = $row['url'];
                $redirect['status'] = $row['status'];
                $redirect['type'] = $row['type'];
                $redirect['final_dest'] = $row['final_dest'];
		$redirect['code'] = $row['code'];
		$redirect['disabled'] = $row['disabled'];
                $redirect['created'] = $row['timestamp'];
        }
        return $redirect;
}

function wbz404_setupRedirect($url, $status, $type, $final_dest, $code, $disabled = 0) {
	global $wpdb;

	$now = time();
	$wpdb->insert($wpdb->prefix . 'wbz404_redirects', 
		array(
			'url' => $url,
			'status' => $status,
			'type' => $type,
			'final_dest' => $final_dest,
			'code' => $code,
			'disabled' => $disabled,
			'timestamp' => $now
		),
		array(
			'%s',
			'%d',
			'%d',
			'%s',
			'%d',
			'%d',
			'%d'
		)
	);
	return $wpdb->insert_id;	
}

function wbz404_logRedirectHit($id, $action) {
	global $wpdb;
	$now = time();
	
	$wpdb->insert($wpdb->prefix . "wbz404_logs",
		array(
			'redirect_id' => $id,
			'timestamp' => $now,
			'remote_host' => $_SERVER['REMOTE_ADDR'],
			'referrer' => $_SERVER['HTTP_REFERER'],
			'action' => $action,
		),
		array(
			'%d',
			'%d',
			'%s',
			'%s',
			'%s'
		)
	);
}

function wbz404_cleanRedirect($id) {
	global $wpdb;
	if ($id != "" && $id != '0') {
		$query="delete from " . $wpdb->prefix . "wbz404_redirects where id = " . $wpdb->escape($id);
		$wpdb->query($query);
		$query="delete from " . $wpdb->prefix . "wbz404_logs where redirect_id = " . $wpdb->escape($id);
		$wpdb->query($query);
	}
}

function wbz404_cleaningCron() {
	global $wpdb;
	$options = wbz404_getOptions();
	$now = time();

	//Remove Captured URLs
	if ($options['capture_deletion'] != '0') {
		$capture_time = $options['capture_deletion'] * 86400;
		$then = $now - $capture_time;

		//Clean up old logs
		$query = "delete from " . $wpdb->prefix . "wbz404_logs where ";
		$query .= "redirect_id in (select id from " . $wpdb->prefix . "wbz404_redirects where status = " . $wpdb->escape(WBZ404_CAPTURED) . " or status = " . $wpdb->escape(WBZ404_IGNORED) . ") ";
		$query .= "and timestamp < " . $wpdb->escape($then);
		$wpdb->query($query);

		//Find unused urls
		$query = "select id from " . $wpdb->prefix . "wbz404_redirects where (status = " . $wpdb->escape(WBZ404_CAPTURED) . " or status = " . $wpdb->escape(WBZ404_IGNORED) . ") and ";
		$query .= "timestamp <= " . $wpdb->escape($then) . " and id not in (";
		$query .= "select redirect_id from " . $wpdb->prefix . "wbz404_logs";
		$query .= ")";
		$rows = $wpdb->get_results($query, ARRAY_A);
		foreach ($rows as $row) {
			//Remove Them
			wbz404_cleanRedirect($row['id']);
		}
	}

	//Remove Automatic Redirects
	if ($options['auto_deletion'] != '0') {
		$auto_time = $options['auto_deletion'] * 86400;
		$then = $now - $auto_time;

		//Clean up old logs
		$query = "delete from " . $wpdb->prefix . "wbz404_logs where ";
		$query .= "redirect_id in (select id from " . $wpdb->prefix . "wbz404_redirects where status = " . $wpdb->escape(WBZ404_AUTO) . ") ";
		$query .= "and timestamp < " . $wpdb->escape($then);
		$wpdb->query($query);

		//Find unused urls
		$query = "select id from " . $wpdb->prefix . "wbz404_redirects where status = " . $wpdb->escape(WBZ404_AUTO) . " status ";
		$query .= "timestamp <= " . $wpdb->escape($then) . " and id not in (";
		$query .= "select redirect_id from " . $wpdb->prefix . "wbz404_logs";
		$query .= ")";
		$rows = $wpdb->get_results($query, ARRAY_A);
		foreach ($rows as $row) {
			//Remove Them
			wbz404_cleanRedirect($row['id']);
		}
	}

	//Remove Manual Redirects
	if ($options['manual_deletion'] != '0') {
		$manual_time = $options['manual_deletion'] * 86400;
		$then = $now - $manual_time;

		//Clean up old logs
		$query = "delete from " . $wpdb->prefix . "wbz404_logs where ";
		$query .= "redirect_id in (select id from " . $wpdb->prefix . "wbz404_redirects where status = " . $wpdb->escape(WBZ404_MANUAL) . ") ";
		$query .= "and timestamp < " . $wpdb->escape($then);
		$wpdb->query($query);

		//Find unused urls
		$query = "select id from " . $wpdb->prefix . "wbz404_redirects where status = " . $wpdb->escape(WBZ404_MANUAL) . " and ";
		$query .= "timestamp <= " . $wpdb->escape($then) . " and id not in (";
		$query .= "select redirect_id from " . $wpdb->prefix . "wbz404_logs";
		$query .= ")";
		$rows = $wpdb->get_results($query, ARRAY_A);
		foreach ($rows as $row) {
			//Remove Them
			wbz404_cleanRedirect($row['id']);
		}
	}
}

function wbz404_removeDuplicatesCron() {
	global $wpdb;
	
	$rtable = $wpdb->prefix . "wbz404_redirects";
	$ltable = $wpdb->prefix . "wbz404_logs";
	
	$query = "SELECT COUNT(id) as repetitions, url FROM " . $rtable . " GROUP BY url HAVING repetitions > 1";
	$rows = $wpdb->get_results($query, ARRAY_A);
	foreach ($rows as $row) {
		$url = $row['url'];
		
		$query2 = "select id from " . $rtable . " where url = '" . $wpdb->escape($url) . "' order by id limit 0,1";
		$orig = $wpdb->get_row($query2, ARRAY_A, 0);
		if ($orig['id'] != 0) {
			$original = $orig['id'];
			
			//Fix the logs table
			$query2 = "update " . $ltable . " set redirect_id = " . $wpdb->escape($original) . " where redirect_id in (select id from " . $rtable . " where url = '" . $wpdb->escape($url) . "' and id != " . $wpdb->escape($original) . ")";
			$wpdb->query($query2);

			$query2 = "delete from " . $rtable . " where url='" . $wpdb->escape($url) . "' and id != " . $wpdb->escape($original);
			$wpdb->query($query2);

		}
	}

}

register_activation_hook(WBZ404_NAME,'wbz404_pluginActivation');
register_deactivation_hook(WBZ404_NAME,'wbz404_pluginRemove');
add_action('init', 'wbz404_load_translations');
