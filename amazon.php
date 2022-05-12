<?php 

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/src/functions.php";
use Scraper\Scraper\Place;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;


$serverUrl = 'http://localhost:4444'; // chrome driver localhost

// setup chrome driver
$desiredCapabilities = DesiredCapabilities::chrome();

$desiredCapabilities->setCapability('acceptSslCerts', false);

$options = new ChromeOptions();
$options->addArguments([
    '-headless',
    // "-useragent=fire"
]);
$desiredCapabilities->setCapability(ChromeOptions::CAPABILITY, $options);

$crawler = RemoteWebDriver::create($serverUrl, $desiredCapabilities);

// $crawler->get(getenv("searchResultsUrl"));
// login($crawler);
// Wait Until the login is finished

$page= 1;

function paginate()
{
    global $crawler,$page;
    
    $links = new Place(getenv("amazonSearchResults")."&page=$page",$crawler);
    // $links = $links->extract();
    foreach($links->links(".s-product-image-container.aok-relative.s-image-overlay-grey.s-text-center.s-padding-left-small.s-padding-right-small.s-flex-expand-height a")["links"] as $link)
    {
      echo "Downloading https://amazon.com/$link"; 
      $scraper =  new Place("https://amazon.com".$link,$crawler,[
            "title"=>[
                "css"=>"#productTitle",
                "all"=>false,
                "text"=>true,
            ],
            "description"=>[
                "css"=>".a-expander-collapsed-height.a-row.a-expander-container.a-spacing-base a-expander-partial-collapse-container p",
                "all"=>false,
                "text"=>true
            ],
            "author"=>[
                "css"=>"[data-asin='B00459IA54']",
                "all"=>false,
                "text"=>true,
            ],
            "ratings"=>[
                "css"=>"#acrCustomerReviewText",
                "all"=>false,
                "text"=>true
            ],
            "isbn-13"=>[
                "css"=>"#detailBullets_feature_div li:nth-child(5) span + span",
                "text"=>true,
                "all"=>false,
            ],
            "publisher"=>[
                "css"=>"#detailBullets_feature_div li:nth-child(1) span + span",
                "text"=>true,
                "all"=>false,
            ],
            "pages no."=>[
                "css"=>"#detailBullets_feature_div li:nth-child(3) span + span",
                "text"=>true,
                "all"=>false,
            ]

            ]);
            $fh = fopen(getenv("amazonOutputfile").".csv","a");
            $extractedData = $scraper->extract();
            print_r($extractedData);
            fputcsv($fh,$extractedData);
            fclose($fh);
    }
    $page++;
    paginate();
}
paginate();
$crawler->quit();

