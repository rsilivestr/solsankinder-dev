<?php namespace ProcessWire;

$blogPosts = $page->children("limit=5");

$pagination = $blogPosts->renderPager(array(
  'nextItemLabel' => ">",
  'previousItemLabel' => "<",
  'listClass' => "pager-nav",
  'currentItemClass' => "pager-nav__item--current",
  'currentLinkMarkup' => "<span class='pager-nav__link'>{out}</span>",
  'firstItemClass' => "pager-nav__item--first",
  'firstNumberItemClass' => "pager-nav__item--first-number",
  'lastItemClass' => "pager-nav__item--last",
  'nextItemClass ' => "pager-nav__item--next",
  'previousItemClass' => "pager-nav__item--prev",
  'separatorItemClass' => "pager-nav__item--separator",
  // 'listMarkup' => "<ul class='MarkupPagerNav pager-nav'>{out}</ul>",
  'itemMarkup' => "<li class='pager-nav__item {class}' aria-label='{aria-label}'>{out}</li>",
  'linkMarkup' => "<a class='pager-nav__link' href='{url}'>{out}</a>"
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
      " 
      . ( $blogPost->images->count() ?
        "<a href='{$blogPost->url}' class='post-card__image-container'>$imageHTML</a>" :
        "" )
      . "<div class='post-card__content'>
        <p class='post-card__summary'>$blogPost->summary</p>
        <p class='post-card__date'>$blogPost->postDate</p>
      </div>
    </div>
  </article>";
}

$content = "<section class='section section--width_m'>
  <h1>$page->title</h1>
  $pagination
  <div class='posts-page'>
    $postsHTML
  </div>
  $pagination
.</section>";
