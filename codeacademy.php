<?php 

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/src/functions.php";
use Scraper\Scraper\Scraper;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;


$serverUrl = 'http://localhost:4444'; // chrome driver localhost

// setup chrome driver
$desiredCapabilities = DesiredCapabilities::chrome();

$desiredCapabilities->setCapability('acceptSslCerts', false);

$options = new ChromeOptions();
$options->addArguments([
    // '-headless',
    "-useragent=edg"
]);
$desiredCapabilities->setCapability(ChromeOptions::CAPABILITY, $options);

$crawler = RemoteWebDriver::create($serverUrl, $desiredCapabilities);

// $crawler->get(getenv("codeacademySearchResults"));
// login($crawler);
// Wait Until the login is finished

$page= 1;

function paginate()
{
    global $crawler;
    
    $links = new Scraper(getenv("codeacademySearchResults"),$crawler);
    // $links = $links->extract();
    foreach($links->extract(".e14vpv2g1.gamut-1ud6i3e-ResetElement-Anchor-AnchorBase.e1bhhzie0")["links"] as $link)
    {
      
      $scraper =  new Scraper("https://codeacademy.com/".$link,$crawler,[
            "tags"=>[
                "css"=>".highlight__3SuMvaTuQpvw3hjCRhVyKP",
                "all"=>true,
                "text"=>true,
            ],
            "experience"=>[
                "css"=>".content__1XzDPbG1jNFucmYX72IzLB .highlight__3SuMvaTuQpvw3hjCRhVyKP",
                "all"=>true,
                "text"=>true,
                
            ],
            "title"=>[
                "css"=>"h1.title__1PSKSbrA1yvrVuIID5Q55I",
                "all"=>false,
                "text"=>true,
            ],
            "description"=>[
                "css"=>".description__Np92NobnlqqdNfnYKQQuU",
                "all"=>false,
                "text"=>true,
    
            ],
            "learning"=>[
                "css"=>"p.supportingPointDescription__1HEyXjpjU79yG22ppMcgjV",
                "all"=>true,
                "text"=>true,
            ],
        
            ]);
       
    }
     $fh = fopen(getenv("outputfile").".csv","a");
   foreach($scraper->extract() as $data)
   {
       print_r($data);
       fputcsv($fh,$data);
   }
}
paginate();
$crawler->quit();

