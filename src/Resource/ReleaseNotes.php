<?php

namespace Nails\ReleaseNotes\Resource;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
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

    /**
     * Renders the message as HTML, parsing signatures and markdown
     *
     * @return string
     * @throws CommonMarkException
     */
    public function renderHtml(): string
    {
        $sOut = $this->filterSignatures($this->message ?? '');
        return $this->parseMarkdown($sOut);
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the message as plain text, parsing signatures
     *
     * @return string
     */
    public function renderText(): string
    {
        return $this->filterSignatures($this->message ?? '');
    }

    // --------------------------------------------------------------------------

    /**
     * Filters GPG signatures from the input
     *
     * @param string $sMessage The input
     *
     * @return string
     */
    private function filterSignatures(string $sMessage): string
    {
        return (string) preg_replace(
            array_map(
                function ($sType) {
                    return '/-----BEGIN ' . $sType . ' SIGNATURE-----.+-----END ' . $sType . ' SIGNATURE-----/s';
                },
                [
                    'PGP',
                ]
            ),
            '',
            $sMessage
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Parses the input as markdown
     *
     * @param string $sMessage The input
     *
     * @return string
     * @throws CommonMarkException
     */
    protected function parseMarkdown(string $sMessage): string
    {
        /**
         * (Pablo 2021-08-12) - Replace n and em dashes at the beginning of lines
         * with a simple dash so they render as lists. This is more to address my
         * [bad?] habit of using emdashes in release notes.
         */
        $sMessage = preg_replace('/^(–|—) /m', '- ', $sMessage);
        $sMessage = htmlspecialchars((string) $sMessage, ENT_QUOTES);

        $oCommonMark = new CommonMarkConverter();
        return $oCommonMark->convert($sMessage);
    }
}
