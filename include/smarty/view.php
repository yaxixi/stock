<?php

    if(!isset($_SESSION))
        session_start();
    require_once("Smarty.class.php");
    class ViewTemplates extends Smarty
    {
		public $data_view;

		public $data_cache_id;

        function  ViewTemplates()
        {



            $this->Smarty();

            $this->template_dir = CORE_DIR ."/view";
            $this->compile_dir  = CACHE_DIR."/template_compile";
            $this->cache_dir    = CACHE_DIR."/template_cache";
	
//	echo $this->template_dir."\n".$this->compile_dir."\n".$this->cache_dir."\n";

            $this->caching = false;
            $this->cache_lifetime = 300;//5 min

            $this->compile_check = true;
            $this->debugging = false;
            $this->left_delimiter = "<{";
            $this->right_delimiter = "}>";
  			$this->assign('site_url', SITE_URL);


        }
}
?>
