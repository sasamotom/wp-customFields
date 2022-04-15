import MicroModal from "micromodal";

export default class {
  constructor() {
    this.modalElem = document.getElementById("modal");
    this.contentElem = document.getElementById("c-modal_content");
    this.triggerElem = document.getElementById("modal-trigger");
    this.movieIframeElems = document.querySelectorAll(".movieModal iframe");

    const onClose = () => {
      if (this.contentElem) {
        this.contentElem.innerHTML = "";
      }
      if (this.movieIframeElems.length > 0) {
        for (let i = 0; i < this.movieIframeElems.length; i++) {
          // モーダルを閉じる際に動画を停止
          this.movieIframeElems[i].contentWindow.postMessage('{"event":"command", "func":"stopVideo"}', '*');
        }
      }
    };

    MicroModal.init({
      onClose,
      disableScroll: true,
      disableFocus: true,
      awaitOpenAnimation: true,
      awaitCloseAnimation: true
    });
  }
}

