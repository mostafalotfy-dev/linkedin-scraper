<?php 


namespace Scraper\Scraper;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

 abstract class Scraper {
    protected  $crawler;
    private $url;
    protected $fh;
    protected $elements;
    public function __construct($url, $crawler,$elements = [])
    {
        $this->url  = $url;

        // $this->fh = fopen(getenv("outputfile").".csv", "a");
        $this->elements = $elements;   
        $this->crawler = $crawler;

     
        // go to first page
        
        $this->crawler->wait(10);
        $this->crawler->get($this->url);
       
        $this->extract();
      
    }
    public function extract(){
       $elements = array_map(function($element)
        {
            $el = null;
            extract($element);
            if(!isset($css))
            {
                $css = "";
            }
            if(!isset($all))
            {
                $all = "";
            }
            if(!isset($text)){
                $text = "";
            }
            if(!isset($attribute))
            {
                $attribute = "";
            }
            $this->waitUntilElementIsClickable(WebDriverBy::cssSelector($css));

            if($all)
            {
              $el = $this->findAll($css);
            }else{
                $el = $this->find($css);
            }
            if($text && $all)
            {
                $el =  array_map(function($el){
                    return $el->getText();
                },$el);
                $el = implode(",",$el);
            }elseif($text && !$all)
            {
                $el = $el->getText();
            }
            if($attribute && $all)
            {
                $el = array_map(function($el) use($attribute){
                    return $el->getAttribute($attribute);
                },$el);
            }elseif ($attribute && !$all){
                $el = $el->getAttribute($attribute);
            }
           
            return $el;
        },$this->elements);
        // print_r($elements);
        return $elements;
    }
    public function links($css)
    {
        $this->waitUntilElementIsClickable(WebDriverBy::cssSelector($css));
        $this->elements = [
            "links"=>[
                "css"=>$css,
                "all"=>true,
                "attribute"=>"href",
            ],
        ];
        return $this->extract();
    }
    public function find($by)
    {
        return $this->crawler->findElement(WebDriverBy::cssSelector($by));
    }
    public function findByXpath($by)
    {
        return $this->crawler->findElement(WebDriverBy::xpath($by));
    }
    public function findAll($by)
    {
        return $this->crawler->findElements(WebDriverBy::cssSelector($by));
    }
    public function waitUntilElementIsClickable(WebDriverBy $by)
    {
        $this->crawler->wait()->until(WebDriverExpectedCondition::elementToBeClickable($by));
    }
    public function click($css)
    {
        $this->find($css)->click();
    }
    
 
}