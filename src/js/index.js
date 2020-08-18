import Glide from '@glidejs/glide';
import '../scss/index.scss';

if (document.querySelector('.glide')) {
  new Glide('.glide', {
    type: 'carousel',
    startAt: 0,
    perView: 3,
  }).mount();
}

// declare UI elements
// const UImenuBtn = document.querySelector('.menu-btn');
// const UIbread = document.querySelector('.bread');
// const UInavPrimary = document.querySelector('.nav-primary');
const UInavLinks = document.querySelectorAll('.navlink');
const UIsubNavs = document.querySelectorAll('.subnav');
// const UImain = document.querySelector('main');
// const UIfooter = document.querySelector('footer');
const UIgalleryCurrent = document.querySelector('.gallery-current');
// const UIgalleryThumbs = document.querySelectorAll('.gallery-thumb');
const UIgallery = document.querySelector('.basic-gallery');
const UIlowVisionBtn = document.querySelector('.toggle-low-vision');
const UIlowVisionSpan = document.querySelector('.toggle-low-vision__span');
// const UIcontactForm = document.querySelector('.contact-form');
// const UIcontactName = document.querySelector('.contact-form__name');
// const UIcontactEmail = document.querySelector('.contact-form__email');
// const UIcontactMessage = document.querySelector('.contact-form__message');
// const UIcontactSubmit = document.querySelector('.contact-form__submit');

// handle menu-btn click
// if (UImenuBtn) {
//   UImenuBtn.addEventListener('click', function (e) {
//     UInavPrimary.classList.toggle('visible');
//     // document.body.classList.toggle('no-scroll');
//     UImenuBtn.classList.toggle('is-open');
//     e.preventDefault();
//   });
// }

const SolSanKinder = (() => {
  const UIselectors = {
    basicGallery: '.basic-gallery',
    bread: '.bread',
    header: '.main-header',
    lowVisionBtn: '.toggle-low-vision',
    lowVisionSpan: '.toggle-low-vision__span',
    navPrimary: '.nav-primary',
    npLink: '.nav-primary__link',
    npSubnav: '.np-subnav',
  };

  /* Toggle mobile navigation */
  const toggleMenu = (e) => {
    const btn = e.target.closest('.menu-btn');

    if (btn) {
      document
        .querySelector(UIselectors.navPrimary)
        .classList.toggle('visible');

      document
        .querySelectorAll(UIselectors.npSubnav)
        .forEach((item) => item.classList.remove('visible'));

      document
        .querySelectorAll(UIselectors.npLink)
        .forEach((item) => item.classList.remove('subnav-open'));

      btn.blur();
    }
  };

  /* Toggle navigation sublevels */
  const toggleSubnav = (e) => {
    const link = e.target.closest('.has-subnav');

    if (link) {
      e.preventDefault();

      const subnav = link.nextElementSibling;

      /* show subnav, hide others */
      document.querySelectorAll(UIselectors.npSubnav).forEach((item) => {
        if (item !== subnav) item.classList.remove('visible');
      });

      /* toggle arrows */
      document.querySelectorAll(UIselectors.npLink).forEach((item) => {
        if (item !== link) item.classList.remove('subnav-open');
      });

      subnav.classList.toggle('visible');
      link.classList.toggle('subnav-open');
    } else if (!e.target.closest(UIselectors.npSubnav)) {
      document
        .querySelectorAll(UIselectors.npSubnav)
        .forEach((item) => item.classList.remove('visible'));

      document
        .querySelectorAll(UIselectors.npLink)
        .forEach((item) => item.classList.remove('subnav-open'));
    }
  };

  /* Gallery on basic pages */
  const handleBasicGallery = (e) => {
    const target = e.target;
    const gal = e.target.closest('.basic-gallery');
    const current = gal.querySelector('.basic-gallery__current');
    const caption = gal.querySelector('.basic-gallery__caption');

    if (
      target.classList.contains('basic-gallery__thumb') &&
      current.src !== target.src
    ) {
      current.src = target.src;
      current.srcset = target.srcset;
      caption.textContent = target.dataset.caption;
    }
  };

  /* toggle low vision mode link click*/
  const toggleLowVision = (e) => {
    e.preventDefault();

    const formData = new FormData();
    formData.append('lowVision', true);

    fetch('/', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        document.body.classList.toggle('low-vision');
        const btnText = document.body.classList.contains('low-vision')
          ? 'обычная версия'
          : 'версия для слабовидящих';
        document.querySelector(UIselectors.lowVisionSpan).textContent = btnText;
      })
      .catch((err) => console.log(err));
  };

  /*
   * lazy loading https://css-tricks.com/a-few-functional-uses-for-intersection-observer-to-know-when-an-element-is-in-view/
   */
  const lazyObserver = new IntersectionObserver(
    function (entries, observer) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          if (entry.target.dataset.srcset) {
            entry.target.srcset = entry.target.dataset.srcset;
          } else {
            entry.target.src = entry.target.dataset.src;
          }
          observer.unobserve(entry.target);
        }
      });
    },
    { rootMargin: '0px 0px 50px 0px' }
  );

  /* Public method that initializes event listeners */
  const init = () => {
    /* navigation menu click */
    document
      .querySelector(UIselectors.header)
      .addEventListener('click', toggleMenu);

    /* nav-primary click */
    document.body.addEventListener('click', toggleSubnav);

    /* basic gallery click */
    const basicGallery = document.querySelector(UIselectors.basicGallery);
    if (basicGallery) {
      basicGallery.addEventListener('click', handleBasicGallery);
    }

    /* low vision click */
    document
      .querySelector(UIselectors.lowVisionBtn)
      .addEventListener('click', toggleLowVision);

    /* lazy content loading */
    if (document.querySelector('.lazy')) {
      document.querySelectorAll('.lazy').forEach((lazyItem) => {
        lazyObserver.observe(lazyItem);
      });
    }
  };

  return {
    init,
  };
})();

SolSanKinder.init();
