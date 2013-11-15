<?php

function theme_dosk_page_init(moodle_page $page) {
    $page->requires->jquery();
	$page->requires->jquery_plugin('ui');
	$page->requires->jquery_plugin('ui-css');
	$page->requires->jquery_plugin('placeholder', 'theme_dosk');
}