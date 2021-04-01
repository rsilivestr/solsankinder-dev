import '../scss/index.scss';

const SolSanKinder = (() => {
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

  const toggleMenu = (e) => {
    const btn = e.target.closest(UIselectors.menuBtn);

    if (btn) {
      const btnIcon = btn.firstElementChild;

      document.querySelector(UIselectors.navPrimary).classList.toggle('visible');

      btnIcon.classList.toggle('icon-menu');
      btnIcon.classList.toggle('icon-cancel');

      document
        .querySelectorAll(UIselectors.npSubnav)
        .forEach((item) => item.classList.remove('visible'));

      document
        .querySelectorAll(UIselectors.npLink)
        .forEach((item) => item.classList.remove('nav-primary__link--subnav-open'));

      btn.blur();
    }
  };

  const toggleSubnav = (e) => {
    const link = e.target.closest('.nav-primary__link--has-subnav');

    if (link) {
      e.preventDefault();

      const subnav = link.nextElementSibling;

      document.querySelectorAll(UIselectors.npSubnav).forEach((item) => {
        if (item !== subnav) item.classList.remove('visible');
      });

      document.querySelectorAll(UIselectors.npLink).forEach((item) => {
        if (item !== link) item.classList.remove('nav-primary__link--subnav-open');
      });

      subnav.classList.toggle('visible');
      link.classList.toggle('nav-primary__link--subnav-open');
    } else if (!e.target.closest(UIselectors.npSubnav)) {
      document
        .querySelectorAll(UIselectors.npSubnav)
        .forEach((item) => item.classList.remove('visible'));

      document
        .querySelectorAll(UIselectors.npLink)
        .forEach((item) => item.classList.remove('nav-primary__link--subnav-open'));
    }
  };

  const handleBasicGallery = (e) => {
    const target = e.target;
    const gal = target.closest('.basic-gallery');
    const current = gal.querySelector('.basic-gallery__current');
    const caption = gal.querySelector('.basic-gallery__caption');

    if (target.classList.contains('basic-gallery__thumb') && current.src !== target.src) {
      current.src = target.src;
      current.srcset = target.srcset;
      caption.textContent = target.dataset.caption;
    }
  };

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
    (entries, observer) => {
      entries.forEach((entry) => {
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
    { rootMargin: '0px -200px -200px 0px' }
  );

  const init = () => {
    document.querySelector(UIselectors.header).addEventListener('click', toggleMenu);

    document.body.addEventListener('click', toggleSubnav);

    const basicGallery = document.querySelector(UIselectors.basicGallery);
    if (basicGallery) {
      basicGallery.addEventListener('click', handleBasicGallery);
    }

    document.querySelector(UIselectors.lowVisionBtn).addEventListener('click', toggleLowVision);

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
