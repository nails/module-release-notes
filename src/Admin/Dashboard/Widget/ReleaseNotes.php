<?php

namespace Nails\ReleaseNotes\Admin\Dashboard\Widget;

use Nails\Admin\Admin\Dashboard\Widget\Base;
use Nails\Admin\Interfaces;
use Nails\Admin\Traits;
use Nails\Common\Factory\HttpRequest\Get;
use Nails\Factory;
use Nails\ReleaseNotes\Admin\Controller\Archive;
use Nails\ReleaseNotes\Admin\Permission;
use Nails\ReleaseNotes\Constants;

/**
 * Class ReleaseNotes
 *
 * @package Nails\ReleaseNotes\\Admin\Dashboard\Widget
 */
class ReleaseNotes implements Interfaces\Dashboard\Widget
{
    use Traits\Dashboard\Widget;

    // --------------------------------------------------------------------------

    const LIMIT = 10;

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'Release Notes';
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return sprintf(
            'Renders the %s most recent release notes.',
            static::LIMIT
        );
    }

    // --------------------------------------------------------------------------

    public function isEnabled(\Nails\Auth\Resource\User $oUser = null): bool
    {
        return userHasPermission(Permission\Archive\Browse::class, $oUser);
    }

    // --------------------------------------------------------------------------

    public function isPadded(): bool
    {
        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getBody(): string
    {
        $sGuid   = md5((string) microtime(true));
        $sStyles = <<<EOT
        <style type="text/css">

            .release-notes-$sGuid--header {
                font-weight: bold;
                font-size: 1.15rem;
                padding: 1rem;
                background: #c6c6c6;
                border-bottom: 2px solid #afafaf;
            }

            .release-notes-$sGuid--header:after {
                clear: both;
                display: block;
                content: '';
            }

            .release-notes-$sGuid--tag {
                float: left;
            }

            .release-notes-$sGuid--date {
                float: right;
                font-size: 0.75rem;
                opacity: 0.6;
            }

            .release-notes-$sGuid--body {
                padding: 1em;
            }

            .release-notes-$sGuid--body ul {
                list-style-type: circle;
                margin-left: 1rem;
            }

            .release-notes-$sGuid--body li {
                margin: 0;
            }

            .release-notes-$sGuid--cta {
                padding: 1em;
                margin: 0;
                border-top: 1px solid #afafaf;
            }

        </style>
        EOT;

        $aTags = array_map(function (\Nails\ReleaseNotes\Resource\ReleaseNotes $oTag) use ($sGuid) {

            return sprintf(
                implode(PHP_EOL, [
                    '<div class="release-notes-' . $sGuid . '">',
                    '<div class="release-notes-' . $sGuid . '--header">',
                    '<div class="release-notes-' . $sGuid . '--tag">%s</div>',
                    '<div class="release-notes-' . $sGuid . '--date">%s</div>',
                    '</div>',
                    '<div class="release-notes-' . $sGuid . '--body">%s</div>',
                    '</div>',
                ]),
                $oTag->tag,
                $oTag->date->formatted,
                $oTag->renderHtml()
            );

        }, $this->getTags());

        return implode(PHP_EOL, [
            $sStyles,
            implode(PHP_EOL, $aTags),
            sprintf(
                '<p class="%s"><a href="%s" class="btn btn-primary btn-block">View All</a></a></p>',
                'release-notes-' . $sGuid . '--cta',
                Archive::url()
            ),
        ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the most recent N tags
     *
     * @return \Nails\ReleaseNotes\Resource\ReleaseNotes[]
     * @throws \Nails\Common\Exception\FactoryException
     * @throws \Nails\Common\Exception\ModelException
     */
    private function getTags(): array
    {
        /** @var \Nails\ReleaseNotes\Model\ReleaseNotes $oReleaseNotesModel */
        $oReleaseNotesModel = Factory::model('ReleaseNotes', Constants::MODULE_SLUG);

        /** @var \Nails\ReleaseNotes\Resource\ReleaseNotes[] $aTags */
        $aTags = $oReleaseNotesModel->getAll([
            'limit' => static::LIMIT,
        ]);

        return $aTags;
    }
}
