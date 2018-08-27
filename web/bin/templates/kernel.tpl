<?php
namespace {$app_name};

use App\Database\Database;
use Core\View;
use Core\Debug\Debug;

class AppKernel extends \Core\Kernel
{

    public function registerBundles()
    {
        if (false === $this->isProd) {
            $this->addBundle(new Debug);
        }
    $this->addBundle(new View);
    $this->addBundle(new Database);
    }

    public function getRootDir()
    {
        return basename(__DIR__);
    }
}
