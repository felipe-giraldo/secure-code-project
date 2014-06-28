<?php  
  if (!defined("_VALID_PHP"))
      die('Direct access to this location is not allowed.');
?>
<?php //error_reporting(E_ALL);
  
  $BASEPATH = str_replace("init.php", "", realpath(__FILE__));
  
  define("BASEPATH", $BASEPATH);
  
  $configFile = BASEPATH . "lib/config.ini.php";
  if (file_exists($configFile)) {
      require_once($configFile);
  } else {
      header("Location: setup/");
  }
  
  require_once(BASEPATH . "lib/class_db.php");
  
  require_once(BASEPATH . "lib/class_registry.php");
  Registry::set('Database',new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE));
  $db = Registry::get("Database");
  $db->connect();
  
  //Include Functions
  require_once(BASEPATH . "lib/functions.php");
  
  require_once(BASEPATH . "lib/class_filter.php");
  $request = new Filter();
	
  //Start Core Class 
  require_once(BASEPATH . "lib/class_core.php");
  Registry::set('Core',new Core());
  $core = Registry::get("Core");

  //Start Paginator Class 
  require_once(BASEPATH . "lib/class_paginate.php");
  $pager = Paginator::instance();
  
  //StartUser Class 
  require_once(BASEPATH . "lib/class_user.php");
  Registry::set('Users',new Users());
  $user = Registry::get("Users");
   
  define("SITEURL", $core->site_url);
  define("ADMINURL", $core->site_url."/admin");
  define("UPLOADS", BASEPATH."uploads/");
  define("UPLOADURL", SITEURL."/uploads/");
  
  define('AUTH_KEY',         '~oD~~SIFc)0B%P)hE@}2`$ ]H$g)M+:H|%[Ev+f}Fd<^:&]jM?B`j4smHpj{cc}X');
  define('SECURE_AUTH_KEY',  'hU=bG8l-Y@h8>)%^82@Ar+!++8tR=52;ti5-J_r DBUzg_ Ozs{XGpq8u] H3RKi');
  define('LOGGED_IN_KEY',    'l]xU~L:>sCs-U/$b8E~onM.mS,oS[[oVQ9c|vX=:SGt>j]mxU[F?eGF.)^%]jU7d');
  define('NONCE_KEY',        '6nbOgr-fX6Cf=Jqu3yO`[?YMKo$4+liek4:sHo[8okYW}&J9hH^5L~JGyCumL.Kd');
  define('AUTH_SALT',        'qF[%(TEe4!,i5<?06]u1rX fDiX<Ixbe!}]vQOe8<cjHz+#k)`dSCeh tJ&hKSs<');
  define('SECURE_AUTH_SALT', '{NH|)!D|k4 pIoT6La)U.u+vs810?)rWx&+OZVHkng^32IVqjqcWdRu5^-|bU|`~');
  define('LOGGED_IN_SALT',   'q(::|2JEE c+BNBxxG|]o(*]3h24+?EkkFvP=U7|+e6QS/=cQH5T-Efe]`-S9z2w');
  define('NONCE_SALT',       '~l<8agXI}CPTVI29w[n;K9AUMm- GQ(Edc6Fu<9vSL-<U7[,#Z3s+{nMAhMD6&a.');
?>