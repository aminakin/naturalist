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
});
