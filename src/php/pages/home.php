<?php namespace ProcessWire;

$content = '';

/* ссылки на запись и путёвку */
$content .= "
  <div class='section section--width_m home-register ta_center'>
    <a class='action-btn action-btn--color_magenta' href='/check-in-form'>Регистрация на заезд</a>
    <a class='action-btn action-btn--color_blue' href='/get-voucher'>Стать кандидатом</a> 
  </div>
";

/* главрач */
$content .= "<section class='section section--width_m section--justified section--font_cursive'>
  <h1>Добро пожаловать в санаторий «Солнечное»</h1>
  {$page->body}
</section>";

/* профили санатория чисто текст */
$content .= "<section class='section section--width_m'>
  <h2>Профили лечения</h2>
  <div class='home-units'>
    <a class='hu-link' href='/info/courses/gastroenterology/'>
      <span class='hu-link__caption'>Гастро&#8203;энтеро&#8203;логия</span>
    </a>
    <a class='hu-link' href='/info/courses/psychoneurology/'>
      <span class='hu-link__caption'>Психо&#8203;невро&#8203;логия</span>
    </a>
    <a class='hu-link' href='/info/courses/nephrology/'>
      <span class='hu-link__caption'>Нефро&#8203;логия</span>
    </a>
    <a class='hu-link' href='/info/courses/pulmonology/'>
      <span class='hu-link__caption'>Пульмо&#8203;но&#8203;логия</span>
    </a>
      <a class='hu-link' href='/info/courses/oncology/'>
      <span class='hu-link__caption'>Онко&#8203;логия</span>
    </a>
  </div>
</section>";

/* фото фон 1 */
$content .= "<div id='home-bg-1' class='home-bg section hide-sm'>
  <a class='action-btn action-btn--size_s action-btn--color_blue' href='http://anketa.rosminzdrav.ru/staticmojustank/9211#reviews'>
    Анкета оценки качества оказания услуг
  </a>
</div>";

/* галерея glidejs */
$galItemsHTML = '';

foreach ($page->gallery as $image) {
  $xs = $image->width('400');
  $sm = $image->width('600');
  $md = $image->width('800');

  $galItemsHTML .= "<li class='glide__slide'
  ><img class='glide__image lazy'
    src='$image->url'
    srcset='{$config->urls->assets}images/4x3.png 320w'
    data-srcset='$xs->url 400w, $sm->url 600w, $md->url 800w'
    sizes='(max-width: 424px) 400px,
      (max-width: 624px) 600px,
      (max-width: 824px) 800px,
      600px'
    alt='$image->description'
  ></li>";
}

$content .= "<div class='section section--width_m'>
    <div class='glide'>
      <div data-glide-el='track' class='glide__track'>
        <ul class='glide__slides'>$galItemsHTML</ul>
      </div>
      <div class='glide__arrows' data-glide-el='controls'>
        <button class='glide__arrow glide__arrow--left' data-glide-dir='<' aria-label='Листать галерею влево'>
          <i class='icon-left-big'></i>
        </button>
        <button class='glide__arrow glide__arrow--right' data-glide-dir='>' aria-label='Листать галерею вправо'>
          <i class='icon-right-big'></i>
        </button>
      </div>
    </div>
  </div>";

/* фото фон 2 */
$content .= "<div id='home-bg-2' class='home-bg section hide-sm'>
  <a class='action-btn action-btn--size_s action-btn--color_blue' href='https://bus.gov.ru/pub/info-card/170512?activeTab=5'>
    Оставьте отзыв о работе санатория
  </a>
</div>";

/* новости */
$content .= "<section class='home-news section section--width_m'>
  <h2 class='home-news__title'>Новости</h2>
  <div class='home-news__content'>";

foreach ($pages->find('template=blog-post, limit=3, sort=-created') as $blogPost) {
  if ($blogPost->images->count()) {
    $previewImg = $blogPost->images->first();
    $previewAlt = $previewImg->description ? $previewImg->description : 'превью новости';
    $thumb_sm = $previewImg->size('444', '333');

    $content .= "<article class='home-news__item post-card'>
      <h3 class='post-card__title'>
        <a class='post-card__title-link' href='$blogPost->url'>$blogPost->title</a>
      </h3>
      <div class='post-card__body'>
        <a href='$blogPost->url' class='post-card__image-container'>
          <img class='post-card__image lazy'
            src='{$previewImg->url}'
            srcset='{$config->urls->assets}images/4x3.png 320w'
            data-srcset='$thumb_sm->url 440w'
            sizes='440px'
            alt='$previewAlt'
        ></a>
        <div class='post-card__content'>
          <p class='post-card__summary'>$blogPost->summary</p>
          <p class='post-card__date'>$blogPost->postDate</p>
        </div>
      </div>
    </article>";
  }
}

$content .= '</div></section>';

/* буннер гусуслуг */
$content .= file_get_contents('./gu-widget.html');
