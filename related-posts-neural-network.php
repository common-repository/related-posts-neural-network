<?php
/*
* Plugin Name: Related Posts Neural Network
* Version: 0.0.4
* Description: A custom Neural Network (Artificial Intelligence) which will learn from your visitors and recommend content on your site based on what they visit. Just install, set up any URL rules and turn it on! Insert recommendations in your pages using the shortcode [rpnnrecommend] or [rpnnrecommend total="3" thumbnails="true" class="cssname" title="You might like..."] where total is the number of recommended links to show.
* Plugin URI: https://www.neiltking.com/neuralnet/
* Author: Neil T King
* Author URI: https://www.neiltking.com
* Text Domain: related-posts-neural-network
* Domain Path: /languages
* License: Apache-2.0
* License URL: http://www.apache.org/licenses/LICENSE-2.0
*/
/*
Copyright 2024+ Neil T King
The plugin offers most features for free but with certain limitations. For a small fee, all of its features
can be unlocked and used on a single WordPress website. Due to the enormous amounts of customisation and
differences between WordPress sites and other plugins, the author accepts no liability or guarantee with this
plugin. The author wants this plugin to work with as many websites as possible and for all users to be very happy
with it but by downloading, installing or activating it you agree that you use it at your own risk. Always backup
your entire site (including databases) before installing new software or plugins.

Features:
+ Works by URL so it will work with all content types in all languages which users may visit, e.g. articles, products, pages.
+ No visitor statistics or data leaves your website. Nothing is processed externally. Everything is done locally.
+ No identifiable information is collected from your visitors to help it abide by GDPR and privacy rules.
+ Easy to install and set up for pretty much any WordPress site.
+ Works with the Gutenberg Block editor and Classic editor, and should work with any theme.
+ PRO version (registration required) will:
  + Increase the neural net beyond 30 synapses/paths, to an unlimited size.
  + Network graphs increase from 10 nodes to 500, and from 30 links/synapses to 1500.
  + Ability to manually adjust the weight/prominence of links between urls.
  + Automatically download a block list of search engine bots/crawlers.
  + Automatically download a block list of bad IP addresses.

The neural network is based on the model in my book, "Make Independent Computer Games" available from Amazon:
https://www.amazon.co.uk/dp/B0CNMFHZ6Z
https://www.amazon.com/dp/B0CNMFHZ6Z

v0.0.4 - 18 September 2024
+ Added fixes to help it work with server-side caching which prevented some page visits being recognised.
+ Fixed duplicate bar graph labels due to changes in newest version of Chart.js.
v0.0.3 - 23 August 2024
+ WordPress translation support added.
+ Chart.js updated to latest version (4.4.4).
+ Links to chart.js and vis-network.js source code added.
+ Inline javascript now queued.
+ Extra sanitization of user settings added.
+ Shortcode changed from [recommend] to [rpnnrecommend] to avoid conflictions.
+ Information on API call added to README.
+ Multiple changes to abide by WordPress.org publication rules.
v0.0.2 Beta - 23 July 2024
+ Added total URLs visited to statistics tab.
+ Added database disk space usage to statistics tab.
+ Search box added for URLs on statistics tab.
+ Ability to delete all references to a URL in the neural net.
*/
if ( ! defined( 'ABSPATH' ) ) exit;

define('NTKRPNN_NEURALNET_API', 'https://www.neiltking.com/neuralnet/');

register_activation_hook( __FILE__, 'ntkrpnn_neuralnet_install' );
register_deactivation_hook( __FILE__, 'ntkrpnn_neuralnet_uninstall' );
add_action( 'ntkrpnn_neuralnet_cronjob', 'ntkrpnn_neuralnet_cleanup' );
add_action( 'wp_head', 'ntkrpnn_neuralnet_head' );
add_action( 'admin_menu', 'ntkrpnn_neuralnet_adminmenu' );
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'ntkrpnn_neuralnet_settings_link' );
add_action( 'init', 'ntkrpnn_neuralnet_setup' );

function ntkrpnn_neuralnet_install() {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    // UPGRADE FROM OLD OPTION NAMES - only needed for beta testers
    if (get_option( 'ntk_neuralnet_learningon' ) !== false) {
        $opt_temp = get_option( 'ntk_neuralnet_siteid' );
        update_option( 'ntkrpnn_neuralnet_siteid', $opt_temp );
        delete_option( 'ntk_neuralnet_siteid' );
        $opt_temp = get_option( 'ntk_neuralnet_pro' );
        update_option( 'ntkrpnn_neuralnet_pro', $opt_temp );
        delete_option( 'ntk_neuralnet_pro' );
        $opt_temp = get_option( 'ntk_neuralnet_learningon' );
        update_option( 'ntkrpnn_neuralnet_learningon', $opt_temp );
        delete_option( 'ntk_neuralnet_learningon' );
        $opt_temp = get_option( 'ntk_neuralnet_deleteold' );
        update_option( 'ntkrpnn_neuralnet_deleteold', $opt_temp );
        delete_option( 'ntk_neuralnet_deleteold' );
        $opt_temp = get_option( 'ntk_neuralnet_urlmusthave' );
        update_option( 'ntkrpnn_neuralnet_urlmusthave', $opt_temp );
        delete_option( 'ntk_neuralnet_urlmusthave' );
        $opt_temp = get_option( 'ntk_neuralnet_urlmustnot' );
        update_option( 'ntkrpnn_neuralnet_urlmustnot', $opt_temp );
        delete_option( 'ntk_neuralnet_urlmustnot' );
        $opt_temp = get_option( 'ntk_neuralnet_stripget' );
        update_option( 'ntkrpnn_neuralnet_stripget', $opt_temp );
        delete_option( 'ntk_neuralnet_stripget' );
        $opt_temp = get_option( 'ntk_neuralnet_removeget' );
        update_option( 'ntkrpnn_neuralnet_removeget', $opt_temp );
        delete_option( 'ntk_neuralnet_removeget' );
        $opt_temp = get_option( 'ntk_neuralnet_debug' );
        update_option( 'ntkrpnn_neuralnet_debug', $opt_temp );
        delete_option( 'ntk_neuralnet_debug' );
        $opt_temp = get_option( 'ntk_neuralnet_noadmin' );
        update_option( 'ntkrpnn_neuralnet_noadmin', $opt_temp );
        delete_option( 'ntk_neuralnet_noadmin' );
        $opt_temp = get_option( 'ntk_neuralnet_maximumweight' );
        update_option( 'ntkrpnn_neuralnet_maximumweight', $opt_temp );
        delete_option( 'ntk_neuralnet_maximumweight' );
        $opt_temp = get_option( 'ntk_neuralnet_maximumscore' );
        update_option( 'ntkrpnn_neuralnet_maximumscore', $opt_temp );
        delete_option( 'ntk_neuralnet_maximumscore' );
    }
    // END OF UPGRADE CHECK
    // Generate visitor session table
	$table_name = $wpdb->prefix . "ntk_neuralnetsession";
	$sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        sessionid varchar(100),
        timeadded int(11) UNSIGNED,
        urls text,
        PRIMARY KEY  (id)
        );";
	dbDelta( $sql );
    // Generate neural net table
	$table_name = $wpdb->prefix . "ntk_neuralnet";
	$sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        url1 varchar(255),
        post1 bigint(20),
        url2 varchar(255),
        post2 bigint(20),
        weight int(11),
        timeupdated int(11) UNSIGNED,
        PRIMARY KEY  (id)
        );";
	dbDelta( $sql );
    // Generate visitor count table
	$table_name = $wpdb->prefix . "ntk_neuralnetcount";
	$sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        url varchar(255),
        postid bigint(20),
        score int(11) UNSIGNED,
        timeupdated int(11) UNSIGNED,
        PRIMARY KEY  (id)
        );";
	dbDelta( $sql );
    // If there isn't one, get a unique ID and store it for this site
    $uniqid = get_option( 'ntkrpnn_neuralnet_siteid' );
    if (trim($uniqid) == "") {
        $apiurl = NTKRPNN_NEURALNET_API . "api.php?ntkid=".urlencode($uniqid)."&ntkh=".urlencode(strtolower($_SERVER['HTTP_HOST']));
        $apiraw = @file_get_contents($apiurl);
        if ($apiraw !== false) {
            $apidata = json_decode($apiraw, true);
            update_option( 'ntkrpnn_neuralnet_siteid', $apidata['id'] );
        } else {
            $apidata['pro'] = "N";
        }
        update_option( 'ntkrpnn_neuralnet_pro', $apidata['pro'] );
    }
    wp_schedule_event( strtotime("+2 minutes"), 'twicedaily', 'ntkrpnn_neuralnet_cronjob' ); // strtotime("+10 minutes"), 'hourly'
}

function ntkrpnn_neuralnet_uninstall() {
    wp_clear_scheduled_hook( 'ntkrpnn_neuralnet_cronjob' );
    delete_option( 'ntkrpnn_neuralnet_siteid' );
    delete_option( 'ntkrpnn_neuralnet_pro' );
}

function ntkrpnn_neuralnet_cleanup() {
	global $wpdb;
    $table_session = $wpdb->prefix . "ntk_neuralnetsession";
    $table_synapse = $wpdb->prefix . "ntk_neuralnet";
    $table_count = $wpdb->prefix . "ntk_neuralnetcount";
    $wpdb->query($wpdb->prepare("DELETE FROM `".$table_session."` WHERE (timeadded < %d);", array(time()-3600))); // 1 hour
    if (get_option( 'ntkrpnn_neuralnet_deleteold' ) == "Y") {
        $ntk_urlcounts = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$table_count."` WHERE (timeupdated < %d);", array(strtotime("-1 year")))); // 1 year old
        foreach ($ntk_urlcounts AS $ntk_urlcount) {
            $wpdb->query($wpdb->prepare("DELETE FROM `".$table_synapse."` WHERE ((url1 = %s) OR (url2 = %s));", array($ntk_urlcount->url,$ntk_urlcount->url)));
        }
        $wpdb->query($wpdb->prepare("DELETE FROM `".$table_count."` WHERE (timeupdated < %d);", array(strtotime("-1 year"))));
    }
    $uniqid = get_option( 'ntkrpnn_neuralnet_siteid' );
    $apiurl = NTKRPNN_NEURALNET_API . "api.php?ntkid=".urlencode($uniqid)."&ntkh=".urlencode(strtolower($_SERVER['HTTP_HOST']));
    $apiraw = @file_get_contents($apiurl);
    if ($apiraw !== false) {
        $apidata = json_decode($apiraw, true);
        update_option( 'ntkrpnn_neuralnet_siteid', $apidata['id'] );
    } else {
        $apidata['pro'] = "N";
    }
    update_option( 'ntkrpnn_neuralnet_pro', $apidata['pro'] );
    if ($apidata['pro'] == "Y") {
        $apiurl = NTKRPNN_NEURALNET_API . "blocklist.php?ntkid=".urlencode($uniqid)."&list=searchbots";
        $apiraw = @file_get_contents($apiurl);
        if (($apiraw !== false) && (trim($apiraw) != "")) {
            @file_put_contents(plugin_dir_path( __FILE__ )."blocks/searchbots.txt", $apiraw);
        }
        $apiurl = NTKRPNN_NEURALNET_API . "blocklist.php?ntkid=".urlencode($uniqid)."&list=ipblock";
        $apiraw = @file_get_contents($apiurl);
        if (($apiraw !== false) && (trim($apiraw) != "")) {
            @file_put_contents(plugin_dir_path( __FILE__ )."blocks/ipblock.txt", $apiraw);
        }
    }
}

function ntkrpnn_neuralnet_setup() {
	add_shortcode('rpnnrecommend', 'ntkrpnn_neuralnet_recommend');
}

function ntkrpnn_neuralnet_head() {
    if (get_option( 'ntkrpnn_neuralnet_learningon' ) == "Y") {
        $ntk_logurl = plugin_dir_url( __FILE__ )."ntk_log.php";
        $ntk_contentid = get_queried_object_id(); // get_the_ID();
        $ntk_thisurl = sanitize_url($_SERVER["REQUEST_URI"]);
        $ntk_script = '// Related Posts Neural Network - by Neil T King (www.neiltking.com)
        var ntkreq = new XMLHttpRequest();
		var ntkresponse;
        var ntkdata;
        var ntkid = sessionStorage.getItem("ntkid");
        var ntkparams = "ntkid=" + ntkid + "&ntkcontentid='.$ntk_contentid.'&ntkurl='.urlencode($ntk_thisurl).'&nocache=" + Math.random();
        ntkreq.onreadystatechange = function() {
			if (ntkreq.readyState == XMLHttpRequest.DONE) {
				ntkresponse = ntkreq.responseText;
        ';
        if (get_option( 'ntkrpnn_neuralnet_debug' ) == "Y") {
            $ntk_script .= '                console.log("Related Posts Neural Network by Neil T King");
                console.log(ntkresponse);
            ';
        }
        $ntk_script .= '                ntkdata = JSON.parse(ntkresponse);
                sessionStorage.setItem("ntkid", ntkdata.id);
            }
        }
        ntkreq.open("POST", "'.$ntk_logurl.'", true);
        ntkreq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ntkreq.send(ntkparams);
        ';
        wp_register_script( 'ntkrpnn-script', '', array(), '', false);
        wp_enqueue_script( 'ntkrpnn-script' );
        wp_add_inline_script( 'ntkrpnn-script', $ntk_script );
    }
}

function ntkrpnn_neuralnet_adminmenu() {
	// add_options_page( 'Related Posts Neural Network Options', 'Related Posts Neural Network', 'manage_options', 'ntk-neuralnet', 'ntkrpnn_neuralnet_options' );
    $svg = file_get_contents(plugin_dir_url( __FILE__ ).'icon.svg');
    add_menu_page( 'Related Posts Neural Network', 'Related Posts Neural Network', 'administrator', 'ntk-neuralnet', 'ntkrpnn_neuralnet_options', 'data:image/svg+xml;base64,'.base64_encode($svg), 78);
}

function ntkrpnn_neuralnet_settings_link( array $links ) {
    $url = get_admin_url() . "admin.php?page=ntk-neuralnet";
    $settings_link = '<a href="' . $url . '">' . __('Settings', 'textdomain') . '</a>';
    $links[] = $settings_link;
    if (get_option( 'ntkrpnn_neuralnet_pro' ) != "Y") {
        $links[] = __("Free", "related-posts-neural-network");
    } else {
        $links[] = __("PRO", "related-posts-neural-network");
    }
    return $links;
  }

function ntkrpnn_neuralnet_options() {
    global $wpdb;
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', "related-posts-neural-network" ) );
	}
    wp_enqueue_style( 'ntk-neuralnet-options-css', plugin_dir_url( __FILE__ )."ntk_options.css",false,"0.0.1",false);
	// variables for the field and option names 
	$opt_learningon = 'ntkrpnn_neuralnet_learningon'; // Toggle N/Y for learning mode
	$opt_deleteold = 'ntkrpnn_neuralnet_deleteold'; // Delete old links if not accessed in more than X days - toggle N/Y
	$opt_urlmusthave = 'ntkrpnn_neuralnet_urlmusthave'; // URLs must contain this to be included
	$opt_urlmustnot = 'ntkrpnn_neuralnet_urlmustnot'; // URLs must NOT contain this to be included
	$opt_stripget = 'ntkrpnn_neuralnet_stripget'; // GET variables which will be removed, e.g. session IDs or search terms
	$opt_removeget = 'ntkrpnn_neuralnet_removeget'; // GET variables get completely removed - toggle N/Y
	$opt_debug = 'ntkrpnn_neuralnet_debug'; // Turn on debugging message in browser console - toggle N/Y
	$opt_noadmin = 'ntkrpnn_neuralnet_noadmin'; // Disable the logging of editors/admins - toggle N/Y
	$hidden_field_name = 'ntkrpnn_neuralnet_saveoptions';
	
	if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) { // Save form submitted
        if (!isset($_POST[$opt_learningon])) { $_POST[$opt_learningon] = "N"; }
        if (!isset($_POST[$opt_deleteold])) { $_POST[$opt_deleteold] = "N"; }
        if (!isset($_POST[$opt_removeget])) { $_POST[$opt_removeget] = "N"; }
        if (!isset($_POST[$opt_debug])) { $_POST[$opt_debug] = "N"; }
        if (!isset($_POST[$opt_noadmin])) { $_POST[$opt_noadmin] = "N"; }
		update_option( $opt_learningon, sanitize_text_field($_POST[ $opt_learningon ]) );
		update_option( $opt_deleteold, sanitize_text_field($_POST[ $opt_deleteold ]) );
		update_option( $opt_urlmusthave, sanitize_textarea_field($_POST[ $opt_urlmusthave ]) );
		update_option( $opt_urlmustnot, sanitize_textarea_field($_POST[ $opt_urlmustnot ]) );
		update_option( $opt_stripget, sanitize_textarea_field($_POST[ $opt_stripget ]) );
		update_option( $opt_removeget, sanitize_text_field($_POST[ $opt_removeget ]) );
		update_option( $opt_debug, sanitize_text_field($_POST[ $opt_debug ]) );
		update_option( $opt_noadmin, sanitize_text_field($_POST[ $opt_noadmin ]) );
        update_option( 'ntkrpnn_neuralnet_siteid', sanitize_text_field($_POST['sitekey']) );
		echo '<div class="notice notice-success is-dismissible"><strong>'.__("Settings saved", "related-posts-neural-network").'</strong></div>';
	}

    $uniqid = sanitize_text_field(get_option( 'ntkrpnn_neuralnet_siteid' ));
    $apiurl = NTKRPNN_NEURALNET_API . "api.php?ntkid=".urlencode($uniqid)."&ntkh=".urlencode(strtolower(sanitize_url($_SERVER['HTTP_HOST'])));
    $apiraw = @file_get_contents($apiurl);
    if ($apiraw !== false) {
        $apidata = json_decode($apiraw, true);
        update_option( 'ntkrpnn_neuralnet_siteid', sanitize_text_field($apidata['id']) );
    } else {
        $apidata['pro'] = "N";
    }
    update_option( 'ntkrpnn_neuralnet_pro', sanitize_text_field($apidata['pro']) );
    $uniqid = get_option( 'ntkrpnn_neuralnet_siteid' );
    $unl = get_option( 'ntkrpnn_neuralnet_pro' );

	if( isset($_POST[ 'ntkrpnn_neuralnet_reset' ]) && $_POST[ 'ntkrpnn_neuralnet_reset' ] == 'Y' ) { // Reset submitted
        $wpdb->query("TRUNCATE TABLE `" . $wpdb->prefix . "ntk_neuralnet`;");
        $wpdb->query("TRUNCATE TABLE `" . $wpdb->prefix . "ntk_neuralnetcount`;");
        $wpdb->query("TRUNCATE TABLE `" . $wpdb->prefix . "ntk_neuralnetsession`;");
        update_option( 'ntkrpnn_neuralnet_maximumweight', 0 );
        update_option( 'ntkrpnn_neuralnet_maximumscore', 0 );
        echo '<div class="notice notice-success is-dismissible"><strong>'.__("Reset Complete", "related-posts-neural-network").'</strong></div>';
    }

    $opt_learningon_value = esc_html(get_option( $opt_learningon ));
	$opt_deleteold_value = esc_html(get_option( $opt_deleteold ));
	$opt_urlmusthave_value = esc_textarea(get_option( $opt_urlmusthave ));
	$opt_urlmustnot_value = esc_textarea(get_option( $opt_urlmustnot ));
	$opt_stripget_value = esc_textarea(get_option( $opt_stripget ));
	$opt_removeget_value = esc_html(get_option( $opt_removeget ));
	$opt_debug_value = esc_html(get_option( $opt_debug ));
	$opt_noadmin_value = esc_html(get_option( $opt_noadmin ));
    $ntk_maximumweight = get_option( 'ntkrpnn_neuralnet_maximumweight' );
    if (intval($ntk_maximumweight) == 0) { $ntk_maximumweight = 1999990000; }

    $tab = isset($_GET['tab']) ? $_GET['tab'] : null;
?>
	<div class="wrap">
	<h2 class="ntktitle">
        <img src="<?php echo plugin_dir_url( __FILE__ ); ?>icon.svg" alt="Logo" style="width: 60px; height: 60px; float: right;">
        <?php _e("Related Posts Neural Network Plugin", "related-posts-neural-network"); ?><br>
        <span style="font-size: 0.8em;">by Neil T King</span>
    </h2>
    <nav class="nav-tab-wrapper">
        <a href="?page=ntk-neuralnet" class="nav-tab <?php if ($tab===null):?>nav-tab-active<?php endif; ?>"><?php _e("Settings", "related-posts-neural-network"); ?></a>
        <a href="?page=ntk-neuralnet&tab=stats" class="nav-tab <?php if($tab==='stats'):?>nav-tab-active<?php endif; ?>"><?php _e("Statistics", "related-posts-neural-network"); ?></a>
        <a href="?page=ntk-neuralnet&tab=reset" class="nav-tab <?php if($tab==='reset'):?>nav-tab-active<?php endif; ?>" style="background: #FF6666; color: #000000;"><?php _e("Reset", "related-posts-neural-network"); ?></a>
    </nav>
<?php
    if ($tab == 'stats') { // Statistics tab
        wp_enqueue_script('ntk-neuralnet-visnet', plugin_dir_url( __FILE__ ).'vis-network.min.js');
        wp_enqueue_script('ntk-neuralnet-chartjs', plugin_dir_url( __FILE__ ).'chart.min.js');
        $table_session = $wpdb->prefix . "ntk_neuralnetsession";
        $table_synapse = $wpdb->prefix . "ntk_neuralnet";
        $table_count = $wpdb->prefix . "ntk_neuralnetcount";
        if (isset($_POST['ntk_neural_updateweightid']) && (isset($_POST['ntkweight']))) { // Save new weight
            if (intval($_POST['ntk_neural_updateweightid']) > 0) {
                $wpdb->query($wpdb->prepare("UPDATE `".$table_synapse."` SET weight = %d WHERE id = %d;", array(esc_sql(intval($_POST['ntkweight'])), intval($_POST['ntk_neural_updateweightid']))));
                if (intval($_POST['ntkweight']) > intval(get_option( 'ntkrpnn_neuralnet_maximumweight' ))) {
                    update_option( 'ntkrpnn_neuralnet_maximumweight', intval($_POST['ntkweight']) );
                }
                echo '<div class="notice notice-success is-dismissible"><strong>'.__("Strength saved", "related-posts-neural-network").'</strong></div>';
            }
        }
        if (isset($_POST['ntk_neural_deleteurl'])) { // Delete URL references
            if (trim($_POST['ntk_neural_deleteurl']) != "") {
                $wpdb->query($wpdb->prepare("DELETE FROM `".$table_synapse."` WHERE ((url1 = %s) OR (url2 = %s));", array($_POST['ntk_neural_deleteurl'], $_POST['ntk_neural_deleteurl'])));
                $wpdb->query($wpdb->prepare("DELETE FROM `".$table_count."` WHERE (url = %s);", array($_POST['ntk_neural_deleteurl'])));
                echo '<div class="notice notice-success is-dismissible"><strong>'.__("URL references removed", "related-posts-neural-network").'</strong></div>';
            }
        }
        $ntk_synapsecount = esc_html($wpdb->get_var("SELECT COUNT(*) FROM `" . $table_synapse. "`"));
        $ntk_sessioncount = esc_html($wpdb->get_var("SELECT COUNT(*) FROM `" . $table_session . "`"));
        $ntk_urlcount = esc_html($wpdb->get_var("SELECT COUNT(*) FROM `" . $table_count. "`"));
        $ntk_dbsize = esc_html($wpdb->get_var("SELECT SUM(data_length)+SUM(index_length) tablesize FROM information_schema.tables WHERE ((TABLE_SCHEMA = '".$wpdb->dbname."') AND (table_name LIKE 'wp_ntk_neuralnet%'))"));
        $nodes = array();
        $l = $unl == "Y" ? 500 : ceil(9.142);
        $urls = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$table_count."` ORDER BY score DESC LIMIT %d;", array(esc_sql($l))));
        $synapses = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$table_synapse."` ORDER BY weight DESC LIMIT %d;", array(esc_sql($l*3))));
        $nodeid = 0;
        foreach ($urls AS $url) {
            $nodeid++;
            $nodes[esc_html($url->url)] = array("id"=>$nodeid, "score"=>esc_html($url->score));
        }
?>
    <div class="ntksection">
        <h2><?php _e("Statistics", "related-posts-neural-network"); ?></h2>
        <table class="wp-list-table widefat fixed striped table-view-list" style="width: auto;">
            <thead>
                <tr><td><?php _e("Description", "related-posts-neural-network"); ?></td><td><?php _e("Value", "related-posts-neural-network"); ?></td></tr>
            </thead>
            <tr><td><?php _e("Total synapse links", "related-posts-neural-network"); ?></td><td><?php echo $ntk_synapsecount; ?></td></tr>
            <tr><td><?php _e("Total URLs visited", "related-posts-neural-network"); ?></td><td><?php echo $ntk_urlcount; ?></td></tr>
            <tr><td><?php _e("Currently open visitor sessions", "related-posts-neural-network"); ?></td><td><?php echo $ntk_sessioncount; ?></td></tr>
            <tr><td><?php _e("Strongest link weight", "related-posts-neural-network"); ?></td><td><?php echo esc_html(get_option( "ntkrpnn_neuralnet_maximumweight" )); ?></td></tr>
            <tr><td><?php _e("Most popular URL visits", "related-posts-neural-network"); ?></td><td><?php echo esc_html(get_option( "ntkrpnn_neuralnet_maximumscore" )); ?></td></tr>
            <?php if (intval($ntk_dbsize) > 0) { ?><tr><td><?php _e("Database size (estimated)", "related-posts-neural-network"); ?>*</td><td><?php echo ntkrpnn_neuralnet_humanfilesize(intval($ntk_dbsize)); ?></td></tr><?php } ?>
        </table>
        <p style="font-size: 0.9em;">* <?php _e("Reported disk usage from database engines are not always accurate due to caching and disk block size etc.", "related-posts-neural-network"); ?></p>
    </div>
    <div class="ntksection">
        <h3><?php _e("Neural Network", "related-posts-neural-network"); ?></h3>
        <p><?php _e("Drag nodes/URLs with your mouse to reposition them. Use the buttons underneath to zoom in and out. Drag on an empty area to scroll the chart around.", "related-posts-neural-network"); ?><br>
            <?php _e("The bigger the neuron, the more popular the URL. The thicker the synapse, the stronger the link.", "related-posts-neural-network"); ?><br>
            <?php _e("Hover over a neuron to see the URL, or click it to highlight it in the table below (and vice versa).", "related-posts-neural-network"); ?><br>
            <?php _e("Hover a synapse/link to see it's strength, click on it to adjust the strength (PRO version only).", "related-posts-neural-network"); ?>
        </p>
        <div id="ntkneuralchart"></div>
        <div class="ntkneuralbuttons">
            <a href="javascript:;" onclick="network.zoom(0.5);"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>magnifyin.png" alt="<?php esc_attr_e('Zoom In', 'related-posts-neural-network'); ?>" title="<?php esc_attr_e('Zoom In', 'related-posts-neural-network'); ?>"></a>
            <a href="javascript:;" onclick="network.zoom(-0.5);"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>magnifyout.png" alt="<?php esc_attr_e('Zoom Out', 'related-posts-neural-network'); ?>" title="<?php esc_attr_e('Zoom Out', 'related-posts-neural-network'); ?>"></a>
            <div class="ntkneuralphysicsbuttons">
                <?php _e("Physics", 'related-posts-neural-network'); ?>:<br>
                <input type="checkbox" id="ntkphysics" name="ntkphysics" class="togswitch" style="margin-top: 5px;" value="Y" checked onchange="console.log(netoptions); if (this.checked) { netoptions.physics.enabled = true; network.setOptions(netoptions); } else { netoptions.physics.enabled = false; network.setOptions(netoptions); };">
            </div>
        </div>
    </div>
<?php
    $ntk_script = '
    var network;
    var netoptions;
    var ntkNeuralBarChart;
    var ntkneuralbarchartdata;
    jQuery( document ).ready(function() {
        vis.Network.prototype.zoom = function (scale) {
            const animationOptions = {
                scale: this.getScale() + scale,
                animation: { duration: 300 }
            };
            this.view.moveTo(animationOptions);
        };
        var nodes = new vis.DataSet([
    ';
        $ntkbarchartlabels = array();
        $ntkbarchartvalues = array();
        $ntkbarchartbackground = array();
        $ntkbarchartborder = array();
        $ntkcharturls = "";
        foreach ($nodes AS $nodeurl=>$nodescore) {
            $ntk_script .= '{ id: '.$nodescore["id"].', label:" '.$nodescore["id"].' ", value:'.$nodescore["score"].', title: "'.$nodeurl.'" },'."\n";
            $ntkbarchartlabels[] = '"'.$nodescore["id"].'"';
            $ntkbarchartvalues[] = intval($nodescore["score"]);
            $ntkbarchartbackground[] = '"#D2E5FF"';
            $ntkbarchartborder[] = '"#2B7CE9"';
            $ntkcharturls .= "nodeurl[".$nodescore["id"]."] = '".esc_html($nodeurl)."';\n";
        }
        $ntk_script .= '      ]);'."\n";
        $ntk_script .= '      var edges = new vis.DataSet(['."\n";
        foreach ($synapses AS $synapse) {
            if ((array_key_exists(esc_html($synapse->url1),$nodes)) && (array_key_exists(esc_html($synapse->url2),$nodes))) {
                $ntk_script .= '{ id: "e'.$synapse->id.'", from: '.$nodes[esc_html($synapse->url1)]["id"].', to:'.$nodes[esc_html($synapse->url2)]["id"].', value:'.$synapse->weight.', title:"Strength:'.$synapse->weight.'" },'."\n";
            }
        }
        $ntk_script .= '      ]);
        var container = document.getElementById("ntkneuralchart");
        var data = {
            nodes: nodes,
            edges: edges,
        };
        netoptions = { 
            nodes: { shape: "circle", color: { background: "#D2E5FF", highlight: { border: "#66FF66", background: "#66FF66"} }, font: { align: "center" }, scaling: { label: { enabled: true, min: 6, max: 42 } }, shadow: { enabled: true, size: 5, x: 3, y: 3 } },
            edges: { color: { color: "#2B7CE9", highlight: "#66FF66" }, smooth: { type: "continuous"} },
            physics: { enabled: true, solver: "barnesHut" },
            interaction: { zoomView: false, navigationButtons: false }
        };
        // var options = { nodes: { shape: "dot" } };
        network = new vis.Network(container, data, netoptions);
        network.on("select", function (params) {
            if (params.nodes.length > 0) { ntkBarHighlight(params.nodes); }
            if (params.edges.length == 1) {
                // var nodeids = network.getConnectedNodes(params.edges);
                var edgeinfo = edges.get(params.edges);
                if (edgeinfo.length == 1) {
                    jQuery("#ntk_neural_updateweightid").val(String(params.edges).substring(1));
                    jQuery("#ntkurl1").text(nodeurl[edgeinfo[0].from]);
                    jQuery("#ntkurl2").text(nodeurl[edgeinfo[0].to]);
                    jQuery("#ntkweight").val(edgeinfo[0].value);
                    console.log(edges.get(params.edges));
                }
            }
        });
        network.on("dragEnd", function(){
            network.unselectAll();
        });
        // Bar Chart
        ntkneuralbarchartdata = {
            labels: ['.implode(",", $ntkbarchartlabels).'],
            datasets: [{
                label: "Visitors",
                data: ['.esc_html(implode(",", $ntkbarchartvalues)).'],
                fill: true,
                backgroundColor: ['.implode(",", $ntkbarchartbackground).'],
                borderColor: ['.implode(",", $ntkbarchartbackground).'],
                borderWidth: 1
            }]
        };
        const ntkneuralbarchartconfig = {type: "bar", data: ntkneuralbarchartdata, 
            options: { responsive: true, maintainAspectRatio: false, 
                scales: {
                    x: { ticks: { autoSkip: false, maxRotation: 90, minRotation: 90 }},
                    xAxes: { ticks: { callback: function(label) { return ""; } }}
                },
                onClick: ntkBarSelect, 
                plugins: { legend: { display: false }, customCanvasBackgroundColor: { color: "white" }, tooltip: { callbacks: { title: function(context) { return nodeurl[context[0].label]; } } } }, 
            }
        };
        Chart.defaults.font.family = "-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen-Sans,Ubuntu,Cantarell,\'Helvetica Neue\',sans-serif";
        Chart.defaults.font.size = 15;
        ntkNeuralBarChart = new Chart( document.getElementById("ntkneuralbarchart"), ntkneuralbarchartconfig );
    });

    var nodeurl = [];
    '.$ntkcharturls.'

    function ntkNodeSelect(id) {
        const nodeId = String(id);
        network.focus(id, {animation: true});
        network.selectNodes([nodeId]);
        ntkBarHighlight(id);
        document.getElementById("ntk_neural_deleteurl").value = nodeurl[id];
    }
    function ntkBarSelect(evt) {
        const points = ntkNeuralBarChart.getElementsAtEventForMode(evt, "nearest", { intersect: true }, true);
        if (points.length) {
            const firstPoint = points[0];
            const label = ntkNeuralBarChart.data.labels[firstPoint.index];
            const value = ntkNeuralBarChart.data.datasets[firstPoint.datasetIndex].data[firstPoint.index];
            ntkNodeSelect(label);
        }
    }
    function ntkBarHighlight(id) {
        for (var i=0; i<ntkneuralbarchartdata.datasets[0].backgroundColor.length; i++) {
            ntkneuralbarchartdata.datasets[0].backgroundColor[i] = "#D2E5FF";
            ntkneuralbarchartdata.datasets[0].borderColor[i] = "#2B7CE9";
        }
        ntkneuralbarchartdata.datasets[0].backgroundColor[id-1] = "#66FF66";
        ntkneuralbarchartdata.datasets[0].borderColor[id-1] = "#66FF66";
        ntkNeuralBarChart.update();
        jQuery(".ntknoderow").removeClass("ntkrowselected");
        jQuery("#ntknode" + id).addClass("ntkrowselected");
        // console.log("Top: " + (jQuery("#ntkneuraltable #ntknode" + id)[0].offsetTop - 37));
        document.getElementById("ntk_neural_deleteurl").value = nodeurl[id];
        jQuery("#ntkneuraltable tbody").animate({
			scrollTop: jQuery("#ntkneuraltable #ntknode" + id)[0].offsetTop - 37
		}, 500);
    }
    function checkNumberValue(sender) {
        let min = sender.min;
        let max = sender.max;
        let value = parseInt(sender.value);
        if (value>max) {
            sender.value = max;
        } else if (value<min) {
            sender.value = min;
        }
    }
    function moveNeuralChart(addx, addy) { // Not currently used
        network.moveTo({position: {x:(network.getViewPosition().x + addx), y:(network.getViewPosition().y + addy)}});
    }

    function searchNodeUrl(searchtext) {
        let search = searchtext.toLowerCase();
        for (i = 1; i <= nodeurl.length; i++) {
            if (nodeurl[i].toLowerCase().indexOf(search) > -1) {
                ntkNodeSelect(i);
                break;
            }
        }
    }

    function ntkDelete() {
        if (document.getElementById("ntk_neural_deleteurl").value.trim() != "") {
            if (confirm("Are you sure you want to delete all references to this URL from the neural network? There is no undo.") == true) {
                document.getElementById("ntkrpnn_neuralnet_delform").submit();
            }
        }
    }
    ';
    wp_register_script( 'ntkrpnn-admin-script', '', array(), '', false);
    wp_enqueue_script( 'ntkrpnn-admin-script' );
    wp_add_inline_script( 'ntkrpnn-admin-script', $ntk_script );
?>
    <div class="halfwidth ntksection">
        <h3><?php _e("URL Visitor Sessions", 'related-posts-neural-network'); ?></h3>
        <table class="wp-list-table widefat fixed striped table-view-list innerhalfwidth" id="ntkneuraltable">
            <thead>
                <tr><td style="width: 5% !important; white-space: nowrap;"><?php _e("ID", 'related-posts-neural-network'); ?></td><td style="width: 85%;"><?php _e("URL", 'related-posts-neural-network'); ?></td><td style="width: 10%;"><?php _e("Visitors", 'related-posts-neural-network'); ?></td></tr>
            </thead>
            <tbody>
<?php
        foreach ($nodes AS $nodeurl=>$nodescore) {
            print('<tr class="ntknoderow" id="ntknode'.$nodescore["id"].'" onclick="ntkNodeSelect('.$nodescore["id"].');"><td style="width: 5%;">'.htmlentities($nodescore["id"]).'</td>');
            print('<td style="width: 85%; white-space: nowrap;">'.$nodeurl.'</td>');
            print('<td style="width: 10%;">'.$nodescore["score"].'<td>');
            print('</tr>'."\n");
        }
?>
            </tbody>
        </table>
        <form name="ntkrpnn_neuralnet_delform" id="ntkrpnn_neuralnet_delform" method="post" action="" onsubmit="return false;">
            <input type="hidden" name="ntk_neural_deleteurl" id="ntk_neural_deleteurl" value="">
            <input type="text" name="urltablesearch" id="urltablesearch" style="height: 35px; width: 70%;" onkeyup="searchNodeUrl(this.value);" placeholder="<?php esc_attr_e('Search...', 'related-posts-neural-network'); ?>"><input type="button" name="ntkdelete" class="button-primary" style="width: 25%; height: 35px;" title="<?php esc_attr_e('Delete all references to the selected URL', 'related-posts-neural-network'); ?>" value="<?php esc_attr_e('Delete', 'related-posts-neural-network'); ?>" onclick="ntkDelete();">
        </form>
    </div>
    <div class="halfwidth ntksection">
<?php
        if ($l <> 500) {
?>
        <h2><?php _e("Unlock the Pro Version for", 'related-posts-neural-network'); ?>:</h2>
            <div class="innerhalfwidth" style="height: 435px;">
            <ul style="list-style: disc; margin-left: 20px;">
                <li><?php _e("Manually edit the strengths of links. This will allow you to help force or avoid certain recommendations between pages.", 'related-posts-neural-network'); ?></li>
                <li><?php _e("Learns and can recommend an unlimited number of URLs.", 'related-posts-neural-network'); ?></li>
                <li><?php _e("Display up to 500 nodes/URLs and 1500 connections on the Statistics page.", 'related-posts-neural-network'); ?><br>
                    <?php _e("(The free version only shows the top 10 nodes and a maximum of 30 connections)", 'related-posts-neural-network'); ?></li>
                <li><?php _e("Automatically download a block list of search engine bots/crawlers to prevent them affecting the neural network.", 'related-posts-neural-network'); ?></li>
                <li><?php _e("Automatically download a block list of IP addresses from bad users to prevent them affecting the neural network.", 'related-posts-neural-network'); ?></li>
                <li><?php _e("Email support and help.", 'related-posts-neural-network'); ?></li>
            </ul>
            <a href="?page=ntk-neuralnet"><?php _e("Click here for plugin settings and unlock link", 'related-posts-neural-network'); ?></a>
<?php
        } else {
?>
        <h3><?php _e("Edit Link Strength", 'related-posts-neural-network'); ?></h3>
            <div class="innerhalfwidth" style="height: 435px;">
            <form name="ntkrpnn_neuralnet_form" method="post" action="">
                <p><?php _e("Click on a synapse/link above to be able to edit it's strength between the two URL neurons. The higher the strength, the more likely it will be recommended. By setting a high value you can help force a recommendation, and by setting a low value you can help avoid a recommendation.", 'related-posts-neural-network'); ?></p>
                <input type="hidden" name="ntk_neural_updateweightid" id="ntk_neural_updateweightid" value="">
                <p><?php _e("Link between", 'related-posts-neural-network'); ?>:<br>
                    <div id="ntkurl1">-</div>
                    <p style="font-size: 3em; text-align: center; height: 1em; margin: 0 0 20px 0;">&#8597;</p>
                    <div id="ntkurl2">-</div>
                </p>
                <p style="text-align: center;">
                    <?php _e("Strength", 'related-posts-neural-network'); ?>:<br>
                    <input type="number" min="0" max="<?php echo ($ntk_maximumweight + 10000); ?>" id="ntkweight" name="ntkweight" value="" oninput="checkNumberValue(this);"><br>
                    <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Strength', 'related-posts-neural-network'); ?>" />
                </p>
            </form>
<?php
        }
?>
        </div>
    </div>
    <div class="ntksection">
        <h3><?php _e("Visitor Session Chart", 'related-posts-neural-network'); ?></h3>
        <div style="width: 100%; height: 500px;">
            <canvas id="ntkneuralbarchart"></canvas>
        </div>
    </div>
    <div style="clear: both;"></div>
<?php
    } elseif ($tab == 'reset') { // Reset database tab
?>
    <h2><?php _e("Reset Neural Network", 'related-posts-neural-network'); ?></h2>
    <p style="color: #FF0000; background: #FFFFFF; padding: 5px;"><?php _e("WARNING: This will reset everything in the neural network as if it was brand new. Your settings are saved but all existing neural links, counters and visitor session data is completely wiped.", 'related-posts-neural-network'); ?></p>
    <p><?php _e("This should only be done if you need to start from scratch. For example, you may have changed your permalinks settings so all of your URLs have changed. Or you may have been testing this plugin and now want to start again with real data.", 'related-posts-neural-network'); ?></p>
    <form name="ntkrpnn_neuralnet_form" method="post" action="">
    	<input type="hidden" name="ntkrpnn_neuralnet_reset" value="N">
        <p class="submit">
            <input type="button" name="Reset" class="button-primary" value="<?php esc_attr_e('RESET', 'related-posts-neural-network'); ?>" onclick="if (confirm('<?php esc_attr_e('Are you sure you want to RESET? There is no undo.', 'related-posts-neural-network'); ?>') == true) { this.form.ntkrpnn_neuralnet_reset.value='Y'; this.form.submit(); }" />
        </p>
    </form>
<?php
    } else { // Settings tab
?>
    <h2><?php _e("Settings", 'related-posts-neural-network'); ?></h2>
    <p><?php _e("This plugin builds a custom Neural Network (Artificial Intelligence) in order to offer your visitors related content that they might be interested in, e.g. <em>&quot;You might also like...&quot;</em>. It does this by learning from existing visitors and what they look at on your site.", 'related-posts-neural-network'); ?></p>
    <p><?php _e("By default it will include all pages, posts, products, articles etc. on your site. By setting options here you can limit what gets added to the neural net so only relevant content is suggested. Once happy with your settings, you can turn on <strong>&quot;Learning Mode&quot;</strong> and it will immediately start learning from your visitors.", 'related-posts-neural-network'); ?>
        <?php _e("Once you are happy it has learned enough, you can add the shortcode into pages where you want it to make suggestions. This shortcode is", 'related-posts-neural-network'); ?>:
        <pre>[rpnnrecommend]</pre>
        <?php _e("This will default to showing 3 recommended links (maximum) with thumbnail images. You can add optional parameters such as", 'related-posts-neural-network'); ?>:
        <pre>[rpnnrecommend total=&quot;6&quot; thumbnails=&quot;false&quot; class=&quot;recommendedlist&quot; title=&quot;You may also like...&quot;]</pre>
        <?php _e("This will show 6 recommendations (maximum), no thumbnail images, and add the CSS class &quot;recommendedlist&quot; to the &lt;ul&gt; list of suggested links. This CSS is included and will show a vertical list of recommended links, but you can use your own CSS classes if you like.", 'related-posts-neural-network'); ?>
        <?php _e("The title parameter is written as a H3 heading if there are suggestions to output. You can leave &quot;Learning Mode&quot; on and it will continue to learn from visitors and improve suggestions.", 'related-posts-neural-network'); ?></p>
	<form name="ntkrpnn_neuralnet_form" method="post" action="">
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
    <div class="ntksection" style="border-color: #44DD44;"><p><?php _e("ONLY switch &quot;Learning Mode&quot; on when you have checked all of the other settings.", 'related-posts-neural-network'); ?></p>
        <input type="checkbox" id="<?php echo $opt_learningon; ?>" name="<?php echo $opt_learningon; ?>" class="togswitch bigswitch" value="Y" <?php if ($opt_learningon_value == "Y") { echo "checked"; } ?>>
        <label for="<?php echo $opt_learningon; ?>"><?php _e("Learning Mode", 'related-posts-neural-network'); ?></label>
    </div>
	<div class="ntksection"><h3><?php _e("Site Key", 'related-posts-neural-network'); ?>:</h3>
        <p><?php _e("Do NOT change this once set, especially if you have unlocked the Pro version of the plugin. It may be a good idea to keep a record of this key in case you need to set up a new server and haven't backed it up.", 'related-posts-neural-network'); ?><br>
        <input type="text" id="sitekey" name="sitekey" value="<?php echo $uniqid; ?>" readonly="readonly">
        <a href="javascript:;" onclick="document.getElementById('sitekey').readOnly = false;"><?php _e("Edit", 'related-posts-neural-network'); ?></a></p>
        <?php
        print("<p>".__("Site URL", 'related-posts-neural-network').": ".strtolower($_SERVER['HTTP_HOST'])."</p>\n");
        if ($unl == "Y") {
            echo "<h3>".__("Pro Version Unlocked. Thank you.", 'related-posts-neural-network')."</h3>\n";
            echo __("For help, see the website", 'related-posts-neural-network').": <a href=\"https://www.neiltking.com/neuralnet/\" target=\"_blank\">https://www.neiltking.com/neuralnet/</a><br>\n";
            echo __("or email", 'related-posts-neural-network').": <a href=\"mailto:wordpress@neiltking.com?subject=Related%20Posts%20Neural%20Network%20plugin%20help&body=Site%20ID=".$uniqid."%0D%0ASite%20URL=".esc_url($_SERVER['HTTP_HOST'])."%0D%0A\">wordpress@neiltking.com</a>\n";
        } else {
        ?>
            <?php _e("You are currently trying the limited version of this plugin.", 'related-posts-neural-network'); ?><br>
            <h3><?php _e("Unlock the Pro Version for extra features like", 'related-posts-neural-network'); ?>:</h3>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><?php _e("Manually edit the strengths of links. This will allow you to help force or avoid certain recommendations between pages.", 'related-posts-neural-network'); ?></li>
                <li><?php _e("Learns and can recommend an unlimited number of URLs.<br>(The free version only logs 30 links)", 'related-posts-neural-network'); ?></li>
                <li><?php _e("Display up to 500 top nodes/URLs and 1500 connections on the Statistics page.", 'related-posts-neural-network'); ?><br>
                    <?php _e("(The free version only shows the top 10 nodes and a maximum of 30 connections)", 'related-posts-neural-network'); ?></li>
                <li><?php _e("Automatically download a block list of search engine bots/crawlers to prevent them affecting the neural network.", 'related-posts-neural-network'); ?></li>
                <li><?php _e("Automatically download a block list of IP addresses from bad users to prevent them affecting the neural network.", 'related-posts-neural-network'); ?></li>
                <li><?php _e("Email support and help.", 'related-posts-neural-network'); ?></li>
            </ul>
            <a href="https://www.neiltking.com/neuralnet/register.php?id=<?php echo esc_url($uniqid); ?>&site=<?php echo esc_url($apidata['url']); ?>" target="_blank" class="button"><?php _e("Register and Unlock Pro Version", 'related-posts-neural-network'); ?></a>
        <?php
        }
        ?>
    </div>
    <div class="ntksection"><h3><?php _e("Clean Up Old Connections", 'related-posts-neural-network'); ?>:</h3>
        <p><?php _e("Tick the box to make the system automatically remove old entries which have not been accessed in 1 year.", 'related-posts-neural-network'); ?></p>
        <input type="checkbox" id="<?php echo $opt_deleteold; ?>" name="<?php echo $opt_deleteold; ?>" class="xtogswitch" value="Y" <?php if ($opt_deleteold_value == "Y") { echo "checked"; } ?>>
        <label for="<?php echo $opt_deleteold; ?>"><?php _e("Delete Old Links", 'related-posts-neural-network'); ?></label>
    </div>
	<div class="ntksection"><h3><?php _e("URLs Must Contain", 'related-posts-neural-network'); ?>:</h3>
        <?php _e("Enter any part of the URL which must be present for the content to be included in the neural net. For example, if you only want it to remember and suggest products which all live at", 'related-posts-neural-network'); ?>:<br>
        https://www.mysite.abc/product/xxxx<br>
        <?php _e("you could type <strong>/product/</strong>. Any URL which does not include that text will be ignored. Add one entry per line.", 'related-posts-neural-network'); ?>
        <textarea name="<?php echo $opt_urlmusthave; ?>" id="<?php echo $opt_urlmusthave; ?>"><?php echo $opt_urlmusthave_value; ?></textarea>
    </div>
	<div class="ntksection"><h3><?php _e("URLs Must NOT Contain", 'related-posts-neural-network'); ?>:</h3>
        <?php _e("Enter any part of the URL which must NOT be present for the content to be included in the neural net. For example, if you don't want it to remember or suggest articles which all live at", 'related-posts-neural-network'); ?>:<br>
        https://www.mysite.abc/articles/xxxx<br>
        <?php _e("you could type <strong>/articles/</strong>. Any URL which includes that text will be ignored. This takes precident over &quot;URLs Must Contain&quot; entries above. Add one entry per line.", 'related-posts-neural-network'); ?>
        <textarea name="<?php echo $opt_urlmustnot; ?>" id="<?php echo $opt_urlmustnot; ?>"><?php echo $opt_urlmustnot_value; ?></textarea>
    </div>
	<div class="ntksection"><h3><?php _e("Strip GET variables", 'related-posts-neural-network'); ?>:</h3>
        <?php _e("Enter the name of any GET variable to be stripped from the URL before it is included in the neural net. For example, if your site adds search terms to the URL such as", 'related-posts-neural-network'); ?>:<br>
        https://www.mysite.abc/blog/?search=stuff<br>
        <?php _e("you can enter <strong>search</strong> here and it will be automatically removed from the stored URL. Add one entry per line. This is also useful if your site uses a session variable as part of the URL.", 'related-posts-neural-network'); ?>
        <textarea name="<?php echo $opt_stripget; ?>" id="<?php echo $opt_stripget; ?>"><?php echo $opt_stripget_value; ?></textarea>
    </div>
	<div class="ntksection"><h3><?php _e("Strip all GET variables", 'related-posts-neural-network'); ?>:</h3>
        <p><?php _e("You can have the system strip all GET variables from the URL before it is saved in the neural net. It is recommended you turn this on if you don't use them to identify your content, e.g. you use permalinks.", 'related-posts-neural-network'); ?></p>
        <input type="checkbox" id="<?php echo $opt_removeget; ?>" name="<?php echo $opt_removeget; ?>" class="togswitch" value="Y" <?php if ($opt_removeget_value == "Y") { echo "checked"; } ?>>
        <label for="<?php echo $opt_removeget; ?>"><?php _e("Off/On", 'related-posts-neural-network'); ?></label>
    </div>
	<div class="ntksection"><h3><?php _e("Debug", 'related-posts-neural-network'); ?>:</h3>
        <p><?php _e("You can turn debugging on to get some feedback on the pages being visited. If this is switched on, everyone who visits the site can see some debugging information by opening the browser console. This includes their unique identifier and a friendly debug message. This should only be turned on while testing.", 'related-posts-neural-network'); ?></p>
        <input type="checkbox" id="<?php echo $opt_debug; ?>" name="<?php echo $opt_debug; ?>" class="togswitch" value="Y" <?php if ($opt_debug_value == "Y") { echo "checked"; } ?>>
        <label for="<?php echo $opt_debug; ?>"><?php _e("Off/On", 'related-posts-neural-network'); ?></label>
    </div>
	<div class="ntksection"><h3><?php _e("Disable Learning if Editor/Admin", 'related-posts-neural-network'); ?>:</h3>
        <p><?php _e("You can disable learning if the current visitor is logged in with editor/admin capabilities. This is very useful so editors can browser and update the site without their actions adding to the neural network.", 'related-posts-neural-network'); ?></p>
        <input type="checkbox" id="<?php echo $opt_noadmin; ?>" name="<?php echo $opt_noadmin; ?>" class="togswitch" value="Y" <?php if ($opt_noadmin_value == "Y") { echo "checked"; } ?>>
        <label for="<?php echo $opt_noadmin; ?>">Off/On</label>
    </div>
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
	</p>
	</form>
	</div>
<?php
    } // End of tab
?>
    <div style="font-size: 0.8em; text-align: right; margin-right: 10px;">&copy;2024+, <a href="https://www.neiltking.com" target="_blank">Neil T King</a></div>
<?php
}

function ntkrpnn_neuralnet_recommend($atts) {
	global $post, $wpdb;
	extract(shortcode_atts(
		array(
			'class' => '',
            'total' => '3',
            'thumbnails' => 'true',
            'title' => 'You may also like...'
		),
		$atts));
    wp_enqueue_style( 'ntk-neuralnet-css', plugin_dir_url( __FILE__ )."ntk_neuralnet.css",false,"0.0.1",false);
    $ntk_stripget = sanitize_textarea_field(get_option( 'ntkrpnn_neuralnet_stripget' ));
    $table_synapse = $wpdb->prefix . "ntk_neuralnet";
    $ntkclass = trim("ntkrecommended {$class}");
    $ntk_url = isset($_SERVER["REQUEST_URI"]) ? trim($_SERVER["REQUEST_URI"]) : "";
    if (trim($ntk_stripget) != "") {
        $ntk_urlparts = wp_parse_url('http://x.x'.$ntk_url);
        if (isset($ntk_urlparts['query'])) {
            parse_str($ntk_urlparts['query'], $ntk_query);
        } else {
            $ntk_query = array();
        }
        $ntk_parts = preg_split('/\r\n|\r|\n/',$ntk_stripget);
        foreach ($ntk_query AS $key => $value) {
            if (in_array($key, $ntk_parts)) { // Found GET variable in URL
                unset($ntk_query[$key]);
            }
        }
        $ntk_urlparts['query'] = http_build_query($ntk_query);
        if (strlen($ntk_urlparts['query']) > 0) {
            $ntk_url = $ntk_urlparts['path'].'?'.$ntk_urlparts['query'];
        } else {
            $ntk_url = $ntk_urlparts['path'];
        }
    }
	$result = "<ul class=\"".$ntkclass."\">";
    $totalwithbuffer = intval("{$total}") + 10;
    $recommendcount = 0;
    $urls = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$table_synapse."` WHERE ((url1 = %s) OR (url2 = %s)) ORDER BY weight DESC LIMIT %d;", array($ntk_url, $ntk_url, $totalwithbuffer)));
    foreach ($urls AS $url) {
        if ($url->url1 == $ntk_url) {
            $recommendurl = $url->url2;
            $recommendid = $url->post2;
        } else {
            $recommendurl = $url->url1;
            $recommendid = $url->post1;
        }
        if (get_post_status($recommendid) === "publish") {
            $recommendthumb = "";
            $recommendtitle = $recommendurl;
            $result .= "<li>";
            if (intval($recommendid) > 0) {
                $recommendthumb = get_the_post_thumbnail(intval($recommendid), 'thumbnail');
                $recommendtitle = get_the_title($recommendid);
            }
            $result .= "<a href=\"".$recommendurl."\">";
            if (trim($recommendurl) != "") {
                if ((strtolower("{$thumbnails}") == "true") && ($recommendthumb != "")) {
                    $result .= $recommendthumb."<br>";
                }
                $result .= $recommendtitle."</a>";
            }
            $recommendcount++;
        }
        if ($recommendcount >= intval("{$total}")) { break; }
    }
	$result .= "</ul>\n";
    if ($recommendcount == 0) { // No results
        $result = "";
    } else {
        $result = "<h3 class=\"ntkrecommendtitle\">".esc_html("{$title}")."</h3>\n".$result;
    }
	return $result;
}

function ntkrpnn_neuralnet_humanfilesize($bytes, $decimals = 2) {
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
  }