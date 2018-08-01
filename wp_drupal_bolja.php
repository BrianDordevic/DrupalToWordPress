<?PHP
set_time_limit (0);
header('Content-type: text/html; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] != "POST") {
	?>
	<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no, user-scalable=0">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Drupal to Wordpress</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    </head>
	<body>
		<h1>Drupal to Wordpress</h1>
		<div class="container">
			<form id="db-form">
				<div class="form-group row">
					<label for="db-host" class="col-4 col-form-label">MySQL Host</label>
					<div class="col-8">
						<input id="db-host" name="db-host" placeholder="localhost" class="form-control here" type="text" value="localhost">
					</div>
				</div>
				<div class="form-group row">
					<label for="db-username" class="col-4 col-form-label">MySQL Username</label>
					<div class="col-8">
						<input id="db-username" name="db-username" placeholder="username" class="form-control here" type="text" value="root">
					</div>
				</div>
				<div class="form-group row">
					<label for="db-username" class="col-4 col-form-label">MySQL Password</label>
					<div class="col-8">
						<input id="db-password" name="db-password" placeholder="password" class="form-control here" type="password" value="">
					</div>
				</div>
				<div class="form-group row">
					<label for="db-wp" class="col-4 col-form-label">Wordpress Database</label>
					<div class="col-8">
						<input id="db-wp" name="db-wp" placeholder="Wordpress Database" class="form-control here" type="text" value="wp">
					</div>
				</div>
				<div class="form-group row">
					<label for="db-drupal" class="col-4 col-form-label">Drupal Database</label>
					<div class="col-8">
						<input id="db-drupal" name="db-drupal" placeholder="Drupal Database" class="form-control here" type="text" value="boljazemlja">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-12 text-center">
						<button id="submit-btn" name="submit" type="submit" class="btn btn-primary">Start</button>
					</div>
				</div>
				<div class="form-group row">
					<div style="display:none;border:1px dotted black;overflow:auto;background-color:#fff;" class="col-12" id="generatedOutput"></div>
				</div>
			</form>
		</div>
		<script
		  src="https://code.jquery.com/jquery-3.3.1.min.js"
		  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
		  crossorigin="anonymous"></script>
		<script src="//cdn.jsdelivr.net/jquery.color-animation/1/mainfile"></script>
		<script>
			$( document ).ready(function() {			
				$('#submit-btn').click(function(e) {
					$(this).html('<i class="fa fa-spinner fa-spin"></i> Copying..');
					$(this).attr('disabled','disabled');
					$('#generatedOutput').css('height', 'auto').html('<span style="font-size:20px;">Please wait..</span>').show('slow');
					$.post('wp_drupal.php', $('#db-form').serialize(), function(data) {
						$('#generatedOutput').animate({height:300},'slow').html(data);
						$('#submit-btn').removeAttr('disabled');
						$('#submit-btn').text('Start');
						$('body').animate({backgroundColor: '#00ff00'}, 'fast').delay(200).animate({backgroundColor: '#ffffff'}, 5000);
					});
					e.preventDefault();
				});
			});
		</script>
	</body>
	</html>
	<?PHP
	die();
}

$db_conf = array(   
	'host' 		=> $_POST['db-host'],
	'uname' 	=> $_POST['db-username'],
	'pwd'		=> $_POST['db-password'],
	'db_wp'		=> $_POST['db-wp'],
	'db_drupal'	=> $_POST['db-drupal']
	);

$addID = 10000;
	
$escape_chars = array(
	':' 	=> '',
	')'		=> '',
	'('		=> '',
	','		=> '',
	'\\'	=> '',
	'\/'	=> '',
	'\"'	=> '',
	'?'		=> '',
	'\''	=> '',
	'&'		=> '',
	'!'		=> '',
	'.'		=> '',
	' '		=> '-',
	'--'	=> '-',
	'č'		=> 'c',
	'ć'		=> 'c',
	'ž'		=> 'z',
	'đ'		=> 'dj',
	'š'		=> 's'
	);

function sql_str_replace($replace, $column) {
	$sql_final = "LOWER(TRIM(" . $column . "))";
	foreach($replace as $char=>$to_char)
		$sql_final = "REPLACE(" . $sql_final . ", '" . addslashes($char) . "', '" . addslashes($to_char) . "')";
	return $sql_final;
}

$db = mysqli_connect($db_conf['host'], $db_conf['uname'], $db_conf['pwd'], $db_conf['db_wp']);
if (!$db) {
	echo("<pre><span style=\"color:red;font-size:40px\"><strong>MySQL connection failed!<br />Check your credentials.</strong></span></pre>");
	flush();
    ob_flush();
	die();
}

/* $sql = array(
		"wp_comments",
		"wp_links",
		"wp_posts",
		"wp_postmeta",
		"wp_term_relationships",
		"wp_term_taxonomy",
		"wp_terms"
		);
		

foreach($sql as $query) {
	mysqli_query($db, "TRUNCATE TABLE " . $query) or die("Unable to \"TRUNCATE TABLE " . $query . "\"");
	echo("<pre><strong><span style=\"color:red;\">TRUNCATE</span></strong> TABLE " . $query . "</pre>");
} */

$sql = "DELETE FROM wp_users WHERE ID > 1";
mysqli_query($db, $sql) or die("Unable to remove clear wp_users \"Do not do this manually\"");
echo("<pre><strong><span style=\"color:red;\">DELETED</span></strong> all users from wp_users except admin </pre>");
flush();
ob_flush();

$sql = "DELETE FROM wp_usermeta WHERE user_id > 1";
mysqli_query($db, $sql) or die("Unable to clear wp_usermeta \"Do not do this manually\"");
echo("<pre><strong><span style=\"color:red;\">DELETED</span></strong> all data from wp_usermeta except admin </pre>");
flush();
ob_flush();

mysqli_query($db, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
echo("<pre><strong><span style=\"color:red;\">SET</span></strong> " . $db_conf['db_wp'] . " to utf8</pre>");
flush();
ob_flush();

$sql = "INSERT INTO wp_terms (term_id, name, slug, term_group) SELECT (d.tid + " . $addID . "), d.name, " . sql_str_replace($escape_chars, "d.name") . ", 0 FROM " . $db_conf['db_drupal'] . ".taxonomy_term_data d INNER JOIN " . $db_conf['db_drupal'] . ".taxonomy_term_hierarchy h USING(tid)";
mysqli_query($db, $sql) or die("Unable to migrate tags");
echo("<pre><strong><span style=\"color:red;\">MIGRATED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> tags to wordpress</pre>");
flush();
ob_flush();

$sql = "INSERT INTO wp_term_taxonomy (term_id, taxonomy, description, parent) SELECT (d.tid + " . $addID . ") `term_id`, 'category' `taxonomy`, d.description `description`, h.parent `parent` FROM " . $db_conf['db_drupal'] . ".taxonomy_term_data d INNER JOIN " . $db_conf['db_drupal'] . ".taxonomy_term_hierarchy h USING(tid)";
mysqli_query($db, $sql) or die("Unable to migrate taxonomy terms / vocabulary");
echo("<pre><strong><span style=\"color:red;\">MIGRATED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> taxonomy terms to wordpress</pre>");
flush();
ob_flush();

$sql = "INSERT INTO wp_posts (id, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_name, post_modified, post_modified_gmt, post_type, post_status, to_ping, pinged, post_content_filtered) SELECT (n.nid + " . $addID . ") `id`, (n.uid + " . $addID . ") `post_author`, FROM_UNIXTIME(n.created) `post_date`, NOW() `post_date_gmt`, r.body_value `post_content`, n.title `post_title`, r.body_summary `post_excerpt`, " . sql_str_replace($escape_chars, "IF(SUBSTR(a.alias, 11, 1) = '/', SUBSTR(a.alias, 12), a.alias)") . " `post_name`, FROM_UNIXTIME(n.changed) `post_modified`, NOW() `post_modified_gmt`, n.type `post_type`, IF(n.status = 1, 'publish', 'private') `post_status`, '', '', '' FROM " . $db_conf['db_drupal'] . ".node n, " . $db_conf['db_drupal'] . ".field_data_body r, " . $db_conf['db_drupal'] . ".url_alias a WHERE n.vid = r.entity_id AND a.source = CONCAT('node/', n.nid)";
mysqli_query($db, $sql) or die("Unable to merge posts " . mysqli_error($db));
echo("<pre><strong><span style=\"color:red;\">MIGRATED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> posts to wordpress</pre>");
flush();
ob_flush();

$sql = "UPDATE wp_posts SET post_type = 'post' WHERE post_type <> 'page' AND post_type <> 'post' AND post_type NOT LIKE '%revision%'";
mysqli_query($db, $sql) or die("Unable to convert pages");
echo("<pre><strong><span style=\"color:red;\">CONVERTED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> posts to post_type='posts'</pre>");
flush();
ob_flush();

$sql = "INSERT INTO wp_term_relationships (object_id, term_taxonomy_id) SELECT (nid + " . $addID . "), (tid + " . $addID . ") FROM " . $db_conf['db_drupal'] . ".taxonomy_index";
mysqli_query($db, $sql) or die("Unable to update post to tag / category relationship");
echo("<pre><strong><span style=\"color:red;\">UPDATED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> wp_term_relationships</pre>");
flush();
ob_flush();

$sql = "UPDATE wp_term_taxonomy tt SET `count` = (SELECT COUNT(tr.object_id) FROM wp_term_relationships tr WHERE tr.term_taxonomy_id = tt.term_taxonomy_id)";
mysqli_query($db, $sql) or die("Unable to update tags / category post count");
echo("<pre><strong><span style=\"color:red;\">UPDATED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> wp_term_taxonomy</pre>");
flush();
ob_flush();

$sql = "INSERT INTO wp_comments (comment_post_ID, comment_date, comment_date_gmt, comment_content, comment_parent, comment_author, comment_author_email, comment_author_url, comment_approved) SELECT (nid + " . $addID . "), FROM_UNIXTIME(created), NOW(), comment_body_value, 0, name, mail, homepage, ((status + 1) % 2) FROM " . $db_conf['db_drupal'] . ".comment, " . $db_conf['db_drupal'] . ".field_data_comment_body WHERE name = IN('admin', 'marija.papic', 'esenca_admin')";
mysqli_query($db, $sql) or die("Unable to insert comments to posts " . mysqli_error($db));
echo("<pre><strong><span style=\"color:red;\">MIGRATED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> comments to wordpress</pre>");
flush();
ob_flush();

$sql = "UPDATE wp_posts SET `comment_count` = (SELECT COUNT(`comment_post_id`) FROM wp_comments WHERE wp_posts.`id` = wp_comments.`comment_post_id`)";
mysqli_query($db, $sql) or die("Unable to update post comments count");
echo("<pre><strong><span style=\"color:red;\">UPDATED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> post comment count</pre>");
flush();
ob_flush();

$sql = "UPDATE IGNORE wp_term_relationships, wp_term_taxonomy SET wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id WHERE wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_id";
mysqli_query($db, $sql) or die("Unable to fix taxonomy");
echo("<pre><strong><span style=\"color:red;\">FIXED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> wp_term_taxonomy</pre>");
flush();
ob_flush();

$sql = "INSERT IGNORE INTO wp_users (ID, user_login, user_pass, user_nicename, user_email, user_registered, user_activation_key, user_status, display_name) SELECT u.uid + " . $addID . ", u.mail, NULL, u.name, u.mail, FROM_UNIXTIME(created), '', 0, u.name FROM " . $db_conf['db_drupal'] . ".users u INNER JOIN " . $db_conf['db_drupal'] . ".users_roles r USING (uid) WHERE (1 AND u.uid > 1)";
mysqli_query($db, $sql) or die("Unable to insert authors");
echo("<pre><strong><span style=\"color:red;\">MIGRATED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> post authors</pre>");
flush();
ob_flush();

$sql = "INSERT IGNORE INTO wp_usermeta (user_id, meta_key, meta_value) SELECT u.uid + " . $addID . ", 'wp_jams_capabilities', 'a:1:{s:6:\"author\";s:1:\"1\";}' FROM " . $db_conf['db_drupal'] . ".users u INNER JOIN " . $db_conf['db_drupal'] . ".users_roles r USING (uid) WHERE (1 AND u.uid > 1);";
mysqli_query($db, $sql) or die("Unable to assign author roles / permissions to users");
flush();
ob_flush();

$sql = "INSERT IGNORE INTO wp_usermeta (user_id, meta_key, meta_value) SELECT u.uid + " . $addID . ", 'wp_jams_user_level', '2' FROM " . $db_conf['db_drupal'] . ".users u INNER JOIN " . $db_conf['db_drupal'] . ".users_roles r USING (uid) WHERE (1 AND u.uid > 1);";
mysqli_query($db, $sql) or die("Unable to assign author roles / permissions to users");
echo("<pre><strong><span style=\"color:red;\">ASSIGNED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> author roles</pre>");
flush();
ob_flush();

$sql = "UPDATE wp_usermeta SET meta_value = 'a:1:{s:13:\"administrator\";s:1:\"1\";}' WHERE user_id IN (1) AND meta_key = 'wp_jams_capabilities';";
mysqli_query($db, $sql) or die("Unable to assign and give administrator rights / privileges");
flush();
ob_flush();

$sql = "UPDATE wp_usermeta SET meta_value = '10' WHERE user_id IN (1) AND meta_key = 'wp_jams_user_level';";
mysqli_query($db, $sql) or die("Unable to assign and give administrator rights / privileges");
echo("<pre><strong><span style=\"color:red;\">ASSIGNED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> administrative privileges</pre>");
flush();
ob_flush();

$sql = "UPDATE wp_posts SET post_content = REPLACE(post_content, '/sites/default/files/', '/wp-content/uploads/old/')";
mysqli_query($db, $sql) or die("Unable to replace file paths");
echo("<pre><strong><span style=\"color:red;\">FIXED</span></strong> <strong>" . mysqli_affected_rows($db) . "</strong> file paths</pre>");
flush();
ob_flush();

die("<pre><strong>Done</strong></pre>");

//attachment SQL //merge attachments to wp_posts
//INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_title, post_status, comment_status, ping_status, post_name, post_modified, post_modified_gmt, post_parent, guid, post_type, post_mime_type, comment_count) SELECT (fid + 20000) as ID, uid as post_author, FROM_UNIXTIME(timestamp) as post_date, FROM_UNIXTIME(timestamp) as post_date_gmt, CONCAT(FLOOR(RAND() * 401) + 100, '-', filename) as post_title, 'inherit' as post_status, 'open' as comment_status, 'closed' as ping_status, CONCAT(FLOOR(RAND() * 401) + 100, '-', REPLACE(filename, '.', '-')) as post_name, FROM_UNIXTIME(timestamp) as post_modified, FROM_UNIXTIME(timestamp) as post_modified_gmt, '0' as post_parent, CONCAT('http://boljazemljastage.bojan.tv/wp-content/uploads/old/',uri) as guid, 'attachment' as post_type, filemime as post_mime_type, '0' as comment_count FROM `file_managed`
//SELECT (field_teaser_image_fid + 20000) as ID, (entity_id + 10000) as post_id FROM `field_data_field_teaser_image`
?>