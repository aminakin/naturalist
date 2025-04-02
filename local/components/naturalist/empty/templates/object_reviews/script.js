document.addEventListener("DOMContentLoaded", function () {
  if (Fancybox !== "undefined") {
    Fancybox.bind('[data-fancybox="reviewGallery"]', {
      Toolbar: {
        display: {
          left: ["infobar"],
          middle: [],
          right: ["close"],
        },
      },

      commonCaption: true,

      Thumbs: {
        type: "classic",
      },
    });
  }

  let tabs = document.querySelectorAll(".reviews__item .list__link");
  
  tabs.forEach((tab) => {
    tab.addEventListener("click", (el) => {
      tabs.forEach((tab) => {
        tab.classList.remove("active");
      }); 
      tab.classList.add("active");

      activeTabContent(tab.getAttribute('data-tab'));
    });
  });
});

function activeTabContent(contentClass){
  let tabsContents = document.querySelectorAll(".reviews__list");
  if(tabsContents){
    tabsContents.forEach((content) => {
      content.classList.remove("active");
      if(content.classList.contains(contentClass)){
        content.classList.add("active");
      }
    });
  }
}