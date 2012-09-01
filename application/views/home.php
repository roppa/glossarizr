<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Glossarizr</title>

	<meta name="description" content="Enter the material you are studying and generate a glossary of the terms">
	<meta name="author" content="Mark Robson">
	
	<meta name="viewport" content="width=device-width">
	
	<link rel="stylesheet" href="/css/style.css" />
	<link href='http://fonts.googleapis.com/css?family=Tangerine:400,700' rel='stylesheet' type='text/css' />
	
	<style type="text/css">
	    #user { text-align:center; position: absolute; top: 20px; right: 20px; }
	    #user img, #user a img { border: none; }
	</style>
	
	<script src="/js/libs/modernizr-2.5.3.min.js"></script>
	
</head>
<body>

	<div id="wrap">
		<header>
			<hgroup>
				<h1>Glossarizr</h1>
				<h2><span>definition</span>: A tool providing the definition or definitions of words being studied</h2>
			</hgroup>
		</header>
		
		<?php echo validation_errors(); ?>
			
		<section id="search" role="main">
			<?php echo form_open('home'); ?>
				<label for="words">Word/s</label>
				<textarea accesskey="1" name="words"></textarea>
				<input type="submit" name="submit" value="Go" />
			</form>
		</section>
		
		<?php
		
			if(isset($word_list)) {
			
				echo '<h1>';
				echo sizeof($word_list) > 1 ? 'Your words (' . count($word_list) . ')' : 'Your word';
				echo '</h1>';
				
				echo '<ul class="word_links">';
				foreach($word_list as $word) {
					echo '<li><span>' . $word . '</span></li>';
				}
				echo '</ul>';
			}

			if(isset($defined_words)) {
			
				echo '<h1>';
				echo sizeof($defined_words) > 1 ? 'Defined words' : 'Defined word';
				echo '</h1>';
				
				echo '<ul class="word_links">';
				foreach($defined_words as $word) {
					echo '<li><a href="#' . $word . '">' . $word . '</a></li>';
				}
				echo '</ul>';
			}

			if(isset($undefined_words)) {
			
				echo '<h1>';
				echo sizeof($undefined_words) > 1 ? 'Undefined words' : 'Undefined word';
				echo '</h1>';
				
				echo '<ul class="word_links">';
				foreach($undefined_words as $word) {
					echo '<li><span>' . $word . '</span></li>';
				}
				echo '</ul>';
			}

			if (isset($results)) {
				echo '<section id="definitions">';
				echo $results;
				echo '</section>';
			}
		?>

		<section id="user">
		<?php
			//print_r($this->session->all_userdata());
			if ($this->session->userdata('id')) {
				echo $this->session->userdata('username') . ' - logout from <a href="' . site_url() . '/home/logout/'. '">' . $this->session->userdata('oauth_provider') . '</a>';
			} else { ?>
					<a href="<?= site_url(); ?>/home/login/twitter"><img src="/img/tw_login.png"></a>
					<a href="<?= site_url(); ?>/home/login/facebook"><img src="/img/fb_login.png"></a>
			<?php } ?>
		</section>

		<footer>
			<small>&copy; 2012 Mark Robson</small>
		</footer>
		
	</div>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.2.min.js"><\/script>')</script>
	<script>
		$(function() {
			$('article').before('<h2>Definition</h2>');
		});	
	</script>
	<script>
		var _gaq=[['_setAccount','UA-1945834-24'],['_trackPageview']];
		(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
		g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
		s.parentNode.insertBefore(g,s)}(document,'script'));
	</script>
</body>
</html>