// --------------------------------------------------------------
// いいねボタンの処理（ajaxを使用する）
// --------------------------------------------------------------
function callGoodBtnTask() {
  const goodbtn = document.getElementById('goodBtn');   // いいねボタン（button）
  const goodbtncnt = document.getElementById('goodCntNum'); // いいね数（span）
  if (goodbtn) {
    // ボタン押下時の処理を定義
    goodbtn.addEventListener('click', () => {
      // ボタンを非アクティブにする
      goodbtn.disabled = true;

      // ajaxにて処理呼び出し
      jQuery.ajax({
        type: "POST",
        url: ad_url.ajax_url,
        data: {
          'action' : 'count_up',    // 処理はacf-item.phpに定義されている
          'postID' : goodbtn.dataset.id
        },
      })
      .done(function(ret) {
        // 【成功した場合】
        if (goodbtncnt) {
          goodbtncnt.innerHTML = ret;
        }
        if (goodbtn.classList.contains('-clicked')) {
          // 【いいね済の場合】
          goodbtn.classList.remove('-clicked');
        }
        else {
          // 【未いいねの場合】
          goodbtn.classList.add('-clicked');
        }
        // ボタンをアクティブにする
        goodbtn.disabled = false;
      // })
      // // .fail(function (xhr,textStatus,errorThrown) {
      // //   // 失敗した場合
      });
    });
  }
}
callGoodBtnTask();
