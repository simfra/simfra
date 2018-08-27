<?php
/* Smarty version 3.1.31, created on 2018-08-26 22:21:55
  from "/home/polo/Projekty/simfra/web/lib/App/Form/Templates/FormXml.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b8319f3d87742_02884654',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c6008aa93c34cc37168c07a8b88db038aa112520' => 
    array (
      0 => '/home/polo/Projekty/simfra/web/lib/App/Form/Templates/FormXml.tpl',
      1 => 1535318514,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b8319f3d87742_02884654 (Smarty_Internal_Template $_smarty_tpl) {
echo '<?xml ';?>version="1.0" encoding="UTF-8"<?php echo '?>';?>
<form>
    <name><?php echo $_smarty_tpl->tpl_vars['form']->value->getName();?>
</name>
    <id><?php echo $_smarty_tpl->tpl_vars['form']->value->getId();?>
</id>
    <class><?php echo $_smarty_tpl->tpl_vars['form']->value->getClass();?>
</class>
    <method><?php echo $_smarty_tpl->tpl_vars['form']->value->getMethod();?>
</method>
    <action><?php echo $_smarty_tpl->tpl_vars['form']->value->getAction();?>
</action>
    <submit><?php echo $_smarty_tpl->tpl_vars['form']->value->getSubmit();?>
</submit>
    <fields>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['form']->value->fields, 'field');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['field']->value) {
?>
        <field>
            <name><?php echo $_smarty_tpl->tpl_vars['field']->value->getName();?>
</name>
            <type><?php echo $_smarty_tpl->tpl_vars['field']->value->getType();?>
</type>
            <options>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['field']->value->getOptions(), 'option', false, 'key');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['option']->value) {
?>
                <<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
><?php echo $_smarty_tpl->tpl_vars['option']->value;?>
</<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

            </options>
            <placeholder></placeholder>
            <label></label>
            <required></required>
            <rule></rule>
        </field>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    </fields>
</form>
<?php }
}
