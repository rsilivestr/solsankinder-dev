<?php namespace ProcessWire;

$aClass = 'section' . ($page->wideContent ? ' section--width_w' : ' section--width_m');
// if ($page->wideContent) $aClass .= " article--wide";

$content = "<article class='$aClass'>
  <h1>$title</h1>
  $page->body
</article>";

// basic-page gallery
if ($page->renderGallery && $page->images->count()) {
  $thumbsHTML = '';

  foreach ($page->images as $image) {
    $thumb = $image->size('160', '120');
    $xs = $image->size('400', '300');
    $sm = $image->size('600', '450');
    $md = $image->size('800', '600');

    $thumbsHTML .= "<img
      class='basic-gallery__thumb lazy'
      src='$image->url'
      data-srcset='$thumb->url 160w,
        $xs->url 400w,
        $sm->url 600w,
        $md->url 800w'
      sizes='(min-width: 320px) 160px'
      data-caption='$image->description'>";
  }

  $content .= "<div class='basic-gallery'>
    <div class='basic-gallery__thumbs'>
      $thumbsHTML
    </div>
    <div class='basic-gallery__display'>
      <img width='100%'
        class='basic-gallery__current'
        src='{$page->images->first()->size('600', '450')->url}'
        sizes='(max-width: 400px) 400px,
        (max-width: 624px) 600px,
        (min-width: 625px) 800px'>
      <figcaption class='basic-gallery__caption'>
        {$page->images->first()->description}
      </figcaption>
    </div>
  </div>";
}
