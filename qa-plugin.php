<?php

/*
	Plugin Name: Faq Page
	Plugin URI: https://github.com/HammerAPI/q2a-faq
	Plugin Update Check URI: https://raw.githubusercontent.com/HammerAPI/q2a-faq/master/qa-plugin.php
	Plugin Description: Adds custom faq page
	Plugin Version: 1.0
	Plugin Date: 2022-07-26
	Plugin Author: NoahY (original author), gturri (fork maintainer), HammerAPI (fork maintainer)
	Plugin Author URI: http://www.question2answer.org/
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.3
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}

    // Register all language files
    qa_register_plugin_phrases('lang/qa-faq-lang-*.php', 'qa-faq');
	qa_register_plugin_layer('qa-faq-layer.php', 'FAQ Layer');
	qa_register_plugin_module('page', 'qa-faq-page.php', 'qa_faq_page', 'FAQ Page');
	qa_register_plugin_module('module', 'qa-faq-admin.php', 'qa_faq_admin', 'Faq Admin');

/*
	Omit PHP closing tag to help avoid accidental output
*/
