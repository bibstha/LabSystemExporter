<?php
include_once('Element.php');

/**
 * Current assumption is a Book is a single html file.
 * Division of Chapters will be done using TOC
 * 
 * Enter description here ...
 * @author bibek
 *
 */
class LSE_Book extends LSE_Element
{
    protected $title;
    protected $authors;
    protected $comment;
    protected $lang;
    
    /**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->title;
    }

	/**
     * @return the $authors
     */
    public function getAuthors()
    {
        return $this->authors;
    }

	/**
     * @return the $comment
     */
    public function getComment()
    {
        return $this->comment;
    }

	/**
     * @return the $lang
     */
    public function getLang()
    {
        return $this->lang;
    }

	public function __construct()
    {
        parent::__construct();
        $this->type = 'book';
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function setAuthors($authors)
    {
        $this->authors = $authors;
    }
    
    public function setComment($comment)
    {
        $this->comment = $comment;
    }
    
    public function setLang($lang)
    {
        $this->lang = $lang;
    }
}
