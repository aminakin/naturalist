document.addEventListener("DOMContentLoaded", function () {
  if (typeof Fancybox !== "undefined") {
    // Удаляем дубли из DOM перед инициализацией
    const seen = new Set();
    const galleryElements = document.querySelectorAll('[data-fancybox="gallery"]');

    galleryElements.forEach(element => {
      const href = element.getAttribute('href');
      if (seen.has(href)) {
        // Убираем data-fancybox у дублирующего элемента
        element.removeAttribute('data-fancybox');
      } else {
        seen.add(href);
      }
    });

    // Стандартная инициализация уже без дублей
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
