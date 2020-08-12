<?php namespace ProcessWire;

$blogPosts = $page->children("limit=5");

$pagination = $blogPosts->renderPager(array(
  'nextItemLabel' => ">",
  'previousItemLabel' => "<",
  'listMarkup' => "<ul class='MarkupPagerNav'>{out}</ul>",
  'itemMarkup' => "<li class='{class}'>{out}</li>",
  'linkMarkup' => "<a href='{url}'><span>{out}</span></a>"
));

$postsHTML = "";

foreach($blogPosts as $blogPost) {
  if($blogPost->images->count()) {
    $thumb_sm = $blogPost->images->first()->size('440', '330', 'north');
    $thumb_lg = $blogPost->images->first()->size('740', '555', 'north');
  }

  $postTitle = $blogPost->headline ? $blogPost->headline : $blogPost->title;

  $imageHTML = "";

  if ($blogPost->images->count()) {
    $imageHTML = "<img class='post-card__image lazy'
      src='{$blogPost->images->first()}'
      srcset='{$config->urls->assets}images/4x3.png'
      data-srcset='$thumb_sm->url 440w, $thumb_lg->url 740w'
      sizes='(max-width: 472px) 440px,
        (max-width: 768px) 740px,
        (min-width: 769px) 440px'>";
  }
    
  $postsHTML .= "<article class='article post-card'>
    <h2 class='post-card__title'>
      <a href='{$blogPost->url}'>
        $postTitle
      </a>
    </h2>
    <div class='post-card__body'>
      <div class='post-card__image-container'>
        $imageHTML
      </div>
      <div class='post-card__content'>
        <p class='post-card__summary'>$blogPost->summary</p>
        <p class='post-card__date'>$blogPost->postDate</p>
      </div>
    </div>
  </article>";
}

$content = "<section class='section section--basic'>
  <h1>$page->title</h1>
  $pagination
  <div class='posts-page'>
    $postsHTML
  </div>
  $pagination
.</section>";
