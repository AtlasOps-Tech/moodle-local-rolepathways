<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

function local_rolepathways_extend_navigation(global_navigation $navigation): void {
    if (!isloggedin() || isguestuser()) {
        return;
    }

    $navigation->add(
        get_string('dashboard', 'local_rolepathways'),
        new moodle_url('/local/rolepathways/index.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        'local_rolepathways',
        new pix_icon('i/course', '')
    );
}

