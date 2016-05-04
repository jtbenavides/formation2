<?php
namespace OCFram;

class Direction {
    public static function askRoute($name,$module,$action,$id = 0){

        if (empty($name) || empty($module) || empty($action) || !is_string($name) || !is_string($module) || !is_string($action)){
            return "/home";
        }
        $xml = new \DOMDocument;
        $xml->load('../App/'.$name.'/Config/routes.xml');

        $routes = $xml->getElementsByTagName('route');
        $url = null;

        foreach ($routes as $route)
        {
            if ($route->getAttribute('module') === $module && $route->getAttribute('action') === $action) {
                $url = $route->getAttribute('url');
                break;
            }
        }

        if(is_null($url))
            return null;

        if($id == 0) {
            $url = str_replace('([0-9]+)\\', '', $url);
        }else{
            $url = str_replace('([0-9]+)\\', $id, $url);
        }

        return $url;
    }
}