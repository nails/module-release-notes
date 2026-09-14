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

use Nails\Admin\Traits\Database\Migration\PermissionMap;
use Nails\Common\Interfaces;
use Nails\Common\Traits;
use Nails\ReleaseNotes\Admin\Permission;

/**
 * Class Migration1
 *
 * Repeatable because `feature/pre-new-admin` has no equivalent migration, so an app
 * arriving from that branch resumes above this number and would never run it.
 *
 * @package Nails\ReleaseNotes\Database\Migration
 */
class Migration1 implements Interfaces\Database\Migration\Repeatable
{
    use Traits\Database\Migration;
    use PermissionMap;

    // --------------------------------------------------------------------------

    const MAP = [
        'admin:releasenotes:releasenotes:browse'  => Permission\Archive\Browse::class,
        'admin:releasenotes:releasenotes:create'  => '',
        'admin:releasenotes:releasenotes:edit'    => Permission\Archive\Edit::class,
        'admin:releasenotes:releasenotes:delete'  => '',
        'admin:releasenotes:releasenotes:restore' => '',
    ];
}
