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

// 検索画面のJS処理
if (document.getElementById('searchForm')) {
  const searchForm = document.getElementById('searchForm'); // 検索フォーム
  const chkList = searchForm.querySelectorAll('.searchCheckList input[type=checkbox]'); // checkboxのリスト
  // 子チェックボックスがある場合は、親のチェック状態変化に合わせる
  chkList.forEach(function (el) {
    const parent = el.parentElement;  // inputの親要素（li要素）
    const childChkList = parent.querySelectorAll('.searchCheckList input[type=checkbox]');  // 子チェックボックスリスト
    if (childChkList.length > 0) {
      el.addEventListener('change', (e) => {
        // 選択状態を取得し、子チェクボックスに値を反映
        // また、チェック無しの場合は、disabel=trueとなるよう対応
        const value = e.target.checked;
        childChkList.forEach(function (child) {
          if (child !== el) {
            child.checked = value;
            child.disabled = !value;
          }
        });
      });
    }
  });
}
