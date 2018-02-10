<?php
return [
    "bundles" => [
            "Database" => [
                "host" => "admin.local",
                "port" => "5432"
            ],
            "Debug" => [
                "show_toolbar" => true,
                "bla bla" => 2
            ],
            "View" => [
                "templateDir" => ROOT_DIR . "templates/",
                "compileDir" => APP_DIR . "cache/templates/"
            ],

    ]
];