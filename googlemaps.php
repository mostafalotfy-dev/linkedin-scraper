<?php

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/src/functions.php";


use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;

use Facebook\WebDriver\Remote\DesiredCapabilities;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Scraper\Scraper\Place;

$serverUrl = 'http://localhost:4444'; // chrome driver localhost

// setup chrome driver
$desiredCapabilities = DesiredCapabilities::chrome();

$desiredCapabilities->setCapability('acceptSslCerts', false);

$options = new ChromeOptions();
$options->addArguments([
    // '-headless',
    "-useragent=edg",

]);
$desiredCapabilities->setCapability(ChromeOptions::CAPABILITY, $options);

$crawler = RemoteWebDriver::create($serverUrl, $desiredCapabilities);
$crawler->manage()->window()->setSize(new WebDriverDimension(1920, 1080));

$page = 1;
$data = [];

$fh = fopen("googlemaps.csv", "a");

function getTitle()
{
    global $crawler;
    $crawler->wait(60)->until(WebDriverExpectedCondition::invisibilityOfElementLocated(WebDriverBy::cssSelector(".a8kjDe")));
    $crawler->wait(60)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector(".DUwDvf.fontHeadlineLarge")));
    echo "Getting Title\n";
    return $crawler->findElement(WebDriverBy::cssSelector(".DUwDvf.fontHeadlineLarge"))->getText();
}
function getDetails()
{
    global $crawler;
    $crawler->wait()->until(WebDriverExpectedCondition::invisibilityOfElementLocated(WebDriverBy::cssSelector(".a8kjDe")));
    $details = $crawler->findElements(WebDriverBy::cssSelector(".Io6YTe.fontBodyMedium"));
    echo "Getting Details\n";
    $details = array_map(function ($details) {
        return $details->getText();
    }, $details);
    return $details;
}
function getAddress1(){
    global $crawler;
    $crawler->wait(60)->until(WebDriverExpectedCondition::invisibilityOfElementLocated(WebDriverBy::cssSelector(".a8kjDe")));
    $crawler->wait(60)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector(".rogA2c .Io6YTe.fontBodyMedium")));
    $address1 = $crawler->findElement(WebDriverBy::cssSelector(".rogA2c .Io6YTe.fontBodyMedium"));

    if($address1->isDisplayed())
    {
        echo "Downloading Address " . $address1->getText()."\n";
        return $address1->getText();
    }
    return "";
}

function paginate()
{
    global $crawler, $place, $data;

    $crawler->wait(60)->until(WebDriverExpectedCondition::invisibilityOfElementLocated(WebDriverBy::cssSelector(".a8kjDe")));
    $links = $crawler->findElements(WebDriverBy::cssSelector(".hfpxzc"));

    foreach ($links as $link) {

        try{
            //scroll down
            $crawler->getMouse()->mouseDown($link->getCoordinates());
            $data[] = $link->getAttribute("href");
        }catch(Facebook\WebDriver\Exception\ElementClickInterceptedException $e){

        }
            
          
               
    }
    print_r($data);
    try{
        $crawler->wait(60)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector("#ppdPk-Ej1Yeb-LgbsSe-tJiF1e")));
        $place->find("#ppdPk-Ej1Yeb-LgbsSe-tJiF1e")->click();
        $crawler->wait(30)->until(WebDriverExpectedCondition::invisibilityOfElementLocated(WebDriverBy::cssSelector(".a8kjDe.noprint.IeJeYc")));
        $crawler->wait(30)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector(".hfpxzc")));
    }catch(Facebook\WebDriver\Exception\ElementClickInterceptedException $e)
    {
       
    }
  
    paginate();
   

  
}
$urls = file_get_contents("googlemapsurls.txt");
$urls = explode("\r\n",$urls);
foreach($urls as $url)
{
    $place =  new Place($url, $crawler);
        try{
            paginate();
        }catch(\Exception $e){
            array_walk($data,function($data) use($crawler,$fh){
                $crawler->get($data);
                $details = getDetails();
                print_r($details);
                $collected =  [
                    "url"=> $data,
                    "title"=> getTitle(),
                    "data"=>implode("-",$details)
                ];
                fputcsv($fh,$collected);
            });
        }
        
    
}
 


$crawler->quit();
