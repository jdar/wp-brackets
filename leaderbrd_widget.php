<?php  
/* Plugin Name: Leaderboard
Description: Adds a sidebar widget to show the top scorers in your WP site. Heavily adapted by Darius Roberts (dariusroberts.com) from Top Commentators plugin by Lorna Timbah (WebGrrrl.net)
Author: Darius Roberts (dariusroberts.com)
Version: 0.1
Author URI: http://dariusroberts.com 
Plugin URI: http://dariusroberts.com/pages/opensource
*/    

// Put functions into one big function we'll call at the plugins_loaded  
// action. This ensures that all required plugin functions are defined.

function widget_leaderbrd_init() {
	// Check for the required plugin functions. This will prevent fatal
  	// errors occurring when you deactivate the dynamic-sidebar plugin.
  	if ( !function_exists('register_sidebar_widget') )
  		return;

	// This is the function that truncates long commentators names to avoid
	// your site design from breaking
	function ns_substr_ellipse($str, $len) {
		if(strlen($str) > $len) {
			$str = substr($str, 0, $len-3) . "...";
		}
		return $str;
	}

	// This is the function that queries whether the commenters have entered a URL
	function ns_get_user_url($user) {
		global $wpdb;
		$url = "";
		return $url;
	}

	// This is the function that writes out the top commentators list
	function ns_show_top_commentators() {
		// Each widget can store its own options. We keep strings here.
		$options = get_option('widget_leaderbrd');

		// This prepares URLs for filtering
		if($options['filterUrl'] != "") {
			$filterUrl = trim($options['filterUrl']);
			$filterUrl = explode(",", $filterUrl);
			for($i=0; $i<count($filterUrl); $i++) {
				$new_urls .= " AND leaderboard_name_url NOT LIKE '%" . trim($filterUrl[$i]) . "%'";
			}
			$filterUrl = $new_urls;
		}

		// This prepares e-mails for filtering
		if($options['filterEmail'] != "") {
			$filterEmail = trim($options['filterEmail']);
			$filterEmail = explode(",", $filterEmail);
			for($i=0; $i<count($filterUrl); $i++) {
				$new_emails .= " AND leaderboard_email NOT LIKE '%" . trim($filterEmail[$i]) . "%'";
			}
			$filterEmail = $new_emails;
		}

		// This prepares names for filtering
		if($options['excludeNames'] != "") {
			$excludeNames = trim($options['excludeNames']);
			$excludeNames = explode(",", $excludeNames);
			for($i=0; $i<count($excludeNames); $i++) {
				$new_names .= " AND leaderboard_name NOT IN ('" . trim($excludeNames[$i]) . "')";
			}
			$excludeNames = $new_names;
		}

		if($options['limitList'] != "")
			$limitList = "LIMIT " . $options['limitList'];
		if($options['limitChar'] != "") {
			$limitChar = $options['limitChar'];
		} else {
			$limitChar = 20;
		}
		$listDesc = $options['listDesc'];
		$listType = $options['listType'];
		$listPeriod = $options['listPeriod'];
		if($options['listNull'] == "") {
			$listNull = "No commentators.";
		} else {
			$listNull = $options['listNull'];
		}
		$makeLink = $options['makeLink'];
		$noFollow = $options['noFollow'];
		$showCount = $options['showCount'];
		$showUtility = $options['showUtility'];


		// Gravatar variables by SNascimento
		$displayGravatar = $options['displayGravatar'];
		$avatarSize = $options['avatarSize'];

		// This sets the type of list to be used
		if($listType == "num") {
			$listStart = "<ol>";
			$listEnd = "</ol>";
		} else {
			$listStart = "<ul>";
			$listEnd = "</ul>";
		}

		// This is the function that prepare time period limitation for filtering
		if($listPeriod == "h") {
			$listPeriod = "DATE_FORMAT(comment_date, '$%Y-%m-%d %H') = DATE_FORMAT(CURDATE(), '%Y-%m-%d %H')";
		} elseif($listPeriod == "d") {
			$listPeriod = "DATE_FORMAT(comment_date, '%Y-%m-%d') = DATE_FORMAT(CURDATE(), '%Y-%m-%d')";
		} elseif($listPeriod == "w") {
			$listPeriod = "DATE_FORMAT(comment_date, '%Y-%v') = DATE_FORMAT(CURDATE(), '%Y-%v')";
		} elseif($listPeriod == "m") {
			$listPeriod = "DATE_FORMAT(comment_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')";
		} elseif($listPeriod == "y") {
			$listPeriod = "DATE_FORMAT(comment_date, '%Y') = DATE_FORMAT(CURDATE(), '%Y')";
		} elseif($listPeriod == "a") {
			$listPeriod = "1=1";
		} elseif(is_numeric($listPeriod)) {
			$listPeriod = "comment_date >= CURDATE() - INTERVAL $listPeriod DAY";
		} else {
			$listPeriod = "DATE_FORMAT(comment_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')";
		}

		// This writes out the proper SQL whether to group commenters
		// by score or by e-mail
		if($options['groupBy'] == "0") {
			$groupBy = "GROUP BY utility_cache";
		} else {
			$groupBy = "GROUP BY leaderboard_email";
		}

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		global $wpdb;
		$commenters = $wpdb->get_results("
			SELECT leaderboard_name, utility_cache
			FROM wp_surveys_responses
				WHERE leaderboard_email != ''
				$excludeNames
				$filterUrl
				$filterEmail
			ORDER BY utility_cache DESC
			$limitList
			");

		if(is_array($commenters)) {
			$countList = 0;
			echo $listDesc . "\n";
			echo $listStart . "\n";
			foreach ($commenters as $k) {
				$url = ns_get_user_url($k->leaderboard_name);

// This writes out the list of commentors and checks for 1) Hyperlink each name,
// 2) NoFollow each name, 3) Limit characters in names, 4) Remarks for blank list, and
// 5) Display only users with URLs variables

// check if onlyWithUrl = 1
if($options['onlyWithUrl'] == '1') {
// if onlyWithUrl = 1, check if trimurl != ''
// write
if(trim($url) != '') {
				echo "<li>";
				if(trim($url) != '' ) {
					if($makeLink == 1) {
						echo "<a href='" . $url . "'";
						if($noFollow == 1) echo " rel='nofollow'";
						echo ">";
					}
				}

				// Gravatar display by SNascimento
				if($displayGravatar == 1)  {
					$image=md5(strtolower($k->leaderboard_email));
					$defavatar=urlencode($defaultGravatar);
					echo '<img class="tcwGravatar" src="http://www.gravatar.com/avatar.php?gravatar_id='.$image.'&amp;size='.$avatarSize.'&amp;default='.$defavatar.'" alt ="'.$k->leaderboard_name.'" title="'.$k->leaderboard_name.'" /> ';
				}

				echo ns_substr_ellipse($k->leaderboard_name, $limitChar);
#				if($showCount == 1) echo " (" . $k->comment_comments . ")";
				if($showUtility == 1) echo " (" . $k->utility_cache . ")";
				echo print_r($k);
				if(trim($url) != '') {
					if($makeLink == 1)
						echo "</a>";
				}
				echo "</li>\n";
				unset($url);
				$countList = $ $countList + 1;
// finish checking trimurl
}
// if onlyWithUrl = 0
// write
} else {
				echo "<li>";
				if(trim($url) != '' ) {
					if($makeLink == 1) {
						echo "<a href='" . $url . "'";
						if($noFollow == 1) echo " rel='nofollow'";
						echo ">";
					}
				}

				// Gravatar display by SNascimento
				if($displayGravatar == 1)  {
					$image=md5(strtolower($k->leaderboard_email));
					$defavatar=urlencode($defaultGravatar);
					echo '<img class="tcwGravatar" src="http://www.gravatar.com/avatar.php?gravatar_id='.$image.'&amp;size='.$avatarSize.'&amp;default='.$defavatar.'" alt ="'.$k->leaderboard_name.'" title="'.$k->leaderboard_name.'" border="0"/> ';
				}

				echo ns_substr_ellipse($k->leaderboard_name, $limitChar);
#				if($showCount == 1) echo " (" . $k->comment_comments . ")";
				if($showUtility == 1) echo " (" . $k->utility_cache . ")";
				
				if(trim($url) != '') {
					if($makeLink == 1)
						echo "</a>";
				}
				echo "</li>\n";
				unset($url);
				$countList = $ $countList + 1;
// end check onlyWithUrl
}
			}
			if($countList == 0)
				echo "<li>" . $listNull . "</li>\n";
			echo $listEnd;
		} else {
			echo "<ul><li>" . $listNull . "</li></ul>" . "\n";
		}
	}

   	// This is the function that outputs our top commentators list
  	function widget_leaderbrd($args) {
 		// $args is an array of strings that help widgets to conform to
  		// the active theme: before_widget, before_title, after_widget,
  		// and after_title are the array keys. Default tags: li and h2.
  		extract($args);

   		// This one string determines whether you want it to appear in the main page or everywhere
  		$options = get_option('widget_leaderbrd');
  		$showInHome = $options['showInHome'];
  		$title = htmlspecialchars(stripcslashes($options['title']), ENT_QUOTES);
  		global $wpdb;
  		if($showInHome == 1) {
  			if(is_home())
   				echo $before_widget . $before_title . $title . $after_title;
  		} else {
  			echo $before_widget . $before_title . $title . $after_title;
  		}
		if($showInHome == 1) {
			if(is_home())
				echo ns_show_top_commentators();
		} else {
			echo ns_show_top_commentators();
		}
		if($showInHome == 1) {
			if(is_home())
	  			echo $after_widget . "<!-- end of widget -->";
		} else {
  			echo $after_widget . "<!-- end of widget -->";
		}
	}

	function widget_leaderbrd_control() {
		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_leaderbrd');
		if (!is_array($options) )
			$options = array('title'=>'Top Commentators', 'listDesc'=>'You commented; therefore you are loved:', 'excludeNames'=>'\'Lorna\', \'Administrator\'','limitList'=>'10', 'limitChar'=>'25', 'listNull'=>'Be the first to comment in my site.', 'filterUrl'=>'', 'filterEmail'=>'', 'listType'=>'bul', 'listPeriod'=>'m', 'makeLink'=>'1', 'noFollow'=>'1', 'showCount'=>'1', 'showUtility'=>'1', 'groupBy'=>'1', 'showInHome'=>'0', 'onlyWithUrl'=>'0', 'displayGravatar'=>'0', 'avatarSize'=>'20');
		if ( $_POST['leaderbrd-submit'] ) {
			// Remember to sanitize and format use input appropriately.
			$options['title'] = $_POST['leaderbrd-title'];
			$options['excludeNames'] = $_POST['leaderbrd-excludeNames'];
			$options['limitList'] = $_POST['leaderbrd-limitList'];
			$options['limitChar'] = $_POST['leaderbrd-limitChar'];
			$options['listDesc'] = $_POST['leaderbrd-listDesc'];
			$options['listType'] = $_POST['leaderbrd-listType'];
			if($_POST['leaderbrd-listPeriodnum'] == '') {
				$options['listPeriod'] = $_POST['leaderbrd-listPeriod'];
			} else {
				$options['listPeriod'] = $_POST['leaderbrd-listPeriodnum'];
			}
			$options['listNull'] = $_POST['leaderbrd-listNull'];
			$options['filterUrl'] = $_POST['leaderbrd-filterUrl'];
			$options['filterEmail'] = $_POST['leaderbrd-filterEmail'];
			$options['makeLink'] = $_POST['leaderbrd-makeLink'];
  			$options['noFollow'] = $_POST['leaderbrd-noFollow'];
			$options['showInHome'] = $_POST['leaderbrd-showInHome'];
			$options['onlyWithUrl'] = $_POST['leaderbrd-onlyWithUrl'];
			$options['showUtility'] = $_POST['leaderbrd-showUtility'];
			$options['showCount'] = $_POST['leaderbrd-showCount'];
			$options['groupBy'] = $_POST['leaderbrd-groupBy'];
			// Gravatar options
			$options['displayGravatar'] = $_POST['leaderbrd-displayGravatar'];
			$options['avatarSize'] = ($_POST['leaderbrd-avatarSize'] ? $_POST['leaderbrd-avatarSize'] : 20) ;
  			update_option('widget_leaderbrd', $options);
  		}
   		// Be sure you format your options to be valid HTML attributes.
    		// Here is our little form segment. Notice that we don't need a
  		// complete form. This will be embedded into the existing form.
		$options = get_option('widget_leaderbrd');
		$title = htmlspecialchars(stripcslashes($options['title']), ENT_QUOTES);
		$excludeNames = htmlspecialchars(stripcslashes($options['excludeNames']), ENT_QUOTES);
		$limitList = $options['limitList'];
		$limitChar = $options['limitChar'];
		$listDesc = $options['listDesc'];
		$listType = $options['listType'];
		$listPeriod = $options['listPeriod'];
		$listNull = $options['listNull'];
		$filterUrl = $options['filterUrl'];
		$filterEmail = $options['filterEmail'];
		$makeLink = $options['makeLink'];
		$noFollow = $options['noFollow'];
		$showInHome = $options['showInHome'];
		$onlyWithUrl = $options['onlyWithUrl'];
		$showCount = $options['showCount'];
		$showUtility = $options['showUtility'];
		$groupBy = $options['groupBy'];
		$displayGravatar = $options['displayGravatar'];
		$avatarSize = $options['avatarSize'];
		?>
  		<p style="text-align:right;"><label for="leaderbrd-title">Change widget title:</label> <input style="width: 200px;" id="leaderbrd-title" name="leaderbrd-title" type="text" value="<?php echo $title ?>" /></p>
		<p style="text-align:right;"><label for="leaderbrd-listDesc">Add description below the title:</label> <input style="width: 200px;" id="leaderbrd-listDesc" name="leaderbrd-listDesc" type="text" value="<?php echo $listDesc ?>" /><br />Leave blank to exclude description.</p>
		<p style="text-align:right;"><label for="leaderbrd-excludeNames">Exclude these users:</label> <input style="width: 200px;" id="leaderbrd-excludeNames" name="leaderbrd-excludeNames" type="text" value="<?php echo $excludeNames ?>" /><br />Separate each name with a comma (,)</p>

		<p style="text-align:right;"><label for="leaderbrd-listPeriod">Reset list every:</label> <select size="1" id="leaderbrd-listPeriod" name="leaderbrd-listPeriod"><option value="h" <?php
  		if($listPeriod== 'h')
  			echo ' selected';
  		echo '>Hour</option><option value="d"';
  		if($listPeriod== 'd')
  			echo ' selected';
  		echo '>Day</option><option value="w"';
  		if($listPeriod== 'w')
  			echo ' selected';
  		echo '>Week</option><option value="m"';
  		if($listPeriod== 'm' || is_null($listPeriod))
  			echo ' selected';
  		echo '>Month</option><option value="y"';
  		if($listPeriod== 'y')
  			echo ' selected';
  		echo '>Year</option><option value="a"';
  		if($listPeriod== 'a')
  			echo ' selected';
  		echo '>List all</option></select><br />Or specify number of days: <input style="width: 50px;" id="leaderbrd-listPeriodnum" name="leaderbrd-listPeriodnum" type="text" value="';
  		if (is_numeric($listPeriod))
   		 	echo $listPeriod;
  		echo '" /></p>';
		?>
  		<p style="text-align:right;"><label for="leaderbrd-limitList">Limit number of names to:</label>  <input style="width: 200px;" id="leaderbrd-limitList" name="leaderbrd-limitList" type="text" value="<?php echo $limitList ?>" /><br />Enter numbers only</p>
  		<p style="text-align:right;"><label for="leaderbrd-limitChar">Limit characters in names to:</label> <input style="width: 200px;" id="leaderbrd-limitChar" name="leaderbrd-limitChar" type="text" value="<?php echo $limitChar ?>" /><br />Enter numbers only</p>
  		<p style="text-align:right;"><label for="leaderbrd-listNull">Remarks for blank list:</label> <input style="width: 200px;" id="leaderbrd-listNull" name="leaderbrd-listNull" type="text" value="<?php echo $listNull ?>" /></p>
  		<p style="text-align:right;"><label for="leaderbrd-filterUrl">Filter the following full/partial URLs:</label> <input style="width: 200px;" id="leaderbrd-filterUrl" name="leaderbrd-filterUrl" type="text" value="<?php echo $filterUrl ?>" /><br />Separate each URl with a comma (,)</p>
  		<p style="text-align:right;"><label for="leaderbrd-filterEmail">Filter the following full/partial e-mail:</label> <input style="width: 200px;" id="leaderbrd-filterEmail" name="leaderbrd-filterEmail" type="text" value="<?php echo $filterEmail ?>" /><br />Separate each e-mail with a comma (,)</p>
		<?php
  		echo '<p style="text-align:right;"><label for="leaderbrd-listType">' . ('Display list type as:') . '</label><select size="1" id="leaderbrd-listType" name="leaderbrd-listType"><option value="bul"';
  		if($listType == 'bul')
  			echo ' selected';
  		echo '>Bulleted</option><option value="num"';
  		if($listType == 'num')
  			echo ' selected';
  		echo '>Numbered</option></select></p>';
  		echo '<p style="text-align:right;"><label for="leaderbrd-makeLink">' . ('Hyperlink each name?') . '</label><select size="1" id="leaderbrd-makeLink" name="leaderbrd-makeLink"><option value="1"';
  		if($makeLink == 1)
  			echo ' selected';
 		echo '>Yes</option><option value="0"';
  		if($makeLink == 0)
  			echo ' selected';
  		echo '>No</option></select></p>';

  		echo '<p style="text-align:right;"><label for="leaderbrd-noFollow">' . ('NoFollow each name if hyperlinked?') . '</label><select size="1" id="leaderbrd-noFollow" name="leaderbrd-noFollow"><option value="1"';
  		if($noFollow == 1)
  			echo ' selected';
 		echo '>Yes</option><option value="0"';
  		if($noFollow== 0)
  			echo ' selected';
  		echo '>No</option></select></p>';

  		echo '<p style="text-align:right;"><label for="leaderbrd-showCount">' . ('Show number of comments for each commenter?') . '</label><select size="1" id="leaderbrd-showCount" name="leaderbrd-showCount"><option value="1"';
  		if($showCount == 1)
  			echo ' selected';
 		echo '>Yes</option><option value="0"';
  		if($showCount == 0)
  			echo ' selected';
  		echo '>No</option></select></p>';

  		echo '<p style="text-align:right;"><label for="leaderbrd-showUtility">' . ('Show score for each leader?') . '</label><select size="1" id="leaderbrd-showUtility" name="leaderbrd-showUtility"><option value="1"';
  		if($showUtility== 1)
  			echo ' selected';
 		echo '>Yes</option><option value="0"';
  		if($showUtility == 0)
  			echo ' selected';
  		echo '>No</option></select></p>';

  		echo '<p style="text-align:right;"><label for="leaderbrd-groupBy">' . ('(Hijack-proof?) Group commentors based on') . '</label><select size="1" id="leaderbrd-groupBy" name="leaderbrd-groupBy"><option value="1"';
		
		echo ' readonly=true';

  		if($groupBy == 1)
  			echo ' selected';
 		echo '>E-mail</option><option value="0"';
  		if($groupBy == 0)
  			echo ' selected';
  		echo '>User names</option></select></p>';

  		echo '<p style="text-align:right;"><label for="leaderbrd-showInHome">' . ('Show in home page only?') . '</label><select size="1" id="leaderbrd-showInHome" name="leaderbrd-showInHome"><option value="1"';
  		if($showInHome == 1)
  			echo ' selected';
 		echo '>Yes</option><option value="0"';
  		if($showInHome == 0)
  			echo ' selected';
  		echo '>No</option></select></p>';

  		echo '<p style="text-align:right;"><label for="leaderbrd-onlyWithUrl">' . ('Display only commentors with URL?') . '</label><select size="1" id="leaderbrd-onlyWithUrl" name="leaderbrd-onlyWithUrl"><option value="1"';
  		if($onlyWithUrl == 1)
  			echo ' selected';
 		echo '>Yes</option><option value="0"';
  		if($onlyWithUrl == 0)
  			echo ' selected';
echo ' disabled="disabled"';
  		echo '>No</option></select></p>';

		// Gravatar option forms
  		echo '<p style="text-align:right;"><label for="leaderbrd-displayGravatar">' . ('Display Gravatar?') . '</label><select size="1" id="leaderbrd-displayGravatar" name="leaderbrd-displayGravatar"><option value="1"';
  		if($displayGravatar == 1)
  			echo ' selected';
 		echo '>Yes</option><option value="0"';
echo" disabled";
  		if($displayGravatar == 0)
  			echo ' selected';
  		echo '>No</option></select></p>';

		?>

		<p style="text-align:right;"><label for="leaderbrd-avatarSize">Gravatar Size:</label> <input style="width: 200px;" id="leaderbrd-avatarSize" name="leaderbrd-avatarSize" type="text" value="<?php echo $avatarSize ?>" /></p>

		<?php

  		echo '<input type="hidden" id="leaderbrd-submit" name="leaderbrd-submit" value="1" />';
  	}
	// This registers our widget so it appears with the other available
  	// widgets and can be dragged and dropped into any active sidebars.
  	register_sidebar_widget(array('Leaderboard', 'widgets'), 'widget_leaderbrd');

    	// This registers our optional widget control form. Because of this
  	// our widget will have a button that reveals a 300x100 pixel form.
  	register_widget_control(array('Leaderboard', 'widgets'), 'widget_leaderbrd_control', 410, 500);
}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_leaderbrd_init');
?>