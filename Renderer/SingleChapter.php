<?php
require_once('LSE/Renderer/Interface.php');
require_once('LSE/Logger.php');
class LSE_Renderer_SingleChapter implements LSE_Renderer_Interface
{
    protected $_log;
    protected $_engine;
    protected $_plugin;
    
    public function __construct(LSE_Engine $engine)
    {
        $this->_log = new LSE_Logger('LSE_Renderer_SingleChapter');
        $this->_engine = $engine;
        $this->_plugin = $engine->getEpub();
    }
    
    public function render()
    {
        $this->_setupTOC();
        $this->_setupChapters();
        $this->_finalize();
    }
    
    protected function _setupTOC()
    {
        $book = $this->_engine->getBook();
        $graph = $book->buildGraph(array("l", "C"));
        $elementTable = $book->getElementTable();
        
        $this->_plugin->setNcxFromGraph($graph, $elementTable);
    }
    
    protected function _setupChapters()
    {
        // we have only one chapter but the same function should work on both single and multi chapter
        $book = $this->_engine->getBook();
        $chapters = $book->getChapters();
        $output = '';
        foreach ($chapters as $chapterId => $chapter) {
            $output = $book->renderChapter($chapterId);
            
            if (LSE_DEBUG) {
                print $output;
            }
            else {
                $this->_plugin->addChapter( $chapterId, $chapterId . '.html', $output, FALSE, EPub::EXTERNAL_REF_ADD);
            }
        }
    }
    
    protected function _finalize()
    {
        $isFinalized = $this->_plugin->finalize();
        $this->_log->log($isFinalized, 'isFinalized');

        $bookTitle = str_replace(' ', '_', strtolower($this->_engine->getBook()->getTitle()));
            
            // bookTitle is usually htmlencoded, so decode this first
        $bookTitle = LSE_Util::filterPTag($bookTitle);
        if (!LSE_DEBUG) {
            $this->_plugin->sendBook($this->_engine->getBook()->getTitle());
        }

        return NULL;
    }
}