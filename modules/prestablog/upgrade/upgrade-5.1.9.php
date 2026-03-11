<?php
/**
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_1_9()
{
    Tools::clearCache();
    // Retrieve the blog URL from the configuration
    $urlBlog = Configuration::get('prestablog_urlblog');

    // If the URL is not 'blog', proceed with the update
    if ($urlBlog !== 'blog') {
        // Path to the updated source file (blog.php)
        $sourceFilePath = _PS_MODULE_DIR_ . 'prestablog/controllers/front/blog.php';

        // Path for the custom controller file based on the configured URL
        $customControllerPath = _PS_MODULE_DIR_ . 'prestablog/controllers/front/' . $urlBlog . '.php';

        // Read the content of the updated source file
        $updatedContent = Tools::file_get_contents($sourceFilePath);

        // Modify the class name in the content
        $newClassName = 'PrestaBlog' . Tools::ucfirst(str_replace(['-', '_'], '', $urlBlog)) . 'ModuleFrontController';
        $updatedContent = str_replace('PrestaBlogBlogModuleFrontController', $newClassName, $updatedContent);

        // Write the updated content into the custom controller file
        file_put_contents($customControllerPath, $updatedContent);
    }

    return true;
}
