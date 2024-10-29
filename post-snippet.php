<?php
$egSqlCrediantial = "SELECT * FROM `".APM_POST_SNIPPET."` WHERE 1";
$record = array();
$snippets =   $wpdb->get_results($egSqlCrediantial,ARRAY_A);

/*foreach ($snippets as $snippet) {
	add_shortcode(
		$snippet['post_snippet_variables'],
		create_function(
			'$atts,$content=null',
			'$snippet = \''. addslashes_gpc($snippet["post_snippet_value"]) .'\';
			// Disables auto conversion from & to &amp; as that should be done in snippet, not code (destroys php etc).
			// $snippet = str_replace("&", "&amp;", $snippet);
			//$snippet = str_replace("{content}", "", $snippet);
			// Strip escaping and execute nested shortcodes
			$snippet = do_shortcode(stripslashes($snippet));
			return $snippet;'
		)
	);
}*/


foreach ($snippets as $snippet) {
	add_shortcode(
		$snippet['post_snippet_variables'],
		function($atts, $content=null) use ($snippet) { $snippet = addslashes_gpc($snippet["post_snippet_value"]); $snippet = do_shortcode(stripslashes($snippet)); return $snippet;}
		
	);
}

add_filter( 'widget_text', 'do_shortcode' );

?>