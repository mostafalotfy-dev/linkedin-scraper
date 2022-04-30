<?php


use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;

function login($crawler,$email,$password)
{
    $crawler->get("https://linkedin.com");
    $emailfield = $crawler->findElement(WebDriverBy::cssSelector("#session_key"));
    $passwordfield = $crawler->findElement(WebDriverBy::cssSelector("#session_password"));
    $emailfield->sendKeys($email);

    $passwordfield->sendKeys($password);
  
    $crawler->getKeyboard()->pressKey(WebDriverKeys::ENTER);
    $crawler->getKeyboard()->releaseKey(WebDriverKeys::ENTER);
}