<?php
return [
    'certificates' => [
        'ca' => ['daysvalid' => 7000, 'x509_extensions' => 'v3_ca'],
        'intermediate' => ['daysvalid' => 7000, 'x509_extensions' => 'v3_intermediate_ca'],
        'user' => ['daysvalid' => 7000, 'x509_extensions' => 'usr_cert'],
        'server' => ['daysvalid' => 7000, 'x509_extensions' => 'server_cert'],
    ],

    'default' => [
        'private_key_bits' => 4096,
        'ca_key_cipher' => 10321033,
        'ica_key_cipher' => 10321033
    ],

    'default_ca' => [
        'name' => 'Hogwarts',
        'country_name' => 'CN',
        'organization_name' => 'Hogwarts School of Witchcraft and Wizardry',
        'common_name' => 'Hogwarts School of Witchcraft and Wizardry Root CA',
    ]
];
