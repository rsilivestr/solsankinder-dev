import '../scss/index.scss';

const SolSanKinder = (() => {
  /* Element selectors here */
  const UIselectors = {
    basicGallery: '.basic-gallery',
    header: '.main-header',
    lowVisionBtn: '.toggle-low-vision',
    lowVisionSpan: '.toggle-low-vision__span',
    menuBtn: '.menu-btn',
    navPrimary: '.nav-primary',
    npLink: '.nav-primary__link',
    npSubnav: '.np-subnav',
  };

  /* Toggle mobile navigation */
  const toggleMenu = (e) => {
    const btn = e.target.closest(UIselectors.menuBtn);

    if (btn) {
      /* Open navigation menu */
      document
        .querySelector(UIselectors.navPrimary)
        .classList.toggle('visible');

      /* Initially hide all subnavs */
      document
        .querySelectorAll(UIselectors.npSubnav)
        .forEach((item) => item.classList.remove('visible'));

      /* Reset arrows indicating subnav status */
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

      /* Hide subnavs save for current */
      document.querySelectorAll(UIselectors.npSubnav).forEach((item) => {
        if (item !== subnav) item.classList.remove('visible');
      });

      /* Reset arrows indicating subnav status */
      document.querySelectorAll(UIselectors.npLink).forEach((item) => {
        if (item !== link) item.classList.remove('subnav-open');
      });

      /* Toggle current subnav and arrow */
      subnav.classList.toggle('visible');
      link.classList.toggle('subnav-open');
    } else if (!e.target.closest(UIselectors.npSubnav)) {
      /* if not clicked inside a subnav or navigation link */
      /* Hide all subnavs */
      document
        .querySelectorAll(UIselectors.npSubnav)
        .forEach((item) => item.classList.remove('visible'));

      /* Reset all arrows */
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
        /* Toggle body class and button text without page reload */
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
    (entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          /* replace placeholder src (srcset) from an entry's dataset */
          if (entry.target.dataset.srcset) {
            entry.target.srcset = entry.target.dataset.srcset;
          } else {
            entry.target.src = entry.target.dataset.src;
          }
          /* stop watching for loaded entry */
          observer.unobserve(entry.target);
        }
      });
    },
    { rootMargin: '0px 0px 0px 0px' }
  );

  /* Public method that initializes event listeners */
  const init = () => {
    /* Navigation menu button click capture */
    document
      .querySelector(UIselectors.header)
      .addEventListener('click', toggleMenu);

    /* Primary navigation click capture */
    document.body.addEventListener('click', toggleSubnav);

    /* Basic gallery click capture */
    const basicGallery = document.querySelector(UIselectors.basicGallery);
    if (basicGallery) {
      basicGallery.addEventListener('click', handleBasicGallery);
    }

    /* Low vision toggle button click */
    document
      .querySelector(UIselectors.lowVisionBtn)
      .addEventListener('click', toggleLowVision);

    /* Lazy content loading */
    if (document.querySelector('.lazy')) {
      document.querySelectorAll('.lazy').forEach((lazyItem) => {
        lazyObserver.observe(lazyItem);
      });
    }

    /* Homepage slider initialization */
    if (document.querySelector('.glide')) {
      const homeGlide = new Glide('.glide', {
        type: 'carousel',
        startAt: 0,
        perView: 3,
      }).mount();
    }
  };

  return {
    init,
  };
})();

SolSanKinder.init();
