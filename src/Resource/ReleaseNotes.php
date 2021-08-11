<?php

namespace Nails\ReleaseNotes\Resource;

use Nails\Common\Resource\DateTime;
use Nails\Common\Resource\Entity;

/**
 * Class ReleaseNotes
 *
 * @package Nails\ReleaseNotes\Resource
 */
class ReleaseNotes extends Entity
{
    public string $tag;
    public string $sha;
    public string $message;
    public DateTime $date;

    // --------------------------------------------------------------------------

    public function renderMessage(): string
    {
        //  @todo (Pablo 2021-08-11) - Compile Markdown
        return nl2br($this->message);
    }
}
