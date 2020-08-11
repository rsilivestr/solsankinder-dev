<?php namespace ProcessWire;

$blogPosts = $page->children("limit=5");

$pagination = $blogPosts->renderPager(array(
  'nextItemLabel' => ">",
  'previousItemLabel' => "<",
  'listMarkup' => "<ul class='MarkupPagerNav'>{out}</ul>",
  'itemMarkup' => "<li class='{class}'>{out}</li>",
  'linkMarkup' => "<a href='{url}'><span>{out}</span></a>"
));

$content = "<section class='blog-pages'><h2>$page->title</h2>";

$content .= $pagination;

foreach($blogPosts as $blogPost) {
  // картинки
  if($blogPost->images->count()) {
    $thumb_sm = $blogPost->images->first()->height('200');
    $thumb_lg = $blogPost->images->first()->width('760');
  }
  // рендер поста
  $content .= "<article class='blog-post'><h3 class='blog-post-title'><a href='{$blogPost->url}'>";
    if($blogPost->headline) { $content .= $blogPost->headline; }
    else { $content .= $blogPost->title; }
  $content .= "</a></h3><div class='flex-container'>";
  if ($blogPost->images->count()) {
    $content .= "<img class='lazy'
      src='{$blogPost->images->first()}'
      srcset='{$config->urls->assets}images/4x3.png'
      data-srcset='$thumb_sm->url 440w, $thumb_lg->url 740w'
      sizes='(max-width: 472px) 440px,
        (max-width: 768px) 740px,
        (min-width: 769px) 440px'>";
  }
  $content .= "<div0><p>$blogPost->summary</p><p>$blogPost->postDate</p></div></div></article>";

}

$content .= $pagination;

$content .= "</section>";