<?php namespace ProcessWire;

// include('_bread.php');

$content = "<section><h1>$title</h1><div class='card-tiles flex-container'>";

foreach($page->cards as $card) {
  $content .= "<figure class='card-tiles__tile'>";
  if($card->cardPhoto) {
    $xs = $card->cardPhoto->size('400', '300');
    $sm = $card->cardPhoto->size('600', '450');
    $md = $card->cardPhoto->size('800', '600');
    $content .= "<img class='lazy'
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
  } else {
    $content .= "<img src='{$config->urls->assets}images/photoPlaceholder.jpg' />";
  }

  $content .= "<div>$card->body</div></figure>";
}
$content .= "</div></section>";