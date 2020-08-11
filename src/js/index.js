import '../sass/index.scss';

// declare UI elements
const UImenuBtn = document.querySelector('#menu-btn');
const UInavPrimary = document.querySelector('.nav-primary');
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
if (UImenuBtn) {
  UImenuBtn.addEventListener('click', function (e) {
    UInavPrimary.classList.toggle('visible');
    // document.body.classList.toggle('no-scroll');
    UImenuBtn.classList.toggle('is-open');
    e.preventDefault();
  });
}

// handle navlink click
UInavPrimary.addEventListener('click', function (e) {
  if (e.target.classList.contains('has-subnav')) {
    for (let i = UIsubNavs.length; i--; i == 0) {
      // if subnav not next after clicked navlink hide it
      if (UIsubNavs[i] !== e.target.nextElementSibling) {
        UIsubNavs[i].classList.remove('visible');
      } else {
        UIsubNavs[i].classList.toggle('visible');
        // toggle navlink arrow
        e.target.classList.toggle('subnav-opened');
        // toggle other navlinks arrows
        UInavLinks.forEach(function (navlink) {
          if (navlink !== e.target) {
            navlink.classList.remove('subnav-opened');
          }
        });
      }
    }
    // prevent page reload if subnav triggered
    e.preventDefault();
  }
});

// hide subnav when clicked outside
document.addEventListener('click', function (e) {
  if (!e.target.closest('.navlink') && !e.target.closest('nav.subnav')) {
    UIsubNavs.forEach(function (subnav) {
      subnav.classList.remove('visible');
    });
    UInavLinks.forEach(function (navlink) {
      navlink.classList.remove('subnav-opened');
    });
  }
});

// если есть галерея на отделении
if (UIgallery) {
  UIgallery.addEventListener('click', function (e) {
    // при нажатии на превью
    if (e.target.classList.contains('gallery-thumb')) {
      // передать картинку на просмотр
      UIgalleryCurrent.srcset = e.target.dataset.srcset;
      // вывести подпись, если есть
      const UIcaption = UIgallery.querySelector('figcaption');
      if (e.target.dataset.caption && !UIcaption) {
        const newCaption = document.createElement('figcaption');
        newCaption.innerText = e.target.dataset.caption;
        UIgallery.appendChild(newCaption);
      } else if (e.target.dataset.caption && UIcaption) {
        UIcaption.innerText = e.target.dataset.caption;
      } else {
        UIgallery.querySelector('figcaption').remove();
      }
    }
  });
}

// Версия для слабовидящих
UIlowVisionBtn.addEventListener('click', toggleLowVisionMode);

function toggleLowVisionMode(e) {
  e.preventDefault();

  const xhr = new XMLHttpRequest();
  xhr.open('POST', '/', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('lowVision');

  xhr.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
      // изменение класса без перезагрузки страницы
      document.body.classList.toggle('low-vision');
      const span = document.body.classList.contains('low-vision')
        ? ' обычная версия'
        : ' версия для слабовидящих';
      UIlowVisionSpan.textContent = span;
    }
  };
}

// const toggleLowVisionFetch = async (e) => {
//   e.preventDefault();

//   const res = await fetch('/', {
//     method: 'POST',
//     headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
//     body: JSON.stringify({ lowVision: true }),
//   });

//   const btnText = await res.text();

//   UIlowVisionSpan.textContent = btnText;
// };

// document.body.addEventListener('dblclick', toggleLowVisionFetch);

// галерея на главной
const UIgalItems = document.querySelectorAll('.pwpswp-gallery__item');

// console.log(document.querySelector('.pwpswp-gallery'));

if (document.querySelector('.pwpswp-gallery')) {
  const gals = [].slice.call(UIgalItems),
    UIgalPrev = document.querySelector('.gal-prev'),
    UIgalNext = document.querySelector('.gal-next');
  // first set of n items 0 to n-1
  let screen = 0;
  // number of items per set
  let set = 1;
  setGalSet();
  window.onresize = setGalSet;
  function setGalSet() {
    set = window.innerWidth < 769 ? 1 : 3;
    // set screen
    setScreen(screen, set);
  }
  // set divs to show
  function setScreen(screen, set) {
    let start = screen * set;
    let end = (screen + 1) * set;
    gals.forEach(function (gal, index) {
      gal.classList.remove('screen');
      if (index >= start && index < end) {
        gal.classList.add('screen');
      }
    });
  }
  // move to left
  UIgalPrev.addEventListener('click', function (e) {
    e.preventDefault();
    if (screen > 0) {
      screen--;
    }
    setScreen(screen, set);
  });
  // move to rihgt
  UIgalNext.addEventListener('click', function (e) {
    e.preventDefault();
    if (screen < gals.length / set - 1) {
      screen++;
    }
    setScreen(screen, set);
  });
}

// lazy loading https://css-tricks.com/a-few-functional-uses-for-intersection-observer-to-know-when-an-element-is-in-view/
let observer = new IntersectionObserver(
  function (entries, observer) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        if (entry.target.dataset.srcset) {
          // entry.target.srcset = entry.target.dataset.srcset;
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

document.querySelectorAll('.lazy').forEach(function (lazy) {
  observer.observe(lazy);
});
