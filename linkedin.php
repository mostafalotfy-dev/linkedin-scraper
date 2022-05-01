<?php 

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/src/functions.php";
use Mostafa\Scraper\Company\Company;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$serverUrl = 'http://localhost:4444'; // chrome driver localhost

// setup chrome driver
$desiredCapabilities = DesiredCapabilities::chrome();

$desiredCapabilities->setCapability('acceptSslCerts', false);

$options = new ChromeOptions();
// $options->addArguments(['-headless']);
$desiredCapabilities->setCapability(ChromeOptions::CAPABILITY, $options);

$crawler = RemoteWebDriver::create($serverUrl, $desiredCapabilities);


login($crawler);
// Wait Until the login is finished
$crawler->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".search-global-typeahead__input")));
$page= 1;

function paginate()
{
    global $crawler,$page;
    $crawler->get(getenv("searchResultsUrl")."&page=$page");
    $links = $crawler->findElements(WebDriverBy::cssSelector(".entity-result__title-text.t-16 .app-aware-link"));
    $links = array_map(function($link){
        return $link->getAttribute("href");
    },$links);
    foreach($links as $link)
    {
        echo $link . "\n";
        new Company($link,$crawler);
        
    }

    echo "Page Number".$page + 1 ."\n";
    
    paginate($page++);
}
paginate();
$crawler->quit();

