<?php

namespace Nails\ReleaseNotes\Admin\Permission\Settings;

use Nails\Admin\Interfaces\Permission;

class ReleaseNotes implements Permission
{
    public function label(): string
    {
        return 'Can manage Release Notes settings';
    }

    public function group(): string
    {
        return 'Settings';
    }
}
