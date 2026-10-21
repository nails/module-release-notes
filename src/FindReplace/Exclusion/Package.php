<?php

namespace Nails\ReleaseNotes\FindReplace\Exclusion;

use Nails\FindReplace\Interfaces\Exclusion;

class Package implements Exclusion
{
    public function hidden(): array
    {
        return [
            NAILS_DB_PREFIX . 'release_notes',
        ];
    }

    public function readOnly(): array
    {
        return [];
    }
}
