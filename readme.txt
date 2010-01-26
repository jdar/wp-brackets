=== WP-Brackets ===
Contact: darius.roberts@gmail.com
Tags: survey, question, bracket, team, runoff
Requires at least: 2.3
Affiliations: collegerag.org - where sports meets snark

== Description ==

Create and Score Sports Brackets for WordPress.

A major customization of [WP-Surveys](http://downloads.wordpress.org/plugin/wp-surveys.zip), which was inspired by the groundwork of [Survey Fly](http://plugins.starkware.net/?p=10), which it was previously inspired by [Instinct Entertainment's Survey Creator Plugin](http://www.instinct.co.nz/?p=11).

Hacky as hell. But sometimes that's what you need.

Some features:

* Create, modify and retire sports brackets forms, for users to predict bracket
* Create brackets of 16, and finals brackets to span up to 4 regular brackets
* Up to 16 answer options with horizontal, vertical and dropdown-menu alignment.
* A template for 'results' display
* A widget leaderboard (based on, but less functional than, Top Commentators plugin by webgrrl.net)

== Installation ==

Upload files to your plugin directory and activate within WordPress.
You can manage by adding new surveys, and then adding questions and answer options.
If you want to be able to track responses, you MUST add a Leaderboard question.
Only the second leaderboard question or tie-breaker question will be visible. You might want to re-hardcode the tie-breaker html (in wp_surveys_out)
The single active survey will show up in the Page or Post where you place the text: [sports_form]

To make the simple (unstyled) results template available, 

TODO:
  - Add a "" - currently the bracket_results_template does not work after the survey has been 'retired' from new entries. Yes this is a little silly. Sorry. I'm not sure how to load the correct bracket_results from among the potential retired survesy, however. Bah.
