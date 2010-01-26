<?php
/**
* Template Name: Bracket
*/

global $table_prefix, $wpdb;
$data_table = $table_prefix . "surveys_data";
$question_table = $table_prefix . "surveys_questions";
$survey_table = $table_prefix . "surveys";

$open_surveys = $wpdb->get_results("SELECT * FROM `".$survey_table."` WHERE `survey_open`='1' LIMIT 1;",ARRAY_A);
$open_survey = $open_surveys[0];
$questions = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE (`survey_id`=".$open_survey['survey_id'].") AND (`question_type`='bracket') LIMIT 4;",ARRAY_A);

	if(!$open_survey || !$questions) {
			$closed_surveys = $wpdb->get_results("SELECT * FROM `".$survey_table."` WHERE `survey_open`='0' LIMIT 1;",ARRAY_A);
			if($closed_surveys) {
			  echo('<h1>Past Surveys</h1><p>Here is a schedule of past brackets.</p>');
			  foreach($closed_surveys as $closed_survey) {
				echo("".$closed_survey['survey_name'].":  ".$closed_survey['description']."");
			  }
			}
			
			$closed_surveys = $wpdb->get_results("SELECT * FROM `".$survey_table."` WHERE `survey_open`='0' LIMIT 1;",ARRAY_A);
			if($closed_surveys) {
			  echo('<h1>Upcoming Surveys</h1><p>Here is a schedule of upcoming brackets. Bookmark this page! Follow us on twitter! <a href=http://twitter.com/sitename>@sitename</a></p>');
			  foreach($closed_surveys as $closed_survey) {
				echo("".$closed_survey['survey_name'].":  ".$closed_survey['description']."");
			  }
			}
			
			
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

 
 function check_for_answer($quest,$start_num,$end_num) {
	 // $question is dynamically defined when this function overwrites itself.
		$answer = $quest['answer_option_'.$start_num.'_'.$end_num.''];
		if($answer){
			return $quest[$answer];
		} elseif(!$answer){
		    return "";
		}
			
	}
  function question_option($quest,$num) {
	$quest['question_option_'.$num.''];
	}

?>
<div>
	<h3><?php echo($open_survey['survey_name']); ?></h3>	
	<br>
	<style type="text/css">
		<?php 
			include 'bracket_results_template.css' 
		?>
	</style>
<?php
  if(!$questions) {
	echo("no bracket questions. an error?");
  }
  elseif($questions) {
	// questions limited to first 4 bracket questions on the currently open survey. See SQL query above.
	// additional question looks for a 'finals' question.
	$question_0 = $questions[0];
	$question_1 = $questions[1];
	$question_2 = $questions[2];
	$question_3 = $questions[3];
	$question_finals = $wpdb->get_results("SELECT * FROM `".$question_table."` WHERE (`survey_id`=".$open_survey['survey_id'].") AND (`question_type`='finals') LIMIT 1;",ARRAY_A);
	$question_finals = $question_finals[0];
	
  /*
	** below, there are 5 tables 
	
	-------------------------
	|	0	|		|	1	|
	-------------------------
	|		|finals |		|
	-------------------------
	|	2	|		|	3	|
	-------------------------
	
	* redundant HTML is used, because a variety of stylistic "position:relative" elements may be used to promote content.
	* (I.e., maximum customizability / minimal reuse-ability makes sense in this case.)
	
	* extra spaces in that table could be filled with content or links.

  */

	
	echo('<table>');
	echo '<tr>';
		echo '<td>';
	if($question_0) {
		$question = $question_0;
		printf('<table summary="Tournament Bracket">
		 <tr>
		  <td><p>'.$question['question_option_0'].'</p></td>
		  <td rowspan="2"><p>
				'.check_for_answer($question,0,1).'               
			          </p></td>
		  <td rowspan="4"><p>	
						'.check_for_answer($question,0,3).'
			          </p></td>
		  <td rowspan="8"><p>	
						'.check_for_answer($question,0,7).'
				          </p>
		  </td>
		  <td rowspan="16"><p>	
						'.check_for_answer($question,0,15).'             
				          </p>
				</td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_1'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_2'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,2,3).'
				          </p>
			</td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_3'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_4'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,4,5).'
				          </p>
			</td>
		  <td rowspan="4"><p>
						'.check_for_answer($question,4,7).'
					      </p>
			</td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_5'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_6'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,6,7).'
				          </p>
			</td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_7'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_8'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,8,9).'
				          </p>
			</td>
		  <td rowspan="4"><p>
						'.check_for_answer($question,8,11).'
				          </p>
			</td>
		  <td rowspan="8"><p>
						'.check_for_answer($question,8,15).'
				          </p>
			</td>
		 <tr>
		  <td><p>'.$question['question_option_9'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_10'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,10,11).'
				          </p>
			</td> </tr>
		 <tr>
		  <td><p>'.$question['question_option_11'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_12'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,12,13).'
				          </p>
			</td>
		  <td rowspan="4"><p>
						'.check_for_answer($question,12,15).'
				          </p>
			</td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_13'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_14'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,14,15).'
				          </p>
			</td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_15'].'</p></td>
		 </tr>
		</table>');
	  }
		echo '</td>';
		echo '<td></td>';
		echo '<td>';	
		if($question_1) {
			$question = $question_1;
			printf('<table summary="Tournament Bracket">
			 <tr>
			  <td rowspan="16"><p>
					'.check_for_answer($question,0,15).'               
				          </p></td>
			  <td rowspan="8"><p>	
							'.check_for_answer($question,0,7).'
				          </p></td>
			  <td rowspan="4"><p>	
							'.check_for_answer($question,0,3).'
					          </p>
			  </td>
			  <td rowspan="2"><p>	
							'.check_for_answer($question,0,1).'             
					          </p>
					</td>
			  <td><p>'.$question['question_option_0'].'</p></td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_1'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="2"><p>
							'.check_for_answer($question,2,3).'
					          </p>
				</td>
			  <td><p>'.$question['question_option_2'].'</p></td>
			  
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_3'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="4"><p>
							'.check_for_answer($question,4,7).'
					          </p>
				</td>
			  <td rowspan="2"><p>
							'.check_for_answer($question,4,5).'
						      </p>
				</td>
			   <td><p>'.$question['question_option_4'].'</p></td>
			  
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_5'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="2"><p>
							'.check_for_answer($question,6,7).'
					          </p>
				</td>
			   <td><p>'.$question['question_option_6'].'</p></td>
			  
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_7'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="8"><p>
							'.check_for_answer($question,8,15).'
					          </p>
				</td>
			  <td rowspan="4"><p>
							'.check_for_answer($question,8,11).'
					          </p>
				</td>
			  <td rowspan="2"><p>
							'.check_for_answer($question,8,9).'
					          </p>
				</td>
			   <td><p>'.$question['question_option_8'].'</p></td>
			  
			 <tr>
			  <td><p>'.$question['question_option_9'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="2"><p>
							'.check_for_answer($question,10,11).'
					          </p>
				</td> </tr>
			   <td><p>'.$question['question_option_10'].'</p></td>
			  
			 <tr>
			  <td colspan=2></td>
			  <td><p>'.$question['question_option_11'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="4"><p>
							'.check_for_answer($question,12,15).'
					          </p>
				</td>
			  <td rowspan="2"><p>
							'.check_for_answer($question,12,13).'
					          </p>
				</td>
			  <td><p>'.$question['question_option_12'].'</p></td>
			  
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_13'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="2"><p>
							'.check_for_answer($question,14,15).'
					          </p>
				</td>
			  <td><p>'.$question['question_option_14'].'</p></td>
			  
			 </tr>
			 <tr>
			  <td colspan="3"></td>
			  <td><p>'.$question['question_option_15'].'</p></td>
			 </tr>
			</table>');
		  }
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td></td>';		
		echo '<td>';
			if($question_finals) {
				$question = $question_finals;
				printf('<table summary="Tournament Bracket">
				 <tr>
				  <td><p>'.$question['question_option_0'].'</p></td>
				  <td rowspan="2"><p>
						'.check_for_answer($question,0,1).'               
					          </p></td>
				  <td rowspan="4"><p>	
								'.check_for_answer($question,0,3).'
					          </p></td>
				 </tr>
				 <tr>
				  <td><p>'.$question['question_option_1'].'</p></td>
				 </tr>
				 <tr>
				  <td><p>'.$question['question_option_2'].'</p></td>
				  <td rowspan="2"><p>
								'.check_for_answer($question,2,3).'
						          </p>
					</td>
				 </tr>
				 <tr>
				  <td><p>'.$question['question_option_3'].'</p></td>
				 </tr>
				</table>');
			 } 
		echo '</td>';
		echo '<td></td>';		
	echo '</tr>';
	echo '<tr>';
		echo '<td>';
	if($question_2) {
		$question = $question_2;
		printf('<table summary="Tournament Bracket">
		 <tr>
		  <td><p>'.$question['question_option_0'].'</p></td>
		  <td rowspan="2"><p>
				'.check_for_answer($question,0,1).'               
			          </p></td>
		  <td rowspan="4"><p>	
						'.check_for_answer($question,0,3).'
			          </p></td>
		  <td rowspan="8"><p>	
						'.check_for_answer($question,0,7).'
				          </p>
		  </td>
		  <td rowspan="16"><p>	
						'.check_for_answer($question,0,15).'             
				          </p>
				</td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_1'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_2'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,2,3).'
				          </p>
			</td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_3'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_4'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,4,5).'
				          </p>
			</td>
		  <td rowspan="4"><p>
						'.check_for_answer($question,4,7).'
					      </p>
			</td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_5'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_6'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,6,7).'
				          </p>
			</td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_7'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_8'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,8,9).'
				          </p>
			</td>
		  <td rowspan="4"><p>
						'.check_for_answer($question,8,11).'
				          </p>
			</td>
		  <td rowspan="8"><p>
						'.check_for_answer($question,8,15).'
				          </p>
			</td>
		 <tr>
		  <td><p>'.$question['question_option_9'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_10'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,10,11).'
				          </p>
			</td> </tr>
		 <tr>
		  <td><p>'.$question['question_option_11'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_12'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,12,13).'
				          </p>
			</td>
		  <td rowspan="4"><p>
						'.check_for_answer($question,12,15).'
				          </p>
			</td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_13'].'</p></td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_14'].'</p></td>
		  <td rowspan="2"><p>
						'.check_for_answer($question,14,15).'
				          </p>
			</td>
		 </tr>
		 <tr>
		  <td><p>'.$question['question_option_15'].'</p></td>
		 </tr>
		</table>');
	  }
		echo '</td>';
		echo '<td></td>';
		echo '<td>';	
		if($question_3) {
			$question = $question_3;
			printf('<table summary="Tournament Bracket">
			 <tr>
			  <td rowspan="16"><p>
					'.check_for_answer($question,0,15).'               
				          </p></td>
			  <td rowspan="8"><p>	
							'.check_for_answer($question,0,7).'
				          </p></td>
			  <td rowspan="4"><p>	
							'.check_for_answer($question,0,3).'
					          </p>
			  </td>
			  <td rowspan="2"><p>	
							'.check_for_answer($question,0,1).'             
					          </p>
					</td>
			  <td><p>'.$question['question_option_0'].'</p></td>
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_1'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="2"><p>
							'.check_for_answer($question,2,3).'
					          </p>
				</td>
			  <td><p>'.$question['question_option_2'].'</p></td>
			  
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_3'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="4"><p>
							'.check_for_answer($question,4,7).'
					          </p>
				</td>
			  <td rowspan="2"><p>
							'.check_for_answer($question,4,5).'
						      </p>
				</td>
			   <td><p>'.$question['question_option_4'].'</p></td>
			  
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_5'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="2"><p>
							'.check_for_answer($question,6,7).'
					          </p>
				</td>
			   <td><p>'.$question['question_option_6'].'</p></td>
			  
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_7'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="8"><p>
							'.check_for_answer($question,8,15).'
					          </p>
				</td>
			  <td rowspan="4"><p>
							'.check_for_answer($question,8,11).'
					          </p>
				</td>
			  <td rowspan="2"><p>
							'.check_for_answer($question,8,9).'
					          </p>
				</td>
			   <td><p>'.$question['question_option_8'].'</p></td>
			  
			 <tr>
			  <td><p>'.$question['question_option_9'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="2"><p>
							'.check_for_answer($question,10,11).'
					          </p>
				</td> </tr>
			   <td><p>'.$question['question_option_10'].'</p></td>
			  
			 <tr>
			  <td colspan=2></td>
			  <td><p>'.$question['question_option_11'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="4"><p>
							'.check_for_answer($question,12,15).'
					          </p>
				</td>
			  <td rowspan="2"><p>
							'.check_for_answer($question,12,13).'
					          </p>
				</td>
			  <td><p>'.$question['question_option_12'].'</p></td>
			  
			 </tr>
			 <tr>
			  <td><p>'.$question['question_option_13'].'</p></td>
			 </tr>
			 <tr>
			  <td rowspan="2"><p>
							'.check_for_answer($question,14,15).'
					          </p>
				</td>
			  <td><p>'.$question['question_option_14'].'</p></td>
			  
			 </tr>
			 <tr>
			  <td colspan="3"></td>
			  <td><p>'.$question['question_option_15'].'</p></td>
			 </tr>
			</table>');
		  }
		echo '</td>';
	echo '</tr>';
	echo('</table>');






	} 

?>

	
	
</div>