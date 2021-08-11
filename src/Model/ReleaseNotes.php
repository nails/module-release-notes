<?php

namespace Nails\ReleaseNotes\Model;

use Nails\Common\Model\Base;
use Nails\ReleaseNotes\Constants;

class ReleaseNotes extends Base
{
    const TABLE             = NAILS_DB_PREFIX . 'release_notes';
    const RESOURCE_NAME     = 'ReleaseNotes';
    const RESOURCE_PROVIDER = Constants::MODULE_SLUG;
}
