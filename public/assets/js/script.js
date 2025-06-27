'use strict';

// modal variables
const modal = document.querySelector('[data-modal]');
const modalCloseBtn = document.querySelector('[data-modal-close]');
const modalCloseOverlay = document.querySelector('[data-modal-overlay]');

// modal function
const modalCloseFunc = function () {
  if (modal) { // Kiểm tra xem modal có tồn tại không
    modal.classList.add('closed');
  }
}

// modal eventListener
// Chỉ thêm event listener nếu phần tử tồn tại
if (modalCloseOverlay) {
  modalCloseOverlay.addEventListener('click', modalCloseFunc);
}
if (modalCloseBtn) {
  modalCloseBtn.addEventListener('click', modalCloseFunc);
}


// notification toast variables
const notificationToast = document.querySelector('[data-toast]');
const toastCloseBtn = document.querySelector('[data-toast-close]');

// notification toast eventListener
if (toastCloseBtn && notificationToast) { // Kiểm tra cả hai phần tử
  toastCloseBtn.addEventListener('click', function () {
    notificationToast.classList.add('closed');
  });
}


// mobile menu variables
const mobileMenuOpenBtn = document.querySelectorAll('[data-mobile-menu-open-btn]');
const mobileMenu = document.querySelectorAll('[data-mobile-menu]');
const mobileMenuCloseBtn = document.querySelectorAll('[data-mobile-menu-close-btn]');
const overlay = document.querySelector('[data-overlay]');

// Chỉ chạy vòng lặp nếu có ít nhất một nút mở menu
if (mobileMenuOpenBtn.length > 0) {
  for (let i = 0; i < mobileMenuOpenBtn.length; i++) {

    // mobile menu function
    const mobileMenuCloseFunc = function () {
      if (mobileMenu[i]) { // Kiểm tra mobileMenu[i]
        mobileMenu[i].classList.remove('active');
      }
      if (overlay) { // Kiểm tra overlay
        overlay.classList.remove('active');
      }
    }

    if (mobileMenuOpenBtn[i] && mobileMenu[i] && overlay) { // Kiểm tra tất cả các phần tử liên quan
      mobileMenuOpenBtn[i].addEventListener('click', function () {
        mobileMenu[i].classList.add('active');
        overlay.classList.add('active');
      });
    }

    if (mobileMenuCloseBtn[i]) { // Kiểm tra mobileMenuCloseBtn[i]
      mobileMenuCloseBtn[i].addEventListener('click', mobileMenuCloseFunc);
    }
    if (overlay) { // Kiểm tra overlay
      overlay.addEventListener('click', mobileMenuCloseFunc);
    }
  }
}


// accordion variables
const accordionBtn = document.querySelectorAll('[data-accordion-btn]');
const accordion = document.querySelectorAll('[data-accordion]');

if (accordionBtn.length > 0) { // Chỉ chạy vòng lặp nếu có ít nhất một nút accordion
  for (let i = 0; i < accordionBtn.length; i++) {

    if (accordionBtn[i]) { // Kiểm tra accordionBtn[i]
      accordionBtn[i].addEventListener('click', function () {

        const clickedBtn = this.nextElementSibling && this.nextElementSibling.classList.contains('active'); // Kiểm tra nextElementSibling

        for (let j = 0; j < accordion.length; j++) { // Đổi biến lặp để tránh xung đột với vòng lặp ngoài

          if (clickedBtn) break;

          if (accordion[j] && accordion[j].classList.contains('active')) { // Kiểm tra accordion[j]

            accordion[j].classList.remove('active');
            accordionBtn[j].classList.remove('active');

          }

        }

        if (this.nextElementSibling) { // Kiểm tra nextElementSibling
          this.nextElementSibling.classList.toggle('active');
        }
        this.classList.toggle('active');

      });
    }
  }
}
