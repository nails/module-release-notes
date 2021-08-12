<?php

/**
 * This config file defines email types for this module.
 *
 * @package     Nails
 * @subpackage  module-release-notes
 * @category    Config
 * @author      Nails Dev Team
 */

use Nails\ReleaseNotes\Constants;

$config['email_types'] = [
    (object) [
        'slug'            => 'release_note_notification',
        'name'            => 'Release Notes: Notification',
        'description'     => 'Email which is sent when new release notes are imported.',
        'template_header' => '',
        'template_body'   => 'releaseNotes/email/notification',
        'template_footer' => '',
        'default_subject' => 'New Website Updates',
        'can_unsubscribe' => true,
        'factory'         => Constants::MODULE_SLUG . '::EmailNotification',
    ],
];
