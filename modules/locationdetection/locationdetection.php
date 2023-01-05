<?php

require_once(_PS_MODULE_DIR_ . 'locationdetection/PrestoChangeoClasses/init.php');

class LocationDetection extends PrestoChangeoModule
{
 	private $_html = '';
	private $_ld_lang_active;
	private $_ld_curr_active;
	private $_ld_country_to_codes;
	private $_ld_browser_first;
	private $_ld_lang_default;
	private $_ld_curr_default;
	private $_ld_map_active;
	private $_ld_lang_map;
	public $_ld_force_lr;
 	protected $_full_version = 13300;
	
	public function __construct()
	{
		$this->name = 'locationdetection';
        $this->tab = 'front_office_features';
		$this->author = 'Presto-Changeo';
		$this->version = '1.3.3';
	 	parent::__construct();
	 	$this->_refreshProperties();
		if ($this->getPSV() >= 1.6)	
			$this->bootstrap = true;
	 	$this->displayName = $this->l('Location and Currency Detection');
		$this->description = $this->l('Uses the user\'s IP Address to detect their location (from a local database), and redirect to their language and currencey.');
		if ($this->upgradeCheck('LDN'))
			$this->warning = $this->l('We have released a new version of the module,') .' '.$this->l('request an upgrade at ').' https://www.presto-changeo.com/en/contact_us';
	}

   	public function install()
   	{
		if (!parent::install())
			return false;
		Configuration::updateValue('LD_INSTALL','inline');
		Configuration::updateValue('LD_LANG_DEFAULT', Configuration::get("PS_LANG_DEFAULT"));
		Configuration::updateValue('LD_CURR_DEFAULT', Configuration::get("PS_CURRENCY_DEFAULT"));
		$language = Language::getLanguages();
		$currency = Currency::getCurrencies();
		if (sizeof($currency) > 1 && sizeof($language) > 1)
			Configuration::updateValue('LD_MAP_ACTIVE', 1);
		Configuration::updateValue('PRESTO_CHANGEO_UC',time());			
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return true;
	}
	
	private function _refreshProperties()
	{
		$this->_ld_lang_active = (int)Configuration::get('LD_LANG_ACTIVE');
		$this->_ld_curr_active = (int)Configuration::get('LD_CURR_ACTIVE');
		$this->_ld_map_active = (int)Configuration::get('LD_MAP_ACTIVE');
		$lang_map = Configuration::get('LD_LANG_MAP');
		$this->_ld_lang_map = strlen($lang_map) > 10?unserialize($lang_map):array();
		$this->_ld_lang_default = (int)Configuration::get('LD_LANG_DEFAULT');
		$this->_ld_curr_default = (int)Configuration::get('LD_CURR_DEFAULT');
		$this->_ld_browser_first = (int)Configuration::get('LD_BROWSER_FIRST');
		$this->_ld_force_lr = (int)Configuration::get('LD_FORCE_LR');
		$this->_ld_country_to_codes = unserialize((Configuration::get('LD_COUNTRY_TO_CODES')));
		if (sizeof($this->_ld_country_to_codes) <= 1)
			$this->_populateCodes();
		$this->_last_updated = Configuration::get('PRESTO_CHANGEO_UC');
	}

	private function _populateCodes()
	{
		if (sizeof($this->_ld_country_to_codes) > 1)
			return;
		$this->_ld_country_to_codes = array(
		"afghanistan"=>array("language"=>"ps","currency"=>"afn"),
		"albania"=>array("language"=>"sq","currency"=>"all"),
		"algeria"=>array("language"=>"ar","currency"=>"dzd"),
		"american samoa"=>array("language"=>"sm","currency"=>"usd"),
		"andorra"=>array("language"=>"ca","currency"=>"eur"),
		"angola"=>array("language"=>"pt","currency"=>"aoa"),
		"anguilla"=>array("language"=>"en","currency"=>"xcd"),
		"antigua and barbuda"=>array("language"=>"en","currency"=>"xcd"),
		"argentina"=>array("language"=>"es","currency"=>"ars"),
		"armenia"=>array("language"=>"hy","currency"=>"amd"),
		"aruba"=>array("language"=>"nl","currency"=>"awg"),
		"australia"=>array("language"=>"en","currency"=>"aud"),
		"austria"=>array("language"=>"de","currency"=>"eur"),
		"azerbaijan"=>array("language"=>"az","currency"=>"azn"),
		"bahamas"=>array("language"=>"en","currency"=>"bsd"),
		"bahrain"=>array("language"=>"ar","currency"=>"bhd"),
		"bangladesh"=>array("language"=>"bn","currency"=>"bdt"),
		"barbados"=>array("language"=>"en","currency"=>"bbd"),
		"belarus"=>array("language"=>"be","currency"=>"byr"),
		"belgium"=>array("language"=>"nl","currency"=>"eur"),
		"belize"=>array("language"=>"en","currency"=>"bzd"),
		"benin"=>array("language"=>"fr","currency"=>"xof"),
		"bermuda"=>array("language"=>"en","currency"=>"bmd"),
		"bhutan"=>array("language"=>"dz","currency"=>"btn"),
		"bolivia"=>array("language"=>"es","currency"=>"bob"),
		"bosnia and herzegovina"=>array("language"=>"bs","currency"=>"bam"),
		"botswana"=>array("language"=>"en","currency"=>"bwp"),
		"brazil"=>array("language"=>"pt","currency"=>"brl"),
		"british virgin islands"=>array("language"=>"en","currency"=>"usd"),
		"brunei"=>array("language"=>"ms","currency"=>"bnd"),
		"bulgaria"=>array("language"=>"bg","currency"=>"eur"),
		"burkina faso"=>array("language"=>"fr","currency"=>"xof"),
		"burma"=>array("language"=>"my","currency"=>""),
		"burundi"=>array("language"=>"fr","currency"=>"bif"),
		"cambodia"=>array("language"=>"km","currency"=>"khr"),
		"cameroon"=>array("language"=>"en","currency"=>"xaf"),
		"canada"=>array("language"=>"en","currency"=>"cad"),
		"cape verde"=>array("language"=>"pt","currency"=>"cve"),
		"cayman islands"=>array("language"=>"en","currency"=>"kyd"),
		"central african republic"=>array("language"=>"fr","currency"=>"xaf"),
		"chad"=>array("language"=>"fr","currency"=>"xaf"),
		"chile"=>array("language"=>"es","currency"=>"clp"),
		"china"=>array("language"=>"cn","currency"=>"cny"),
		"christmas island"=>array("language"=>"en","currency"=>"aud"),
		"cocos islands"=>array("language"=>"my","currency"=>"aud"),
		"colombia"=>array("language"=>"es","currency"=>"cop"),
		"comoros"=>array("language"=>"ar","currency"=>"xmf"),
		"congo"=>array("language"=>"fr","currency"=>"cdf"),
		"cook islands"=>array("language"=>"en","currency"=>"nzd"),
		"costa rica"=>array("language"=>"es","currency"=>"crc"),
		"cote d'ivoire"=>array("language"=>"fr","currency"=>"xdf"),
		"croatia"=>array("language"=>"hr","currency"=>"hrk"),
		"cuba"=>array("language"=>"es","currency"=>"cup"),
		"cyprus"=>array("language"=>"gr","currency"=>"eur"),
		"czech republic"=>array("language"=>"cs","currency"=>"czk"),
		"denmark"=>array("language"=>"dk","currency"=>"dkk"),
		"dhekelia"=>array("language"=>"en","currency"=>""),
		"djibouti"=>array("language"=>"fr","currency"=>"djf"),
		"dominica"=>array("language"=>"en","currency"=>"xcd"),
		"dominican republic"=>array("language"=>"es","currency"=>"dop"),
		"east timor"=>array("language"=>"te","currency"=>"usd"),
		"ecuador"=>array("language"=>"es","currency"=>"usd"),
		"egypt"=>array("language"=>"ar","currency"=>"egp"),
		"el salvador"=>array("language"=>"es","currency"=>"svc"),
		"equatorial guinea"=>array("language"=>"es","currency"=>"xaf"),
		"eritrea"=>array("language"=>"aa","currency"=>"ern"),
		"estonia"=>array("language"=>"et","currency"=>"eek"),
		"ethiopia"=>array("language"=>"am","currency"=>"etb"),
		"falkland islands"=>array("language"=>"en","currency"=>"fkp"),
		"faroe islands"=>array("language"=>"fo","currency"=>"dkk"),
		"figi"=>array("language"=>"en","currency"=>"fjd"),
		"finland"=>array("language"=>"fi","currency"=>"eur"),
		"france"=>array("language"=>"fr","currency"=>"eur"),
		"french guiana"=>array("language"=>"fr","currency"=>"eur"),
		"french polynesia"=>array("language"=>"fr","currency"=>"xpf"),
		"gabon"=>array("language"=>"fr","currency"=>"xaf"),
		"gambia"=>array("language"=>"en","currency"=>"gmd"),
		"gaza strip"=>array("language"=>"ar","currency"=>"nis"),
		"georgia"=>array("language"=>"ge","currency"=>"gel"),
		"germany"=>array("language"=>"de","currency"=>"eur"),
		"ghana"=>array("language"=>"en","currency"=>"ghs"),
		"gibraltar"=>array("language"=>"en","currency"=>"gip"),
		"greece"=>array("language"=>"gr","currency"=>"eur"),
		"greenland"=>array("language"=>"da","currency"=>"dkk"),
		"grenada"=>array("language"=>"en","currency"=>"xcd"),
		"guadeloupe"=>array("language"=>"fr","currency"=>"eur"),
		"guam"=>array("language"=>"en","currency"=>"usr"),
		"guatemala"=>array("language"=>"es","currency"=>"gtq"),
		"guernsey"=>array("language"=>"en","currency"=>"gbp"),
		"guinea"=>array("language"=>"fr","currency"=>"gnf"),
		"guinea-bissau"=>array("language"=>"pt","currency"=>"gwp"),
		"guyana"=>array("language"=>"en","currency"=>"gyd"),
		"haiti"=>array("language"=>"fr","currency"=>"htg"),
		"holy see (vatican city state)"=>array("language"=>"it","currency"=>"eur"),
		"honduras"=>array("language"=>"es","currency"=>"hnl"),
		"hong kong"=>array("language"=>"cn","currency"=>"hkd"),
		"hungary"=>array("language"=>"hu","currency"=>"huf"),
		"iceland"=>array("language"=>"is","currency"=>"isk"),
		"india"=>array("language"=>"hi","currency"=>"inr"),
		"indonesia"=>array("language"=>"id","currency"=>"idr"),
		"iran"=>array("language"=>"fa","currency"=>"irr"),
		"iraq"=>array("language"=>"ar","currency"=>"iqd"),
		"ireland"=>array("language"=>"en","currency"=>"eur"),
		"israel"=>array("language"=>"he","currency"=>"nis"),
		"italy"=>array("language"=>"it","currency"=>"eur"),
		"jamaica"=>array("language"=>"en","currency"=>"jmd"),
		"japan"=>array("language"=>"jp","currency"=>"jpy"),
		"jersey"=>array("language"=>"en","currency"=>"gbp"),
		"jordan"=>array("language"=>"ar","currency"=>"jod"),
		"kazakhstan"=>array("language"=>"kk","currency"=>"kzt"),
		"kenya"=>array("language"=>"en","currency"=>"kes"),
		"kiribati"=>array("language"=>"en","currency"=>"aud"),
		"republic of korea"=>array("language"=>"ko","currency"=>"krw"),
		"kuwait"=>array("language"=>"ar","currency"=>"kwd"),
		"kyrgyzstan"=>array("language"=>"ky","currency"=>"kzt"),
		"laos"=>array("language"=>"lo","currency"=>"lak"),
		"latvia"=>array("language"=>"lv","currency"=>"lvl"),
		"lebanon"=>array("language"=>"ar","currency"=>"lbp"),
		"lesotho"=>array("language"=>"en","currency"=>"zar"),
		"liberia"=>array("language"=>"en","currency"=>"lrd"),
		"libya"=>array("language"=>"ar","currency"=>"lyd"),
		"liechtenstein"=>array("language"=>"de","currency"=>"chf"),
		"lithuania"=>array("language"=>"lt","currency"=>"ltl"),
		"luxembourg"=>array("language"=>"lb","currency"=>"eur"),
		"macau"=>array("language"=>"cn","currency"=>"mop"),
		"macedonia"=>array("language"=>"mk","currency"=>"mkd"),
		"madagascar"=>array("language"=>"fr","currency"=>"mga"),
		"malawi"=>array("language"=>"ny","currency"=>"mwk"),
		"malaysia"=>array("language"=>"en","currency"=>"myr"),
		"maldives"=>array("language"=>"dv","currency"=>"mvr"),
		"mali"=>array("language"=>"fr","currency"=>"xof"),
		"malta"=>array("language"=>"mt","currency"=>"eur"),
		"marshall islands"=>array("language"=>"mh","currency"=>"usd"),
		"martinique"=>array("language"=>"fr","currency"=>"eur"),
		"mauritania"=>array("language"=>"ar","currency"=>"mro"),
		"mauritius"=>array("language"=>"en","currency"=>"mur"),
		"mayotte"=>array("language"=>"fr","currency"=>"eur"),
		"mexico"=>array("language"=>"es","currency"=>"mxn"),
		"federated states of micronesia"=>array("language"=>"en","currency"=>"usd"),
		"moldova"=>array("language"=>"ro","currency"=>"mdl"),
		"monaco"=>array("language"=>"fr","currency"=>"eur"),
		"mongolia"=>array("language"=>"ro","currency"=>"mnt"),
		"serbia and montenegro"=>array("language"=>"sr","currency"=>"eur"),
		"montserrat"=>array("language"=>"en","currency"=>"xcd"),
		"morocco"=>array("language"=>"ar","currency"=>"mad"),
		"mozambique"=>array("language"=>"pt","currency"=>"mzn"),
		"nauru"=>array("language"=>"na","currency"=>"aud"),
		"nepal"=>array("language"=>"ne","currency"=>"npr"),
		"netherlands"=>array("language"=>"nl","currency"=>"eur"),
		"netherlands antilles"=>array("language"=>"en","currency"=>"ang"),
		"new caledonia"=>array("language"=>"fr","currency"=>"xpf"),
		"new zealand"=>array("language"=>"en","currency"=>"nzd"),
		"nicaragua"=>array("language"=>"sp","currency"=>"nio"),
		"niger"=>array("language"=>"fr","currency"=>"xof"),
		"nigeria"=>array("language"=>"en","currency"=>"ngn"),
		"niue"=>array("language"=>"en","currency"=>"nzd"),
		"norfolk island"=>array("language"=>"en","currency"=>"aud"),
		"northern mariana islands"=>array("language"=>"cn","currency"=>"usd"),
		"norway"=>array("language"=>"no","currency"=>"nok"),
		"oman"=>array("language"=>"ar","currency"=>"omr"),
		"pakistan"=>array("language"=>"pa","currency"=>"pkr"),
		"palau"=>array("language"=>"en","currency"=>"usd"),
		"panama"=>array("language"=>"es","currency"=>"pab"),
		"papua new guinea"=>array("language"=>"en","currency"=>"pgk"),
		"paraguay"=>array("language"=>"es","currency"=>"pyg"),
		"peru"=>array("language"=>"es","currency"=>"pen"),
		"philippines"=>array("language"=>"en","currency"=>"php"),
		"poland"=>array("language"=>"pl","currency"=>"pln"),
		"portugal"=>array("language"=>"pt","currency"=>"eur"),
		"puerto rico"=>array("language"=>"es","currency"=>"usr"),
		"qatar"=>array("language"=>"ar","currency"=>"qar"),
		"reunion"=>array("language"=>"fr","currency"=>"eur"),
		"romania"=>array("language"=>"ro","currency"=>"ron"),
		"russia"=>array("language"=>"ru","currency"=>"rub"),
		"rwanda"=>array("language"=>"en","currency"=>"rwf"),
		"saint helena"=>array("language"=>"en","currency"=>"shp"),
		"saint kitts and nevis"=>array("language"=>"en","currency"=>"xcd"),
		"saint lucia"=>array("language"=>"en","currency"=>"xcd"),
		"saint pierre and miquelon"=>array("language"=>"fr","currency"=>"xcd"),
		"saint vincent and the grenadines"=>array("language"=>"en","currency"=>"xcd"),
		"samoa"=>array("language"=>"sn","currency"=>"wst"),
		"san marino"=>array("language"=>"it","currency"=>"eur"),
		"sao tome and principe"=>array("language"=>"pt","currency"=>"std"),
		"saudi arabia"=>array("language"=>"ar","currency"=>"sar"),
		"senegal"=>array("language"=>"fr","currency"=>"xof"),
		"seychelles"=>array("language"=>"en","currency"=>"scr"),
		"sierra leone"=>array("language"=>"en","currency"=>"sll"),
		"singapore"=>array("language"=>"cn","currency"=>"sgd"),
		"slovakia"=>array("language"=>"sk","currency"=>"eur"),
		"slovenia"=>array("language"=>"sl","currency"=>"eur"),
		"solomon islands"=>array("language"=>"cn","currency"=>"sbd"),
		"somalia"=>array("language"=>"so","currency"=>"sos"),
		"south africa"=>array("language"=>"af","currency"=>"zar"),
		"spain"=>array("language"=>"es","currency"=>"eur"),
		"sri lanka"=>array("language"=>"si","currency"=>"lkr"),
		"sudan"=>array("language"=>"ar","currency"=>"sdg"),
		"suriname"=>array("language"=>"nl","currency"=>"srd"),
		"svalbard"=>array("language"=>"nn","currency"=>"nok"),
		"swaziland"=>array("language"=>"en","currency"=>"szl"),
		"sweden"=>array("language"=>"se","currency"=>"sek"),
		"switzerland"=>array("language"=>"de","currency"=>"chf"),
		"syria"=>array("language"=>"ar","currency"=>"syp"),
		"taiwan"=>array("language"=>"cn","currency"=>"twd"),
		"tajikistan"=>array("language"=>"tg","currency"=>"tjs"),
		"tanzania"=>array("language"=>"sw","currency"=>"tzs"),
		"thailand"=>array("language"=>"th","currency"=>"thb"),
		"togo"=>array("language"=>"fr","currency"=>"xof"),
		"tokelau"=>array("language"=>"en","currency"=>"nzd"),
		"tonga"=>array("language"=>"to","currency"=>"top"),
		"trinidad and tobago"=>array("language"=>"en","currency"=>"ttd"),
		"tunisia"=>array("language"=>"ar","currency"=>"tng"),
		"turkey"=>array("language"=>"tr","currency"=>"try"),
		"turkmenistan"=>array("language"=>"tk","currency"=>"tmt"),
		"turks and caicos islands"=>array("language"=>"en","currency"=>"usd"),
		"tuvalu"=>array("language"=>"en","currency"=>"aud"),
		"uganda"=>array("language"=>"en","currency"=>"ugx"),
		"ukraine"=>array("language"=>"ua","currency"=>"hau"),
		"united arab emirates"=>array("language"=>"ar","currency"=>"aed"),
		"united kingdom"=>array("language"=>"en","currency"=>"gbp"),
		"united states"=>array("language"=>"en","currency"=>"usd"),
		"uruguay"=>array("language"=>"es","currency"=>"uyu"),
		"uzbekistan"=>array("language"=>"uz","currency"=>"uzs"),
		"vanuatu"=>array("language"=>"en","currency"=>"vuv"),
		"venezuela"=>array("language"=>"es","currency"=>"vef"),
		"vietnam"=>array("language"=>"vn","currency"=>"vnd"),
		"virgin islands, u.s."=>array("language"=>"es","currency"=>"usd"),
		"wallis and futuna"=>array("language"=>"fr","currency"=>"xpf"),
		"west bank"=>array("language"=>"ar","currency"=>"nis"),
		"western sahara"=>array("language"=>"ar","currency"=>"mad"),
		"yemen"=>array("language"=>"ar","currency"=>"yer"),
		"zambia"=>array("language"=>"en","currency"=>"zmk"),
		"zimbabwe"=>array("language"=>"en","currency"=>"zwl"));
		Configuration::updateValue('LD_COUNTRY_TO_CODES', serialize($this->_ld_country_to_codes));
	}

	public function getContent()
	{
		$this->_postProcess();
		$this->_displayForm();
		return $this->_html;
	}

    private function _displayForm()
    {
		if (Tools::isSubmit('submitIPLookup'))
		{
			include_once('Ip2Country.php');
			$ip2c = new Ip2Country;
			$ip2c->load(Tools::getValue('ip_lookup'));
			if ($ip2c->country == "RESERVED")
				$ip2c->country = "UNITED STATES";
			$country_iso = $this->_ld_country_to_codes[strtolower($ip2c->country)]['language'];
			$country_name = strtolower($ip2c->country);
			//$this->_html .= '<div class="'.($this->getPSV() >= 1.6?'module_confirmation conf confirm alert alert-success"':'conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" /').'>'.$this->l('Country Name: '). $country_name .' ('.$country_iso.')</div>';
		}
    	$language = Language::getLanguages();
        $currency = Currency::getCurrencies();
   		if ($this->getPSV() == 1.4)
   		{ 
   			$server_file = @file(dirname(__FILE__).'/../../override/classes/FrontController.php');
   			$modified_file = @file(dirname(__FILE__).'/override_1.4/classes/FrontController.php');
			$modified_file_tools = @file(dirname(__FILE__).'/override_1.4/classes/Tools.php');
   		}
   		else
   		{
   			$server_file = @file(dirname(__FILE__).'/../../override/classes/controller/FrontController.php');
   			$modified_file = @file(dirname(__FILE__).'/override_'.$this->getPSV().'/classes/controller/FrontController.php');
			$modified_file_tools = @file(dirname(__FILE__).'/override_'.$this->getPSV().'/classes/Tools.php');
   		}
		$server_file_tools = @file(dirname(__FILE__).'/../../override/classes/Tools.php');
   		$is_match = $this->overrideCheck($modified_file, $server_file);
   		$is_match_tools = $this->overrideCheck($modified_file_tools, $server_file_tools);
   		$db_installed = file_exists(dirname(__FILE__).'/db/0.php')?true:false;

		$this->_html .= ($this->getPSV() >= 1.5?'<div id="aw_bo_container" '.($this->getPSV() < 1.6? 'style="width:900px;"' : '' ).'>':'').$this->getModuleRecommendations('LDN').'<h2 style="clear:both;padding-top:5px;">'.$this->displayName.' '.$this->version.'</h2>';
		if ($url = $this->upgradeCheck('LDN'))
			$this->_html .= '
			'.($this->getPSV() < 1.6 ? '<fieldset class="width3" style="background-color:#FFFAC6;width:800px;">' : '<div class="panel">' ).'
			'.($this->getPSV() < 1.6 ? '<legend>' : '<h3>').'
			<img src="'.$this->_path.'logo.gif" />'.$this->l('New Version Available').'
			'.($this->getPSV() < 1.6 ? '</legend>' : '</h3>' ).'
			'.$this->l('We have released a new version of the module. For a list of new features, improvements and bug fixes, view the ').'<a href="'.$url.'" target="_index"><b><u>'.$this->l('Change Log').'</b></u></a> '.$this->l('on our site.').'
			<br />
			'.$this->l('For real-time alerts about module updates, be sure to join us on our') .' <a href="http://www.facebook.com/pages/Presto-Changeo/333091712684" target="_index"><u><b>Facebook</b></u></a> / <a href="http://twitter.com/prestochangeo1" target="_index"><u><b>Twitter</b></u></a> '.$this->l('pages').'.
			<br />
			<br />
			'.$this->l('Please').' <a href="https://www.presto-changeo.com/en/contact_us" target="_index"><b><u>'.$this->l('contact us').'</u></b></a> '.$this->l('to request an upgrade to the latest version').'.
			'.($this->getPSV() < 1.6 ? '</fieldset>' : '</div>' ).'
			<br />';
		if (!is_array($this->_ld_lang_map) || sizeof($this->_ld_lang_map) == 0)
		{
			$arr = array();
			foreach ($language AS $lang)
			{	
				$arr["id"][$lang['id_lang']] = Tools::getValue('ld_curr_map_'.$lang['id_lang']);
				$arr["iso"][$lang['iso_code']] = Tools::getValue('ld_curr_map_'.$lang['id_lang']);
			}
			$this->_ld_lang_map = $arr;
			Configuration::updateValue('LD_LANG_MAP',serialize($arr));
		}
        $this->_html .= '
			<link type="text/css" rel="stylesheet" href="'.$this->_path.'css/admin.css" />
			<link rel="stylesheet" type="text/css" media="all" href="'.$this->_path.'css/tooltipster.css" />
			<script type="text/javascript" src="'.$this->_path.'js/jquery.tooltipster.min.js"></script>
			<form action="'.$_SERVER['REQUEST_URI'].'" name="filter_form" method="post" enctype="multipart/form-data">
			'.($this->getPSV() < 1.6 ? '<fieldset class="width3" style="width:850px">' : '<div class="panel">' ).'
				'.($this->getPSV() < 1.6 ? '<legend>' : '<h3>' ).'
			<img src="'.$this->_path.'logo.gif" /> '.$this->l('Installation Instructions').' (<a href="'.$_SERVER['REQUEST_URI'].'&ld_shi='.Configuration::get('LD_INSTALL').'" style="color:blue;text-decoration:underline">'.(Configuration::get('LD_INSTALL')=="inline"?"Hide":"Show").'</a>)
				'.($this->getPSV() < 1.6 ? '</legend>' : '</h3>').'
			<div id="ld_install" style="display:'.Configuration::get('LD_INSTALL').'">
				<table '.($this->getPSV() < 1.6 ? 'width="850"' : '' ).'>
				<tr height="40">
				<td align="left" nowrap>
				<b>'.$this->l('The database table with the IP address to Country information has been installed').'.</b>
				<br />
				<li>
				'.($is_match?'
				<b style="color:green">'.$this->l('Override File (FrontController) Installed Successfully').'</b>
				':
				'<b>'.$this->l('You must copy').'&nbsp; <b style="color:red">/locationdetection/override_'.($this->getPSV() == 1.5?$this->getPSV().'/controller':$this->getPSV()).'/classes/FrontController.php</b> &nbsp;'.$this->l('to').' &nbsp;/override/classes/'.($this->getPSV() == 1.5?'controller/':'').'
				<br />
				'.$this->l('If the file already exists there (not _FrontController.php), you will have to merge them').'.
				').'
				</li>
				<br />
				<li>
				'.($is_match_tools?'
				<b style="color:green">'.$this->l('Override File (Tools) Installed Successfully').'</b>
				':
				'<b>'.$this->l('You must copy').'</b>
				<span class="info_tooltip" title="'.$this->l('Only required if you activate \'Force Currency Link\' language & currency (See more below)').'"></span>	
				<b style="color:red">/locationdetection/override_'.$this->getPSV().'/classes/Tools.php</b> &nbsp;<b>'.$this->l('to').' &nbsp;/override/classes/
				<br />
				'.$this->l('If the file already exists there you will have to merge them').'.
				').'
				</li>
				<br />';
			if ($this->comparePSV('>=', '1.6'))
				$this->_html .= '<li style="margin-left:10px"><b style="color:blue">'.$this->l('Make sure to clear the cache in Advanced Parameteres->Performance->Clear Cache').'.</b></li><br />';
			elseif ($this->comparePSV('>=', '1.5.6'))
				$this->_html .= '<li style="margin-left:10px"><b style="color:blue">'.$this->l('Make sure to clear the cache in Advanced Parameteres->Performance->Clear Smarty cache & Autoload cache').'.</b></li><br />';
			else if ($this->comparePSV('>=', '1.5.4'))
				$this->_html .= '<li style="margin-left:10px"><b style="color:blue">'.$this->l('Make sure to delete /cache/class_index.php (it will be automatically regenrated)').'.</b></li><br />';
   		$this->_html .= '
				<hr />
				<li><b>'.$this->l('Ip 2 Location Status: ').'</b>'.($db_installed?'<b style="color:green">'.$this->l('Installed'):'<b style="color:red">'.$this->l('Requires Installation, See Instructions Below')).'</b></li>
				<br />
				<li><b>'.$this->l('An updated version of the IP to Country file comes out on a daily basis').'.
				<br />
				'.$this->l('To get the most accurate results, ').'<a style="color:blue;textdecoration:underline" href="http://software77.net/geo-ip?DL=1">'.$this->l('download the lastest version from software77').'</a>, '.$this->l('and upload & install it below').'</li>
				<br />
				<br />
				<input type="file" name="ipdb" style="display:inline" />
				<input type="submit" value="'.$this->l('Upload & Install IP Database (IP2Country.csv.gz)').'" name="submitIPDB" class="button" style="display:inline" />
				
				</td>
				</tr>
				</table>
			</div>
			'.($this->getPSV() < 1.6 ? '</fieldset>' : '</div>' ).'
			<br />
			'.($this->getPSV() < 1.6 ? '<fieldset class="width3" style="width:850px">' : '<div class="panel">' ).'
				'.($this->getPSV() < 1.6 ? '<legend>' : '<h3>' ).'
					<img src="'.$this->_path.'logo.gif" /> '.$this->l('IP 2 Country Lookup').'
				'.($this->getPSV() < 1.6 ? '</legend>' : '</h3>' ).'
				<table '.($this->getPSV() < 1.6 ? 'width="100%"' : '' ).'>
				<tr height="40">
				<td align="left" style="width:120px">
					<b>'.$this->l('IP Address').':</b> 
					<span class="info_tooltip" title=\''.$this->l('Test which country is found for any IP Address (useful for testing)').'\'></span>	
				</td>
				<td align="left" style="width:300px">
					&nbsp;<input type="text" name="ip_lookup" value="'.Tools::getValue('ip_lookup').'" style="width:120px;display:inline" />
					'.(Tools::isSubmit('submitIPLookup')?' <b>&nbsp; = &nbsp;'. ucwords($country_name) .' ('.strtoupper($country_iso).')</b>':'').'
				</td>
			</tr>';
    	$this->_html .= '
			<tr height="40">
				<td colspan="2" align="center">
					<input type="submit" value="'.$this->l('Find Matching Country').'" name="submitIPLookup" class="button" />
				</td>
			</tr>
			</table>
			'.($this->getPSV() < 1.6 ? '</fieldset>' : '</div>' ).'
			<br />
			'.($this->getPSV() < 1.6 ? '<fieldset class="width3" style="width:850px">' : '<div class="panel">' ).'
				'.($this->getPSV() < 1.6 ? '<legend>' : '<h3>' ).'
					<img src="'.$this->_path.'logo.gif" /> '.$this->l('Location Detection Options').'
				'.($this->getPSV() < 1.6 ? '</legend>' : '</h3>' ).'
				<table '.($this->getPSV() < 1.6 ? 'width="850"' : '' ).'>
				<tr height="40">
				<td align="left">
					<b>'.$this->l('Language Redirect').':</b> 
					<span class="info_tooltip" title="'.$this->l('When active, the language will be set to match the detected location of the customer, based on the selection in the table below').'"></span>

				</td>
				<td align="left">
					&nbsp;<input type="radio" name="ld_lang_active" value="1" '.(Tools::getValue('ld_lang_active', $this->_ld_lang_active) != 2 ? 'checked' : '').' />
				</td>
				<td align="left">
					'.$this->l('Active').'
				</td>
				<td align="left">
					&nbsp;&nbsp;&nbsp;
				</td>
				<td align="left">
					<input type="radio" name="ld_lang_active" value="2" '.(Tools::getValue('ld_lang_active', $this->_ld_lang_active) == 2 ? 'checked' : '').' />
				</td>
				<td align="left">
					'.$this->l('Inactive').'
				</td>
			</tr>
			<tr style="height:30px">
				<td align="left">
					<b>'.$this->l('Options').':</b> 
					<span class="info_tooltip" title=\''.$this->l('Select which language detection method should be used first, if one is not found (for example, there is no match for the IP address), the other will be used.').'\'></span>	
				</td>
				<td align="left">
					&nbsp;<input type="radio" name="ld_browser_first" value="1" '.(Tools::getValue('ld_browser_first', $this->_ld_browser_first) != 2 ? 'checked' : '').' />
				</td>
				<td align="left">
				'.$this->l('IP Address to Country Detection First').'
				</td>
				<td align="left">
					&nbsp;&nbsp;&nbsp;
				</td>
				<td align="left">
					<input type="radio" name="ld_browser_first" value="2" '.(Tools::getValue('ld_browser_first', $this->_ld_browser_first) == 2 ? 'checked' : '').' />
				</td>
				<td align="left">
					'.$this->l('Browser Language Detection First').'
				</td>
			</tr>
			<tr style="height:30px">
				<td align="left">
					<b>'.$this->l('Currency Redirect').':</b> 
					<span class="info_tooltip" title=\''.$this->l('When active, the currency will be set to match the detected location of the customer, based on the selection in the table below.').'\'></span>	
				</td>
				<td align="left">
					&nbsp;<input type="radio" name="ld_curr_active" value="1" '.(Tools::getValue('ld_curr_active', $this->_ld_curr_active) != 2 ? 'checked' : '').' />
				</td>
				<td align="left">
					'.$this->l('Active').'
				</td>
				<td align="left">
					&nbsp;&nbsp;&nbsp;
				</td>
				<td align="left">
					<input type="radio" name="ld_curr_active" value="2" '.(Tools::getValue('ld_curr_active', $this->_ld_curr_active) == 2 ? 'checked' : '').' />
				</td>
				<td align="left">
					'.$this->l('Inactive').'
				</td>
			</tr>';
    	if (sizeof($currency) > 1 && sizeof($language) > 1)
    	{
    		$this->_html .= '
			<tr style="height:30px" class="lang_map">
				<td align="left">
					<b>'.$this->l('Language & Currency Link').':</b> 
					<span class="info_tooltip" title=\''.$this->l('When active, the currency will be changed to match the language the customer selected (in the block languages module), the customer would still be able to change the currency if they want.').'<br /><br />'.$this->l('Use the table below to link a language to a currency.').'\'></span>	

				</td>
				<td align="left">
					&nbsp;<input type="radio" name="ld_map_active" value="1" '.(Tools::getValue('ld_map_active', $this->_ld_map_active) != 2 ? 'checked' : '').' />
				</td>
				<td align="left">
					'.$this->l('Active').'
				</td>
				<td align="left">
					&nbsp;&nbsp;&nbsp;
				</td>
				<td align="left">
					<input type="radio" name="ld_map_active" value="2" '.(Tools::getValue('ld_map_active', $this->_ld_map_active) == 2 ? 'checked' : '').' />
				</td>
				<td align="left">
					'.$this->l('Inactive').'
				</td>
			</tr>';
    		$curr_lang = Configuration::get("PS_CURRENCY_DEFAULT");
    		foreach  ($language AS $lang)
    		{
    			$this->_html .= '
				<tr style="height:30px" class="lang_map">
					<td align="left"></td>
					<td align="left" colspan="2">
						'.$lang['name'].'	
					</td>
					<td align="left"></td>
					<td align="left" colspan="2">
					<select name="ld_curr_map_'.$lang['id_lang'].'">';
    			foreach ($currency AS $curr)
    				$this->_html .= '<option value="'.$curr['id_currency'].'" '.(array_key_exists('id',$this->_ld_lang_map) && array_key_exists($lang['id_lang'],$this->_ld_lang_map['id'])?($this->_ld_lang_map['id'][$lang['id_lang']] == $curr['id_currency']?'selected':''):($curr_lang == $curr['id_currency']?'selected':'')).' />'.$curr['name'].'</option>';
    			$this->_html .= '
    				</select>
    				</td>
				</tr>';
        	}
   		$this->_html .= '
			<tr style="height:30px" class="lang_map">
				<td align="left">
					<b>'.$this->l('Force Currency Link').':</b> 
					<span class="info_tooltip" title=\''.$this->l('When active, the currency will be linked to a language and the customer will not be able to manually change it. This option does not require the block languages module, the currency will change even when entering a URL with a different language code (IE site.com/fr/)').'\'></span>	
				</td>
				<td align="left">
					&nbsp;<input type="radio" name="ld_force_lr" value="1" '.(Tools::getValue('ld_force_lr', $this->_ld_force_lr) == 1 ? 'checked' : '').' />
				</td>
				<td align="left">
					'.$this->l('Active').'
				</td>
				<td align="left">
					&nbsp;&nbsp;&nbsp;
				</td>
				<td align="left">
					<input type="radio" name="ld_force_lr" value="2" '.(Tools::getValue('ld_force_lr', $this->_ld_force_lr) != 1 ? 'checked' : '').' />
				</td>
				<td align="left">
					'.$this->l('Inactive').'
				</td>
			</tr>';
        	
    	}
    	$this->_html .= '
			<tr height="40">
				<td colspan="7" align="center">
					<input type="submit" value="'.$this->l('Update').'" name="submitChanges" class="button" />
				</td>
			</tr>
			</table>
			'.($this->getPSV() < 1.6 ? '</fieldset>' : '</div>' ).'
			<br />
			'.($this->getPSV() < 1.6 ? '<fieldset class="width3" style="width:850px">' : '<div class="panel">' ).'
				'.($this->getPSV() < 1.6 ? '<legend>' : '<h3>' ).'
					<img src="'.$this->_path.'logo.gif" /> '.$this->l('Country Specific Settings').'
				'.($this->getPSV() < 1.6 ? '</legend>' : '</h3>' ).'
				<table '.($this->getPSV() < 1.6 ? 'width="850"' : '' ).'>
				<tr height="40">
				<td align="left">
					<b>'.$this->l('You can edit any of the codes below if they don\'t match your Prestashop settings').'</b>
				</td>
			</tr>
			<tr>
				<td align="left">
					<table width="100%">
					<thead style="background-color: white">
					<tr height="40">
						<td align="left valign="top">
							<b>'.$this->l('Country').'</b>
						</td>
						<td align="center">
							<b>'.$this->l('Prestashop').'
							'.$this->l('Language Code').'</b>
						</td>
						<td align="center">
							<b>'.$this->l('Prestashop').'
							'.$this->l('Currency Code').'</b>
						</td>
					</tr>
					</thead>';
        	$this->_html .= '<tr>
        		<td align="left">
        			'.$this->l('Default').'
					<span class="info_tooltip" title=\''.$this->l('If the detected language is not available in your shop, use this language / currency').'\'></span>	

        		</td>
        		<td align="center">
        			<select name="ld_lang_default" style="width:70px">';
        			foreach ($language AS $lang)
        				$this->_html .= "<option value=\"".($lang['id_lang'])."\" ".($this->_ld_lang_default == strtoupper($lang['id_lang'])?"selected":"").">".strtoupper($lang['iso_code'])."</option>\n";
					$this->_html .= '</select>
        		</td>
        		<td align="center">
        			<select name="ld_curr_default" style="width:70px">';
        			foreach ($currency AS $curr)
        				$this->_html .= "<option value=\"".($curr['id_currency'])."\" ".($this->_ld_curr_default == ($curr['id_currency'])?"selected":"").">".strtoupper($curr['iso_code'])."</option>\n";
					$this->_html .= '</select>
        		</td>
        	</tr>';
        $i = 1;
        if (is_array($this->_ld_country_to_codes))
        foreach ($this->_ld_country_to_codes AS $country => $codes)
        {
        	$this->_html .= '<tr height="40">
        		<td align="left">
        			&nbsp;'.ucwords($country).'
        			<input type="hidden" name="ld_cont_'.$i.'" value="'.$country.'" />
        		</td>
        		<td align="center">
        			<input type="text" style="width:70px" name="ld_lang_'.$i.'" value="'.strtoupper($codes['language']).'" />
        		</td>
        		<td align="center">
        			<input type="text" style="width:70px" name="ld_curr_'.$i.'" value="'.strtoupper($codes['currency']).'" />
        		</td>
        	</tr>';
        	$i++;
        }
	$this->_html .= '</table>
				</td>
			</tr>
			<tr height="40">
				<td colspan="6" align="center">
					<input type="submit" value="'.$this->l('   Update   ').'" name="submitChanges" class="button" />
				</td>
			</tr>
			</table>
			'.($this->getPSV() < 1.6 ? '</fieldset>' : '</div>' ).'
		</form>
		';
		$this->_html .= ($this->getPSV() >= 1.5?'</div>':'');
	}
	
	private function _postProcess()
	{
		if (Tools::getValue('ld_shi') != "")
			if (Tools::getValue('ld_shi') == "inline")
				Configuration::updateValue('LD_INSTALL',"none");
			else
				Configuration::updateValue('LD_INSTALL',"inline");
		if (Tools::isSubmit('submitChanges'))
		{
        	$language = Language::getLanguages();
			if (!Configuration::updateValue('LD_LANG_ACTIVE', (int)Tools::getValue('ld_lang_active'))
				|| !Configuration::updateValue('LD_CURR_ACTIVE', (int)Tools::getValue('ld_curr_active'))
				|| !Configuration::updateValue('LD_MAP_ACTIVE', (int)Tools::getValue('ld_map_active'))
				|| !Configuration::updateValue('LD_LANG_DEFAULT', (int)Tools::getValue('ld_lang_default'))
				|| !Configuration::updateValue('LD_CURR_DEFAULT', (int)Tools::getValue('ld_curr_default'))
				|| !Configuration::updateValue('LD_FORCE_LR', (int)Tools::getValue('ld_force_lr'))
				|| !Configuration::updateValue('LD_BROWSER_FIRST', (int)Tools::getValue('ld_browser_first')))
				$this->_html .= '<div class="'.($this->getPSV() >= 1.6?'module_error alert alert-danger"':'alert error" style="background-color:#ece400"').'">'.$this->l('Cannot update settings').'</div>';
			else
				$this->_html .= '<div class="'.($this->getPSV() >= 1.6?'module_confirmation conf confirm alert alert-success"':'conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" /').'>'.$this->l('Settings updated').'</div>';
			$i = 1;
			$arr = array();
			while(Tools::getValue('ld_cont_'.$i))
			{
				$arr[Tools::getValue('ld_cont_'.$i)] = array("language"=>strtolower(trim(Tools::getValue('ld_lang_'.$i))),"currency"=>strtolower(trim(Tools::getValue('ld_curr_'.$i))));
				$i++;
			}
			if (!Configuration::updateValue('LD_COUNTRY_TO_CODES',serialize($arr)))
				$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
			
			$arr = array();
			foreach ($language AS $lang)
			{
				$arr["id"][$lang['id_lang']] = Tools::getValue('ld_curr_map_'.$lang['id_lang']);
				$arr["iso"][$lang['iso_code']] = Tools::getValue('ld_curr_map_'.$lang['id_lang']);
			}
			if (!Configuration::updateValue('LD_LANG_MAP',serialize($arr)))
				$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
		}
		if (Tools::isSubmit('submitIPDB'))
		{
			if (!move_uploaded_file($_FILES['ipdb']['tmp_name'], dirname(__FILE__) . '/'.(strpos($_FILES['ipdb']['name'],'.gz') !== false?'Ip2Country.csv.gz':'Ip2Country.csv')))
				$this->_html .= '<div class="alert error">'.$this->l('Cannot Upload File').'</div>';
			else
			{
				if (strpos($_FILES['ipdb']['name'],'.gz') !== false  && function_exists('gzopen'))
					$this->uncompress(dirname(__FILE__) . '/Ip2Country.csv.gz', dirname(__FILE__) . '/Ip2Country.csv');
				if (file_exists(dirname(__FILE__) . '/Ip2Country.csv'))
				{
					include_once('Ip2Country.php');
					$ip2c = new Ip2Country;
					$files = glob(dirname(__FILE__).'/db/*'); // get all file names
					foreach($files as $file) // iterate files
  						if(is_file($file))
    						unlink($file); // delete file

					
					if ($ip2c->parseCSV2(dirname(__FILE__) . '/Ip2Country.csv'))
						$this->_html .= '<div class="'.($this->getPSV() >= 1.6?'module_confirmation conf confirm alert alert-success"':'conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" /').'>'.$this->l('Ip 2 Country Database Loaded.').'</div>';
					else
						$this->_html .= '<div class="'.($this->getPSV() >= 1.6?'module_error alert alert-danger"':'alert error" style="background-color:#ece400"').'>'.$this->l('Error Loading Database').'</div>';
				}
				else
					$this->_html .= '<div class="'.($ps_version >= 1.6?'module_error alert alert-danger"':'alert error" style="background-color:#ece400"').'>'.$this->l('Error Extracting File').'</div>';
			}
		}
		$this->_refreshProperties();
	}
	
	private function uncompress( $in, $out)
	{
		// getting content of the compressed file
    	$in_file = gzopen ($in, "rb");
    	$out_file = fopen ($out, "wb");

	    while (!gzeof ($in_file)) {
        	$buffer = gzread ($in_file, 4096);
        	fwrite ($out_file, $buffer, 4096);
    	}
 
	    gzclose ($in_file);
	    fclose ($out_file);
	}
	
	public function redirect_user($test = false)
	{
		if (!$this->active)
			return;
		$log = false;
		if ($log)
		{
			$myFile = dirname(__FILE__)."/1llog.txt";
			$fh = fopen($myFile, 'a') or die("can't open file");
			fwrite($fh, "Starting '".$this->_ld_browser_first."' = ".$_SERVER['REQUEST_URI']." (".$_SERVER['HTTP_ACCEPT_LANGUAGE'].")\n\r ");
			fclose($fh);
		}
		$rewrite = intval(Configuration::get('PS_REWRITING_SETTINGS'));
		$server_host = @$_SERVER['HTTP_HOST'];
		$protocol_link = @$_SERVER['HTTPS'] == "on"?"https://":"http://";
		$default_lang = Language::getIsoById(Configuration::get('PS_LANG_DEFAULT'))."/";
    	// Redirect back to the original URL //
    	if ($this->context->cookie->ld_redirect == 1 && substr($_SERVER['REQUEST_URI'],-strlen('redirected')) == "redirected")
    	{
			$redirect_url = $protocol_link.$server_host.substr($_SERVER['REQUEST_URI'],0,-(strlen('redirected')+1));
			if ($log)
			{
				$myFile = dirname(__FILE__)."/1llog.txt";
				$fh = fopen($myFile, 'a') or die("can't open file");
				fwrite($fh, "1) ".$redirect_url."\n\r ");
				fclose($fh);
			}
			Header( "HTTP/1.1 301 Moved Permanently" );
			Header( "Location: ".$redirect_url );
			exit; 
    	}
    	else if ($this->context->cookie->ld_redirect == 1 && substr($_SERVER['REQUEST_URI'],-strlen('redirected=')) == "redirected=")
    	{
			$redirect_url = $protocol_link.$server_host.substr($_SERVER['REQUEST_URI'],0,-(strlen('redirected=')+1));
			if ($log)
			{
				$myFile = dirname(__FILE__)."/1llog.txt";
				$fh = fopen($myFile, 'a') or die("can't open file");
				fwrite($fh, "2) ".$redirect_url."\n\r ");
				fclose($fh);
			}
			Header( "HTTP/1.1 301 Moved Permanently" );
			Header( "Location: ".$redirect_url );
			exit; 
    	}
		if ($this->context->cookie->ld_redirect == 1 || isset($_GET['redirected']))
		{
			if ($log)
			{
				$myFile = dirname(__FILE__)."/1llog.txt";
				$fh = fopen($myFile, 'a') or die("can't open file");
				fwrite($fh, "No redirct\n\r ");
				fclose($fh);
			}
			return;
		}
		// No Redirection active //
		if (Configuration::get('LD_LANG_ACTIVE') == "2" &&
			Configuration::get('LD_MAP_ACTIVE') == "2" &&
			Configuration::get('LD_CURR_ACTIVE') == "2")
		{
			if ($log)
			{
				$myFile = dirname(__FILE__)."/1llog.txt";
				$fh = fopen($myFile, 'a') or die("can't open file");
				fwrite($fh, "No active redirection\n\r ");
				fclose($fh);
			}
			return;
		}
		// Not first visit //
		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false)
		{
			if ($log)
			{
				$myFile = dirname(__FILE__)."/1llog.txt";
				$fh = fopen($myFile, 'a') or die("can't open file");
				fwrite($fh, "Not first visit\n\r ");
				fclose($fh);
			}
			return;
		}
		// Search Engine or Crawler
		if (stripos($_SERVER['HTTP_USER_AGENT'],'bot') !== false ||
			stripos($_SERVER['HTTP_USER_AGENT'],'baidu') !== false ||
			stripos($_SERVER['HTTP_USER_AGENT'],'spider') !== false ||
			stripos($_SERVER['HTTP_USER_AGENT'],'Ask Jeeves') !== false ||
			stripos($_SERVER['HTTP_USER_AGENT'],'slurp') !== false ||
			stripos($_SERVER['HTTP_USER_AGENT'],'crawl') !== false)
			return;
		include_once('Ip2Country.php');
		$ip2c = new Ip2Country;
		// Added special check to see if SERVER_ADDR is the same as REMOTE_ADDR, that indicates a misconfiguration
		// and means the customer's IP is actuall in 	HTTP_X_FORWARDED_FOR
		$ip2c->load($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']?$_SERVER['REMOTE_ADDR']:$_SERVER['HTTP_X_FORWARDED_FOR']);
		if ($ip2c->country == "RESERVED")
			$ip2c->country = "UNITED STATES";
		$country_iso = $this->_ld_country_to_codes[strtolower($ip2c->country)]['language'];
		$country_name = strtolower($ip2c->country);
		if ($log)
		{
			$myFile = dirname(__FILE__)."/2llog.txt";
			$fh = fopen($myFile, 'a') or die("can't open file");
			fwrite($fh, "!$country_name! -- $country_iso\n\r ");
			fclose($fh);
		}
		$browser_lang_iso = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
		$available_langs = Language::getLanguages();
		$available_currencies = Currency::getCurrencies();
		$currency_change = false;
		$cur_lang = new Language($this->context->cookie->id_lang);
		$no_lang_redirect = false;
		if (Configuration::get('LD_CURR_ACTIVE') != "2")
		{
			foreach ($available_currencies AS $cur)
			{
				if ($log)
				{
					$myFile = dirname(__FILE__)."/2llog.txt";
					$fh = fopen($myFile, 'a') or die("can't open file");
					fwrite($fh, "1) strtolower(".$cur['iso_code'].") == strtolower(".$this->_ld_country_to_codes[strtolower($country_name)]['currency'].") - (!$country_name! -- ".print_r($this->_ld_country_to_codes[strtolower($country_name)],true).") &&	strtolower(".$cur['iso_code'].") != strtolower(".$this->context->currency->iso_code.")\n\r ");
					fclose($fh);
				}
				if (strtolower($cur['iso_code']) == strtolower($this->_ld_country_to_codes[strtolower($country_name)]['currency']))
				{
					if ($log)
					{
						$myFile = dirname(__FILE__)."/2llog.txt";
						$fh = fopen($myFile, 'a') or die("can't open file");
						fwrite($fh, "2) strtolower(".$cur['iso_code'].") != strtolower(".$this->context->currency->iso_code.")\n\r ");
						fclose($fh);
					}
					if (strtolower($cur['iso_code']) == strtolower($this->context->currency->iso_code))
					{
						if ($log)
						{
							$myFile = dirname(__FILE__)."/2llog.txt";
							$fh = fopen($myFile, 'a') or die("can't open file");
							fwrite($fh, "1) No Lang Redirect\n\r ");
							fclose($fh);
						}
						$no_lang_redirect = true;
						break;
					}
					else
					{
						if ($log)
						{
							$myFile = dirname(__FILE__)."/2llog.txt";
							$fh	= fopen($myFile, 'a') or die("can't open file");
							fwrite($fh, "Redirecting currency to (".print_r($cur,true).")\n\r ");
							fclose($fh);
						}
						$this->context->cookie->id_currency = $cur['id_currency'];
						if ($this->getPSV() >= 1.5)
							Tools::setCurrency($this->context->cookie);
						else
							Tools::setCurrency();
						if ($log)
						{
							$myFile = dirname(__FILE__)."/2llog.txt";
							$fh = fopen($myFile, 'a') or die("can't open file");
							fwrite($fh, "1) currency_change\n\r ");
							fclose($fh);
						}
						$currency_change = true;
						break;
					}
				}
			}
			if ($log)
			{
				$myFile = dirname(__FILE__)."/2llog.txt";
				$fh = fopen($myFile, 'a') or die("can't open file");
				fwrite($fh, "!$currency_change && ".$this->context->cookie->id_currency." != $this->_ld_curr_default		&& ".$this->context->currency->id." != $this->_ld_curr_default)\n\r ");
				fclose($fh);
			}
			if (!$currency_change && !$no_lang_redirect && $this->context->cookie->id_currency != $this->_ld_curr_default
				&& $this->context->currency->id != $this->_ld_curr_default)
			{
				$this->context->cookie->id_currency = $this->_ld_curr_default;
				if ($this->getPSV() >= 1.5)
					Tools::setCurrency($this->context->cookie);
				else
					Tools::setCurrency();
				if ($log)
				{
					$myFile = dirname(__FILE__)."/2llog.txt";
					$fh = fopen($myFile, 'a') or die("can't open file");
					fwrite($fh, "2) currency_change\n\r ");
					fclose($fh);
				}
				$currency_change = true;
			}
		}
		$redirect_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		if (strpos($redirect_url, "?") !== false)
			$redirect_url .= "&redirected";
		else
			$redirect_url .= "?redirected";
		if (Configuration::get('LD_LANG_ACTIVE') != "2")
		{
			foreach ($available_langs AS $row)
			{
				if ($log)
				{
					$myFile = dirname(__FILE__)."/1llog.txt";
					$fh = fopen($myFile, 'a') or die("can't open file");
					fwrite($fh, "3) ($cur_lang->iso_code) if (".$this->context->cookie->id_lang." != ".$row['id_lang']." && $this->_ld_browser_first == 2 && ".Configuration::get('PS_LANG_DEFAULT')." != ".$row['id_lang']." && $browser_lang_iso == ".$row['iso_code']." && ".$row['active']." == 1)\n\r ");
					fclose($fh);
				}
				// If Browser Language redirection first,
				if ($this->context->cookie->id_lang != $row['id_lang'] && $this->_ld_browser_first == 2 && Configuration::get('PS_LANG_DEFAULT') != $this->context->cookie->id_lang && $browser_lang_iso == $row['iso_code'] && $row['active'] == 1)
				{
					$this->context->cookie->id_lang = $row['id_lang'];
					$this->context->cookie->ld_redirect = 1;
					if (strpos($redirect_url, __PS_BASE_URI__.$cur_lang->iso_code) !== false)
						$redirect_url = str_replace(__PS_BASE_URI__.$cur_lang->iso_code, __PS_BASE_URI__.$row['iso_code']."/", $redirect_url);
					else
					{
						$redirect_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$row['iso_code'].'/';
						if (strpos($redirect_url, "?") !== false)
							$redirect_url .= "&redirected";
						else
							$redirect_url .= "?redirected";
					}
					$redirect_url = str_replace("//", "/", $redirect_url);
					if ($log)
					{
						$myFile = dirname(__FILE__)."/1llog.txt";
						$fh = fopen($myFile, 'a') or die("can't open file");
						fwrite($fh, "3) ".$redirect_url."  str_replace(".__PS_BASE_URI__.$default_lang.",". __PS_BASE_URI__.$row['iso_code']."/)\n\r ");
						fclose($fh);
					}
					header("Location: http://$redirect_url");
					exit;
				}
				// If IP redirect
				if ($log)
					{
						$myFile = dirname(__FILE__)."/1llog.txt";
						$fh = fopen($myFile, 'a') or die("can't open file");
						fwrite($fh, "4) if (".$this->context->cookie->id_lang." != ".$row['id_lang']." && ".Configuration::get('PS_LANG_DEFAULT')." != ".$row['id_lang']." && $country_iso == ".$row['iso_code']." && ".$row['active']." == 1)\n\r ");
							fclose($fh);
					}
				if ($this->context->cookie->id_lang != $row['id_lang'] && Configuration::get('PS_LANG_DEFAULT') != $this->context->cookie->id_lang && $country_iso == $row['iso_code'] && $row['active'] == 1)
				{
					$this->context->cookie->id_lang = $row['id_lang'];
					$this->context->cookie->ld_redirect = 1;
					if (strpos($redirect_url, __PS_BASE_URI__.$cur_lang->iso_code) !== false)
						$redirect_url = str_replace(__PS_BASE_URI__.$cur_lang->iso_code, __PS_BASE_URI__.$row['iso_code']."/", $redirect_url);
					else
					{
						$redirect_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$row['iso_code'].'/';
						if (strpos($redirect_url, "?") !== false)
							$redirect_url .= "&redirected";
						else
							$redirect_url .= "?redirected";
					}
					$redirect_url = str_replace("//", "/", $redirect_url);
					if ($log)
					{
						$myFile = dirname(__FILE__)."/1llog.txt";
						$fh = fopen($myFile, 'a') or die("can't open file");
						fwrite($fh, "4) ".$row['id_lang']. " -- ".$redirect_url."\n\r ");
						fclose($fh);
					}
					header("Location: http://$redirect_url");
					exit;
				}
			}
			foreach ($available_langs AS $row)
			{
				// Default to Browser lanaguge if not IP redirect.
				if ($this->context->cookie->id_lang != $row['id_lang'] && $this->_ld_browser_first != 2 && Configuration::get('PS_LANG_DEFAULT') != $this->context->cookie->id_lang && $browser_lang_iso == $row['iso_code'] && $row['active'] == 1)
				{
					$this->context->cookie->id_lang = $row['id_lang'];
					$this->context->cookie->ld_redirect = 1;
					if (strpos($redirect_url, __PS_BASE_URI__.$cur_lang->iso_code) !== false)
						$redirect_url = str_replace(__PS_BASE_URI__.$cur_lang->iso_code, __PS_BASE_URI__.$row['iso_code']."/", $redirect_url);
					else
					{
						$redirect_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$row['iso_code'].'/';
						if (strpos($redirect_url, "?") !== false)
							$redirect_url .= "&redirected";
						else
							$redirect_url .= "?redirected";
					}
					$redirect_url = str_replace("//", "/", $redirect_url);
					if ($log)
					{
						$myFile = dirname(__FILE__)."/1llog.txt";
						$fh = fopen($myFile, 'a') or die("can't open file");
						fwrite($fh, "5) $browser_lang_iso - ".$redirect_url."\n\r ");
						fclose($fh);
					}
					header("Location: http://$redirect_url");
					exit;
				}
			}
			
		}
		if (!$currency_change && !$no_lang_redirect && $this->_ld_map_active != 2 &&
			array_key_exists('id',$this->_ld_lang_map) && array_key_exists($this->context->cookie->id_lang, $this->_ld_lang_map['id']) && 
			$this->context->cookie->id_currency != $this->_ld_lang_map['id'][$this->context->cookie->id_lang] &&
			$this->context->currency->id != $this->_ld_lang_map['id'][$this->context->cookie->id_lang] && $this->_ld_lang_map['id'][$this->context->cookie->id_lang] != '')
		{
			$this->context->cookie->id_currency = $this->_ld_lang_map['id'][$this->context->cookie->id_lang];
			if ($this->getPSV() >= 1.5)
				Tools::setCurrency($this->context->cookie);
			else
				Tools::setCurrency();
			$currency_change = true;
			if ($log)
			{
				$myFile = dirname(__FILE__)."/2llog.txt";
				$fh = fopen($myFile, 'a') or die("can't open file");
				fwrite($fh, "3) currency_change\n\r ");
				fclose($fh);
			}
		}
		
		if ($currency_change)
		{
			$this->context->cookie->ld_redirect = 1;
			if ($log)
			{
				$myFile = dirname(__FILE__)."/1llog.txt";
				$fh = fopen($myFile, 'a') or die("can't open file");
				fwrite($fh, "6) ".$redirect_url."\n\r ");
				fclose($fh);
			}
			header("Location: http://$redirect_url");
			exit;
		}
	}
	
	public function hookTop()
	{
		return false;
	}
	
	public function getCurrencyIdByLang($id_lang)
	{
		if (isset($this->_ld_lang_map['id'][$id_lang]))
			return $this->_ld_lang_map['id'][$id_lang];
		return false; 
	}
}
?>