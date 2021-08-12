<?php

namespace Nails\ReleaseNotes\Settings;

use Nails\Common\Helper\Form;
use Nails\Common\Interfaces;
use Nails\Common\Service\Input;
use Nails\Components\Setting;
use Nails\Factory;

/**
 * Class ReleaseNotes
 *
 * @package Nails\ReleaseNotes\Settings
 */
class ReleaseNotes implements Interfaces\Component\Settings
{
    const KEY_GH_REPO  = 'github_repo';
    const KEY_GH_USER  = 'github_user';
    const KEY_GH_TOKEN = 'github_token';

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return 'Release Notes';
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getPermissions(): array
    {
        return [];
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        /** @var Setting $oGitHubRepo */
        $oGitHubRepo = Factory::factory('ComponentSetting');
        $oGitHubRepo
            ->setKey(static::KEY_GH_REPO)
            ->setLabel('Repository')
            ->setPlaceholder('user/repo');

        /** @var Setting $oGitHubUser */
        $oGitHubUser = Factory::factory('ComponentSetting');
        $oGitHubUser
            ->setKey(static::KEY_GH_USER)
            ->setLabel('User')
            ->setFieldset('Authentication')
            ->setInfo('Required for private repos, and to increase rate limiting allowance.');

        /** @var Setting $oGitHubToken */
        $oGitHubToken = Factory::factory('ComponentSetting');
        $oGitHubToken
            ->setKey(static::KEY_GH_TOKEN)
            ->setType(Form::FIELD_PASSWORD)
            ->setLabel('Access Token')
            ->setEncrypted(true)
            ->setFieldset('Authentication')
            ->setInfo(sprintf(
                'Required for private repos, and to increase rate limiting allowance.<br><a href="%s" target="_blank" class="btn btn-xs btn-default">Create an access token</a>',
                'https://docs.github.com/en/github/authenticating-to-github/keeping-your-account-and-data-secure/creating-a-personal-access-token'
            ));

        return [
            $oGitHubRepo,
            $oGitHubUser,
            $oGitHubToken,
        ];
    }
}
