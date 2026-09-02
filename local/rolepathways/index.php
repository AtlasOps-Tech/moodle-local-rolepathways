<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/completionlib.php');

require_login();

$context = context_system::instance();
require_capability('local/rolepathways:view', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/rolepathways/index.php'));
$PAGE->set_title(get_string('dashboard', 'local_rolepathways'));
$PAGE->set_heading(format_string($SITE->fullname));
$PAGE->set_pagelayout('standard');
$PAGE->requires->css('/local/rolepathways/styles.css');

$courses = enrol_get_my_courses('id,fullname,shortname,summary,enablecompletion', 'sortorder ASC', 0);
$completionrecords = $DB->count_records_select(
    'course_modules_completion',
    'userid = :userid AND completionstate > 0',
    ['userid' => $USER->id]
);
$gradeditems = $DB->count_records_select(
    'grade_grades',
    'userid = :userid AND finalgrade IS NOT NULL',
    ['userid' => $USER->id]
);

$courseprogress = [];
foreach (array_slice($courses, 0, 6, true) as $course) {
    $total = 0;
    $complete = 0;

    if (!empty($course->enablecompletion)) {
        $completion = new completion_info($course);
        foreach ($completion->get_activities() as $activity) {
            $total++;
            $data = $completion->get_data($activity, true, $USER->id);
            if ((int) $data->completionstate > COMPLETION_INCOMPLETE) {
                $complete++;
            }
        }
    }

    $percent = $total > 0 ? (int) round(($complete / $total) * 100) : 0;
    $courseprogress[] = (object) [
        'course' => $course,
        'total' => $total,
        'complete' => $complete,
        'percent' => $percent,
    ];
}

$canviewsite = has_capability('moodle/site:config', $context)
    || has_capability('moodle/cohort:view', $context);

echo $OUTPUT->header();
?>
<main aria-labelledby="rp-title">
    <section class="rp-hero">
        <div class="rp-eyebrow"><?= get_string('pluginname', 'local_rolepathways') ?></div>
        <h2 id="rp-title"><?= get_string('dashboard', 'local_rolepathways') ?></h2>
        <p>One permission-aware view of assigned learning, completion evidence, and the tools available to your role.</p>
    </section>

    <section class="rp-grid" aria-label="Learning summary">
        <article class="rp-card">
            <strong class="rp-metric"><?= count($courses) ?></strong>
            <span class="rp-muted">Enrolled courses</span>
        </article>
        <article class="rp-card">
            <strong class="rp-metric"><?= $completionrecords ?></strong>
            <span class="rp-muted">Completed activities</span>
        </article>
        <article class="rp-card">
            <strong class="rp-metric"><?= $gradeditems ?></strong>
            <span class="rp-muted">Recorded grades</span>
        </article>
    </section>

    <section class="rp-grid mt-3">
        <article class="rp-card" style="grid-column: span 2">
            <h3>Current pathways</h3>
            <?php if (!$courseprogress): ?>
                <p class="rp-muted">No courses are currently assigned.</p>
            <?php endif; ?>
            <?php foreach ($courseprogress as $item): ?>
                <div class="rp-course">
                    <a href="<?= (new moodle_url('/course/view.php', ['id' => $item->course->id]))->out() ?>">
                        <strong><?= format_string($item->course->fullname) ?></strong>
                    </a>
                    <div class="rp-muted"><?= $item->complete ?> of <?= $item->total ?> tracked activities complete</div>
                    <div class="rp-progress" role="progressbar" aria-label="<?= s($item->course->fullname) ?> progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $item->percent ?>">
                        <span style="width: <?= $item->percent ?>%"></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </article>

        <aside class="rp-card">
            <h3>Available to your role</h3>
            <p><a href="<?= (new moodle_url('/my/courses.php'))->out() ?>">My courses</a></p>
            <p><a href="<?= (new moodle_url('/grade/report/overview/index.php'))->out() ?>">My grades</a></p>
            <?php if ($canviewsite): ?>
                <hr>
                <p><a href="<?= (new moodle_url('/admin/cohort/index.php'))->out() ?>">Cohort administration</a></p>
                <p><a href="<?= (new moodle_url('/report/log/index.php'))->out() ?>">Activity logs</a></p>
            <?php endif; ?>
        </aside>
    </section>
</main>
<?php
echo $OUTPUT->footer();
