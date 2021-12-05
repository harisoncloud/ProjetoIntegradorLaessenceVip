#######################################################

 Magic 360™
 OpenCart module version v4.5.32 [v1.6.91:v4.6.12]

 www.magictoolbox.com
 support@magictoolbox.com

 Copyright 2021 Magic Toolbox

#######################################################

INSTALLATION:

Before you start, we recommend you open readme.txt and follow those instructions. It is faster and easier than these readme_manual.txt instructions. If you use AyelShop, AceShop and MijoShop or if installation failed using the readme.txt procedure, please continue with these instructions below:

1. Copy the 'admin' and 'catalog' folders to your OpenCart directory, keeping the file structure.

2. Backup your /catalog/controller/product/product.php file and open it in a text editor (e.g. Notepad).

3. Find the line that looks like '<?php' and insert after it:

    global $aFolder;
    if (!defined('HTTP_ADMIN')) define('HTTP_ADMIN','admin');
    $aFolder = preg_replace('/.*\/([^\/].*)\//is','$1',HTTP_ADMIN);
    if (!isset($GLOBALS['magictoolbox']['magic360']) && !isset($GLOBALS['magic360_module_loaded'])) {
	include (preg_match("/components\/com_(ayelshop|aceshop|mijoshop)\/opencart\//ims",__FILE__,$matches)?'components/com_'.$matches[1].'/opencart/':'').$aFolder.'/controller/module/magic360-opencart-module/module.php';
    };

4. If your version of OpenCart is lower than '1.5.0', find the first line looking like '$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));'.

5. Replace that code with the following:

    $this->response->setOutput(magic360($this->render(TRUE),$this,'product',$product_info), $this->config->get('config_compression'));

6. If your version of OpenCart is greater than '1.5.0', find the first line looking like '$this->response->setOutput($this->render());'.

7. Replace that code with the following:

    $this->response->setOutput(magic360($this->render(TRUE),$this,'product',$product_info), $this->config->get('config_compression'));

8. Find the line that looks like '$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);'.

9. Insert the following line after it:

    $product_info['images'] = $results;

10. You are done! Now you can open the 'Extensions' page in your OpenCart admin panel to activate and customize the module.

11. Magic 360 demo version is ready to use!

12. To upgrade your version of Magic 360 (which removes the "Please upgrade" text), buy Magic 360 and overwrite the catalog/view/javascript/magic360.js file file with the new one in your licensed version.

Buy a single license here:

http://www.magictoolbox.com/buy/magic360/

