<?php

/**
 * Migration: 1
 * Started:   11/08/2022
 *
 * @package     Nails
 * @subpackage  module-elasticsearch
 * @category    Database Migration
 * @author      Nails Dev Team
 */

namespace Nails\ReleaseNotes\Database\Migration;

use Nails\Common\Interfaces;
use Nails\Common\Traits;
use Nails\ReleaseNotes\Admin\Permission;

/**
 * Class Migration1
 *
 * @package Nails\ReleaseNotes\Database\Migration
 */
class Migration1 implements Interfaces\Database\Migration
{
    use Traits\Database\Migration;

    // --------------------------------------------------------------------------

    const MAP = [
        'admin:releasenotes:releasenotes:browse'  => Permission\Archive\Browse::class,
        'admin:releasenotes:releasenotes:create'  => '',
        'admin:releasenotes:releasenotes:edit'    => Permission\Archive\Edit::class,
        'admin:releasenotes:releasenotes:delete'  => '',
        'admin:releasenotes:releasenotes:restore' => '',
    ];

    // --------------------------------------------------------------------------

    /**
     * Execute the migration
     */
    public function execute(): void
    {
        //  On a fresh build, this table might not yet exist
        $oResult = $this->query('SHOW TABLES LIKE "{{NAILS_DB_PREFIX}}user_group"');
        if ($oResult->rowCount() === 0) {
            return;
        }

        $oResult = $this->query('SELECT id, acl FROM `{{NAILS_DB_PREFIX}}user_group`');
        while ($row = $oResult->fetchObject()) {

            $acl = json_decode($row->acl) ?? [];

            foreach ($acl as &$old) {
                $old = self::MAP[$old] ?? $old;
            }

            $acl = array_filter($acl);
            $acl = array_unique($acl);
            $acl = array_values($acl);

            $this
                ->prepare('UPDATE `{{NAILS_DB_PREFIX}}user_group` SET `acl` = :acl WHERE `id` = :id')
                ->execute([
                    ':id'  => $row->id,
                    ':acl' => json_encode($acl),
                ]);
        }
    }
}
