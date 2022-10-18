<?php

namespace Nails\ReleaseNotes\Admin\Permission\Archive;

use Nails\Admin\Interfaces\Permission;

class Edit implements Permission
{
    public function label(): string
    {
        return 'Can edit release notes';
    }

    public function group(): string
    {
        return 'Release Notes';
    }
}
