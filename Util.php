<?php
class LSE_Util
{
    /**
     * Decodes id into sub parts
     */
    public static function getIdParts($string)
    {
        $separator = ".";
        return explode($separator, $string);
    }
    
    public static function getTypeFromPart($string)
    {
        if (strlen($string) < 2) {
            return $string;
        }
        
        return substr($string, 0, 1);
    }
    
    public static function checkParentType($fullId, $parentType)
    {
        $parts = self::getIdParts($fullId);
        if (($numOfParts = count($parts)) < 2) return false;
        
        $parentId = $parts[ $numOfParts - 2 ];
        $parentType = self::getTypeFromPart($parentId);
        
        return $parentType;
    }
    
    public static function filterPTag($string)
    {
        $domDoc = new DOMDocument();
        $domDoc->loadHTML($string);
        
        return self::get_inner_html($domDoc->documentElement->firstChild);
    }
    
    public static function get_inner_html( $node )
    {
        $innerHTML= '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveXML( $child );
        }
    
        return $innerHTML;
    } 
}