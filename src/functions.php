<?php


use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;

function login($crawler)
{
    $email= getenv("email");
    $password= getenv("password");

    $crawler->get("https://linkedin.com");
    $emailfield = $crawler->findElement(WebDriverBy::cssSelector("#session_key"));
    $passwordfield = $crawler->findElement(WebDriverBy::cssSelector("#session_password"));
    $emailfield->sendKeys($email);

    $passwordfield->sendKeys($password);

    $crawler->getKeyboard()->pressKey(WebDriverKeys::ENTER);
    $crawler->getKeyboard()->releaseKey(WebDriverKeys::ENTER);
}
function loginOnce($crawler)
{
    $email= getenv("email");
    $password= getenv("password");

  
    $emailfield = $crawler->findElement(WebDriverBy::cssSelector("#session_key"));
    if($email  && $emailfield->isDisplayed())
    {
        return;
    }
    $passwordfield = $crawler->findElement(WebDriverBy::cssSelector("#session_password"));
    $emailfield->sendKeys($email);

    $passwordfield->sendKeys($password);

    $crawler->getKeyboard()->pressKey(WebDriverKeys::ENTER);
    $crawler->getKeyboard()->releaseKey(WebDriverKeys::ENTER);
}
function loadEnv()
{
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__."/../");
    $dotenv->load();
}

loadEnv();