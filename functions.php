<?php
load_plugin_textdomain('wp-surveys',$path='wp-content/plugins/wp-surveys');
	
function edit($survey_id) {
	global $table_prefix, $wpdb;
	$survey_table = $table_prefix . "surveys";
 	$question_table = $table_prefix . "surveys_questions";
	$survey_raw_data = $wpdb->get_results("SELECT * FROM `".$survey_table."` WHERE `survey_id`=".$survey_id." LIMIT 1;",ARRAY_A);
	$survey = $survey_raw_data[0];
	echo '<div class="wrap">';
	echo '<h2>'.__('Edit Survey', 'wp-surveys').'</h2><br />';
	echo '<form action="" method="post" >';
	echo '<table class="widefat" id="the-list-x" width="100%" cellpadding="4" cellspacing="4">';
	echo '<tr valign="top"><td class="alternate" width="22%"><strong>'.__('Survey Title', 'wp-surveys');
	echo ':</strong>&nbsp;</td><td class="alternate" width="78%"><textarea class="alternate-active" name="name" rows="1" cols="86">';
	if(isset($_POST['name']) && (trim($_POST['name'] != '')))
		echo stripcslashes(trim($_POST['name']));
	elseif((!$_POST['name']) || (trim($_POST['name'] == '')))
		echo stripcslashes($survey['survey_name']);
	echo '</textarea><br /></td></tr><tr valign="top"><td class="alternate" width="22%"><strong>'.__('Survey Description', 'wp-surveys');
	echo ':</strong>&nbsp;</td><td class="alternate" width="78%"><textarea class="alternate-active" name="describe" rows="2" cols="86">';
	if(isset($_POST['describe']))
		echo stripcslashes(trim($_POST['describe']));
	elseif(!$_POST['describe'])
		echo stripcslashes($survey['survey_describe']);
	echo '</textarea><br /></td></tr><tr valign="top"><td class="alternate" width="22%"><strong>'.__('Days until user can repost', 'wp-surveys');
	echo ':</strong>&nbsp;</td><td class="alternate" width="78%"><input class="alternate-active" type="text" name="repost" value="';
	if(isset($_POST['repost']) && (ctype_digit(trim($_POST['repost']))) && (trim($_POST['repost'] != 0)))
		echo stripcslashes(trim($_POST['repost']));
	elseif((!$_POST['repost']) || (!ctype_digit(trim($_POST['repost']))) || (trim($_POST['repost'] == 0)))
		echo stripcslashes($survey['survey_repost']);
	echo '"/><br /></td></tr>';
	/*echo '<tr valign="top"><td valign="top"><strong>'.__('Re-direct Link', 'wp-surveys').':&nbsp;<font color=red>'.__('Coming in V 8.0!', 'wp-surveys').'</font></strong><br/>'.__('Blank will display thank-you message', 'wp-surveys').'.<br/>[ '.__('NOTE: should start with http://', 'wp-surveys').' ] </td><td>';
	echo '<textarea class="alternate-active" name="go_url" rows="2" cols="42">'.stripcslashes($survey['survey_redirect_URL']).'</textarea><br /></td></tr>';
	echo '<tr valign="top"><td valign="top"><strong>'.__('Results are Public?', 'wp-surveys').' <font color=red>'.__('Coming in V 8.0!', 'wp-surveys').'</strong></font><br/>'.__('Yes, will display results page after completion', 'wp-surveys').'.<br/>'.__('Overrides redirect URL', 'wp-surveys').'.</td><td>';
	echo '<select name="public"><option value=0';
	if($survey['survey_share_results']=='0')
		echo ' selected="selected"';
	echo '>'.__('No', 'wp-surveys').'</option><option value=1';
	if($survey['survey_share_results']=='1')
		echo ' selected="selected"';
	echo '>'.__('Yes', 'wp-surveys').'</option></select></td></tr>';*/
	$quests = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE `survey_id`=".$survey_id.";",ARRAY_A);
	if($quests) {
		echo '<tr><td width="22%" align="center"><input type="hidden" name="survey_id" value="'.$survey_id.'" />';
		echo '<a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" >'.__('Go back!', 'wp-surveys').'</a></td>';
		echo '<td class="submit" width="78%"><input type="submit" name="wpsurv_submit" value="'.__('Add More Questions', 'wp-surveys').'" />';
		echo '<input type="submit" name="wpsurv_submit" value="'.__('Update', 'wp-surveys').'" />';
		$open = $wpdb->get_var("SELECT `survey_open` FROM `".$survey_table."` WHERE `survey_id`=".$survey_id." LIMIT 1;");
		if ($open == 0)
			echo '<input type="submit" name="wpsurv_submit" value="'.__('Activate', 'wp-surveys').'" />';
		elseif ($open == 1)
			echo '<input type="submit" name="wpsurv_submit" value="'.__('Retire', 'wp-surveys').'" />';
			echo '<input type="submit" name="wpsurv_submit" value="'.__('Update Leaderboard', 'wp-surveys').'" />';
		echo '</td></tr>';
		$i = 1;
		foreach($quests as $question) {
			echo '<tr id="list-quest-'.$i.'" valign="top"><td class="alternate" align="right" width="22%"><strong>';
			printf(__('Question %s:', 'wp-surveys'), $i);
			echo '</strong><br />';
			if($question['question_type']=='one-vert')
				_e('Alignment Vertical', 'wp-surveys');
			elseif($question['question_type']=='one-hori')
				_e('Alignment Horizontal', 'wp-surveys');
			elseif($question['question_type']=='one-menu')
				_e('Alignment Menu', 'wp-surveys');
			elseif($question['question_type']=='bracket')
				_e('Bracket Menu', 'wp-surveys');
			elseif($question['question_type']=='finals')
				_e('Bracket Menu', 'wp-surveys');
			elseif($question['question_type']=='tie-breaker')
				_e('Tie Breaker Questions', 'wp-surveys');
			/*elseif($question['question_type']=='mul-vert')
				_e('Choice - Multiple Answers (Vertical)', 'wp-surveys');
			elseif($question['question_type']=='mul-hori')
				_e('Choice - Multiple Answers (Horizontal)', 'wp-surveys');
			elseif($question['question_type']=='open-onep')
				_e('Open Ended - One Line w/Prompt', 'wp-surveys');
			elseif($question['question_type']=='open-more')
				_e('Open Ended - One or More Lines w/Prompt', 'wp-surveys');
			elseif($question['question_type']=='open-essa')
				_e('Open Ended - Essay', 'wp-surveys');
			elseif($question['question_type']=='open-date')
				_e('Open Ended - Date and/or Time', 'wp-surveys');
			else _e('Undefined type', 'wp-surveys');*/
			echo '</td><td class="alternate" width="78%"><p>';
			if(isset($_POST['question'][$i][$question['question_id']]) && (trim($_POST['question'][$i][$question['question_id']] != '')))
				echo '<textarea class="alternate-active" name="question['.$i.']['.$question['question_id'].']" rows="1" cols="86">'.trim($_POST['question'][$i][$question['question_id']]).'</textarea></p>';
			elseif((!$_POST['question'][$i][$question['question_id']]) || (trim($_POST['question'][$i][$question['question_id']] == '')))
				echo '<textarea class="alternate-active" name="question['.$i.']['.$question['question_id'].']" rows="1" cols="86">'.$question['question_name'].'</textarea></p>';
			$i++;
			if(($question['question_type']=='bracket') || ($question['question_type']=='finals') || ($question['question_type']=='one-vert') || ($question['question_type']=='one-hori') || ($question['question_type']=='one-menu') || ($question['question_type']=='mul-vert') || ($question['question_type']=='mul-hori')) {
				$current_quest = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE `question_id`=".$question['question_id'].";",ARRAY_A);
				$empty_opts = 0;
				for($j = 0; $j < 16; $j++) {
					$opt = 'question_option_'.$j;
					if ($current_quest[0][$opt] == '')
						$empty_opts++;
					}
				echo '<form action="" method="post"><input type="hidden" name="survey_id" value="'.$survey_id.'" />';
				echo '<input class="button" type="submit" name="wpsurv_submit" value="';
				if($empty_opts == 0)
					_e('Edit Options', 'wp-surveys');
				elseif($empty_opts < 16)
					_e('Edit or Add more Options', 'wp-surveys');
				elseif($empty_opts == 16)
					_e('Add Some Options', 'wp-surveys');
				echo '" /><input type="hidden" name="question_id" value="'.$question['question_id'].'" /></form>';
				}
			//echo '<input type="submit" name="wpsurv_submit" value="'.__('Move Up', 'wp-surveys').'" />';
			//echo '<input type="submit" name="wpsurv_submit" value="'.__('Move Down', 'wp-surveys').'" />';
			//echo '<input type="submit" name="wpsurv_submit" value="'.__('Remove', 'wp-surveys').'" />';
			echo '</td></tr>';
			}
		}
	elseif(!$quests) {
		echo '<tr><input type="hidden" name="survey_id" value="'.$survey_id.'" /><td width="22%" align="center">';
		echo '<a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" >'.__('Go back!', 'wp-surveys').'</a></td>';
		echo '<td class="submit"><input type="submit" name="wpsurv_submit" value="'.__('Add Some Questions', 'wp-surveys').'" />';
		echo '<input type="submit" name="wpsurv_submit" value="'.__('Update', 'wp-surveys').'" />';
		$open = $wpdb->get_var("SELECT `survey_open` FROM `".$survey_table."` WHERE `survey_id`=".$survey_id." LIMIT 1;");
		if ($open == 0)
			echo '<input type="submit" name="wpsurv_submit" value="'.__('Activate', 'wp-surveys').'" />';
		elseif ($open == 1)
			echo '<input type="submit" name="wpsurv_submit" value="'.__('Retire', 'wp-surveys').'" />';
		echo '</td></tr>';
		}
	echo '</table></form></div>';
	}

function update($survey_id) {
	global $table_prefix, $wpdb;
	$survey_table = $table_prefix . "surveys";
 	$question_table = $table_prefix . "surveys_questions";
	$s_name = false; $s_desc = false; $s_repost = false; $s_quest = false; $fields = 0;
	$current_name = $wpdb->get_var("SELECT `survey_name` FROM `".$survey_table."` WHERE `survey_id`=".$survey_id." LIMIT 1;");
	if(trim($_POST['name']) == '') {
		echo '<div id="message" class="updated fade"><p><strong>'.__('ERROR!!', 'wp-surveys').'</strong></p>';
		echo '<p><strong>'.__('Question Title have been left empty!', 'wp-surveys').'</strong></p><p>'.__('No changes had been made in database', 'wp-surveys').'</p></div>';
		edit($_POST['survey_id']);
		return;
		}
	elseif($current_name != trim($_POST['name'])) $s_name = true;
	else $fields++;
	$current_describe = $wpdb->get_var("SELECT `survey_describe` FROM `".$survey_table."` WHERE `survey_id`=".$survey_id." LIMIT 1;");
	if($current_describe != trim($_POST['describe'])) $s_desc = true;
	else $fields++;
	//$current_url = $wpdb->get_var("SELECT `survey_redirect_URL` FROM `".$survey_table."` WHERE `survey_id`=".$survey_id." LIMIT 1;");
	//if($current_url != trim($_POST['go_url']))
		//$wpdb->query("UPDATE `".$survey_table."` SET `survey_redirect_URL` = '".$wpdb->escape(trim($_POST['go_url']))."' WHERE `survey_id`=".$survey_id." LIMIT 1;");
	$current_repost = $wpdb->get_var("SELECT `survey_repost` FROM `".$survey_table."` WHERE `survey_id`=".$survey_id." LIMIT 1;");
	if($current_repost != trim($_POST['repost'])) {
		if ((!ctype_digit(trim($_POST['repost']))) || (trim($_POST['repost']) == 0)) {
			echo '<div id="message" class="updated fade"><p><strong>'.__('ERROR!!', 'wp-surveys').'</strong></p>';
			echo '<p><strong>'.__('Days until user can repost must be a number bigger than zero!', 'wp-surveys').'</strong></p><p>'.__('No changes had been made in database', 'wp-surveys').'</p></div>';
			edit($_POST['survey_id']);
			return;
			}
		else $s_repost = true;
		}
	else $fields++;
	$quests = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE `survey_id`=".$survey_id.";",ARRAY_A);
	if($quests) {
		$j = 1;
		foreach($_POST['question'] as $quest_number => $question) {
			$cur_quest_name = $wpdb->get_var("SELECT `question_name` FROM `".$question_table."` WHERE `question_id`= ".key(&$question)." LIMIT 1;");
			if(trim($question[key(&$question)]) == '') {
				echo '<div id="message" class="updated fade"><p><strong>'.__('ERROR!!', 'wp-surveys').'</strong></p><p><strong>';
				printf(__('Question %s have been left empty!', 'wp-surveys'), $j);
				echo '</strong></p><p>'.__('No changes had been made in database', 'wp-surveys').'</p></div>';
				edit($_POST['survey_id']);
				return;
				}
			elseif(trim($question[key(&$question)]) != $cur_quest_name) $s_quest = true;
			else $fields++;
			$j++;
			}
		}
	if($fields == count($_POST['question']) + 3) {
		echo '<div id="message" class="updated fade"><p><strong>'.__('No changes had been made', 'wp-surveys').'</strong></p></div>';
		edit($_POST['survey_id']);
		return;
		}
	elseif(($s_name) || ($s_desc) || ($s_repost) || ($s_quest)) {
		$wpdb->query("UPDATE `".$survey_table."` SET `survey_name` = '".$wpdb->escape(trim($_POST['name']))."', `survey_describe` = '".$wpdb->escape(trim($_POST['describe']))."', `survey_repost` = '".$wpdb->escape(trim($_POST['repost']))."' WHERE `survey_id`=".$survey_id." LIMIT 1;");
		if($quests) {
			for($k=1; $k <= count($quests); $k++)
				$wpdb->query("UPDATE `".$question_table."` SET `question_name`='".$wpdb->escape(trim($_POST['question'][$k][key(&$_POST['question'][$k])]))."' WHERE `question_id`=".key(&$_POST['question'][$k])." LIMIT 1;");
			}
		echo '<div id="message" class="updated fade"><h3><strong>'.__('Update Complete!', 'wp-surveys').'</strong></h3><p>'.__('Changes have been applied', 'wp-surveys').'.</p></div>';
		edit($_POST['survey_id']);
		return;
		}
	}
	
function update_options($survey_id, $question_id) {
	global $table_prefix, $wpdb;
 	$question_table = $table_prefix . "surveys_questions";
	$fields = 0;
	for($i=0;$i<16;$i++) {
		$column = "question_option_".$i;
		$current_option = $wpdb->get_var("SELECT `".$column."` FROM `".$question_table."` WHERE `question_id`=".$question_id." LIMIT 1;");
		if($current_option != trim($_POST[$column]))
			$wpdb->query("UPDATE `".$question_table."` SET `".$column."`='".$wpdb->escape(trim($_POST[$column]))."' WHERE `question_id`=".$question_id." LIMIT 1;");
		else $fields++;
		}
		
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

		foreach($answer_options as $answer_option) {
		   	$current_option = $wpdb->get_var("SELECT `".$answer_option."` FROM `".$question_table."` WHERE `question_id`=".$question_id." LIMIT 1;");
			if($current_option != trim($_POST[$answer_option])) {
				$column_name = trim($_POST[$answer_option]);
				preg_match("/_(\d+)/",$column_name, $results);
#				$array_set = preg_match_batch("/$results[1]/"),$answer_options);				
#				echo 'Results: '.$array_set[1];


				$wpdb->query("UPDATE `".$question_table."` SET `".$answer_option."`='".$wpdb->escape(trim($_POST[$answer_option]))."' WHERE `question_id`=".$question_id." LIMIT 1;");
			} else $fields++; //to 15
		}	
	
	//$column = "question_forever";
	//$current_option = $wpdb->get_var("SELECT `".$column."` FROM `".$question_table."` WHERE `question_id`=".$question_id." LIMIT 1;");
	//if($current_option != $_POST[$column])
		//$wpdb->query("UPDATE `".$question_table."` SET `".$column."`='".$wpdb->escape($_POST[$column])."' WHERE `question_id`=".$question_id." LIMIT 1;");
	if($fields == (16 + 15)) {
		echo '<div id="message0" class="updated fade"><p><strong>'.__('No changes had been made', 'wp-surveys').'</strong></p></div>';
		edit_options($_POST['survey_id'], $_POST['question_id']);
		return;
		}
	else {
		echo '<div id="message" class="updated fade"><h3><strong>'.__('Update Complete!', 'wp-surveys').'</strong></h3><p class="submit">'.__('Changes have been applied', 'wp-surveys').'.</p><form method="post" action=""><p class="submit"><input type="hidden" name="survey_id" value="'.$survey_id.'" />';
		echo '<input type="submit" name="wpsurv_submit" value="'.__('Back to Edit Survey', 'wp-surveys').'" /></p></form>';
		echo '<p><a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" >'.__('Continue', 'wp-surveys').'...</a></p></div>';
		}
	}

	function preg_match_batch( $expr, $batch=array() ) {
	// create a placeholder for our results
	    $returnMe = array();
	// for every string in our batch ...
	    foreach( $batch as $str )
	    {
	// test it, and dump our findings into $found
	        preg_match($expr, $str, $found);
	// append our findings to the placeholder
	        $returnMe[$str] = $found;
	    }
	    return $returnMe;
	}	
	
function activate($survey_id) {
	global $table_prefix, $wpdb;
	$survey_table = $table_prefix . "surveys";
	$open = $wpdb->get_var("SELECT `survey_id` FROM `".$survey_table."` WHERE `survey_open`= '1' LIMIT 1;");
	$wpdb->query("UPDATE `".$survey_table."` SET `survey_open`='0' WHERE `survey_id`= ".$open." LIMIT 1;");
	$wpdb->query("UPDATE `".$survey_table."` SET `survey_open`='1' WHERE `survey_id`= ".$survey_id." LIMIT 1;");
	echo '<div id="message" class="updated fade"><h3><strong>'.__('Activation Complete!', 'wp-surveys').'</strong></h3><p>'.__('Changes have been applied', 'wp-surveys').'.</p></div>';
	unset($_REQUEST["wpsurv_submit"]);
	manage_surveys();
	}
	
	function recalculate($survey_id) {
		global $table_prefix, $wpdb;
		$survey_table = $table_prefix . "surveys";
		$users_table = $table_prefix . "users";
		$users = $wpdb->get_var("SELECT `survey_id` FROM `".$survey_table."` WHERE `survey_open`= '1' LIMIT 1;");
#		for($users as $user) {		
#			echo "something";
#				$wpdb->query("UPDATE `".$users_table."` SET `survey_open`='0' WHERE `survey_id`= ".$open." LIMIT 1;");
#			}
#		$wpdb->query("UPDATE `".$survey_table."` SET `survey_open`='0' WHERE `survey_id`= ".$open." LIMIT 1;");

		echo '<div id="message" class="updated fade"><h3><strong>'.__('Recalculation Complete!', 'wp-surveys').'</strong></h3><p>'.__('Changes have been applied', 'wp-surveys').'.</p></div>';
		unset($_REQUEST["wpsurv_submit"]);
		manage_surveys();
		}
		
function retire($survey_id) {
	global $table_prefix, $wpdb;
	$survey_table = $table_prefix . "surveys";
	$wpdb->query("UPDATE `".$survey_table."` SET `survey_open`='0' WHERE `survey_id`= ".$survey_id." LIMIT 1;");
	echo '<div id="message" class="updated fade"><h3><strong>'.__('Retire Complete!', 'wp-surveys').'</strong></h3><p>'.__('Changes have been applied', 'wp-surveys').'.</p></div>';
	unset($_REQUEST["wpsurv_submit"]);
	manage_surveys();
	}
	
function edit_options($survey_id, $question_id) {
	global $table_prefix, $wpdb;
 	$question_table = $table_prefix . "surveys_questions";
	$question_raw_data = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE `question_id`=".$question_id." LIMIT 1",ARRAY_A);
	$question = $question_raw_data[0];
	$empty_opts = 0;
	for($j = 0; $j < 16; $j++) {
		$opt = 'question_option_'.$j;
		if ($question[$opt] == '')
			$empty_opts++;
		}
	if($empty_opts != 16)
		echo '<div id="message" class="updated fade"><h3><strong>'.__('CAUTION!!', 'wp-surveys').'</strong></h3><p>'.__('Changing the text of options will affect the way that results are compiled for display on your site. Only current options (case-sensitive text) will be tallied in result generation', 'wp-surveys').'.</p><p><a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" >'.__('Back to manage surveys', 'wp-surveys').'...</a></p></div>';
	echo '<div class="wrap">';
	echo '<h2>'.__('Edit/Update Options', 'wp-surveys').'</h2>';
	$current_plugins = get_option('active_plugins');
	if((file_exists(ABSPATH . PLUGINDIR . '/polyglot.php')) && (in_array('polyglot.php', $current_plugins)))
		$polyglot = true;
	if($polyglot)
		echo '<p><strong>'.__('Question', 'wp-surveys').':</strong>&nbsp;&nbsp;'.polyglot_filter(stripcslashes($question['question_name'])).'</p>';
	elseif(!$polyglot)
		echo '<p><strong>'.__('Question', 'wp-surveys').':</strong>&nbsp;&nbsp;'.stripcslashes($question['question_name']).'</p>';
	echo '<form action="" method="post"><input type="hidden" name="question_id" value="'.$question_id.'" /><input type="hidden" name="survey_id" value="'.$survey_id.'" />';
	echo '<table id="the-list-x" width="100%" cellpadding="4" cellspacing="4">';
	for($i=0;$i<16;$i++) {
		echo '<tr valign="middle"><td class="alternate" align="center" width="22%"><strong>';
		printf(__('Option %s', 'wp-surveys'), $i+1);
		echo ':</strong>&nbsp;</td><td><textarea class="alternate-active" name="question_option_'.$i.'" rows="2" cols="86">';
		echo stripcslashes($question['question_option_'.$i.'']).'</textarea><br /></td></tr>';
		}
		if(($question['question_type']) == "bracket" || ($question['question_type']) == "finals") {
			printf('
				<style type="text/css">
				<!--
				table {
				  border-collapse: collapse;
				  border: none;
				  font: small arial, helvetica, sans-serif;
				}
				td {
				  vertical-align: middle;
				  width: 10em;
				  margin: 0;
				  padding: 0;
				}
				td p {
				  border-bottom: solid 1px black;
				  margin: 0;
				  padding: 5px 5px 2px 5px;
				}
				-->
				</style>');
				
			printf('<table summary="Tournament Bracket">
			 <tr>
			  <td><p>'.$question['question_option_0'].'</p></td>
			  <td rowspan="2"><p>	<select name="answer_option_0_1" style="width: 75px;">
					<option value="">-- Select an answer below</option>
					'.option_against_from_to($question,0,1).'               
				</select></p></td>
			  <td rowspan="4"><p>	
					<select name="answer_option_0_3" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,0,3).'
				</select></p></td>
			  <td rowspan="8"><p>	
					<select name="answer_option_0_7" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,0,7).'
					</select></p>
			  </td>
			  <td rowspan="16"><p>	
					<select name="answer_option_0_15" style="width: 75px;">
							<option value="">-- Select an answer below</option>
							'.option_against_from_to($question,0,15).'             
					</select></p>
					</td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_1'].'</p></td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_2'].'</p></td>
			  <td rowspan="2"><p>
					<select name="answer_option_2_3" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,2,3).'
					</select></p>
				</td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_3'].'</p></td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_4'].'</p></td>
			  <td rowspan="2"><p>
					<select name="answer_option_4_5" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,4,5).'
					</select></p>
				</td>
			  <td rowspan="4"><p>
					<select name="answer_option_4_7" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,4,7).'
						</select></p>
				</td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_5'].'</p></td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_6'].'</p></td>
			  <td rowspan="2"><p>
					<select name="answer_option_6_7" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,6,7).'
					</select></p>
				</td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_7'].'</p></td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_8'].'</p></td>
			  <td rowspan="2"><p>
					<select name="answer_option_8_9" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,8,9).'
					</select></p>
				</td>
			  <td rowspan="4"><p>
					<select name="answer_option_8_11" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,8,11).'
					</select></p>
				</td>
			  <td rowspan="8"><p>
					<select name="answer_option_8_15" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,8,15).'
					</select></p>
				</td>
			 <tr>
			  <td><p>'.$question['question_option_9'].'</p></td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_10'].'</p></td>
			  <td rowspan="2"><p>
					<select name="answer_option_10_11" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,10,11).'
					</select></p>
				</td> </tr>
			 <tr>
			  <td><p>'.$question['question_option_11'].'</p></td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_12'].'</p></td>
			  <td rowspan="2"><p>
					<select name="answer_option_12_13" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,12,13).'
					</select></p>
				</td>
			  <td rowspan="4"><p>
					<select name="answer_option_12_15" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,12,15).'
					</select></p>
				</td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_13'].'</p></td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_14'].'</p></td>
			  <td rowspan="2"><p>
					<select name="answer_option_14_15" style="width: 75px;">
							<option value="">-- Select an answer below</option>               
							'.option_against_from_to($question,14,15).'
					</select></p>
				</td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_15'].'</p></td>
			 </tr>
			</table>');
		}
	//echo '<tr valign="top"><td><strong>'.__('Extra Options', 'wp-surveys').':<br />'.__('(For admins looking for MANY additional options, comma separated list)', 'wp-surveys').'</strong></td><td><textarea class="alternate-active" name="question_forever"  rows="2" cols="42">'.stripcslashes($question['question_forever']).'</textarea><br /></td></tr>';
	echo '</table><input type="hidden" name="survey_id" value="'.$survey_id.'" />';
	echo '<table class="submit" width="100%" cellpadding="9" cellspacing="0"><tr><td align="left">';
	echo '<input type="submit" name="wpsurv_submit" value="'.__('Update Options', 'wp-surveys').'" /></td><td align="right">';
	echo '<input type="submit" name="wpsurv_submit" value="'.__('Cancel', 'wp-surveys').'" /></td><td align="right">';
	echo '<a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" >'.__('Back to manage surveys', 'wp-surveys').'...</a>';
	echo '</td></tr></table></form></div>';
	}
function option_against($question,$answer_option,$option_name) {
	if($question[$answer_option] == $option_name) { $selected = ' selected '; } else { $selected = ''; }
	return '<option value="'.$option_name.'"'.$selected.'>'.$question[$option_name].'</option>';
}	
function option_against_from_to($question,$start_num,$end_num) {
	$accum = '';
	for($p=$start_num;$p<($end_num+1);$p++) {
		$accum = "".$accum."".option_against($question,("answer_option_".$start_num."_".$end_num.""),("question_option_".$p.""))."";
	}
	return "".$accum."";
}

	
function survey($survey_id, $name='', $desc='') {
	echo '<div class="wrap">';
	echo '<h2>'.__('Add a New Survey', 'wp-surveys').'</h2><br /><form action="" method="post">';
	echo '<table width="100%" cellpadding="4" cellspacing="4">';
	echo '<tr valign="middle"><td class="alternate" align="center"><strong>'.__('Survey Title', 'wp-surveys').':</strong>&nbsp;&nbsp;</td><td><textarea class="alternate-active" name="name" rows="1" cols="86" >'.stripcslashes($name).'</textarea></td></tr>';
	echo '<tr valign="middle"><td class="alternate" align="center"><strong>'.__('Description', 'wp-surveys').':</strong>&nbsp;&nbsp;</td><td><textarea class="alternate-active" name="describe" rows="3" cols="86" >'.stripcslashes($desc).'</textarea></td></tr>';
	echo '</table><p class="submit">';                        
	echo '<input type="hidden" name="survey_id" value="'.$survey_id.'" /><input type="submit" name="wpsurv_submit" value="'.__('Add This Survey', 'wp-surveys').'" /></p></form><br /></div>';
	}
	
function add_question($survey_id, $name='', $type='0') {
	echo '<div class="wrap">';
	echo '<h2>'.__('Step 1: Define Question', 'wp-surveys').'...</h2><form action="" method="post"><br />';
	echo '<table width="100%" cellpadding="4" cellspacing="4">';
	echo '<tr valign="middle"><td class="alternate" align="center"><strong>'.__('Question', 'wp-surveys').':</strong>&nbsp;</td><td>';
	echo '<textarea class="alternate-active" name="name" rows="2" cols="86">'.stripcslashes($name).'</textarea></td></tr>';
	echo '<tr valign="middle"><td class="alternate" align="center"><strong>'.__('Alignment', 'wp-surveys').':</strong>&nbsp;</td><td>';
	echo '<select class="alternate" name="type">';                         
	// THESE ARE THE questions that I hope to support in the ultimate version of the plugin.
	// The concepts are based off existing commercial survey software, but the functionality is 
	// duplicated with invented techniques. The commented options are only maintained for 
	// reference purposes. They are not functional in this version.
	echo '<option value="0"';
	if (($type == "0")) echo ' selected="selected"';
	echo '>-- '.__('Select answers alignment', 'wp-surveys').' --</option>';
	echo '<option value="one-vert"';
	if (($type == "one-vert")) echo ' selected="selected"';
	echo '>'.__('Choice - Vertical', 'wp-surveys').'</option>';
	echo '<option value="one-hori"';
	if (($type == "one-hori")) echo ' selected="selected"';
	echo '>'.__('Choice - Horizontal', 'wp-surveys').'</option>';
	echo '<option value="one-menu"';
	if (($type == "one-menu")) echo ' selected="selected"';
	echo '>'.__('Choice - Menu', 'wp-surveys').'</option>';
	echo '<option value="leaderboard"';
	if (($type == "leaderboard")) echo ' selected="selected"';
	echo '>'.__('Leaderboard', 'wp-surveys').'</option>';
	echo '<option value="bracket"';
	if (($type == "bracket")) echo ' selected="selected"';
	echo '>'.__('Bracket', 'wp-surveys').'</option>';
	echo '<option value="finals"';
	if (($type == "finals")) echo ' selected="selected"';
	echo '>'.__('Finals (4) Bracket', 'wp-surveys').'</option>';
	echo '<option value="tie-breaker"';
	if (($type == "tie-breaker")) echo ' selected="selected"';
	echo '>'.__('Tie-Breaker Questions', 'wp-surveys').'</option>';
	echo '<option value="fantasy-team"';
	if (($type == "fantasy-team")) echo ' selected="selected"';
	echo '>'.__('Fantasy Team', 'wp-surveys').'</option>';
	//echo '<option value="mul-vert">'.__('Choice - Multiple Answers (Vertical)', 'wp-surveys').'</option>';
	//echo '<option value="mul-hori">'.__('Choice - Multiple Answers (Horizontal)', 'wp-surveys').'</option>';
	//<!--<option value="7">Matrix - One Answer per Row</option>-->
	//<!--<option value="20">Matrix - One Answer per Row (Rating Scale)</option>-->
	//<!--<option value="18">Matrix - Multiple Answers per Row</option>-->
	//<!--<option value="11">Matrix - Multiple Answers per Row (Menus)</option>-->
	//echo '<option value="open-onep">'.__('Open Ended - One Line w/Prompt', 'wp-surveys').'</option>';
	//echo '<option value="open-more">'.__('Open Ended - One or More Lines w/Prompt', 'wp-surveys').'</option>';
	//echo '<option value="open-essa">'.__('Open Ended - Essay', 'wp-surveys').'</option>';
	//<!--<option value="19">Open Ended - Constant Sum</option>-->
	//echo '<option value="open-date">'.__('Open Ended - Date and/or Time', 'wp-surveys').'</option>';
	//<!--<option value="1">Presentation - Descriptive Text</option>-->
	//<!--<option value="15">Presentation - Image</option>-->
	//<!--<option value="16">Presentation - Spacer</option>-->
	echo '</select></td></tr></table>';                        
	echo '<input type="hidden" name="survey_id" value="'.$survey_id.'" /><p  class="submit"><input type="submit" name="wpsurv_submit" value="'.__('Step 2', 'wp-surveys').'" /></p></form><br /></div>';
	}
	
function step2($survey_id) {
	if((trim($_POST['name']) == '') && ($_POST['type'] == "0")) {
		echo '<div id="message" class="updated fade"><p><strong>'.__('Please, fill all fields!!', 'wp-surveys').' </strong></p></div>';
		add_question($survey_id, trim($_POST['name']), $_POST['type']);
		}
	elseif((trim($_POST['name'])) == '') {
		echo '<div id="message" class="updated fade"><p><strong>'.__('Please, fill Question field!!', 'wp-surveys').' </strong></p></div>';
		add_question($survey_id, trim($_POST['name']), $_POST['type']);
		}
	elseif(($_POST['type']) == "0") {
		echo '<div id="message" class="updated fade"><p><strong>'.__('Please, select answers Alignment!!', 'wp-surveys').' </strong></p></div>';
		add_question($survey_id, trim($_POST['name']), $_POST['type']);
		}
	else {
		echo '<div class="wrap">';
		echo '<h2>'.__('Step 2: Create your question options', 'wp-surveys').'...</h2><br />';
		echo '<form action="" method="post">';
		if(($_POST['type']) == "leaderboard") {
				echo("Thanks. You have just enabled a 'leaderboard' for this survey. Users will be prompted for a leaderboard name. It will be pre-filled with their username. \n \n Optionally, you may add funny nicknames, to be randomly appended to users' names. (below)");
				echo '<table id="the-list-x" width="100%" cellpadding="4" cellspacing="4">';
								for($i=0;$i<16;$i++) {
									echo '<tr valign="middle"><td class="alternate" align="center" width="22%"><strong>';
									printf(__('Option %s', 'wp-surveys'), $i+1);
									echo ':</strong>&nbsp;</td><td><textarea class="alternate-active" name="question_option_'.$i.'" rows="2" cols="86">';
									if($i == 0) { echo '"The Boss"';}
									if($i == 1) { echo '"Half-court"';}
									echo '</textarea></td></tr>';
									}
		} 
		echo __('Questions can have up to 16 response options. List your options below', 'wp-surveys').':<br /><br />';
		if(($_POST['type']) == "bracket" || ($_POST['type']) == "finals" || ($_POST['type']) == "tie-breaker" || ($_POST['type']) == "one-vert" || ($_POST['type']) == "one-hori" || ($_POST['type']) == "one-menu" || ($_POST['type']) == "mul-vert" || ($_POST['type']) == "mul-hori") {
			echo '<table id="the-list-x" width="100%" cellpadding="4" cellspacing="4">';
			for($i=0;$i<16;$i++) {
				if((($_POST['type']) == "finals") && ($i > 3)) { break; }
				elseif($_POST['type'] == "tie-breaker") { echo "<tr><th>Tie-breakers ask for high-low scores</th><td>no options necessary from admin.</td></tr>"; break; }
				echo '<tr valign="middle"><td class="alternate" align="center" width="22%"><strong>';
				printf(__('Option %s', 'wp-surveys'), $i+1);
				echo ':</strong>&nbsp;</td><td><textarea class="alternate-active" name="question_option_'.$i.'" rows="2" cols="86"></textarea></td></tr>';
				}
			}	
		 

		//if(($_POST['type']) == "open-date" || ($_POST['type']) == "open-essa" || ($_POST['type']) == "open-more" || ($_POST['type']) == "open-onep") {
			//_e('Your options are predefined for this type of question. Click submit to create question.', 'wp-surveys');
			//echo '<input type="hidden" name="rows" value="1" />';
			//for($i=0;$i <16;$i++)
				//echo '<input type="hidden" name="option'.$i.'" />';
			//}
		echo '<input type="hidden" name="survey_id" value="'.$survey_id.'" /><input type="hidden" name="type" value="'.$_POST['type'].'" /><input type="hidden" name="name" value="'.trim($_POST['name']).'" /></table>';
		echo '<p class="submit"><input type="submit" name="wpsurv_submit" value="'.__('Create Question', 'wp-surveys').'" /></p></form></div>';
		}
	}

function recache_utility($survey_id) {
	global $table_prefix, $wpdb;
	$survey_table = $table_prefix . "surveys";
 	$question_table = $table_prefix . "surveys_questions";
	$data_table = $table_prefix . "surveys_data";
	$answer_table = $table_prefix . "surveys_answer";
	$questions = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE `survey_id`=".$survey_id.";",ARRAY_A);
	$leaders = $wpdb->get_results("SELECT * FROM `".$answer_table."` WHERE `survey_id`=".$survey_id.";",ARRAY_A);
	# $leaders have 'leaderboard_name' 'utility_cache' ... are actually the 'answers' join table for survey and unique response
	
	$open = $wpdb->get_var("SELECT `survey_open` FROM `".$survey_table."` WHERE `survey_id`=".$survey_id." LIMIT 1;");
	if (count($open) == 0) {
		echo 'This survey is not open!';	
		return;
	}
	
#	"`answer_option_0_1` text,".
#			"`answer_option_0_3` text,".
#	"`answer_option_2_3` text,".
#					"`answer_option_0_7` text,".																
#	"`answer_option_4_5` text,".
#			"`answer_option_4_7` text,".									
#	"`answer_option_6_7` text,".		
#										"`answer_option_0_15` text,".
#	"`answer_option_8_9` text,".
#			"`answer_option_8_11` text,".
#	"`answer_option_10_11` text,".
#					"`answer_option_8_15` text,".
#	"`answer_option_12_13` text,".
#			"`answer_option_12_15` text,".
#	"`answer_option_14_15` text,".
	
	
	
	
	
	
	if($questions) {
		echo '<div class="wrap">';
		echo '<h2>'.__('The Full Leaderboard', 'wp-surveys').'</h2>';
		echo '<p align="right"><a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" >'.__('Go back!', 'wp-surveys').'</a></p>';
		echo '<table class="widefat" id="the-list-x" width="100%" cellpadding="4" cellspacing="4">';
		$current_plugins = get_option('active_plugins');
		if ((file_exists(ABSPATH . PLUGINDIR . '/polyglot.php')) && (in_array('polyglot.php', $current_plugins)))
			$polyglot = true;
			
		include 'score_algorithms/1_march_madness.php';
			
		foreach($questions as $question) {		
			if($question['question_type']=='one-vert' || $question['question_type']=='one-hori' || $question['question_type']=='one-menu' || $question['question_type']=='mul-vert' || $question['question_type']=='mul-hori') {
				$all_responses = array("RESPONSESARRAY");
				$response_data = $wpdb->get_results("SELECT data_option FROM ".$data_table." WHERE question_id = ".$question['question_id']." ORDER BY data_option", ARRAY_A);
				if($response_data) {
					foreach($response_data as $responses) {
						$mash_up=$responses['data_option'];    			       				   
						if($mash_up) {
							$these_responses = explode("|", $mash_up);
							foreach($these_responses as $response)
								array_push($all_responses, trim($response));
							}
						}
					}
				$dropt = array_shift($all_responses);
				$first_options = array("OPTIONARRAY");	
				$score_options = array("0");
				$option_data = $wpdb->get_results("SELECT * FROM ".$question_table." WHERE question_id = ".$question['question_id'], ARRAY_A);
				for($o=0;$o <16;$o++) {
					$option = "question_option_".$o;
					if($option_data[0][$option]) {
						$first_options[] = $option_data[0][$option];
						array_push($score_options, "0");
						}
					}
				//$forever = $option_data[0]['question_forever']; 
				//if($forever != '') {
					//$extra_options = explode(",", $forever);  
					//foreach($extra_options as $extra) {
						//array_push($first_options, trim($extra));
						//array_push($score_options, "0"); 
						//}
					//}
				array_push($first_options, __('No response recorded', 'wp-surveys'));
				array_push($score_options, "0");
					$all_options = $first_options;
				foreach($all_responses as $count_response) {
					$key = array_search(trim($count_response), $all_options);
					if($key)
						$score_options[$key]++;
					}
				$running = 0;
				$totalscore = 0;
				foreach($score_options as $scores)       
					$totalscore = $totalscore + $scores;
				if($polyglot)
					echo '<tr class="alternate"><td><u>'.__('Question', 'wp-surveys').'</u>:&nbsp;&nbsp;<strong>'.polyglot_filter($question['question_name']).'</strong></td>';
				elseif(!$polyglot)
					echo '<tr class="alternate"><td><strong>'.$question['question_name'].'</strong></td>';
				$current_quest = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE `question_id`=".$question['question_id'].";",ARRAY_A);
				$empty_opts = 0;
				for($j = 0; $j < 16; $j++) {
					$opt = 'question_option_'.$j;
					if ($current_quest[0][$opt] == '')
					$empty_opts++;
					}
				if($empty_opts < 16) {
					echo '<td class="alternate"><strong>';
					printf(__('%s responses', 'wp-surveys'), $totalscore);
					echo '</strong></td></tr>';
					foreach($all_options as $this_option) {
						if($score_options[$running] > 0) {
							if($polyglot)
								echo '<tr><td>'.polyglot_filter($this_option).'</td><td>'.$score_options[$running].' / '.$totalscore.'</td></tr>';
							elseif(!$polyglot)
								echo '<tr><td>'.$this_option.'</td><td>'.$score_options[$running].' / '.$totalscore.'</td></tr>';
							}
						$running++;
						}
					}
				elseif($empty_opts == 10) {
					echo '<form method="post" action="">';
					echo '<input type="hidden" name="survey_id" value="'.$survey_id.'" />';
					echo '<input type="hidden" name="question_id" value="'.$question['question_id'].'" />';
					echo '<td class="submit"><strong>'.__('No option(s) defined yet', 'wp-surveys').'...</strong>&nbsp;&nbsp;';
					echo '<input type="submit" name="wpsurv_submit" value="'.__('Add Some Options', 'wp-surveys').'" /></form>';
					echo '</td></tr>';
					}
				}
			//if($question['question_type']=='open-more' || $question['question_type']=='open-onep' || $question['question_type']=='one-essa') {
				//echo '<tr><td><strong>'.$question['question_name'].'</strong></td><td></td></tr>';
				//if(!is_null($response_data)) {
					//foreach($response_data as $responses) {
						//if($score_options[$running] != __('No Response Recorded', 'wp-surveys'))
							//echo '<tr><td>'.trim($responses['data_option']).'</td><td></td></tr>';
						//}
					//}
				//}
			}
		echo '</table>';
		echo '<p align="right"><a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" >'.__('Go back!', 'wp-surveys').'</a></p>';
		echo '</div>';
		}
	elseif(!$questions) {
		echo '<form method="post" action=""><input type="hidden" name="survey_id" value="'.$survey_id.'" />';
		echo '<div id="message" class="updated fade"><h3><strong>'.__('Leaderboard cannot be calculated yet! Wait for users to play and games to be won.', 'wp-surveys').'</strong></h3>';
		echo '<p class="submit"><input type="submit" name="wpsurv_submit" value="'.__('Add Some Questions', 'wp-surveys').'" /></p></form>';
		echo '<p><a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" >'.__('Go back!', 'wp-surveys').'</a></p></div>';
		}
	}
	
function create_quest($survey_id) {
	global $table_prefix, $wpdb;
	$question_table = $table_prefix . "surveys_questions";
	$add_sql = "INSERT INTO `".$question_table."` (`question_id`, `question_name`, `question_type`, `question_option_0`, `question_option_1`, `question_option_2`, `question_option_3`, `question_option_4`, `question_option_5`, `question_option_6`, `question_option_7`, `question_option_8`, `question_option_9`, `question_option_10`, `question_option_11`, `question_option_12`, `question_option_13`, `question_option_14`, `question_option_15`, `question_rows`, `survey_id`) VALUES ( '', '".$wpdb->escape(stripcslashes(trim($_POST['name'])))."', '".$wpdb->escape($_POST['type'])."',  '".$wpdb->escape(trim($_POST['question_option_0']))."',  '".$wpdb->escape(trim($_POST['question_option_1']))."', '".$wpdb->escape(trim($_POST['question_option_2']))."', '".$wpdb->escape(trim($_POST['question_option_3']))."', '".$wpdb->escape(trim($_POST['question_option_4']))."', '".$wpdb->escape(trim($_POST['question_option_5']))."', '".$wpdb->escape(trim($_POST['question_option_6']))."', '".$wpdb->escape(trim($_POST['question_option_7']))."', '".$wpdb->escape(trim($_POST['question_option_8']))."', '".$wpdb->escape(trim($_POST['question_option_9']))."', '".$wpdb->escape(trim($_POST['question_option_10']))."',  '".$wpdb->escape(trim($_POST['question_option_11']))."', '".$wpdb->escape(trim($_POST['question_option_12']))."', '".$wpdb->escape(trim($_POST['question_option_13']))."', '".$wpdb->escape(trim($_POST['question_option_14']))."', '".$wpdb->escape(trim($_POST['question_option_15']))."', '".$wpdb->escape($_POST['rows'])."', '".$wpdb->escape($survey_id)."');";
#	$add_sql = "INSERT INTO `".$question_table."` (`question_id`, `question_name`, `question_type`, `question_option_0`, `question_option_1`, `question_option_2`, `question_option_3`, `question_option_4`, `question_option_5`, `question_option_6`, `question_option_7`, `question_option_8`, `question_option_9`, `question_rows`, `survey_id`) VALUES ( '', '".$wpdb->escape(trim($_POST['name']))."', '".$wpdb->escape($_POST['type'])."',  '".$wpdb->escape(trim($_POST['question_option_0']))."',  '".$wpdb->escape(trim($_POST['question_option_1']))."', '".$wpdb->escape(trim($_POST['question_option_2']))."', '".$wpdb->escape(trim($_POST['question_option_3']))."', '".$wpdb->escape(trim($_POST['question_option_4']))."', '".$wpdb->escape(trim($_POST['question_option_5']))."', '".$wpdb->escape(trim($_POST['question_option_6']))."', '".$wpdb->escape(trim($_POST['question_option_7']))."', '".$wpdb->escape(trim($_POST['question_option_8']))."', '".$wpdb->escape(trim($_POST['question_option_9']))."', '".$wpdb->escape($_POST['rows'])."', '".$wpdb->escape($survey_id)."');";
	$wpdb->query($add_sql);
	echo '<div class="wrap"><h2>'.__('Step 3: Updating Database...', 'wp-surveys').'</h2></div><br />';
	echo '<div id="message" class="updated fade"><h3><strong>'.__('Complete!', 'wp-surveys').'</strong></h3>';
	echo '<form method="post" action=""><p class="submit"><input type="hidden" name="survey_id" value="'.$survey_id.'" />';
	echo '<input type="submit" name="wpsurv_submit" value="'.__('Back to Edit Survey', 'wp-surveys').'" /></p></form>';
	echo '<p><a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" >'.__('Continue', 'wp-surveys').'...</a></p></div>';
	}
	
function add_survey($survey_id) {
	if(trim($_POST['name']) == '') {
		echo '<div id="message" class="updated fade"><p><strong>'.__('Please, fill Title field!!', 'wp-surveys').' </strong></p></div>';
		survey($survey_id, stripcslashes(trim($_POST['name'])), trim($_POST['describe']));
		}
	else {
		global $table_prefix, $wpdb;
		$survey_table = $table_prefix . "surveys";
		if($wpdb->query("SELECT `survey_id` FROM `".$survey_table."` WHERE `survey_open` = '1';"))
			$wpdb->query("UPDATE `".$survey_table."` SET `survey_open` = '0' WHERE `survey_open` = '1';");
		$this_sql = "INSERT INTO `".$survey_table."` (`survey_name`, `survey_describe`, `survey_open`) VALUES ('".$wpdb->escape(stripcslashes(trim($_POST['name'])))."', '".$wpdb->escape(stripcslashes(trim($_POST['describe'])))."', 1);";
		if($wpdb->query($this_sql)) {
			echo '<div id="message" class="updated fade"><h3><strong>'.__('Survey succesfully created!', 'wp-surveys').'</strong></h3><p>'.__('Thank You. A new survey has been added and the previous survey has been retired', 'wp-surveys').'.</p>';
			echo '<form method="post" action=""><p class="submit"><input type="hidden" name="survey_id" value="'.$survey_id.'" /><input type="submit" name="wpsurv_submit" value="'.__('Add Some Questions', 'wp-surveys').'" /></p></form>';
			echo '<p><a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'">'.__('Continue', 'wp-surveys').'...</a></p></div>';
			}
		}
	}

function results($survey_id) {
	global $table_prefix, $wpdb;
	$survey_table = $table_prefix . "surveys";
 	$question_table = $table_prefix . "surveys_questions";
	$data_table = $table_prefix . "surveys_data";
	$questions = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE `survey_id`=".$survey_id.";",ARRAY_A);
	if($questions) {
		echo '<div class="wrap">';
		echo '<h2>'.__('Your Survey Results', 'wp-surveys').'</h2>';
		echo '<p align="right"><a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" >'.__('Go back!', 'wp-surveys').'</a></p>';
		echo '<table class="widefat" id="the-list-x" width="100%" cellpadding="4" cellspacing="4">';
		$current_plugins = get_option('active_plugins');
		if ((file_exists(ABSPATH . PLUGINDIR . '/polyglot.php')) && (in_array('polyglot.php', $current_plugins)))
			$polyglot = true;
		foreach($questions as $question) {
			if(($question['question_type']=='bracket' || $question['question_type']=='one-vert' || $question['question_type']=='one-hori' || $question['question_type']=='one-menu' || $question['question_type']=='mul-vert' || $question['question_type']=='mul-hori')) {
				$all_responses = array("RESPONSESARRAY");
				$response_data = $wpdb->get_results("SELECT data_option FROM ".$data_table." WHERE question_id = ".$question['question_id']." ORDER BY data_option", ARRAY_A);
				if($response_data) {
					foreach($response_data as $responses) {
						$mash_up=$responses['data_option'];    			       				   
						if($mash_up) {
							$these_responses = explode("|", $mash_up);
							foreach($these_responses as $response)
								array_push($all_responses, trim($response));
							}
						}
					}
				$dropt = array_shift($all_responses);
				$first_options = array("OPTIONARRAY");	
				$score_options = array("0");
				$option_data = $wpdb->get_results("SELECT * FROM ".$question_table." WHERE question_id = ".$question['question_id'], ARRAY_A);
				for($o=0;$o <16;$o++) {
					$option = "question_option_".$o;
					if($option_data[0][$option]) {
						$first_options[] = $option_data[0][$option];
						array_push($score_options, "0");
						}
					}
				//$forever = $option_data[0]['question_forever']; 
				//if($forever != '') {
					//$extra_options = explode(",", $forever);  
					//foreach($extra_options as $extra) {
						//array_push($first_options, trim($extra));
						//array_push($score_options, "0"); 
						//}
					//}
				array_push($first_options, __('No response recorded', 'wp-surveys'));
				array_push($score_options, "0");
					$all_options = $first_options;
				foreach($all_responses as $count_response) {
					$key = array_search(trim($count_response), $all_options);
					if($key)
						$score_options[$key]++;
					}
				$running = 0;
				$totalscore = 0;
				foreach($score_options as $scores)       
					$totalscore = $totalscore + $scores;
				if($polyglot)
					echo '<tr class="alternate"><td><u>'.__('Question', 'wp-surveys').'</u>:&nbsp;&nbsp;<strong>'.polyglot_filter($question['question_name']).'</strong></td>';
				elseif(!$polyglot)
					echo '<tr class="alternate"><td><strong>'.$question['question_name'].'</strong></td>';
				$current_quest = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE `question_id`=".$question['question_id'].";",ARRAY_A);
				$empty_opts = 0;
				for($j = 0; $j < 16; $j++) {
					$opt = 'question_option_'.$j;
					if ($current_quest[0][$opt] == '')
					$empty_opts++;
					}
				if($empty_opts < 16) {
					echo '<td class="alternate"><strong>';
					printf(__('%s responses', 'wp-surveys'), $totalscore);
					echo '</strong></td></tr>';
					foreach($all_options as $this_option) {
						if($score_options[$running] > 0) {
							if($polyglot)
								echo '<tr><td>'.polyglot_filter($this_option).'</td><td>'.$score_options[$running].' / '.$totalscore.'</td></tr>';
							elseif(!$polyglot)
								echo '<tr><td>'.$this_option.'</td><td>'.$score_options[$running].' / '.$totalscore.'</td></tr>';
							}
						$running++;
						}
					}
				elseif($empty_opts == 10) {
					echo '<form method="post" action="">';
					echo '<input type="hidden" name="survey_id" value="'.$survey_id.'" />';
					echo '<input type="hidden" name="question_id" value="'.$question['question_id'].'" />';
					echo '<td class="submit"><strong>'.__('No option(s) defined yet', 'wp-surveys').'...</strong>&nbsp;&nbsp;';
					echo '<input type="submit" name="wpsurv_submit" value="'.__('Add Some Options', 'wp-surveys').'" /></form>';
					echo '</td></tr>';
					}
				}
			//if($question['question_type']=='open-more' || $question['question_type']=='open-onep' || $question['question_type']=='one-essa') {
				//echo '<tr><td><strong>'.$question['question_name'].'</strong></td><td></td></tr>';
				//if(!is_null($response_data)) {
					//foreach($response_data as $responses) {
						//if($score_options[$running] != __('No Response Recorded', 'wp-surveys'))
							//echo '<tr><td>'.trim($responses['data_option']).'</td><td></td></tr>';
						//}
					//}
				//}
			}
		echo '</table>';
		echo '<p align="right"><a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" >'.__('Go back!', 'wp-surveys').'</a></p>';
		echo '</div>';
		}
	elseif(!$questions) {
		echo '<form method="post" action=""><input type="hidden" name="survey_id" value="'.$survey_id.'" />';
		echo '<div id="message" class="updated fade"><h3><strong>'.__('There are no Question defined yet', 'wp-surveys').'</strong></h3>';
		echo '<p class="submit"><input type="submit" name="wpsurv_submit" value="'.__('Add Some Questions', 'wp-surveys').'" /></p></form>';
		echo '<p><a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" >'.__('Go back!', 'wp-surveys').'</a></p></div>';
		}
	} ?>