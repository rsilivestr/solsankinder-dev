import Glide from '@glidejs/glide';

const homeGlide = new Glide('.glide', {
  type: 'carousel',
  startAt: 0,
  perView: 3,
  breakpoints: {
    768: { perView: 1 },
    1024: { perView: 2 },
  },
});

homeGlide.mount();
