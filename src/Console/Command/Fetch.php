<?php

namespace Nails\ReleaseNotes\Console\Command;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Exception\ValidationException;
use Nails\Common\Factory\HttpRequest\Get;
use Nails\Common\Factory\HttpResponse;
use Nails\Common\Resource\DateTime;
use Nails\Common\Service\HttpCodes;
use Nails\Components;
use Nails\Console\Command\Base;
use Nails\Console\Exception\ConsoleException;
use Nails\Factory;
use Nails\ReleaseNotes\Constants;
use Nails\ReleaseNotes\Resource;
use Nails\ReleaseNotes\Interfaces;
use Nails\ReleaseNotes\Settings;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Fetch
 *
 * @package Nails\ReleaseNotes\Console\Command
 */
class Fetch extends Base
{
    /** @var \stdClass[] */
    private array $aTags = [];

    /** @var Resource\ReleaseNotes[] */
    private array $aNewTags = [];

    // --------------------------------------------------------------------------

    /**
     * Configures the command
     */
    protected function configure(): void
    {
        $this
            ->setName('releasenotes:fetch')
            ->setDescription('Fetches new tags from GitHub');
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the command
     *
     * @param InputInterface  $oInput
     * @param OutputInterface $oOutput
     *
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput): int
    {
        parent::execute($oInput, $oOutput);

        try {

            $this->banner('Fetch tags from GitHub');

            [$sRepo, $sUser, $sToken] = $this->getSettings();

            $this
                ->validateSettings($sRepo, $sUser, $sToken)
                ->fetchTags($sRepo, $sUser, $sToken)
                ->syncTags($sUser, $sToken)
                ->sendNotifications();

            return static::EXIT_CODE_SUCCESS;

        } catch (\Throwable $e) {
            $this->error([$e->getMessage()]);
            return static::EXIT_CODE_FAILURE;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the settings
     *
     * @return string[]
     * @throws FactoryException
     */
    private function getSettings(): array
    {
        return [
            appSetting(Settings\ReleaseNotes::KEY_GH_REPO, Constants::MODULE_SLUG),
            appSetting(Settings\ReleaseNotes::KEY_GH_USER, Constants::MODULE_SLUG),
            appSetting(Settings\ReleaseNotes::KEY_GH_TOKEN, Constants::MODULE_SLUG),
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Validates settings
     *
     * @param string|null $sRepo
     * @param string|null $sUser
     * @param string|null $sToken
     *
     * @return $this
     * @throws ValidationException
     */
    private function validateSettings(?string $sRepo, ?string $sUser, ?string $sToken): self
    {
        if (empty($sRepo)) {
            throw new ValidationException(sprintf(
                'Setting `%s` is required',
                Settings\ReleaseNotes::KEY_GH_REPO
            ));

        } elseif (!empty($sUser) && empty($sToken)) {
            throw new ValidationException(sprintf(
                'Setting `%s` is required when `%s` is not empty',
                Settings\ReleaseNotes::KEY_GH_TOKEN,
                Settings\ReleaseNotes::KEY_GH_USER
            ));
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches all the tags for the configured repo
     *
     * @param string|null $sRepo
     * @param string|null $sUser
     * @param string|null $sToken
     *
     * @return $this
     * @throws ConsoleException
     * @throws FactoryException
     */
    private function fetchTags(?string $sRepo, ?string $sUser, ?string $sToken): self
    {
        $this->oOutput->write('Fetching tags... ');
        $oResponse = $this->callGitHubApi(
            sprintf(
                'repos/%s/git/refs/tags',
                $sRepo
            ),
            $sUser,
            $sToken
        );

        $this->aTags = (array) $oResponse->getBody();

        $this->oOutput->writeln(sprintf(
            '<info>done</info>, found <info>%s</info> tags.',
            count($this->aTags)
        ));

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Processes each tag, importing new ones as they're found
     *
     * @param string|null $sUser
     * @param string|null $sToken
     *
     * @return $this
     * @throws FactoryException
     * @throws ModelException
     */
    private function syncTags(?string $sUser, ?string $sToken): self
    {
        /** @var \Nails\ReleaseNotes\Model\ReleaseNotes $oReleaseNotesModel */
        $oReleaseNotesModel = Factory::model('ReleaseNotes', Constants::MODULE_SLUG);

        $aExisting = $oReleaseNotesModel->getAllFlat();

        $this->oOutput->writeln('Syncing tags...');
        foreach ($this->aTags as $oTag) {

            try {

                $sTag = $this->parseTagRef($oTag->ref);

                $this->oOutput->write(sprintf(
                    ' - Processing tag <info>%s</info>... ',
                    $sTag
                ));

                if (in_array($sTag, $aExisting)) {
                    throw new ConsoleException('already imported');
                }

                $oResponse = $this->callGitHubApi(
                    $oTag->object->url,
                    $sUser,
                    $sToken
                );

                /** @var \stdClass $oTag */
                $oTag = $oResponse->getBody();

                // If the tag is a commit reference, the format is slightly different
                $sDate = $oTag->tagger->date ?? $oTag->committer->date ?? $oTag->author->date ?? null;

                /** @var DateTime|null $oDate */
                $oDate = $sDate
                    ? Factory::resource('DateTime', null, ['raw' => $sDate])
                    : null;

                $oNewTag = $oReleaseNotesModel->create([
                    'tag'     => $sTag,
                    'sha'     => $oTag->sha,
                    'message' => $oTag->message,
                    'date'    => $oDate ? $oDate->format('Y-m-d H:i:s') : null,
                ], true);

                if (empty($oNewTag)) {
                    throw new ConsoleException('Failed to write tag. ' . $oReleaseNotesModel->lastError());
                }

                $this->aNewTags[] = $oNewTag;

                $this->oOutput->writeln('<info>done</info>');

            } catch (\Throwable $e) {
                $this->oOutput->writeln(sprintf(
                    '<error>%s</error>',
                    $e->getMessage()
                ));
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Sends notifications if required
     *
     * @return $this
     * @throws FactoryException
     * @throws \Nails\Common\Exception\NailsException
     */
    protected function sendNotifications(): self
    {
        if (empty($this->aNewTags)) {
            return $this;
        }

        //  Reverse the array so the most recent release is first
        $this->aNewTags = array_reverse($this->aNewTags);

        $aEmails = [];

        foreach (Components::available() as $oComponent) {

            $aClasses = $oComponent
                ->findClasses('ReleaseNotes\\Notification')
                ->whichImplement(Interfaces\Notification::class)
                ->whichCanBeInstantiated();

            foreach ($aClasses as $sClass) {

                /** @var Interfaces\Notification $oNotification */
                $oNotification = new $sClass();
                $aEmails       = array_merge($aEmails, $oNotification->getEmails());
            }
        }

        $aEmails = array_map('trim', $aEmails);
        $aEmails = array_unique($aEmails);
        $aEmails = array_filter($aEmails);
        $aEmails = array_values($aEmails);

        if (empty($aEmails)) {
            return $this;
        }

        $this->oOutput->writeln('Sending notifications... ');

        /** @var \Nails\ReleaseNotes\Factory\Email\Notification $oEmail */
        $oEmail = Factory::factory('EmailNotification', Constants::MODULE_SLUG);
        $oEmail->data([
            'tags' => array_map(function (Resource\ReleaseNotes $oTag) {
                return [
                    'tag'     => $oTag->tag,
                    'date'    => $oTag->date->formatted,
                    'message' => [
                        'html' => $oTag->renderHtml(),
                        'text' => $oTag->renderText(),
                    ],
                ];
            }, $this->aNewTags),
        ]);

        foreach ($aEmails as $sEmail) {
            $this->oOutput->write(sprintf(
                ' — <info>%s</info>... ',
                $sEmail
            ));

            try {

                $oEmail
                    ->to($sEmail)
                    ->send();

                $this->oOutput->writeln('<info>done</info>');

            } catch (\Throwable $e) {
                $this->oOutput->writeln('<error>' . $e->getMessage() . '</error>');
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Call the GitHub API
     *
     * @param string      $sPath
     * @param string|null $sUser
     * @param string|null $sToken
     *
     * @return HttpResponse
     * @throws ConsoleException
     * @throws FactoryException
     */
    private function callGitHubApi(string $sPath, ?string $sUser, ?string $sToken): HttpResponse
    {
        /** @var Get $oHttpGet */
        $oHttpGet = Factory::factory('HttpRequestGet');
        $oHttpGet
            ->baseUri('https://api.github.com')
            ->path($sPath);

        if (!empty($sUser) || !empty($sToken)) {
            $oHttpGet->auth((string) $sUser, (string) $sToken);
        }

        $oResponse = $oHttpGet->execute();

        if ($oResponse->getStatusCode() !== HttpCodes::STATUS_OK) {
            /** @var \stdClass $oError */
            $oError = $oResponse->getBody();
            throw new ConsoleException(
                sprintf(
                    'HTTP request returned a non-200 status code. %s',
                    $oError->message ?? ''
                ),
                $oResponse->getStatusCode()
            );
        }

        return $oResponse;
    }

    // --------------------------------------------------------------------------

    /**
     * Parse the tag out of the ref
     *
     * @param string $sRef
     *
     * @return string
     */
    private function parseTagRef(string $sRef): string
    {
        return (string) preg_replace('/^refs\/tags\//', '', $sRef);
    }
}
