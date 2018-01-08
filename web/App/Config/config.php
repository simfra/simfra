
<?php
define("PATH", realpath($_SERVER['DOCUMENT_ROOT'] . "/../") . "/");
define("PATH_USER_CONTROLLER", "\App\Controller\\");
return [
    "bundles" => [
            "Database" => [
                "host" => "localhost",
                "port" => "5432"
            ],
            "Debug" => [
                "show_toolbar" => true,
                "bla bla" => 2
            ],
            "View" => [
                "templateDir" => "asdas"
            ],
            "Baza2" => [
                "host" => "localhost2",
                "port" => "5432a"
            ],
    ]
];