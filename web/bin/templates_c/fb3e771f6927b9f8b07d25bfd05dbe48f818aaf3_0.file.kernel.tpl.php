<?php
/* Smarty version 3.1.31, created on 2018-08-21 19:43:21
  from "/home/polo/Projekty/simfra/web/bin/templates/kernel.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b7c5d49389237_30777093',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fb3e771f6927b9f8b07d25bfd05dbe48f818aaf3' => 
    array (
      0 => '/home/polo/Projekty/simfra/web/bin/templates/kernel.tpl',
      1 => 1534876999,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b7c5d49389237_30777093 (Smarty_Internal_Template $_smarty_tpl) {
echo '<?php
';?>namespace <?php echo $_smarty_tpl->tpl_vars['app_name']->value;?>
;

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
    $this->addBundle(new Database, "Baza2");
    }

    public function getRootDir()
    {
        return basename(__DIR__);
    }
}
<?php }
}
