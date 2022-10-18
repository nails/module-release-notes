<?php

namespace Nails\ReleaseNotes\Factory\Email;

use Nails\Email\Factory\Email;

class Notification extends Email
{
    /**
     * The email's type
     *
     * @var string
     */
    protected $sType = 'release_note_notification';

    // --------------------------------------------------------------------------

    /**
     * Returns test data to use when sending test emails
     *
     * @return mixed[]
     */
    public function getTestData(): array
    {
        return [
            'tags' => [
                [
                    'tag'     => '1.2.3',
                    'date'    => toUserDatetime(time()),
                    'message' => [
                        'html' => implode(PHP_EOL, [
                            '<p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>',
                            '<ul>',
                            '<li>Nullam quis risus eget urna mollis ornare vel eu leo.</li>',
                            '<li>Nulla vitae elit libero, a pharetra augue.</li>',
                            '<li>Praesent commodo cursus magna, vel scelerisque nisl consectetur et.</li>',
                            '</ul>',
                        ]),
                        'text' => implode(PHP_EOL, [
                            'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
                            '',
                            '- Nullam quis risus eget urna mollis ornare vel eu leo.',
                            '- Nulla vitae elit libero, a pharetra augue.',
                            '- Praesent commodo cursus magna, vel scelerisque nisl consectetur et.',
                        ]),
                    ],
                ],
                [
                    'tag'     => '1.2.2',
                    'date'    => toUserDatetime(time() - 86000),
                    'message' => [
                        'html' => implode(PHP_EOL, [
                            '<p>Donec sed odio dui. Maecenas faucibus mollis interdum. Nullam quis risus eget urna mollis ornare vel eu leo.</p>',
                            '<ul>',
                            '<li>Vestibulum id ligula porta felis euismod semper.</li>',
                            '<li>Aenean lacinia bibendum nulla sed consectetur.</li>',
                            '</ul>',
                        ]),
                        'text' => implode(PHP_EOL, [
                            'Donec sed odio dui. Maecenas faucibus mollis interdum. Nullam quis risus eget urna mollis ornare vel eu leo.',
                            '',
                            '- Vestibulum id ligula porta felis euismod semper.',
                            '- Aenean lacinia bibendum nulla sed consectetur.',
                        ]),
                    ],
                ],
            ],
        ];
    }
}
