<?php
/**
* @author      SSS
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class SssDiscount extends Module{
    protected $_html = '';
    protected $templateFile;
    const TOKEN = 'sssdiscount';

    public function __construct()
    {
        
        $this->name          = 'sssdiscount';
        $this->tab           = 'shipping_logistics';
        $this->version       = '1.0.0';
        $this->author        = 'PrestaShop';
        $this->need_instance = 0;
        $this->secure_key    = Tools::encrypt($this->name);
        $this->bootstrap     = true;
        $this->_path         = __FILE__;
        


        parent::__construct();

        $this->displayName = $this->l('SSS Discount');
        $this->description = $this->l('SSS Discount');
        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);

        $this->ajax_token = $this->getToken();

        $this->mediasave = $this->context->link->getMediaLink(_MODULE_DIR_.'sssdiscount/views/files/');


    }

    /**
     * @see Module::install()
     */
    public function install()
    {
        parent::install();

        return true;
       
    }




    /**
     * @see Module::uninstall()
     */
    public function uninstall()
    {
       
       parent::uninstall();
        return true;
    }


    /**
     * getToken()
    */
    public static function getToken()
    {
        return Tools::encrypt(self::TOKEN);
    }


    //Module page start from here.
    public function getContent()
    {




         if (Tools::isSubmit('submitProductupdateequi') 
        ) {
            
            if ($this->_postValidation()) {
                
                $this->_postProcess();

                 $this->_html .= $this->renderAddForm();
             
            }else{

                 $this->_html .= $this->renderAddForm();
            }

           

        }else{



            if(isset($_POST['discount'])){

                //save data

                $sqlsup = 'DELETE FROM '._DB_PREFIX_.'group_reduction  WHERE id_group = "3"  ';
                $prsupplier = Db::getInstance()->executeS($sqlsup);

                foreach($_POST['discount'] as $key =>$value){

                    if($value != ''){

                        //group reduction

                        $getCat = $this->getSubCategoriesall($key);

                        if(count( $getCat) > 0){

                            

                            foreach( $getCat as $catinfo){

                                $sql='INSERT INTO `'._DB_PREFIX_.'group_reduction` (`id_group`, `id_category`, `reduction`)
                            VALUES( "3", "'.$catinfo['id_category'].'","'.($value/100).'")';
                            $res &= Db::getInstance()->execute($sql);
                                

                                  
                            }


                        }




                         $sql = 'SELECT * FROM `'._DB_PREFIX_.'sssdiscount` as p where categoryid = "'.$key.'"';
                         $categorydiscounts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);


                         if(count($categorydiscounts) > 0){

                            $id_discount = $categorydiscounts[0]['id_discount'];
                            
                              $sql = 'UPDATE `'._DB_PREFIX_.'sssdiscount` SET `discount` = '.$value.' WHERE id_discount = '. $id_discount.' ';

                                $res &= Db::getInstance()->execute($sql);



                         }else{


                            $sql='INSERT INTO `'._DB_PREFIX_.'sssdiscount` (`categoryid`, `discount`)
                            VALUES( "'.$key.'", "'.$value.'")';
                            $res &= Db::getInstance()->execute($sql);

                         }

                    }

                }


                 $this->_html .= $this->displayConfirmation("Successfully updated");

            }


             $this->_html .= $this->renderAddForm();
        } 


        return $this->_html;

    }



    //validation form
    protected function _postValidation()
    {


         return true;
    }


    //save data
    protected function _postProcess(){


        


    }

    public function getSubCategoriesall($catid){


        $subcategories= array();

        $categoryObj2 = new Category($catid);
        $categoryList2 = $categoryObj2->getSubCategories($this->context->language->id);
         if ($categoryList2 && count($categoryList2) > 0 ) {
                foreach ($categoryList2 AS $key => $val) {


                    $subcategories[] = array('id_category'   =>  $val['id_category']);


                    $categoryObj5 = new Category($val['id_category']);
                    $categoryObj5 = $categoryObj5->getSubCategories($this->context->language->id);

                     if ($categoryObj5 && count($categoryObj5) > 0 ) {
                     foreach ($categoryObj5 AS $key2 => $val2) {

                         $subcategories[] = array('id_category'   =>  $val2['id_category']);

                         $categoryObj6 = new Category($val2['id_category']);
                        $categoryObj6 = $categoryObj6->getSubCategories($this->context->language->id);

                         if ($categoryObj6 && count($categoryObj6) > 0 ) {
                         foreach ($categoryObj6 AS $key3 => $val3) {

                             $subcategories[] = array('id_category'   =>  $val3['id_category']);


                        }
                        }


                    }
                    }
                       
                }
            }

            return $subcategories;


    }


    public function getSubCategories($catid){

        $subcategories= array();

        $categoryObj2 = new Category($catid);
        $categoryList2 = $categoryObj2->getSubCategories($this->context->language->id);
         if ($categoryList2 && count($categoryList2) > 0 ) {
                foreach ($categoryList2 AS $key => $val) {

                    $value = '';
                    $sql = 'SELECT * FROM `'._DB_PREFIX_.'sssdiscount` as p where categoryid = "'.$val['id_category'].'"';

                   
                     $categorydiscounts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);


                     if(count($categorydiscounts) > 0){
                        
                        $value = $categorydiscounts[0]['discount'];
                     }


                    $subcategories[] = array(
                                                        'id_category'   =>  $val['id_category'],
                                                        'name'          =>  $val['name'],
                                                        'description'   =>  $val['description'],
                                                        'value'         =>  $value 
                                                 );
                    
                }
            }

            return $subcategories;
                    

    }

    
    //Form Add
    public function renderAddForm()
    {



        $id_parent_category = 2;
         
        // Fetch parent category
        $categoryObj = new Category($id_parent_category);

        $allcategories = array();
           
         if (Validate::isLoadedObject($categoryObj)) {
            $categoryList = $categoryObj->getSubCategories($this->context->language->id);
            if ($categoryList && count($categoryList) > 0 ) {
                foreach ($categoryList AS $key => $val) {

                     $subcategory = $this->getSubCategories($val['id_category']);
                  

                     $allcategories[] = array(
                            'id_category' =>  $val['id_category'],
                            'name' =>  $val['name'],
                            'description' =>  $val['description'],
                            'subcategory' => $subcategory
                     );


                 
                }
            }
        }



        $this->context->smarty->assign(
            array(
                'link'       => $this->context->link,
                'categories' =>$allcategories

            )
        );
        
      
        return $this->display(__FILE__, 'views/templates/categoryform.tpl');

    }


   
   
}
