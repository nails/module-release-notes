<?php

namespace Nails\ReleaseNotes\Admin\Permission\Archive;

use Nails\Admin\Interfaces\Permission;

class Browse implements Permission
{
    public function label(): string
    {
        return 'Can browse release notes';
    }

    public function group(): string
    {
        return 'Release Notes';
    }
}
