<?php namespace ProcessWire;

$cardsHTML = "";

foreach($page->cards as $card) {
  $imageHTML = "<img class='card-tile__image' src='{$config->urls->assets}images/no-photo.png' />";

  if($card->cardPhoto) {
    $xs = $card->cardPhoto->size('400', '300');
    $sm = $card->cardPhoto->size('600', '450');
    $md = $card->cardPhoto->size('800', '600');

    $imageHTML = "<img class='card-tile__image lazy'
      src='{$card->cardPhoto->url}'
      srcset='/site/assets/images/4x3.png'
      data-srcset='
        $xs->url 400w,
        $sm->url 600w,
        $md->url 800w'
      sizes='
        (max-width: 424px) 400px,
        (max-width: 624px) 600px,
        (max-width: 824px) 800px,
        (min-width: 825px) 400px
      '>";
  }

  $cardsHTML .= "<div class='card-tile'>
    $imageHTML
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