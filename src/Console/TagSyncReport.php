<?php

namespace Nails\ReleaseNotes\Console;

class TagSyncReport
{
    private int $iChecked = 0;

    private int $iImported = 0;

    /**
     * @param string[] $aExisting
     */
    public function consider(string $sTag, array $aExisting): bool
    {
        $this->iChecked++;

        return !in_array($sTag, $aExisting, true);
    }

    public function imported(string $sTag): string
    {
        $this->iImported++;

        return 'Imported tag ' . $sTag;
    }

    public function summary(): string
    {
        return sprintf('checked %d tags, imported %d', $this->iChecked, $this->iImported);
    }

    /**
     * @param string[] $aTags
     * @param string[] $aExisting
     *
     * @return string[]
     */
    public function lines(array $aTags, array $aExisting): array
    {
        $aLines = [];

        foreach ($aTags as $sTag) {
            if (!$this->consider($sTag, $aExisting)) {
                continue;
            }

            $aLines[] = $this->imported($sTag);
        }

        $aLines[] = $this->summary();

        return $aLines;
    }
}
