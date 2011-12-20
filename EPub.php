<?php
error_reporting(E_ALL & ~E_NOTICE);

/**
 * EPub Engine for exporting
 * 
 * @author Bibek Shrestha <bibekshrestha@
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
        print_r($graph);
        exit(0);
        
        require_once('PHPePub/EPub.php');
        $epub = $this->getEpub();

        $epub->addChapter($this->book->getTitle(), 'Chapter1', $output, false, EPub::EXTERNAL_REF_IGNORE, "", 
            array('graph' => $graph, 'elementTable' => $elementTable)
        );
        $epub->finalize();
        file_put_contents('/tmp/test.epub', $epub->sendBook("Example1Book"));
        return NULL;
    }
    
    public function getEpub()
    {
        $book = new EPub();
        
        $book->setTitle("Test book");
        $book->setIdentifier("http://JohnJaneDoePublications.com/books/TestBook.html", EPub::IDENTIFIER_URI); // Could also be the ISBN number, prefered for published books, or a UUID.
        $book->setLanguage("en"); // Not needed, but included for the example, Language is mandatory, but EPub defaults to "en". Use RFC3066 Language codes, such as "en", "da", "fr" etc.
        $book->setDescription("This is a brief description\nA test ePub book as an example of building a book in PHP");
        $book->setAuthor("John Doe Johnson", "Johnson, John Doe"); 
        $book->setPublisher("John and Jane Doe Publications", "http://JohnJaneDoePublications.com/"); // I hope this is a non existant address :) 
        $book->setDate(time()); // Strictly not needed as the book date defaults to time().
        $book->setRights("Copyright and licence information specific for the book."); // As this is generated, this _could_ contain the name or licence information of the user who purchased the book, if needed. If this is used that way, the identifier must also be made unique for the book.
        $book->setSourceURL("http://JohnJaneDoePublications.com/books/TestBook.html");
        
        // $cssData = "body {\n  margin-left: .5em;\n  margin-right: .5em;\n  text-align: justify;\n}\n\np {\n  font-family: serif;\n  font-size: 10pt;\n  text-align: justify;\n  text-indent: 1em;\n  margin-top: 0px;\n  margin-bottom: 1ex;\n}\n\nh1, h2 {\n  font-family: sans-serif;\n  font-style: italic;\n  text-align: center;\n  background-color: #6b879c;\n  color: white;\n  width: 100%;\n}\n\nh1 {\n    margin-bottom: 2px;\n}\n\nh2 {\n    margin-top: -2px;\n    margin-bottom: 2px;\n}\n";

        $cssData = file_get_contents(dirname(__FILE__) . '/files/style.css');
        $book->addCSSFile("styles.css", "css1", $cssData);

        return $book;
    }
}