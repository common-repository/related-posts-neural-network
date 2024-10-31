<?php
    require_once('../../../wp-load.php');
    header("Expires: " . gmdate("D, d M Y H:i:s", time()-604800) . " GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    if (isset($_POST['ntkid'])) {
        $debug = "";
        if (strlen($_POST['ntkid']) < 30) { // Non existent or invalid visitor ID, so generate one
            $ntk_uniqid = "";
            for ($t = 0; $t < 4; $t++) {
                $ntk_uniqid .= (string) wp_rand(10000, 99999);
            }
            for ($t = 0; $t < 10; $t++) {
                $ntk_uniqid .= chr(wp_rand(65, 90));
            }
        } else {
            $ntk_uniqid = sanitize_textarea_field($_POST['ntkid']);
        }
        $ntk_contentid = isset($_POST["ntkcontentid"]) ? intval($_POST["ntkcontentid"]) : "0";
        if ( is_404() ) { // Not real content
            $ntk_contentid = 0;
            $debug .= "404 ignore. ";
        }
        $ntk_url = isset($_POST["ntkurl"]) ? trim(sanitize_url($_POST["ntkurl"])) : "";
        if (($ntk_url != "") && ($ntk_contentid > 0)) { // Search results page returns an id of 0 so it can be ignored
            // Insert into session database table and if necessary, the neural net and count database tables
            $ntk_learningon = get_option( 'ntkrpnn_neuralnet_learningon' );
            if ($ntk_learningon == "Y") {
                $ntk_urlmusthave = sanitize_textarea_field(get_option( 'ntkrpnn_neuralnet_urlmusthave' ));
                $ntk_urlmustnot = sanitize_textarea_field(get_option( 'ntkrpnn_neuralnet_urlmustnot' ));
                $ntk_stripget = sanitize_textarea_field(get_option( 'ntkrpnn_neuralnet_stripget' ));
                $ntk_removeget = sanitize_text_field(get_option( 'ntkrpnn_neuralnet_removeget' ));
                $ntk_noadmin = sanitize_text_field(get_option( 'ntkrpnn_neuralnet_noadmin' ));
                $ntk_maximumweight = sanitize_text_field(get_option( 'ntkrpnn_neuralnet_maximumweight' ));
                $ntk_maximumscore = sanitize_text_field(get_option( 'ntkrpnn_neuralnet_maximumscore' ));
                $unl = sanitize_text_field(get_option( 'ntkrpnn_neuralnet_pro' ));
                $ntk_include = true;
                if (mb_strpos(strtolower($ntk_url), '?preview_id=') !== false) { $ntk_include = false; } // Prevent preview pages being added
                if (mb_strpos(strtolower($ntk_url), '/wp-admin/') !== false) { $ntk_include = false; } // Prevent admin pages being added
                if (($ntk_noadmin == "Y") && (current_user_can('edit_posts'))) { // Editors/admins should be ignored.
                    $ntk_include = false;
                    $debug .= "Editor role, so ignore. ";
                }
                if ($unl == "Y") {
                    if (@file_exists("blocks/searchbots.txt")) {
                        $searchfor = "";
                        $useragent = $_SERVER['HTTP_USER_AGENT'].'';
                        $searchfor = @file_get_contents("blocks/searchbots.txt");
                        $rules = preg_split("/\r\n|\n|\r/", $searchfor);
                        foreach ($rules AS $rule) {
                            if (trim($rule) != "") {
                                $testrule = str_replace(".",'\.', $rule);
                                $testrule = str_replace("*", '.*', $testrule);
                                if (@preg_match("/".$testrule."/i", $useragent)) {
                                    $ntk_include = false;
                                    $debug .= "Identified as bot. ";
                                }
                            }
                        }
                    }
                    if (@file_exists("blocks/ipblock.txt")) {
                        $searchfor = "";
                        $useragent = $_SERVER['REMOTE_ADDR'].'';
                        $searchfor = @file_get_contents("blocks/ipblock.txt");
                        $rules = preg_split("/\r\n|\n|\r/", $searchfor);
                        foreach ($rules AS $rule) {
                            if (trim($rule) != "") {
                                $testrule = str_replace(".",'\.', $rule);
                                $testrule = str_replace("*", '.*', $testrule);
                                if (@preg_match("/".$testrule."/i", $useragent)) {
                                    $ntk_include = false;
                                    $debug .= "Appears in IP block list. ";
                                }
                            }
                        }
                    }
                }
                if ($ntk_maximumweight > 2000000000) {
                    $debug .= "Maximum strength limit hit. ";
                } elseif ($ntk_maximumscore > 2000000000) {
                    $debug .= "Maximum hit score limit hit. ";
                } elseif ($ntk_include == true) {
                    if (trim($ntk_urlmusthave) != "") {
                        $ntk_include = false;
                        $ntk_parts = preg_split('/\r\n|\r|\n/',$ntk_urlmusthave);
                        foreach ($ntk_parts AS $key => $value) {
                            if (mb_strpos(mb_strtolower($ntk_url), mb_strtolower($value)) !== false) { // Found "must have" text in URL
                                $ntk_include = true;
                                $debug .= "Found in MUST HAVE. ";
                            }
                        }
                    }
                    if (trim($ntk_urlmustnot) != "") {
                        $ntk_parts = preg_split('/\r\n|\r|\n/',$ntk_urlmustnot);
                        foreach ($ntk_parts AS $key => $value) {
                            if (mb_strpos(mb_strtolower($ntk_url), mb_strtolower($value)) !== false) { // Found "must NOT have" text in URL
                                $ntk_include = false;
                                $debug .= "Found in MUST NOT HAVE. ";
                            }
                        }
                    }
                    if ($ntk_removeget == "Y") { // Remove all GET variables
                        $ntk_urlparts = wp_parse_url('http://x.x'.$ntk_url);
                        $ntk_url = $ntk_urlparts['path'];
                        $debug .= "Removed GET variables. ";
                    } elseif (trim($ntk_stripget) != "") {
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
                    if ($ntk_include) { // Insert into database tables
                        $table_session = $wpdb->prefix . "ntk_neuralnetsession";
                        $table_synapse = $wpdb->prefix . "ntk_neuralnet";
                        $table_count = $wpdb->prefix . "ntk_neuralnetcount";
                        $visitorsess = $wpdb->get_row($wpdb->prepare("SELECT * FROM `".$table_session."` WHERE (sessionid = %s);", array($ntk_uniqid)));
                        if (!is_null($visitorsess)) { // Visitor has an existing session
                            $ntk_prevarray = array();
                            $urllines = preg_split('/\r\n|\r|\n/',stripslashes($visitorsess->urls));
                            if (count($urllines) > 150) { // Each visitor session should not store more than 150 URLs just to be safe
                                $dummy = array_shift($urllines); // Drop first element in array so it doesn't grow out of control
                            }
                            foreach ($urllines AS $urlline) {
                                $ntk_prevarray[] = json_decode($urlline, true);
                            }
                            $ntk_prevurls = array_column($ntk_prevarray, "url");
                            $ntk_previds = array_column($ntk_prevarray, "id");
                            if (in_array($ntk_url, $ntk_prevurls) === false) { // URL is NOT in list of this visitors previously visited URLs
                                $debug .= "URL not previously visited. ";
                                $wpdb->query($wpdb->prepare("UPDATE `".$table_session."` SET urls = %s WHERE id = %d;", array($visitorsess->urls."\n".esc_sql(wp_json_encode(array("url" => $ntk_url, "id" => $ntk_contentid))), $visitorsess->id)));
                                // Now check if there are existing links which include the new URL
                                $synapses = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$table_synapse."` WHERE ((url1 = %s) OR (url2 = %s));", array($ntk_url, $ntk_url)));
                                foreach ($synapses AS $synapse) {
                                    if ((in_array($synapse->url1, $ntk_prevurls)) || (in_array($synapse->url2, $ntk_prevurls))) { // Visitor has been to the other URL in the synapse before
                                        $newweight = intval($synapse->weight) + 1;
                                        $wpdb->query($wpdb->prepare("UPDATE `".$table_synapse."` SET weight = %d WHERE id = %d;", array(esc_sql($newweight), $synapse->id)));
                                        $wpdb->query($wpdb->prepare("UPDATE `".$table_synapse."` SET timeupdated = %d WHERE id = %d;", array(time(), $synapse->id)));
                                        if ($newweight > $ntk_maximumweight) {
                                            update_option( 'ntkrpnn_neuralnet_maximumweight', $newweight );
                                        }
                                        // The link between URLs has been updated so remove it from the list
                                        if (in_array($synapse->url1, $ntk_prevurls)) { unset($ntk_prevurls[array_search($synapse->url1, $ntk_prevurls)]); }
                                        if (in_array($synapse->url2, $ntk_prevurls)) { unset($ntk_prevurls[array_search($synapse->url2, $ntk_prevurls)]); }
                                    }
                                }
                                // Any of the visitors previous URLs which do not already have synapses linked to the new URL should be added
                                $addsynapse = true;
                                if ($unl != "Y") {
                                    $ntk_urlcount = intval($wpdb->get_var("SELECT COUNT(*) FROM `" . $table_count. "`"));
                                    if ($ntk_urlcount > 29) { $addsynapse = false; }
                                }
                                foreach ($ntk_prevurls AS $prevkey => $prevurl) {
                                    if ($addsynapse) {
                                        $prevurlpart = json_decode($prevurl, true);
                                        $rows_affected = $wpdb->insert( $table_synapse, array( 'url1' => esc_sql($ntk_url), 'post1' => esc_sql($ntk_contentid), 'url2' => esc_sql($prevurl), 'post2' => esc_sql($ntk_previds[$prevkey]), 'weight' => 1, 'timeupdated' => time() ) );
                                    }
                                }
                                // Add the visit to the url counter table
                                ntk_updatescore($ntk_url, $ntk_contentid);
                            } else {
                                $debug .= "URL previously visited. ";
                            }
                        } else { // Visitor has a new session
                            $rows_affected = $wpdb->insert( $table_session, array( 'sessionid' => esc_sql($ntk_uniqid), 'timeadded' => time(), 'urls' => esc_sql(wp_json_encode(array("url" => $ntk_url, "id" => $ntk_contentid))) ) );
                            ntk_updatescore($ntk_url, $ntk_contentid);
                        }
                    }
                } // End if check for maximum weight
            }
        }
        print("{\"id\":\"".$ntk_uniqid."\", \"url\":\"".$ntk_url."\", \"contentid\":\"".$ntk_contentid."\", \"debug\":\"".$debug."\"}");
    }

    function ntk_updatescore($ntk_url, $ntk_contentid) { // Add the visit to the url counter table
        global $wpdb, $table_count, $ntk_maximumscore;
        $urlcount = $wpdb->get_row($wpdb->prepare("SELECT * FROM `".$table_count."` WHERE (url = %s) ;", array($ntk_url)));
        if (!is_null($urlcount)) { // URL exists in the database already, so update it
            $newscore = intval($urlcount->score) + 1;
            $wpdb->query($wpdb->prepare("UPDATE `".$table_count."` SET score = %d WHERE id = %d;", array(esc_sql($newscore), $urlcount->id)));
            $wpdb->query($wpdb->prepare("UPDATE `".$table_count."` SET timeupdated = %d WHERE id = %d;", array(time(), $urlcount->id)));
            if ($newscore > $ntk_maximumscore) {
                update_option( 'ntkrpnn_neuralnet_maximumscore', $newscore );
            }
        } else { // URL does not exist in the database, so add it
            $rows_affected = $wpdb->insert( $table_count, array( 'url' => esc_sql($ntk_url), 'postid' => esc_sql($ntk_contentid), 'score' => 1, 'timeupdated' => time() ) );
        }
    }
?>