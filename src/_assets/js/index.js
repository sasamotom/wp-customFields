import swiper from './class/swiper';
import microModal from './class/micromodal'

window.addEventListener('DOMContentLoaded', () => {
  new swiper();
  new microModal();
});

// スムーススクロール
$('a[href^="#"]:not([href="#"])').on('click', function() {
  let target = 0;
  if ($(this).attr('href') !== "##") {
    target = $($(this).attr('href')).offset().top;
  }
  $('html, body').animate({scrollTop: target}, 500);
  return false;
});

