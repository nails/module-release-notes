<?php

namespace Test\ReleaseNotes\Console;

use Nails\ReleaseNotes\Console\TagSyncReport;
use PHPUnit\Framework\TestCase;

class TagSyncReportTest extends TestCase
{
    public function test_existing_tags_collapse_to_one_summary(): void
    {
        $aLines = (new TagSyncReport())->lines(
            ['0.9.0', '0.9.1', '1.0.0'],
            ['0.9.0', '0.9.1', '1.0.0']
        );

        self::assertSame(['checked 3 tags, imported 0'], $aLines);
        self::assertStringNotContainsString('already imported', implode("\n", $aLines));
    }

    public function test_only_imported_tags_get_a_line(): void
    {
        $aLines = (new TagSyncReport())->lines(
            ['0.9.0', '1.0.0', '1.1.0'],
            ['0.9.0']
        );

        self::assertSame(
            [
                'Imported tag 1.0.0',
                'Imported tag 1.1.0',
                'checked 3 tags, imported 2',
            ],
            $aLines
        );
    }
}
