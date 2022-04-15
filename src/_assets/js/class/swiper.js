import Swiper from 'swiper/dist/js/swiper.min.js';

export default class {
  constructor() {
    const swiper = new Swiper(".mySwiperThumb", {
      spaceBetween: 10,
      slidesPerView: 4,
      navigation: {
        nextEl: '.swiper-button-next',    // 次に進むボタン（矢印）要素
        prevEl: '.swiper-button-prev',    // 前に戻るボタン（矢印）要素
      },
    });

    const swiper2 = new Swiper(".mySwiperMain", {
      thumbs: {
        swiper: swiper
      },
      loop: true,
      autoplay: {
        delay: 5000,    // 間隔（ミリ秒）
        disableOnInteraction: false // 操作されたら自動再生をストップさせる（true）設定（規定値true）
      },
      speed: 1000,
      effect: 'fade',
    });
  }
}

