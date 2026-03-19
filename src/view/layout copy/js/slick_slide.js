$('.slicker').slick({
    dots: true,
    infinite: true,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 4,
    Arrows: true,
    Prev_Arrow: '<button type="button" class="slick-prev slick_arrow"><i class="fa-solid fa-angle-left"></i></button>',
    Next_Arrow: '<button type="button" class="slick-next slick_arrow"><i class="fa-solid fa-angle-right"></i></button>',
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true,
          arrows: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
          arrows: true
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: true
        }
      }
      // You can unslick at a given breakpoint now by adding:
      // settings: "unslick"
      // instead of a settings object
    ]
  });

  $('.fade').slick({
    dots: true,
    infinite: true,
    speed: 300,
    autoplay: true,
    autoplaySpeed: 2000,
    Arrows: false
  });

  $('.center').slick({
    centerMode: true,
    dots: true,
    infinite: true,
    slidesToShow: 1,
    speed: 300,
    autoplay: true,
    autoplaySpeed: 2000,
    responsive: [
      {
        breakpoint: 768,
        settings: {
          arrows: false,
          centerMode: true,
          centerPadding: '40px',
          autoplay: true,
          autoplaySpeed: 2000,
          slidesToShow: 3
        }
      },
      {
        breakpoint: 480,
        settings: {
          arrows: false,
          centerMode: true,
          autoplay: true,
          autoplaySpeed: 2000,
          centerPadding: '40px',
          slidesToShow: 1
        }
      }
    ]
  });
