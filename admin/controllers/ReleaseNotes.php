<?php

namespace Nails\Admin\ReleaseNotes;

use Nails\Admin\Controller\DefaultController;
use Nails\ReleaseNotes\Constants;

class ReleaseNotes extends DefaultController
{
    const CONFIG_MODEL_NAME         = 'ReleaseNotes';
    const CONFIG_MODEL_PROVIDER     = Constants::MODULE_SLUG;
    const CONFIG_PERMISSION         = 'releasenotes';
    const CONFIG_SIDEBAR_GROUP      = 'Dashboard';
    const CONFIG_SIDEBAR_FORMAT     = '%s';
    const CONFIG_SORT_DIRECTION     = self::SORT_DESCENDING;
    const CONFIG_CAN_CREATE         = false;
    const CONFIG_CAN_DELETE         = false;
    const CONFIG_CAN_RESTORE        = false;
    const CONFIG_INDEX_NOTES_ENABLE = false;
    const CONFIG_EDIT_NOTES_ENABLE  = false;
    const CONFIG_SORT_OPTIONS       = [
        'Date' => 'date',
        'Tag'  => 'tag',
    ];
    const CONFIG_INDEX_FIELDS       = [
        'Tag'     => 'tag',
        'Message' => null,
        'Date'    => 'date',
    ];

    // --------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->aConfig['INDEX_FIELDS']['Message'] = function (\Nails\ReleaseNotes\Resource\ReleaseNotes $oNote) {
            return $oNote->renderHtml();
        };
    }
}
