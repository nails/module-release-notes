<?php

namespace Nails\ReleaseNotes\Interfaces;

/**
 * Interface Notification
 *
 * @package Nails\ReleaseNotes\Interfaces
 */
interface Notification
{
    /**
     * Returns an array of email addresses to send notifications to
     *
     * @return string[]
     */
    public function getEmails(): array;
}
