<?php

require_once('LSE/Util.php');

class LSE_Decorator
{
    /**
     * Generates string from the element
     * 
     * @param type $type
     * @param output $content
     * @param LSE_Element $element
     */
    public function decorate($type, $content, $element)
    {
        switch ($type) {
            case 'book':
                return $this->decorateBook($content, $element);
                
            case 'BC':
                return $this->decorateBigC($content, $element);
                
            case 'Lc':
                return $this->decorateLowC($content, $element);
            
            case 'Lp':
                return $this->decorateLowP($content, $element);
                
            case 'Lm':
                return $this->decorateLowM($content, $element);
                
            case 'Li':
                return $this->decorateLowI($content, $element);
                
            default:
                return $this->decorateDefault($content, $element);
        }
    }
    
    public function decorateDefault($content, $element)
    {
        // print("<pre>============================================================\n");
        // var_dump($element);
        // print("===========================================================</pre>");
        return $content;
    }
    
    public function decorateBook($content, $element)
    {
        $content_start =
"<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
. "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n"
. "    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"
. "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n"
. "<head>"
. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n"
. "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" />\n"
. "<title>Test Book</title>\n"
. "</head>\n"
. "<body>\n";
        
        $bookEnd = "</body>\n</html>\n";
        
        $this->decorateDefault($content, $element);
        return $content_start . "\n" . $content . "\n" . $bookEnd;
    }
    
    public function decorateBigC($content, $element)
    {
        $this->decorateDefault($content, $element);
        // Do nothing since we will have this element in Lc as well
    }
    
    public function decorateLowC($content, $element)
    {
        $template = "<h3 class='section' id='%s'>%s</h3>\n";
        // $template .= "<div class='collection_content'>%s</div>\n";
        $this->decorateDefault($content, $element);
        return sprintf($template, $element->getId(), htmlentities($element->getOption('title')));
    }
    
    public function decorateLowP($content, $element)
    {
        // $template = "<h3 class='section' id='%s'>%s</h3>\n";
        $template = "<div class='collection_content' id='%s'>%s</div>\n";
        $this->decorateDefault($content, $element);
        return sprintf($template, $element->getId(), LSE_Util::filterPTag($element->getContent()));
    }
    
    public function decorateLowI($content, $element)
    {
        $template = "<h3 class='section' id='%s'>%s</h3>\n";
        $template .= "<div class='collection_content'>%s</div>\n";
        $this->decorateDefault($content, $element);
        return sprintf($template, $element->getId(), htmlentities($element->getOption('title')), 
            $element->getContent());
    }
    
    public function decorateLowM($content, $element)
    {
        // $template = "<h3 class='section' id='%s'>%s</h3>\n";
        $template = "<div class='collection_content' id='%s'>%s</div>\n";
        $this->decorateDefault($content, $element);
        return sprintf($template, $element->getId(), $element->getOption('question'), 
            $element->getOption('answerArray'));
    }
}
