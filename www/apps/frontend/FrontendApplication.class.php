<?php
    require_once dirname(__FILE__).'/../../lib/autoload.php';
    
    class FrontendApplication extends Application
    {
        public function __construct()
        {
            parent::__construct();
            
            $this->setName('frontend');
        }
        
        public function run()
        {
            $router = new Router($this);
            
            $controller = $router->getController();
            $controller->page()->addFileToInclude(dirname(__FILE__).'/lang/'.$this->user()->getAttribute('lang').'.php');
            $controller->execute();
            
            $this->httpResponse()->setPage($controller->page());
            $this->httpResponse()->send();
        }
    } 
?>