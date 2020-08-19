<?php namespace ProcessWire;

$cardsHTML = "";

foreach($page->cards as $card) {
  $imageHTML = "";

  if (!$card->cardTextOnly) {
    $imageHTML = "<img class='card-tile__image' src='{$config->urls->assets}images/no-photo.png' />";

    if($card->cardPhoto) {
      $xs = $card->cardPhoto->width('320');
      $sm = $card->cardPhoto->width('450');
  
      $imageHTML = "<img class='card-tile__image lazy'
        src='{$card->cardPhoto->url}'
        srcset='{$config->urls->assets}images/no-photo.png'
        data-srcset='
          $xs->url 320w,
          $sm->url 450w'
        sizes='(max-width: 344px) 320px, 450px'
        alt='$image->description'
      >";
    }
  }

  $imageContainerHTML = "";

  if ($imageHTML !== "") {
    $imageContainerHTML = "<div class='card-tile__image-container'>$imageHTML</div>";
  }  

  $cardsHTML .= "<div class='card-tile'>
    $imageContainerHTML
    <div class='card-tile__text-content'>
      $card->body
    </div>
  </div>";
}

$content = "<section class='section section--width_m'>
  <h1>$title</h1>
  <div class='card-tiles'>
    $cardsHTML
  </div>
</section>";