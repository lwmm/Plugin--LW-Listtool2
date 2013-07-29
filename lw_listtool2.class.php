<?php

class lw_listtool2 extends lw_plugin
{
    public function __construct()
    {
        parent::__construct();
        spl_autoload_register(array($this, 'loader'));
    }
   
    private function loader($className) 
    {
        if (strstr($className, 'LWddd')) {
            $config = \lw_registry::getInstance()->getEntry('config');
            $path = $this->config['plugin_path']['lw'].'lw_ddd';
            $filename = str_replace('LWddd', $path, $className);
        }
        elseif (strstr($className, 'LWmvc')) {
            $config = \lw_registry::getInstance()->getEntry('config');
            $path = $this->config['plugin_path']['lw'].'lw_mvc';
            $filename = str_replace('LWmvc', $path, $className);
        }
        elseif (strstr($className, 'LwI18n')) {
            $config = \lw_registry::getInstance()->getEntry('config');
            $path = $this->config['plugin_path']['lw'].'lw_i18n';
            $filename = str_replace('LwI18n', $path, $className);
        }
        else {
            $className = str_replace("Factory", "", $className);
            $filename = $this->config['path']['package'].$className;
        }
        $filename = str_replace('\\', '/', $filename).'.php';
        
        if (is_file($filename)) {
            include_once($filename);
        }
    }    
    
    public function deleteEntry()
    {
        $response = $this->executeControllerAction('LwListtool', 'ContentoryBackend', 'deleteList', $this->request->getInt('oid'));
        return true;
    }    
    
    public function getOutput()
    {
        if ($this->request->getAlnum("pcmd")) {
            $cmd = $this->request->getAlnum("pcmd");
        }
        else {
            $cmd = 'showForm';
        }
        $response = $this->executeControllerAction('LwListtool', 'ContentoryBackend', $cmd, $this->request->getInt('oid'));
        
        if ($response->getParameterByKey('reloadParent') == 1) {
            die('<script>parent.location.reload();</script>');
        }
        if ($response->getParameterByKey('cmd')) {
            $url = $this->config['url']['client']."admin.php?obj=content&cmd=open&oid=".$this->request->getInt('oid');
            $this->pageReload($url);
        }
        elseif ($response->getParameterByKey('pcmd')) {
            $url = $this->config['url']['client']."admin.php?obj=content&cmd=open&oid=".$this->request->getInt('oid')."&pcmd=".$response->getParameterByKey('pcmd')."&ltid=".$this->request->getInt('oid');
            $this->pageReload($url);
        }
        else {
            if ($response->getParameterByKey('die') == 1){
                die($response->getOutputByKey('output'));
            }
            return $response->getOutputByKey('output');
        } 
    }    
    
    protected function executeControllerAction($package, $controller, $cmd, $oid)
    {
        $bootstrapClass = "\\".$package."\\Controller\\Bootstrap";
        if (class_exists($bootstrapClass, true)) {
            $bootstrap = new $bootstrapClass();
            $bootstrap->execute();
        }
        $ControllerClass = "\\".$package."\\Controller\\".$controller;
        $Controller = new $ControllerClass($cmd, $oid);
        return $Controller->execute();
    }
    
    public function buildPageOutput()
    {
        if ($this->request->getAlnum("cmd")) {
            $cmd = $this->request->getAlnum("cmd");
        }
        else {
            $cmd = 'showList';
        }
        $response = $this->executeControllerAction('LwListtool', 'Frontend', $cmd, $this->params['oid']);
        
        if ($response->getParameterByKey('reloadParent') == 1) {
            die('<script>parent.location.reload();</script>');
        }
        if ($response->getParameterByKey('cmd')) {
            $url = lw_page::getInstance()->getUrl($response->getParameterArray());
            $this->pageReload($url);
        }
        else {
            if ($response->getParameterByKey('die') == 1){
                die($response->getOutputByKey('output'));
            }
            return $response->getOutputByKey('output');
        }        
    }
}