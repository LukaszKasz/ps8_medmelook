<?php
function upgrade_module_3_10_0($module)
{

	$module->registerHook('displayProductAdditionalInfo');

	return true;
}