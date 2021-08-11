<?php

use Nails\ReleaseNotes\Model;
use Nails\ReleaseNotes\Resource;

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
];
