<?php

declare(strict_types=1);

return [
    'baseUrl' => 'https://musicbrainz.org/ws/2/',
    'client' => [
        'options' => [
            'headers' => [
                'User-Agent' => 'CalliostroMusicBrainzClient/1.0.0 (https://github.com/calliostro/musicbrainz-client)',
            ],
        ],
    ],
    'operations' => [
        // ===========================
        // ARTIST METHODS
        // ===========================
        'lookupArtist' => [
            'httpMethod' => 'GET',
            'path' => 'artist/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'browseArtists' => [
            'httpMethod' => 'GET',
            'path' => 'artist',
            'parameters' => [
                'area' => ['required' => false, 'location' => 'query'],
                'collection' => ['required' => false, 'location' => 'query'],
                'recording' => ['required' => false, 'location' => 'query'],
                'release' => ['required' => false, 'location' => 'query'],
                'release_group' => ['required' => false, 'location' => 'query'],
                'work' => ['required' => false, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchArtists' => [
            'httpMethod' => 'GET',
            'path' => 'artist',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // RELEASE METHODS
        // ===========================
        'lookupRelease' => [
            'httpMethod' => 'GET',
            'path' => 'release/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'browseReleases' => [
            'httpMethod' => 'GET',
            'path' => 'release',
            'parameters' => [
                'artist' => ['required' => false, 'location' => 'query'],
                'label' => ['required' => false, 'location' => 'query'],
                'recording' => ['required' => false, 'location' => 'query'],
                'release_group' => ['required' => false, 'location' => 'query'],
                'track' => ['required' => false, 'location' => 'query'],
                'track_artist' => ['required' => false, 'location' => 'query'],
                'collection' => ['required' => false, 'location' => 'query'],
                'area' => ['required' => false, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'inc' => ['required' => false, 'location' => 'query'],
                'type' => ['required' => false, 'location' => 'query'],
                'status' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchReleases' => [
            'httpMethod' => 'GET',
            'path' => 'release',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // RELEASE GROUP METHODS
        // ===========================
        'lookupReleaseGroup' => [
            'httpMethod' => 'GET',
            'path' => 'release-group/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'browseReleaseGroups' => [
            'httpMethod' => 'GET',
            'path' => 'release-group',
            'parameters' => [
                'artist' => ['required' => false, 'location' => 'query'],
                'release' => ['required' => false, 'location' => 'query'],
                'collection' => ['required' => false, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'inc' => ['required' => false, 'location' => 'query'],
                'type' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchReleaseGroups' => [
            'httpMethod' => 'GET',
            'path' => 'release-group',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // RECORDING METHODS
        // ===========================
        'lookupRecording' => [
            'httpMethod' => 'GET',
            'path' => 'recording/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'browseRecordings' => [
            'httpMethod' => 'GET',
            'path' => 'recording',
            'parameters' => [
                'artist' => ['required' => false, 'location' => 'query'],
                'release' => ['required' => false, 'location' => 'query'],
                'collection' => ['required' => false, 'location' => 'query'],
                'work' => ['required' => false, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchRecordings' => [
            'httpMethod' => 'GET',
            'path' => 'recording',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // LABEL METHODS
        // ===========================
        'lookupLabel' => [
            'httpMethod' => 'GET',
            'path' => 'label/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'browseLabels' => [
            'httpMethod' => 'GET',
            'path' => 'label',
            'parameters' => [
                'area' => ['required' => false, 'location' => 'query'],
                'collection' => ['required' => false, 'location' => 'query'],
                'release' => ['required' => false, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchLabels' => [
            'httpMethod' => 'GET',
            'path' => 'label',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // WORK METHODS
        // ===========================
        'lookupWork' => [
            'httpMethod' => 'GET',
            'path' => 'work/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'browseWorks' => [
            'httpMethod' => 'GET',
            'path' => 'work',
            'parameters' => [
                'artist' => ['required' => false, 'location' => 'query'],
                'collection' => ['required' => false, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchWorks' => [
            'httpMethod' => 'GET',
            'path' => 'work',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // AREA METHODS
        // ===========================
        'lookupArea' => [
            'httpMethod' => 'GET',
            'path' => 'area/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchAreas' => [
            'httpMethod' => 'GET',
            'path' => 'area',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // ISRC METHODS (International Standard Recording Code)
        // ===========================
        'lookupIsrc' => [
            'httpMethod' => 'GET',
            'path' => 'isrc/{isrc}',
            'parameters' => [
                'isrc' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // URL METHODS
        // ===========================
        'lookupUrl' => [
            'httpMethod' => 'GET',
            'path' => 'url/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchUrls' => [
            'httpMethod' => 'GET',
            'path' => 'url',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // GENRE METHODS
        // ===========================
        'lookupGenre' => [
            'httpMethod' => 'GET',
            'path' => 'genre/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchGenres' => [
            'httpMethod' => 'GET',
            'path' => 'genre',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // INSTRUMENT METHODS
        // ===========================
        'lookupInstrument' => [
            'httpMethod' => 'GET',
            'path' => 'instrument/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchInstruments' => [
            'httpMethod' => 'GET',
            'path' => 'instrument',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // SERIES METHODS
        // ===========================
        'lookupSeries' => [
            'httpMethod' => 'GET',
            'path' => 'series/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchSeries' => [
            'httpMethod' => 'GET',
            'path' => 'series',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // EVENT METHODS
        // ===========================
        'lookupEvent' => [
            'httpMethod' => 'GET',
            'path' => 'event/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'browseEvents' => [
            'httpMethod' => 'GET',
            'path' => 'event',
            'parameters' => [
                'area' => ['required' => false, 'location' => 'query'],
                'artist' => ['required' => false, 'location' => 'query'],
                'collection' => ['required' => false, 'location' => 'query'],
                'place' => ['required' => false, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchEvents' => [
            'httpMethod' => 'GET',
            'path' => 'event',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // PLACE METHODS
        // ===========================
        'lookupPlace' => [
            'httpMethod' => 'GET',
            'path' => 'place/{mbid}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'browsePlaces' => [
            'httpMethod' => 'GET',
            'path' => 'place',
            'parameters' => [
                'area' => ['required' => false, 'location' => 'query'],
                'collection' => ['required' => false, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'inc' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'searchPlaces' => [
            'httpMethod' => 'GET',
            'path' => 'place',
            'parameters' => [
                'query' => ['required' => true, 'location' => 'query'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],

        // ===========================
        // RATING METHODS (Authenticated)
        // ===========================
        'submitRating' => [
            'httpMethod' => 'POST',
            'path' => 'rating',
            'parameters' => [
                'client' => ['required' => true, 'location' => 'query'],
                'entity_type' => ['required' => true, 'location' => 'query'], // artist, release, recording, release-group, work, label, event, place, series, instrument
                'entity_id' => ['required' => true, 'location' => 'query'], // MBID
                'rating' => ['required' => true, 'location' => 'query'], // 0-100 (0 = remove rating)
            ],
        ],

        // ===========================
        // TAG METHODS (Authenticated)
        // ===========================
        'submitTags' => [
            'httpMethod' => 'POST',
            'path' => 'tag',
            'parameters' => [
                'client' => ['required' => true, 'location' => 'query'],
                'entity_type' => ['required' => true, 'location' => 'query'], // artist, release, recording, release-group, work, label, area, event, place, series, instrument
                'entity_id' => ['required' => true, 'location' => 'query'], // MBID
                'tags' => ['required' => true, 'location' => 'query'], // Comma-separated list of tags
            ],
        ],

        // ===========================
        // COLLECTION METHODS (Authenticated)
        // ===========================
        'getUserCollections' => [
            'httpMethod' => 'GET',
            'path' => 'collection',
            'parameters' => [
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'getCollectionReleases' => [
            'httpMethod' => 'GET',
            'path' => 'collection/{mbid}/releases',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'],
                'limit' => ['required' => false, 'location' => 'query'],
                'offset' => ['required' => false, 'location' => 'query'],
                'fmt' => ['required' => false, 'location' => 'query'],
            ],
        ],
        'addReleasesToCollection' => [
            'httpMethod' => 'PUT',
            'path' => 'collection/{mbid}/releases/{release_list}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'], // Collection MBID
                'release_list' => ['required' => true, 'location' => 'path'], // Semicolon-separated release MBIDs
                'client' => ['required' => true, 'location' => 'query'],
            ],
        ],
        'removeReleasesFromCollection' => [
            'httpMethod' => 'DELETE',
            'path' => 'collection/{mbid}/releases/{release_list}',
            'parameters' => [
                'mbid' => ['required' => true, 'location' => 'path'], // Collection MBID
                'release_list' => ['required' => true, 'location' => 'path'], // Semicolon-separated release MBIDs
                'client' => ['required' => true, 'location' => 'query'],
            ],
        ],
    ],
];
