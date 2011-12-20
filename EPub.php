<?php
error_reporting(E_ALL & ~E_NOTICE);

/**
 * EPub Engine for exporting
 * 
 * @author Bibek Shrestha <bibekshrestha@gmail.com>
 *
 */
require_once('LSE/Decorator.php');
require_once('LSE/Book.php');
require_once('LSE/Element.php');

class LSE_Epub
{
    protected $decorator;
    protected $book;
    
    public function __construct()
    {
        $this->decorator = new LSE_Decorator();
        $this->book = new LSE_Book();
    }
    
    public function save($type, $id, $content, array $options = array())
    {
        if ('l' == $type) {
            $element = new LSE_Book();
            $element->setDecorator($this->decorator);
            
            $element->setTitle( $options['title'] );
            $element->setAuthors( $options['authors'] );
            $element->setLang( $options['lang'] );
            $element->setComment( $options['comment'] );
            $element->setId( $id );
            
            $this->book = $element;
        }
        else {
            $element = new LSE_Element();
            $element->setDecorator($this->decorator);
            
            $element->setType( $type );
            $element->setId( $id );
            $element->setContent( $content );
            $element->setOptions( $options );
            
            $this->book->addElement( $element );
        }
    }
    
    public function render()
    {
        $output = $this->book->render();
        $graph = $this->book->buildGraph(array("l", "C"));
        $elementTable = $this->book->getElementTable(array("l", "C"));
        
        require_once('PHPePub/EPub.php');
        $epub = $this->getEpub();
        $epub->addChapter($this->book->getTitle(), 'Chapter1', $output, false, EPub::EXTERNAL_REF_ADD, 
            '',
            array('graph' => $graph, 'elementTable' => $elementTable)
        );
        $epub->finalize();
        $bookTitle = str_replace(' ', '_', strtolower($this->book->getTitle()));
        $epub->sendBook($bookTitle);
        return NULL;
    }
    
    public function getEpub()
    {
        $book = new EPub();
        
        $book->setTitle($this->book->getTitle());
        $book->setIdentifier("http://ilab.net.in.tum.de/", EPub::IDENTIFIER_URI); // Could also be the ISBN number, prefered for published books, or a UUID.
        $book->setLanguage("en"); // Not needed, but included for the example, Language is mandatory, but EPub defaults to "en". Use RFC3066 Language codes, such as "en", "da", "fr" etc.
        $book->setDescription("This is a brief description\nA test ePub book as an example of building a book in PHP");
        $book->setAuthor($this->book->getAuthors(), $this->book->getAuthors()); 
        $book->setPublisher("Technische Universität München", "http://ilab.net.in.tum.de/"); // I hope this is a non existant address :) 
        $book->setDate(time()); // Strictly not needed as the book date defaults to time().
        $book->setRights("Copyright and licence information specific for the book."); // As this is generated, this _could_ contain the name or licence information of the user who purchased the book, if needed. If this is used that way, the identifier must also be made unique for the book.
        $book->setSourceURL("http://ilab.net.in.tum.de-");
        
        $book->setDocRoot(LSE_PATH_LABSYSTEM);
        return $book;
    }
}