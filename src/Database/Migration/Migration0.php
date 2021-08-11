<?php

/**
 * Migration: 0
 * Started:   11/08/2021
 */

namespace Nails\ReleaseNotes\Database\Migration;

use Nails\Common\Console\Migrate\Base;

/**
 * Class Migration0
 *
 * @package Nails\ReleaseNotes\Database\Migration
 */
class Migration0 extends Base
{
    /**
     * Execute the migration
     *
     * @return Void
     */
    public function execute()
    {
        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}release_notes` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `tag` varchar(150) DEFAULT NULL,
                `sha` varchar(150) DEFAULT NULL,
                `message` text,
                `date` datetime NOT NULL,
                `created` datetime NOT NULL,
                `created_by` int unsigned DEFAULT NULL,
                `modified` datetime DEFAULT NULL,
                `modified_by` int unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}release_notes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}release_notes_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }
}
