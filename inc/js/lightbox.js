(function ($) {
   $(function() {
    $('.lightbox-hwp').hwplightbox({
        src:'href',
        group:'data-lightbox',
        grouping: true,
        infinite: true,
        animationGroup: 'slide',
        animationDuration: 600,
        fadeDuration: 600,
        easing: 'swing',
        enableKeypress: true,
        enableSwipe: true
    });
    });
  if($(window).width()<851) {
    $('.lightbox-hwp').unbind('click');
    $('.lightbox-hwp').click(function(e) {
      e.preventDefault();
    })
  }
  $(window).resize(function() {
    if($(window).width()<851) {
      $('.lightbox-hwp').unbind('click');
      $('.lightbox-hwp').click(function(e) {
        e.preventDefault();
      })

    } else {
      $('.lightbox-hwp').unbind('click').hwplightbox({
        src:'href',
        group:'data-lightbox',
        grouping: true,
        infinite: true,
        animationGroup: 'slide',
        animationDuration: 600,
        fadeDuration: 600,
        easing: 'swing',
        enableKeypress: true,
        enableSwipe: true
      });
    }
  });

  $(function() {
    $('.lightbox-hwp-nogroup').hwplightbox({
        src:'href',
        group:'data-lightbox',
        grouping: false,
        infinite: true,
        animationGroup: 'slide',
        animationDuration: 600,
        fadeDuration: 600,
        easing: 'swing',
        enableKeypress: true,
        enableSwipe: true
    });
    });
  if($(window).width()<751) {
    $('.lightbox-hwp-nogroup').unbind('click');
    $('.lightbox-hwp-nogroup').click(function(e) {
      e.preventDefault();
    })
  }
  $(window).resize(function() {
    if($(window).width()<751) {
      $('.lightbox-hwp-nogroup').unbind('click');
      $('.lightbox-hwp-nogroup').click(function(e) {
        e.preventDefault();
      })

    } else {
      $('.lightbox-hwp-nogroup').unbind('click').hwplightbox({
        src:'href',
        group:'data-lightbox',
        grouping: false,
        infinite: true,
        animationGroup: 'slide',
        animationDuration: 600,
        fadeDuration: 600,
        easing: 'swing',
        enableKeypress: true,
        enableSwipe: true
      });
    }
  });

})(jQuery);


(function ($) {

  //you need to install jquery.scrolllock.js and hammer.js to use this library
  $.fn.hwplightbox = function (settings) {

      var template = '\
     <div class="hwp-lightbox" style="opacity:0;">\
         <div class="hwp-lightbox-close"><span class="ri ri-cross-circle"></span></div>\
         <div class="hwp-lightbox-container">\
             <div class="hwp-lightbox-image">\
                 <img class="hwp-lightbox-image-image">\
                 </img>\
                <div class="hwp-lightbox-title"></div>\
             </div>\
         </div>\
         <div class="hwp-lightbox-navigation" style="display:none;">\
             <div class="hwp-lightbox-button-prev"><span class="ri ri-arrow-left"></span></div>\
             <div class="hwp-lightbox-button-next"><span class="ri ri-arrow-right"></span></div>\
         </div>\
     </div>';

      var selector = $(this);
      $(this).each(function () {
          $(this).click(function (e) {
              e.preventDefault();
              var options = {
                  selectedImages: selector,
                  isAnimating: false,
                  template: template,
                  config: $(this),
                  image: $(this).find('img'),
                  lightbox: $(template).insertAfter('body'),
                  currentImage: 0,
                  useroptions: {
                      src: 'data-lightbox-src',
                      group: 'data-hwp-lightbox-group',
                      grouping: true,
                      infinite: true,
                      animationGroup: 'slide',
                      animationDuration: 600,
                      fadeDuration: 600,
                      easing: 'swing',
                      enableKeypress: true,
                      enableSwipe: true
                  }
              };
              $.each(settings, function (key, value) {
                  options['useroptions'][key] = value;
              });

              $.scrollLock(true);
              initLightbox(options.lightbox, options.useroptions.fadeDuration);
              generateNavigationEvents(options);
              generateKeyAndSlideEvents(options);
              updateTemplate(options.image, options.config, options.lightbox.find('.hwp-lightbox-container'), options.useroptions.src);
          });
      });


      return this;
  };

  //create startig animation of lightbox
  function initLightbox(lightbox, fadeDuration) {
      lightbox.animate({opacity: 1}, fadeDuration);
  }

  function generateNavigationEvents(options) {
      //close
      options.lightbox.find('.hwp-lightbox-close').click(function () {
          $.scrollLock(false);
          $('body').unbind('keyup.lightbox');
          options.lightbox.animate({opacity: '-=1'}, {
              duration: options.useroptions.fadeDuration, complete: function () {
                  options.lightbox.remove();
              }
          });
      });
      if (options.useroptions.grouping) {
          options.lightbox.find('.hwp-lightbox-navigation').css('display','block');
          var groupLength = 0;
          options.selectedImages.each(function () {
              if ($(this).attr(options.useroptions.group) === options.config.attr(options.useroptions.group)) {
                  if ($(this)[0] === $(options.config)[0]) {
                      options.currentImage = groupLength;
                  }
                  groupLength++;
              }
          });
          if (groupLength > 1) {
              $('<div class="hwp-lightbox-item-number">' + (options.currentImage + 1) + '/' + groupLength + '</div>').insertBefore(options.lightbox.find('.hwp-lightbox-navigation'));
              options.lightbox.find('.hwp-lightbox-button-prev').on('click', function () {
                  createAnimation(false, options)
              });
              options.lightbox.find('.hwp-lightbox-button-next').on('click', function () {
                  createAnimation(true, options)
              });

              if (!options.useroptions.infinite) {
                  //next
                  if (options.currentImage === 0) {
                      options.lightbox.find('.hwp-lightbox-button-prev').hide();
                  }
                  //prev
                  if (options.currentImage === groupLength - 1) {
                      options.lightbox.find('.hwp-lightbox-button-next').hide();
                  }
              }
          } else {
              options.lightbox.find('.hwp-lightbox-navigation').hide();
          }
      }
  }

  function generateKeyAndSlideEvents(options) {
      var isClosing = false;
      options.lightbox.mousedown(function (e) {
          isClosing = $(e.target).hasClass('hwp-lightbox');
      }).mouseout(function () {
          isClosing = false;
      }).mouseup(function (e) {
          if ($(e.target).hasClass('hwp-lightbox') && isClosing) {
              options.lightbox.find('.hwp-lightbox-close').trigger('click');
          }
      });
      if (options.useroptions.enableKeypress) {
          $('body').on('keyup.lightbox', function (event) {
              //down and right
              if (event.keyCode === 39 || event.keyCode === 40) {
                  options.lightbox.find('.hwp-lightbox-button-next:visible').trigger('click');
              }
              //up and left
              if (event.keyCode === 37 || event.keyCode === 38) {
                  options.lightbox.find('.hwp-lightbox-button-prev:visible').trigger('click');
              }
              //escape key
              if (event.keyCode === 27) {
                  options.lightbox.find('.hwp-lightbox-close').trigger('click');
              }
          });
      }
      if (options.useroptions.enableSwipe) {
          var hammerLightbox = new Hammer(options.lightbox[0]);
          hammerLightbox.on('swipeleft swiperight', function (event) {
              if (event.type === 'swipeleft') {
                  options.lightbox.find('.hwp-lightbox-button-next:visible').trigger('click');
              } else if (event.type === 'swiperight') {
                  options.lightbox.find('.hwp-lightbox-button-prev:visible').trigger('click');
              }
          });
      }
  }

  function updateTemplate(image, config, lightbox, src) {
      var title = image.attr('alt');
      if (title === '') {
          title = image.attr('title')
      }
      if (title === '') {
          var srcSplit = config.attr(src).split('/');
          title = srcSplit[srcSplit.length - 1];
      }
      lightbox.find('.hwp-lightbox-image-image').attr({
          'src': config.attr(src),
          'title': title,
          'alt': image.attr('alt')
      });
      if ($(image).height() > $(image).width()) {
          lightbox.addClass('vertical');
      } else if ($(image).height() == $(image).width()) {
          lightbox.addClass('square');
      } else {
        lightbox.removeClass('vertical');
      }
      lightbox.find('.hwp-lightbox-title').text(title);
  }

  function createAnimation(direction, options) {
      if (!options.isAnimating) {
          options.isAnimating = true;
          var type = options.useroptions.animationGroup;
          animateLightbox(direction, options, type);
      }
  }

  function slideLightbox(animationOptions, newImageContainer, oldImageContainer, options, nextImage, currentImage) {
      options.lightbox.find('.hwp-lightbox-container').animate(animationOptions, options.useroptions.animationDuration, options.useroptions.easing, function () {
          newImageContainer.css('left', '');
          oldImageContainer.remove();
          options.image = nextImage;
          options.currentImage = currentImage;
          options.isAnimating = false;
      });
  }

  function animateLightbox(direction, options, type) {
      var factor = direction ? 1 : -1;
      var windowWidth = $(window).width();
      var oldImageContainer = options.lightbox.find('.hwp-lightbox-container');
      var newImageContainer = $('<div class="hwp-lightbox-container"><div class="hwp-lightbox-image"><img class="hwp-lightbox-image-image"></div><div class="hwp-lightbox-title"></div></div></div>').insertAfter(oldImageContainer);
      var nextImage = null;
      var groupLength = 0;
      var currentImage = 0;
      var firstImage = null;
      var lastImage = null;
      options.selectedImages.each(function () {
          if ($(this).attr(options.useroptions.group) === options.config.attr(options.useroptions.group)) {
              if (groupLength === 0) {
                  firstImage = $(this);
              }
              if (groupLength === options.currentImage + factor) {
                  currentImage = groupLength;
                  nextImage = $(this);
              }
              lastImage = $(this);
              groupLength++;
          }
      });

      if (options.useroptions.infinite) {
          if (nextImage === null) {
              currentImage = direction ? 0 : groupLength - 1;
              nextImage = direction ? firstImage : lastImage;
          }
      } else {

          if (currentImage !== 0) {
              options.lightbox.find('.hwp-lightbox-button-prev').show();
          } else {
              options.lightbox.find('.hwp-lightbox-button-prev').hide();
          }
          if (currentImage !== groupLength - 1) {
              options.lightbox.find('.hwp-lightbox-button-next').show();
          } else {
              options.lightbox.find('.hwp-lightbox-button-next').hide();
          }
      }
      updateTemplate(nextImage.find('img'), nextImage, newImageContainer, options.useroptions.src);
      options.lightbox.find('.hwp-lightbox-item-number').text(currentImage + 1 + '/' + groupLength);
      if (type === 'slide') {
          if (direction) {
              newImageContainer.css('left', parseInt(newImageContainer.css('left').split('px')[0]) + windowWidth + 'px');
              slideLightbox({left: '-=' + windowWidth + 'px'}, newImageContainer, oldImageContainer, options, nextImage, currentImage);
          } else {
              newImageContainer.css('left', parseInt(newImageContainer.css('left').split('px')[0]) - windowWidth + 'px');
              slideLightbox({left: '+=' + windowWidth + 'px'}, newImageContainer, oldImageContainer, options, nextImage, currentImage);
          }
      }
      if (type === 'fade') {
          newImageContainer.css('opacity', '0');
          oldImageContainer.animate({opacity: 0}, options.useroptions.animationDuration, options.useroptions.easing, function () {
              oldImageContainer.remove();
              newImageContainer.animate({opacity: 1}, options.useroptions.animationDuration, options.useroptions.easing, function () {
                  options.image = nextImage;
                  options.currentImage = currentImage;
                  options.isAnimating = false;
              });
          });
      }
  }

}(jQuery));