# Moodle Role Pathways

`local_rolepathways` is a lightweight, permission-aware Moodle dashboard for
learners, instructors, and administrators. It uses native Moodle enrolment,
completion, grade, cohort, and capability records; it does not create a second
learning-record system.

## Features

- Learner landing page with enrolled courses and live activity progress.
- Permission-aware links: learners see their records; privileged users see
  administration and reporting tools.
- Aggregate site metrics are restricted to users with appropriate Moodle
  capabilities.
- Responsive, high-contrast presentation with semantic progress indicators.
- No additional personal data is stored by the plugin.

## Compatibility

Moodle 4.2 and later. The initial release has been exercised on Moodle 4.5.

## Installation

Copy `local/rolepathways` to your Moodle installation:

```bash
cp -R local/rolepathways /path/to/moodle/local/rolepathways
php /path/to/moodle/admin/cli/upgrade.php --non-interactive
php /path/to/moodle/admin/cli/purge_caches.php
```

After installation, authenticated users can open:

```text
/local/rolepathways/index.php
```

Administrators can also find **Role pathways** under Site administration →
Plugins → Local plugins.

## Security and privacy

- The plugin calls `require_login()` and checks `local/rolepathways:view`.
- Privileged site data requires existing core capabilities.
- All rendered names and values are escaped through Moodle APIs.
- The plugin stores no personal data of its own.

## Contributing

Issues and focused pull requests are welcome. Please include reproduction steps
and testing evidence with behavioral changes.

## License

GNU General Public License v3 or later, consistent with Moodle.

