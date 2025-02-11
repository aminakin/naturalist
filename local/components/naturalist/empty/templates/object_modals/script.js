document.addEventListener("DOMContentLoaded", function () {
  if (Fancybox !== "undefined") {
    Fancybox.bind('[data-fancybox="gallery"]', {
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
