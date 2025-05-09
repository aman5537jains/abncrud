<?php

namespace Aman5537jains\AbnCmsCRUD\Components;


use Aman5537jains\AbnCmsCRUD\ViewComponent;

class PopupComponent extends ViewComponent{




    function js(){
        
        $name = $this->getConfig("name","default");
        return "
       <style>
     

    .open-popup {
      margin: 20px;
      padding: 10px 20px;
      font-size: 16px;
    }

    /* Overlay */
    .popup-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 999;
      display: none;
    }

    .popup-overlay.show {
      display: block;
    }

    /* Popup */
    .popup {
      position: fixed;
      top: 0;
      right: -40%;
      width: 40%;
      height: 100%;
      background-color: #fff;
      box-shadow: -2px 0 10px rgba(0, 0, 0, 0.3);
      z-index: 1000;
      transition: right 0.3s ease-in-out;
      overflow-y: auto;
    }

    .popup.show {
      right: 0;
    }

    .popup-content {
      padding: 20px;
    }

    .close-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 24px;
      font-weight: bold;
      cursor: pointer;
    }
  </style>
      <script>
    document.querySelectorAll('.open-popup').forEach(button => {
      button.addEventListener('click', () => {
        const targetId = button.getAttribute('data-target');
        const wrapper = document.querySelector('.popup-wrapper[data-popup=\"'+targetId+'\"]');
        if (wrapper) {
          wrapper.classList.add('show');
          wrapper.querySelector('.popup').classList.add('show');
           wrapper.querySelector('.popup-overlay').classList.add('show');
        }
      });
    });

    document.querySelectorAll('.popup-wrapper').forEach(wrapper => {
      const popup = wrapper.querySelector('.popup');
      const overlay = wrapper.querySelector('.popup-overlay');
      const closeBtn = wrapper.querySelector('.close-btn');

      function closePopup() {
        popup.classList.remove('show');
        overlay.classList.remove('show');
      }

      closeBtn.addEventListener('click', closePopup);
      overlay.addEventListener('click', closePopup);
    });
  </script>
        ";

}
    function view(){
        $btnTitle = $this->getConfig("btn-title","Open Popup");
        $name = $this->getConfig("name","default");
        $content = $this->getConfig("content",'
        <div>
            <h2>Right Side Popup</h2>
      <p>This is a popup that slides in from the right and covers 40% of the screen.</p>
      <p>You can put any content here, including forms, images, or other components.</p>
        </div>
        ');
        return  ' 
        <div '.$this->getAttributesString().'>
        <button type="button" class="open-popup buttons secondary buttons-lg ms-0 ms-sm-3 advance-filter" data-target="'.$name.'">'.$btnTitle.'</button>

  <!-- Overlay -->
  <div class="popup-wrapper" data-popup="'.$name.'">
    <div class="popup-overlay"></div>
    <div class="popup">
      <div class="popup-content">
        <span class="close-btn">&times;</span>
         '.$content.'
      </div>
    </div>
  </div></div>'  ;
    }

}
