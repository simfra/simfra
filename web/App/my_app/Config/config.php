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
            ],
            "View" => [
                "templateDir" => ROOT_DIR . "templates/",
                "compileDir" =>  APP_DIR . "cache/templates/",
                "polo" => APP_DIR
            ],
            "Baza2" => [
                "host" => "localhost2",
                "port" => "5432a"
            ],
    ]
];