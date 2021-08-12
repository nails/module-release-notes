<?php

use Nails\ReleaseNotes\Model;
use Nails\ReleaseNotes\Resource;
use Nails\ReleaseNotes\Factory;

return [
    'models' => [
        'ReleaseNotes' => function (): Model\ReleaseNotes {
            if (class_exists('\App\ReleaseNotes\Model\ReleaseNotes')) {
                return new \App\ReleaseNotes\Model\ReleaseNotes();
            } else {
                return new Model\ReleaseNotes();
            }
        },
    ],

    'resources' => [
        'ReleaseNotes' => function ($oObj): Resource\ReleaseNotes {
            if (class_exists('\App\ReleaseNotes\Resource\ReleaseNotes')) {
                return new \App\ReleaseNotes\Resource\ReleaseNotes($oObj);
            } else {
                return new Resource\ReleaseNotes($oObj);
            }
        },
    ],

    'factories' => [
        'EmailNotification' => function (): Factory\Email\Notification {
            if (class_exists('\App\ReleaseNotes\Factory\ReleaseNotes')) {
                return new \App\ReleaseNotes\Factory\Email\Notification();
            } else {
                return new Factory\Email\Notification();
            }
        },
    ],
];
