<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_int_keygen_countcode', get_string('countcode', 'block_int_keygen'),
                       '', 2, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_int_keygen_prefixcode', get_string('prefixcode', 'block_int_keygen'),
                       '', 'DOSK', PARAM_TEXT));   
}