<?php namespace ProcessWire;

$cardsHTML = "";

foreach($page->cards as $card) {
  $imageHTML = "<img class='card-tile__image' src='{$config->urls->assets}images/no-photo.png' />";

  if($card->cardPhoto) {
    $xs = $card->cardPhoto->width('320');
    $sm = $card->cardPhoto->width('450');

    $imageHTML = "<img class='card-tile__image lazy'
      src='{$card->cardPhoto->url}'
      srcset='/site/assets/images/4x3.png'
      data-srcset='
        $xs->url 320w
        $sm->url 450w'
      sizes='(max-width: 352px) 320px, 450px'
    >";
  }

  $cardsHTML .= "<div class='card-tile'>
    <div class='card-tile__image-container'>
      $imageHTML
    </div>
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