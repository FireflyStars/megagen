<?php 
class cmshomepage extends Module {
	function __construct(){
		$this->name = 'cmshomepage';
		$this->tab = 'front_office_features';
        $this->author = 'MyPresta.eu';
		$this->version = '1.2.0';
        $this->dir = '/modules/cmshomepage/';
		parent::__construct();
		$this->displayName = $this->l('CMS on homepage');
		$this->description = $this->l('Insert CMS content to your homepage');
        $this->mkey="nlc";
        if (@file_exists('../modules/'.$this->name.'/key.php'))
            @require_once ('../modules/'.$this->name.'/key.php');
        else if (@file_exists(dirname(__FILE__) . $this->name.'/key.php'))
            @require_once (dirname(__FILE__) . $this->name.'/key.php');
        else if (@file_exists('modules/'.$this->name.'/key.php'))
            @require_once ('modules/'.$this->name.'/key.php');                        
        $this->checkforupdates();
	}
    
    function checkforupdates(){
            if (isset($_GET['controller']) OR isset($_GET['tab'])){
                if (Configuration::get('update_'.$this->name) < (date("U")>86400)){
                    $actual_version = cmshomepageUpdate::verify($this->name,$this->mkey,$this->version);
                }
                if (cmshomepageUpdate::version($this->version)<cmshomepageUpdate::version(Configuration::get('updatev_'.$this->name))){
                    $this->warning=$this->l('New version available, check MyPresta.eu for more informations');
                }
            }
    }
        
	public function psversion() {
		$version=_PS_VERSION_;
		$exp=$explode=explode(".",$version);
		return $exp[1];
	}
        
	function install(){
        if (parent::install() == false 
	    OR $this->registerHook('home') == false
        OR Configuration::updateValue('update_'.$this->name,'0') == false
        OR Configuration::updateValue('cmshomepage', '0') == false
        ){
            return false;
        }
        return true;
	}
    
	public function getContent(){
	   	$output="";
		if (Tools::isSubmit('module_settings')){            		
			Configuration::updateValue('cmshomepage', $_POST['cmshomepage']);                                   
        }	   
        $output.="";
        return $output.$this->displayForm();
	}
    
    public function getCMS($lang){
    	return CMS::listCms($lang);
    }
    
    public function mypresta_socials(){
        return '<table><td>'.$this->l('follow us!').'</td><td><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2Fmypresta&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=true&amp;font=verdana&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=276212249177933" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe></td><td>'."<div class=\"g-follow\" data-annotation=\"bubble\" data-height=\"15\" data-href=\"//plus.google.com/116184657854665082523\" data-rel=\"publisher\"></div>
<script type=\"text/javascript\">
  window.___gcfg = {lang: 'en-GB'};
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>".'</td></table>';
    } 
    
	public function displayForm(){
	    $options="<option>".$this->l('-- SELECT --')."</option>";
	    $idlang = (int)Configuration::get('PS_LANG_DEFAULT');
        foreach (self::getCMS($idlang) AS $k=>$v){
            if (Configuration::get('cmshomepage')==$v['id_cms']){
                $selected='selected="yes"';
            } else {
                $selected='';
            }
            $options.="<option value=\"".$v['id_cms']."\" $selected>".$v['meta_title']."</option>";
        }
		$form='';
		return $form.'		
		<div style="diplay:block; clear:both; margin-bottom:20px;">
		<iframe src="//apps.facepages.eu/somestuff/whatsgoingon.html" width="100%" height="150" border="0" style="border:none;"></iframe>
		</div>
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            <fieldset style="position:relative; margin-bottom:10px;">
            <legend>'.$this->l('Select CMS page').'</legend>
            <div style="display:block; margin:auto; overflow:hidden; width:100%; vertical-align:top;">
                <label>'.$this->l('CMS Page').':</label>
                    <div class="margin-form" style="text-align:left;" >
                    <select name="cmshomepage">'.$options.'
                    </select>
                </div>
                                          
                <div style="margin-top:20px; clear:both; overflow:hidden; display:block; text-align:center">
	               <input type="submit" name="module_settings" class="button" value="'.$this->l('save').'">
	            </div>
            </div>
            </fieldset>
		</form>'.$this->mypresta_socials();
	}   
   
	function hookhome($params){
	    if ($this->psversion()==4 || $this->psversion()==3){
            global $cookie;
            $this->context = new StdClass();
            $this->context->cookie=$cookie;
        }
        global $smarty;
        $smarty->assign('cmsonhome', new CMS(Configuration::get('cmshomepage'), $this->context->cookie->id_lang));
        return ($this->display(__FILE__, '/cmshomepage.tpl'));
	}
}

class cmshomepageUpdate extends cmshomepage {  
    public static function version($version){
        $version=(int)str_replace(".","",$version);
        if (strlen($version)==3){$version=(int)$version."0";}
        if (strlen($version)==2){$version=(int)$version."00";}
        if (strlen($version)==1){$version=(int)$version."000";}
        if (strlen($version)==0){$version=(int)$version."0000";}
        return (int)$version;
    }
    
    public static function encrypt($string){
        return base64_encode($string);
    }
    
    public static function verify($module,$key,$version){
        if (ini_get("allow_url_fopen")) {
             if (function_exists("file_get_contents")){
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module='.$module."&version=".self::encrypt($version)."&lic=$key&u=".self::encrypt(_PS_BASE_URL_.__PS_BASE_URI__));
             }
        }
        Configuration::updateValue("update_".$module,date("U"));
        Configuration::updateValue("updatev_".$module,$actual_version); 
        return $actual_version;
    }
}
?>