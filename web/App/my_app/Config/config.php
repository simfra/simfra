<?php
return [
    "bundles" => [
            "Database" => [
                "host" => "localhost",
                "port" => "5432"
            ],
            "Debug" => [
                "show_toolbar" => true,
                "bla" => 2,
                "minimalized_toolbar" => false
//                "theme" => "default"

            ],
            "View" => [
                "templateDir" => APP_DIR . "templates/",
                "compileDir" =>  APP_DIR . "cache/templates/",
                "polo" => APP_DIR,
            ],
            "Baza2" => [
                "host" => "localhost2",
                "port" => "5432a"
            ],
    ],
    "app" => [
        "languages" => ["en","pl"]
    ]
];