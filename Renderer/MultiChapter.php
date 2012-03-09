<?php
require_once('LSE/Renderer/Interface.php');
require_once('LSE/Logger.php');
class LSE_Renderer_MultiChapter implements LSE_Renderer_Interface
{
    protected $_log;
    protected $_engine;
    protected $_plugin;
    
    public function __construct(LSE_Engine $engine)
    {
        $this->_log = new LSE_Logger('LSE_Renderer_MultiChapter');
        $this->_engine = $engine;
        $this->_plugin = $engine->getEpub();
    }
    
    public function render()
    {
        $this->_setupCoverImage();
        $this->_setupMultiChapterTOC();
        $this->_setupPreface();
        $this->_setupChapters();
        $this->_finalize();
    }
    
    protected function _setupCoverImage()
    {
        // $this->_log->log($mixed, $name)
        $coverImage = $this->_engine->getBook()->getCoverImage();
        if ($coverImage) {
            $this->_plugin->addChapter( 'coverImage', 'coverImage.html', $coverImage, FALSE, EPub::EXTERNAL_REF_ADD);
        }
    }
    
    protected function _setupMultiChapterTOC()
    {
        $book = $this->_engine->getBook();
        $graph = $book->buildGraph(array("l"));
        $elementTable = $book->getElementTable();
        
        // Add a TOC in each chapter in graph
        foreach ($graph as $key => $lElement) {
            $graph[$key] = array("toc" => array()) + $lElement;
        }
        $elementTable += array('toc' => array('toc', 'Table of Contents'));
        
        $graphPrefix = array();
        $elementTablePrefix = array();
        if ($book->getCoverImage()) {
            $graphPrefix['coverImage'] = array();
            $elementTablePrefix['coverImage'] = array('coverImage', 'Cover');
        }
        
        $graphPrefix['multiChapterTocPage'] = array();
        $elementTablePrefix['multiChapterTocPage'] = array('multiChapterTocPage', 'Table of Contents');
        
        if ($book->getPreface()) {
            $graphPrefix['preface'] = array();
            $elementTablePrefix['preface'] = array('preface', 'Preface');
        }
        $graph = $graphPrefix + $graph;
        $elementTable = $elementTablePrefix + $elementTable;
        
//        $this->_log->log($graph);
//        $this->_log->log($elementTable);
        
        $this->_plugin->setNcxFromGraph($graph, $elementTable);
        
        $view = new SPT_View();
        $view->assign(array('graph' => $graph, 'elementTable' => $elementTable));
        $tocChapter = $view->render(LSE_ROOT . '/templates/multichapter/toc.phtml', true);
        
        $this->_plugin->addChapter( 'multiChapterTocPage', 'multiChapterTocPage.html', $tocChapter, FALSE, 
            EPub::EXTERNAL_REF_ADD);
    }
    
    protected function _setupPreface()
    {
        $preface = $this->_engine->getBook()->getPreface();
        if ($preface) {
            $view = new SPT_View();
            $view->assign(array('preface' => $preface));
            $prefaceContent = $view->render(LSE_ROOT . '/templates/multichapter/preface.phtml', true);
            $this->_plugin->addChapter( 'preface', 'preface.html', $prefaceContent, FALSE, EPub::EXTERNAL_REF_ADD);
        }
    }
    
    protected function _setupChapters()
    {
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