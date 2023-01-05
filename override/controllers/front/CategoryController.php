<?php

class CategoryController extends CategoryControllerCore
{
    public function initContent()
    {
        /* loading the default code */
        parent::initContent();

        /* please add all categories ID for which you want to use this custom template */
        $custom_categories = array(3, 30, 301);

        if (isset($this->category) && in_array($this->category->id, $custom_categories))
        {
            /* please change the file name 'category-1.tpl'
            to any other file name you want to use in your theme directory,
            i. e. themes/[yourtheme]/category-1.tpl */
            $this->setTemplate(_PS_THEME_DIR_.'category-301.tpl');
        }
    }
}

?>