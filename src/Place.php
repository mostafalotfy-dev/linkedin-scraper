<?php

namespace Scraper\Scraper;

use Scraper\Scraper\Scraper;

class Place extends Scraper
{
  

    public function getTitle()
    {
        $title =  $this->find(".lMbq3e .DUwDvf.fontHeadlineLarge span")->getText();
        return $title;
    }
    public function getType()
    {
        return $this->find(".DkEaL")->getText();
    }
    public function getDetails()
    {
        $details = $this->findAll(".Io6YTe.fontBodyMedium");
        $details = array_map(function ($details) {
            return $details->getText();
        }, $details);
        return implode("-", $details);
    }
    public function paginate()
    {
        $this->paginate();
    }
}
