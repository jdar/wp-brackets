<?php

/*
Plugin Name: WP-Brackets
Description: Create Tournament Brackets for WordPress. A heavy customization of WP-surveys by Martin Mozos, with a scoring algorithm and a Leaderboard widget thrown in (activate separately)  Some features: Create, modify and retire brackets; Display on a (hardcoded) templated page. Finals brackets question combine regular brackets. Leaderboard contact question-type and tie-breaker question types also included.

Author: Darius Roberts (dariusroberts.com)
Version: 0.1
Author URI: http://dariusroberts.com 
Plugin URI: http://dariusroberts.com/pages/opensource
*/

/*  
Copyright 2008 Darius Roberts (email: dariusroberts AT gmail DOT com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

For a copy of the GNU General Public License, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
http://www.gnu.org/licenses/gpl.html
*/

/*******************************************************************************************************/

if ( file_exists(ABSPATH . PLUGINDIR . '/polyglot.php') ) {
	$current_plugins = get_option('active_plugins');
	if (in_array('polyglot.php', $current_plugins)) {
		require_once(ABSPATH . PLUGINDIR . '/polyglot.php');
		polyglot_init();
		}
	}

load_plugin_textdomain('wp-surveys',$path='wp-content/plugins/wp-surveys'); 

// ----------------------------------------------------------------
register_activation_hook(__FILE__, 'surveys_install');
add_filter('the_content', 'view_surveys');
add_action('admin_menu', 'surveys_admin_page');
//add_filter('admin_head', 'admin_header');
//add_action('wp_head', 'plugin_header');
add_action('init', 'wpsurv_submit_survey');
// ----------------------------------------------------------------

function view_surveys($content = '') {
	global $table_prefix, $wpdb;
	ob_start();
	require_once("wp-surveys-out.php");
	$output = ob_get_contents();
	ob_end_clean();
		return preg_replace("/\[sports_form\]/", $output, $content);
	}
 
function surveys_admin_page() {
	if(function_exists('add_menu_page'))
		add_menu_page(__('Surveys', 'wp-surveys'), __('Surveys', 'wp-surveys'), 2, __FILE__, 'manage_surveys');
	}
/*
function admin_header() {
	if(substr(($_REQUEST[page]), 0, 10) == 'wp-surveys') {
		echo	'<style type="text/css">\n
					</style>';
		}
		return;
	}

function plugin_header() { ?>
	<style type="text/css" media="screen">
	</style><?php
   }
*/
function wpsurv_submit_survey() {
	global $table_prefix, $wpdb;
	$survey_table = $table_prefix . "surveys";
	$question_table = $table_prefix . "surveys_questions";
	$answer_table = $table_prefix . "surveys_responses";
	$data_table = $table_prefix . "surveys_data";	
	
	session_start();
	$_SESSION['voted'] = false;
	$_SESSION['novote'] = false;
    if(($_POST['wpsurveys_button'] == __('Submit Survey', 'wp-surveys')) && is_numeric($_POST['survey_id']) && (!isset($_COOKIE['voted']))) {
		$survey_id = $_POST['survey_id'];

		$unique_id = md5(uniqid(rand(), true)); # $_SESSION['user_id']; #
		# BREAK if user has already played. ... send them to the edit screen.

		$questions = $_POST['option'];

/*  THIS SECTION COULD BE USED TO VALIDATE ALL QUESTIONS ANSWERED
 * however, the more important validation would be reseting the bracket-node children to reflect new parent choices.
 * In any case, this is not vital to the Mission!

		$answer_count = 0;
		if($questions) {
			foreach($questions as $question_id => $score)
				$answer_count++;
			}
		$question_count = $wpdb->get_var("SELECT COUNT(*) FROM `".$question_table."` WHERE `survey_id`=".$survey_id.";");
		if(($answer_count != $question_count) || (!$questions)) {
			$_SESSION['novote'] = true;
			session_write_close();
			return;
			}	
*/	   
		$leaderboard_name = $_POST['leaderboard_name']; #|| "anonymous coward"
		$leaderboard_email = $_POST['leaderboard_email']; #|| "anonymous coward"
		
		$wpdb->query("INSERT INTO `".$answer_table."` (`response_id`, `leaderboard_name`,`leaderboard_email`,`survey_id`, `response_unique_id`, `response_datestamp`) VALUES ('', '".$leaderboard_name."', '".$leaderboard_email."', '".$survey_id."', '".$unique_id."', '".gmdate("Y-m-d H:i:s", time())."');");
		$all_survey_questions = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE (`survey_id`=".$survey_id.") AND (`question_type` != 'leaderboard');",ARRAY_A);
		
		foreach($all_survey_questions as $current_quest) {
			unset ($full_options); 
			$option_count = 0; 
			$questionid = $current_quest['question_id']; #$all_survey_questions[$i]['question_id'];
			$roll = count($questions[$questionid]) + 1;
			for($j = 0; $j < $roll; $j++) {
				if(($questions[$questionid][$j] != '') && ($option_count > 0))
					$full_options = $full_options." | ";
				if($questions[$questionid][$j] != '') {
					$full_options = $full_options.htmlspecialchars($questions[$questionid][$j]);
					$option_count++;
					}
				}
				
			$current_answer_options['columns'] = "";
			$current_answer_options['values'] = "";
			
				$answer_options = array(
					"answer_option_0_1",
							"answer_option_0_3",
					"answer_option_2_3",
									"answer_option_0_7",																
					"answer_option_4_5",
							"answer_option_4_7",									
					"answer_option_6_7",		
														"answer_option_0_15",
					"answer_option_8_9",
							"answer_option_8_11",
					"answer_option_10_11",
									"answer_option_8_15",
					"answer_option_12_13",
							"answer_option_12_15",
					"answer_option_14_15"			
					);
						
			
				$empty_opts = 0;
				for($k = 0; $k < 16; $k++) {
					$opt = 'question_option_'.$k;
					if ($current_quest[$opt] == '')
					$empty_opts++;
					}
				if(($empty_opts == 16) && (is_null($full_options)))
					$full_options=__('No option(s) defined yet', 'wp-surveys');
				elseif(is_null($full_options))
					$full_options=__('No response recorded', 'wp-surveys');

			if(($current_quest['question_type'] == 'bracket') || ($current_quest['question_type'] == 'finals')) {
				$full_options="";
				# for brackets / finals ...				
				foreach($answer_options as $answer_option) {
					$response_answer = $_POST['answer_option'][$questionid][$answer_option];
					if($response_answer != '') {
						$current_answer_options['columns'] = $current_answer_options['columns']."`".$answer_option."`, ";
						$current_answer_options['values'] = $current_answer_options['values']."'".htmlspecialchars($response_answer)."', ";						
					}
				}			
			 }
			elseif($current_quest['question_type'] == 'tie-breaker') {
				$full_options = "";
				if($_POST['tie-breaker-1'] != "")	{			
					$full_options = $full_options.htmlspecialchars($_POST['tie-breaker-1']);
					if($_POST['tie-breaker-2'] != "")	{	
						$full_options = $full_options."-".htmlspecialchars($_POST['tie-breaker-2']);
					}
				}
			}

		# model to database			
		$responded_survey_id = $wpdb->get_var("SELECT `response_id` FROM `".$answer_table."` WHERE `response_unique_id`='".$unique_id."' LIMIT 1;");
		if(is_numeric($questionid))
			$wpdb->query("INSERT INTO `".$data_table."` (`data_id`, `question_id`, `data_option`, ".$current_answer_options['columns']."`response_id`) VALUES ('', '$questionid', '$full_options', ".$current_answer_options['values']." '$responded_survey_id');");
		}				
		

		$survey_repost = $wpdb->get_var("SELECT `survey_repost` FROM `".$survey_table."` WHERE `survey_id`=".$survey_id." LIMIT 1;");
		$expiry 	   = gmmktime()+(60 * 60 * 24 * $survey_repost);
		//$expiry = gmmktime() + 60; // expires in 60 seconds for testing


		# COMMENT THIS OUT IF YOU WANT USERS TO BE ABLE TO VOTE MULTIPLE TIMES!!!
		setcookie("voted[survey_id]", $survey_id, $expiry);
		setcookie("voted[unique_id]", $unique_id, $expiry);
		$_SESSION['voted'] = true;		
		
		}
	session_write_close();
	}
 
function manage_surveys() {
	global $table_prefix, $wpdb;
	$survey_table = $table_prefix . "surveys";
	if(isset($_REQUEST["wpsurv_submit"])) {
		require_once('functions.php');
	 	if(($_POST["wpsurv_submit"] == __('Edit', 'wp-surveys')) || ($_POST["wpsurv_submit"] == __('Cancel', 'wp-surveys')) || ($_POST["wpsurv_submit"] == __('Back to Edit Survey', 'wp-surveys')))
			edit($_POST['survey_id']);
	    elseif($_POST["wpsurv_submit"] == __('Update', 'wp-surveys'))
			update($_POST['survey_id']);
		elseif($_POST["wpsurv_submit"] == __('Update Options', 'wp-surveys'))
			update_options($_POST['survey_id'], $_POST['question_id']);
		elseif(($_POST["wpsurv_submit"] == __('Activate', 'wp-surveys')) || ($_POST["wpsurv_submit"] == __('Make Active', 'wp-surveys')))
			activate($_POST['survey_id']);
		elseif($_POST["wpsurv_submit"] == __('Retire', 'wp-surveys'))
			retire($_POST['survey_id']);
		elseif($_POST["wpsurv_submit"] == __('Update Leaderboard', 'wp-surveys'))
			recach_utility($_POST['survey_id']);
		elseif(($_POST["wpsurv_submit"] == __('Edit Options', 'wp-surveys')) || ($_POST["wpsurv_submit"] == __('Add Some Options', 'wp-surveys')) || ($_POST["wpsurv_submit"] == __('Edit or Add more Options', 'wp-surveys')))
			edit_options($_POST['survey_id'], $_POST['question_id']);
		elseif(($_POST["wpsurv_submit"] == __('Add More Questions', 'wp-surveys')) || ($_POST["wpsurv_submit"] == __('Add Some Questions', 'wp-surveys')))
			add_question($_POST['survey_id']);
  		elseif($_POST["wpsurv_submit"] == __('Step 2', 'wp-surveys'))
			step2($_POST['survey_id']);
		elseif($_POST["wpsurv_submit"] == __('Create Question', 'wp-surveys'))
			create_quest($_POST['survey_id']);
  		elseif(($_POST["wpsurv_submit"] == __('Change Survey', 'wp-surveys')) || ($_POST["wpsurv_submit"] == __('Add Survey', 'wp-surveys')))
			survey($_POST['survey_id']);
  		elseif($_POST["wpsurv_submit"] == __('Add This Survey', 'wp-surveys')) 
			add_survey($_POST['survey_id']);
		elseif($_POST["wpsurv_submit"] == __('View Survey Results', 'wp-surveys'))
			results($_POST['survey_id']);
		//elseif($_POST["wpsurv_submit"] == __('View Survey Results in CSV File', 'wp-surveys'))
			//results_CSV($_POST['survey_id']);    			
    	//elseif($_POST["wpsurv_submit"] == __('Delete File', 'wp-surveys'))
			//delete_file($_POST['survey_id']);
		}
	else {
		$current_plugins = get_option('active_plugins');
		if ((file_exists(ABSPATH . PLUGINDIR . '/polyglot.php')) && (in_array('polyglot.php', $current_plugins)))
			$polyglot = true;
		echo '<div class="wrap">';	
		$open_surveys = $wpdb->get_results("SELECT * FROM `".$survey_table."` WHERE `survey_open`='1' LIMIT 1;",ARRAY_A);
		echo '<h2>'.__('Survey Management', 'wp-surveys').'</h2><h3><u>'.__('Active Survey', 'wp-surveys').'</u>:</h3>';		
		if($open_surveys) {
			echo '<table class="widefat" width="100%" cellpadding="4" cellspacing="4">';
			echo '<tr><th align="left">'.__('Title', 'wp-surveys').'</th><th align="left">'.__('Description', 'wp-surveys').'</th><th></th><th></th><th></th></tr>';
			foreach($open_surveys as $survey) {
				echo '<tr class="alternate">';
				if($polyglot)
					echo '<td><b>'.polyglot_filter(stripcslashes($survey['survey_name'])).'</b></td><td>'.polyglot_filter(stripcslashes($survey['survey_describe'])).'</td>';
				elseif(!$polyglot)
					echo '<td><b>'.stripcslashes($survey['survey_name']).'</b></td><td>'.stripcslashes($survey['survey_describe']).'</td>';
				echo '<td class="submit" align="center"><form method="post" action=""><input type="hidden" name="survey_id" value="'.$survey['survey_id'].'" />';
				echo '<input type="submit" name="wpsurv_submit" value="'.__('Edit', 'wp-surveys').'" /></form></td>';
				echo '<td class="submit" align="center"><form method="post" action=""><input type="hidden" name="survey_id" value="'.$survey['survey_id'].'" />';
				echo '<input type="submit" name="wpsurv_submit" value="'.__('Retire', 'wp-surveys').'" /></form></td>';
				echo '<td class="submit" align="center"><form method="post" action=""><input type="hidden" name="survey_id" value="'.$survey['survey_id'].'" />';
				echo '<input type="submit" name="wpsurv_submit" value="'.__('View Survey Results', 'wp-surveys').'" /></form></td>';
				//echo '<td align="center"><form method="post" action=""><input type="hidden" name="survey_id" value="'.$survey['survey_id'].'" />';
				//echo '<input type="submit" name="wpsurv_submit" value="'.__('View Survey Results in CSV File', 'wp-surveys').'" /></form></td>';
				echo '</tr>';
				}
			echo '</table>';
			$next = ++$survey['survey_id'];
			echo '<br /><form method="post" action=""><input type="hidden" name="survey_id" value="'.$next.'" /><input class="button" type="submit" name="wpsurv_submit" value="'.__('Change Survey', 'wp-surveys').'" title="'.__('Current Survey will be saved as Retired', 'wp-surveys').'" /></form>';
			}
		else {
			echo __('There are no open tournaments or contests.', 'wp-surveys').'.';
			//$last = $wpdb->get_var("SELECT COUNT(`survey_id`) FROM `".$survey_table."`;");
			$last = $wpdb->get_var("SELECT `survey_id` FROM `".$survey_table."` ORDER BY `survey_id` DESC LIMIT 1;");
			$next = ++$last;
			echo '<br /><br /><form method="post" action=""><input type="hidden" name="survey_id" value="'.$next.'" /><input class="button" type="submit" name="wpsurv_submit" value="'.__('Add Survey', 'wp-surveys').'" /></form>';
			}
		$closed_surveys = $wpdb->get_results("SELECT * FROM `".$survey_table."` WHERE `survey_open`='0';",ARRAY_A);
		echo '<h3><u>'.__('Retired Surveys', 'wp-surveys').'</u>:</h3>';
		if($closed_surveys) {
			echo '<table class="widefat" width="100%" cellpadding="4" cellspacing="4">';
			echo '<tr><th align="left">'.__('Title', 'wp-surveys').'</th><th align="left">'.__('Description', 'wp-surveys').'</th><th></th><th></th><th></th></tr>';
			foreach($closed_surveys as $survey) {
				echo '<tr class="alternate">';
				if($polyglot)
					echo '<td><b>'.polyglot_filter(stripcslashes($survey['survey_name'])).'</b></td><td>'.polyglot_filter(stripcslashes($survey['survey_describe'])).'</td>';
				elseif(!$polyglot)
					echo '<td><b>'.stripcslashes($survey['survey_name']).'</b></td><td>'.stripcslashes($survey['survey_describe']).'</td>';
				echo '<td class="submit" align="center"><form method="post" action=""><input type="hidden" name="survey_id" value="'.$survey['survey_id'].'" />';
				echo '<input type="submit" name="wpsurv_submit" value="'.__('Edit', 'wp-surveys').'" /></form></td>';
				echo '<td class="submit" align="center"><form method="post" action=""><input type="hidden" name="survey_id" value="'.$survey['survey_id'].'" />';
				echo '<input type="submit" name="wpsurv_submit" value="'.__('Make Active', 'wp-surveys').'" /></form></td>';
				echo '<td class="submit" align="center"><form method="post" action=""><input type="hidden" name="survey_id" value="'.$survey['survey_id'].'" />';
				echo '<input type="submit" name="wpsurv_submit" value="'.__('View Survey Results', 'wp-surveys').'" /></form></td>';
				//echo '<td align="center"><form method="post" action=""><input type="hidden" name="survey_id" value="'.$survey['survey_id'].'" />';
				//echo '<input type="submit" name="wpsurv_submit" value="'.__('View Survey Results in CSV File', 'wp-surveys').'" /></form></td>';
				echo '</tr>';
				}
				echo '</table>';
			}
		else echo __('There are no retired surveys', 'wp-surveys').'.';
		echo '</div>';
		}
	}
	
function surveys_install() {
	global $table_prefix, $wpdb;
	$survey_table = $table_prefix . "surveys";
	$question_table = $table_prefix . "surveys_questions"; 
	$answer_table = $table_prefix . "surveys_responses";
	$data_table = $table_prefix . "surveys_data";
	if($wpdb->get_var("SHOW TABLES LIKE '$survey_table'") != $survey_table) {
		$sql = "CREATE TABLE `".$survey_table."` (".
		"`survey_id` bigint(16) unsigned NOT NULL auto_increment,".
		"`survey_name` varchar(255) NOT NULL,".
		"`survey_describe` text,".
		"`survey_open` bigint(16) NOT NULL default '0',".
		"`survey_repost` bigint(16) NOT NULL default '90',".
		"`survey_page_chain` text NOT NULL,".
		"`survey_redirect_URL` text NOT NULL,".
		"`survey_share_results` bigint(16) NOT NULL default '0',".
		"PRIMARY KEY (`survey_id`)) DEFAULT CHARACTER SET utf8;";
		$wpdb->query($sql);
		}
	if($wpdb->get_var("SHOW TABLES LIKE '$question_table'") != $question_table) {
		$sql = "CREATE TABLE `".$question_table."` (".
		"`question_id` bigint unsigned NOT NULL auto_increment,".
		"`question_name` text NOT NULL,".
		"`question_type` varchar( 128 ) NOT NULL,".
		"`question_option_0` text,".
		"`question_option_1` text,".
		"`question_option_2` text,".
		"`question_option_3` text,".
		"`question_option_4` text,".
		"`question_option_5` text,".
		"`question_option_6` text,".
		"`question_option_7` text,".
		"`question_option_8` text,".
		"`question_option_9` text,".
		"`question_option_10` text,".
		"`question_option_11` text,".
		"`question_option_12` text,".
		"`question_option_13` text,".
		"`question_option_14` text,".
		"`question_option_15` text,".

		"`answer_option_0_1` text,".
				"`answer_option_0_3` text,".
		"`answer_option_2_3` text,".
						"`answer_option_0_7` text,".																
		"`answer_option_4_5` text,".
				"`answer_option_4_7` text,".									
		"`answer_option_6_7` text,".		
											"`answer_option_0_15` text,".
		"`answer_option_8_9` text,".
				"`answer_option_8_11` text,".
		"`answer_option_10_11` text,".
						"`answer_option_8_15` text,".
		"`answer_option_12_13` text,".
				"`answer_option_12_15` text,".
		"`answer_option_14_15` text,".


		"`question_rows` bigint(16) NOT NULL default '1',".
		"`survey_id` bigint unsigned NOT NULL,".    
		"`question_forever` text NOT NULL,".
		"`question_manditory` bigint(16) NOT NULL default '0',".
		"PRIMARY KEY (`question_id`)) DEFAULT CHARACTER SET utf8;";
		$wpdb->query($sql);
		}
	if($wpdb->get_var("SHOW TABLES LIKE '$answer_table'") != $answer_table) {
		$sql = "CREATE TABLE `$answer_table` (".
		"`response_id` bigint unsigned NOT NULL auto_increment,".
		"`survey_id` bigint unsigned NOT NULL,".
		
		# these are special, for leaderboard purposes (utility_cache gets recalculated by manual function.)
		"`leaderboard_name` text NOT NULL,".
		"`leaderboard_email` text NOT NULL,".
		"`utility_cache` bigint unsigned NOT NULL,".
		
		"`response_unique_id` varchar( 128 ) NOT NULL,".
		"`response_datestamp` datetime NOT NULL default '0000-00-00 00:00:00',".
		"PRIMARY KEY (`response_id`)) DEFAULT CHARACTER SET utf8;";
		$wpdb->query($sql);
		}
	if($wpdb->get_var("SHOW TABLES LIKE '$data_table'") != $data_table) {
		$sql = "CREATE TABLE `$data_table` (".
		"`data_id` bigint unsigned NOT NULL auto_increment,".
		"`question_id` bigint unsigned NOT NULL,".
		"`data_option` text NOT NULL,".				
		
		# these are special, for bracket question type
		"`answer_option_0_1` text,".
				"`answer_option_0_3` text,".
		"`answer_option_2_3` text,".
						"`answer_option_0_7` text,".																
		"`answer_option_4_5` text,".
				"`answer_option_4_7` text,".									
		"`answer_option_6_7` text,".		
											"`answer_option_0_15` text,".
		"`answer_option_8_9` text,".
				"`answer_option_8_11` text,".
		"`answer_option_10_11` text,".
						"`answer_option_8_15` text,".
		"`answer_option_12_13` text,".
				"`answer_option_12_15` text,".
		"`answer_option_14_15` text,".
		
		
		"`response_id` bigint unsigned NOT NULL,".
		"PRIMARY KEY (`data_id`)) DEFAULT CHARACTER SET utf8;";
		$wpdb->query($sql);
		}
	} 


function recach_utility($survey_id) {
	
	global $table_prefix, $wpdb;
	$survey_table = $table_prefix . "surveys";
 	$question_table = $table_prefix . "surveys_questions";
	$data_table = $table_prefix . "surveys_data";
	$answer_table = $table_prefix . "surveys_responses";
	$responses = $wpdb->get_results("SELECT * FROM `".$answer_table."` WHERE `survey_id`=".$survey_id.";",ARRAY_A);
	# $responses have 'leaderboard_name' 'utility_cache' ... an overloaded join table for survey and data

	
	#hard coded
		$bracket_questions = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE `survey_id`=".$survey_id." and `question_type` = 'bracket' ORDER BY question_id ASC;",ARRAY_A);
		$finals_questions = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE `survey_id`=".$survey_id." and `question_type` = 'finals' ORDER BY question_id ASC;",ARRAY_A);

			$first_round_questions = array('answer_option_0_1','answer_option_2_3','answer_option_4_5','answer_option_6_7','answer_option_8_9','answer_option_8_9','answer_option_10_11','answer_option_12_13','answer_option_14_15');    // 10
			$second_round_questions = array('answer_option_0_3','answer_option_4_7','answer_option_8_11','answer_option_12_15');   // 20
			$third_round_questions = array('answer_option_0_7','answer_option_8_15');    // 40
			$fourth_round_questions = array('answer_option_0_15');      // 80
	#		semifinals_questions   // 160
	#		finals_questions   // 320

			if($responses) {

          echo "<p>There are ".count($responses)." total responses to this survey so far.";

				echo "<table>";
				foreach($responses as $leader) {
					if($leader['leaderboard_email'] == "")
					    $leader['leaderboard_email'] = "Anonymous Coward";
					echo "<tr><td>".$leader['leaderboard_name']."</td><th>".$leader['leaderboard_email']."</th><td></td></tr><tr><td></td><td>";
					$utility = 0;
                                        $possible_points = 0;
                                        $possible_points_string = "";
					foreach($bracket_questions as $question) {
							$predicted = $wpdb->get_results("SELECT * FROM ".$data_table." WHERE (question_id = ".$question['question_id'].") AND (response_id = ".$leader['response_id'].")", ARRAY_A);
							$predicted = $predicted[0];
							$possible_points_string = $possible_points_string."<br />".($question['question_name']);
							echo "<br />".($question['question_name']);
							
	    foreach($first_round_questions  as $option) { 
                        if($question[$option] != "") {
                                 $possible_points_string = $possible_points_string."->".($possible_points += 10);
                                 if($predicted[$option] == $question[$option]) 
                                               echo "->".($utility += 10); 
                         }
             }
	    foreach($second_round_questions as $option) { 
                        if($question[$option] != "") {
                                 $possible_points_string = $possible_points_string."->".($possible_points += 20);
                                 if($predicted[$option] == $question[$option]) 
                                               echo "->".($utility += 20); 
                         }
             }
	     foreach($third_round_questions  as $option) { 
                        if($question[$option] != "") {
                                 $possible_points_string = $possible_points_string."->".($possible_points += 40);
                                 if($predicted[$option] == $question[$option]) 
                                               echo "->".($utility += 40); 
                         }
            }
	    foreach($fourth_round_questions as $option) { 
                        if($question[$option] != "") {
                                 $possible_points_string = $possible_points_string."->".($possible_points += 80);
                                 if($predicted[$option] == $question[$option]) 
                                               echo "->".($utility += 80); 
                         }
            }
					}
					echo "</td><td>";
					foreach($finals_questions as $question) {
							$predicted = $wpdb->get_results("SELECT * FROM ".$data_table." WHERE (question_id = ".$question['question_id'].") AND (response_id = ".$leader['response_id'].")", ARRAY_A);								
							$p = $predicted[0];
							
							if($p){
								
							$winner_names = array();
							for($z=0;$z<count($bracket_questions);$z++) {
								// these kill the bracket if anything left unchecked.
								// this is also where integrity checking would occur
#									if($bracket_questions[$z] == "") { break; }
#									if($bracket_questions[$z]['answer_option_0_15'] == "") { break; }

								if($bracket_questions[$z][$bracket_questions[$z]['answer_option_0_15']] != "")
									$winner_names[$z] = $bracket_questions[$z][$bracket_questions[$z]['answer_option_0_15']];
							}


                                             $possible_points_string = $possible_points_string."<br />FINALS ";
                                            if($question['answer_option_0_1'] != "") 
                                                           $possible_points_string = $possible_points_string."->".($possible_points += 160);
                                            if($question['answer_option_2_3'] != "") 
                                                           $possible_points_string = $possible_points_string."->".($possible_points += 160);
                                            if($question['answer_option_0_3'] != "") 
                                                            $possible_points_string = $possible_points_string."->".($possible_points += 320);

							$semis = 0;
							foreach($winner_names as $name) {
								if($question[$p['answer_option_0_1']] == $name) { $semis++; }
								if($question[$p['answer_option_2_3']] == $name) { $semis++; }
							}
							for($semis;$semis>0;$semis--) 
							    echo "->".($utility += 160); 
							
								
							$finals = 0;
							foreach($winner_names as $name) {
								# when the name is right...
								if($question[$p['answer_option_0_3']] == $name) {
									# check that option string keys are the same... (i.e., 'option_2' is the same as 'option_2')
								  if($p['answer_option_0_1'] == $p['answer_option_0_3'] || $p['answer_option_2_3'] == $p['answer_option_0_3']) { 
									  echo "->JACKPOT";
									  $finals++; 
									}
								}
							}
							if($finals != 0)
							    echo "->".($utility += 320);
						
						
						}	
					}
				$wpdb->query("UPDATE `".$answer_table."` SET `utility_cache` = $utility WHERE `response_id` = ".$leader['response_id'].";");
				echo "</td></tr>";
				}
				echo "</table>";
				echo "<br /><b> TOTAL POSSIBLE POINTS, BY QUESTION:<br />".$possible_points_string."</b>";
				echo "<a href=admin.php?page=wp-bracket/wp-surveys.php>Back</a>";
			}
	
	
	
}


?>
