<?php
/**
 * A Bridge class between Labsystem to PHPePUB
 *
 * Architecturally, the class behaves as a Controller in an MVC patter. It uses LSE_Element objects as Models
 * to store the data passed through the save function. It uses an instance of Decorator to generate output
 * required.
 * 
 * Responsibilities
 * - Models : Save the rendered html string
 * - Decorator (View) : Put extra elements around it make it suitable for output
 * - PHPePUB : Class which stores the output
 *
 * @author Bibek Shrestha <bibekshrestha@gmail.com>
 * @todo Call PHPePUB class structures
 */

set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(INCLUDE_DIR . "/../plugins")
)));

include_once('LSE/EPub.php');

class LSE_Exporter
{
    static protected $instance;
    protected $filename;
    protected $exportEngine;
    
    /**
     * behaves as a Registry for one single instance of the object
     */
    public static function getInstance()
    {
        if ( self::$instance == null ) {
            self::$instance = new LSE_Exporter();
        }
        return self::$instance;
    }
    
    public static function removeInstance()
    {
        self::$instance = null;
    }
    
    public function __construct()
    {
        // assumption is, we could have other type of exporters in future
        $this->exportEngine = new LSE_EPub();
    }
    
    /**
     * Delegates the save task to ExportEngine
     * 
     * @param string $type
     * @param string $id
     * @param string $content
     * @param array $options
     */
    function save($type, $id, $content, array $options = array())
    {
        $this->exportEngine->save($type, $id, $content, $options);
    }
    
    public function render()
    {
        return $this->exportEngine->render();
    }
}
