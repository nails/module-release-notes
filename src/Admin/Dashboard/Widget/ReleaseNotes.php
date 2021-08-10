<?php

namespace App\Admin\Dashboard\Widget;

use Nails\Admin\Admin\Dashboard\Widget\Base;
use Nails\Admin\Interfaces;
use Nails\Admin\Traits;
use Nails\Common\Factory\HttpRequest\Get;
use Nails\Factory;

/**
 * Class ReleaseNotes
 *
 * @package App\\Admin\Dashboard\Widget
 */
class ReleaseNotes implements Interfaces\Dashboard\Widget
{
    use Traits\Dashboard\Widget;

    // --------------------------------------------------------------------------

    const REPO       = 'shedcollective/ncl-website';
    const AUTH_USER  = null;
    const AUTH_TOKEN = null;
    //  @todo (Pablo 2021-08-10) - support pagination, somehow
    const LIMIT = 3;

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
        return 'Renders release notes.';
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getBody(): string
    {
        $aTags = array_map(function ($oTag) {

            return sprintf(
                implode(PHP_EOL, [
                    '<h1 style="float: left;">%s</h1>',
                    '<h2 style="border: none; padding: 0; margin:0; float: right">%s</h2>',
                    '<div style="clear: both;">%s</div>',
                ]),
                $oTag->tag,
                $oTag->date->formatted,
                $oTag->body
            );

        }, $this->getTags());

        return implode(PHP_EOL, array_reverse($aTags));
    }

    // --------------------------------------------------------------------------

    private function getTags(): array
    {
        //  @todo (Pablo 2021-08-10) - cache results to avoid rate limiting
        //  @todo (Pablo 2021-08-10) - support GH Access Token

        /** @var Get $oHttpGet */
        $oHttpGet = Factory::factory('HttpRequestGet');
        $aTags    = $oHttpGet
            ->baseUri('https://api.github.com')
            ->path(sprintf(
                'repos/%s/git/refs/tags',
                static::REPO
            ))
            ->auth(...$this->getGitHubCredentials())
            ->execute()
            ->getBody();

        $aTags = array_reverse($aTags);
        $aTags = array_slice($aTags, 0, static::LIMIT);

        //  @todo (Pablo 2021-08-10) - sort results in a reliable fashion (date tagged might not be chronological)

        return array_map(function ($oItem) {

            //  @todo (Pablo 2021-08-10) - return an actual class/resource

            /** @var Get $oHttpGet */
            $oHttpGet = Factory::factory('HttpRequestGet');
            $oResult  = $oHttpGet
                ->baseUri($oItem->object->url)
                ->auth(...$this->getGitHubCredentials())
                ->execute()
                ->getBody();

            return (object) [
                'tag'  => $oResult->tag,
                'date' => Factory::resource('DateTime', null, ['raw' => $oResult->tagger->date]),
                'body' => $this->renderMarkdown($oResult->message),
            ];

        }, $aTags);
    }

    // --------------------------------------------------------------------------

    private function getGitHubCredentials(): array
    {
        //  @todo (Pablo 2021-08-10) - Get these from settings/env
        return [
            static::AUTH_USER,
            static::AUTH_TOKEN,
        ];
    }

    // --------------------------------------------------------------------------

    private function renderMarkdown(string $sInput): string
    {
        //  @todo (Pablo 2021-08-10) - actually render markdown
        return nl2br($sInput);
    }
}
