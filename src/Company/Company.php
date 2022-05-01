<?php

namespace Mostafa\Scraper\Company;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

/**
 * this script is used to scrape compnies from linkedin.com using php-selenuim.
 * the script will go to each page from page index 1 to last page in the web site 
 */
class Company
{
    protected $crawler;
    private $url;
    protected $fh;
   
    public function __construct($url, $crawler)
    {
        $this->url  = $url;

        $this->fh = fopen("companies.csv", "a");
        
        $this->crawler = $crawler;

        // $this->index = @file_get_contents("page");
        // go to first page
        $this->crawler->get($this->url);
        $this->waitUntilElementIsClickable(WebDriverBy::cssSelector(".lazy-image.ember-view.org-top-card-primary-content__logo"));

        $extractedData =  $this->extract();
     
        fputcsv($this->fh, $extractedData);
        // $this->crawler->quit();
    }


    // extract each element
    public function extract()
    {
        
        $this->waitUntilElementIsClickable(WebDriverBy::cssSelector(".t-16.t-bold.t-black--light.org-page-navigation__item-anchor.ember-view.pv3.ph4"));
        $data = [
            "url" =>$this->url,
            "image" => $this->getImage(),
            "title" => $this->getTitle(),
            "Description" => $this->getDescription(),
            "Description / Location / Followers No." => $this->getType(),
        ];
        $this->gotoAboutPage();
        $data["Website - Phone Number"] = $this->getWebsite();
        $data["Industry"] = $this->getIndustry();
        $data["aboutUsDescription"] = $this->getAboutUs();

        // file_put_contents("page",$this->index);
        return $data;
    }
    public function getImage()
    {

        $image =  $this->find(".lazy-image.ember-view.org-top-card-primary-content__logo");

        if ($image && $image->isDisplayed()) {
            return $image->getAttribute("src");
        }
        return "";
    }
    public function getAboutUs()
    {
        $aboutUs = $this->find(".break-words.white-space-pre-wrap.mb5.text-body-small.t-black--light");
        return $aboutUs && $aboutUs->isDisplayed() ? $aboutUs->getText() : "";
    }
    public function find($by)
    {
        return $this->crawler->findElement(WebDriverBy::cssSelector($by));
    }
    public function getTitle()
    {
        $title = $this->find("h1.t-24.t-black.t-bold.full-width");
        return $title && $title->isDisplayed() ? $title->getAttribute("title") : "";
    }
    public function getType()
    {
        $metadata = $this->findAll("div.org-top-card-summary-info-list__info-item");
        $metadata =  array_map(function ($meta) {
            return $meta->getText();
        }, $metadata);
        return implode(",", $metadata);
    }
    public function getWebsite()
    {
        try{
            $this->waitUntilElementIsClickable(WebDriverBy::cssSelector(".ember-view .t-black--light span.link-without-visited-state"));
            $companyData = $this->findAll(".link-without-visited-state.ember-view");
            $companyData = array_map(function ($companyData) {
                return $companyData->getText();
            }, $companyData);
            return implode("-", $companyData);
        }catch(\Exception $e)
        {
            echo "unable to locate element website\n";
            return "";
        }
        
     
        
    }

    public function getIndustry()
    {
        
        $industry = $this->find(".org-grid__content-height-enforcer .mb4.text-body-small.t-black--light");
        return $industry->getText();
    }
    public function findAll($by)
    {
        return $this->crawler->findElements(WebDriverBy::cssSelector($by));
    }
    public function gotoAboutPage()
    {
        $this->find(".org-page-navigation__item:nth-child(2) a")->click();
    }

    public function waitUntilElementIsClickable(WebDriverBy $by)
    {
        $this->crawler->wait()->until(WebDriverExpectedCondition::elementToBeClickable($by));
    }
    public function getDescription()
    {
        try{
            $description = $this->find("p.org-top-card-summary__tagline.t-16.t-black");
            return  $description->getText() ;
        }catch(\Exception $e)
        {
            echo "couldn't find a description\n";
            return "";
        }
        
        
    }
}
