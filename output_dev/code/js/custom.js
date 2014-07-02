/*
 * @file
 * Custom JS behavior
 * @copyright 2014 palantir.net
 */

$(document).ready(function () {
  // Catch clicks on the navigation button to open/close the menu.
  $('a.navigation-button').click(function(event) {
    // Open the menu if it isn't open.
    if (! $(this).hasClass('active')) {
      $('.navigation-button').addClass('active');
      $('.navigation-list').addClass('is-active');
    }
    else {
      $('.navigation-button').removeClass('active');
      $('.navigation-list').removeClass('is-active');
    }
  });

  // Catch clicks on the parent navigation to open/close child menu items.
  $('li.navigation-list__item:has(ul)').click(function() {
    if ($(this).hasClass('active')) {
      $(this).removeClass('active');
      $('ul', this).removeClass('is-active');
    }
    else {
      $(this).addClass('active');
      $('ul', this).addClass('is-active');
    }
  });


  // Catch clicks on the page when the menu is open to close it.
  $('html').click(function(event) {
    // Remove classes from both because we don't know what element was clicked.
    //$('.navigation-button').removeClass('active');
    //$('.navigation-list').removeClass('is-active');
  });


  //   // Catch clicks on contact list item to open the location drawer
  // $('.office-location').click(function() {
  //   if($(window).width() < 655) {
  //       // If it is already active, remove the class (closing the drawer)
  //     if ($(this).hasClass('active')) {
  //       $(this).removeClass('active');
  //       $( this ).children('.office-address').removeClass('is-active');
  //     }
  //       // else add the classes to open the drawer
  //     else {
  //       $(this).addClass('active');
  //       $( this ).children('.office-address').addClass('is-active');
  //     }
  //     // Prevent the click.
  //     return false;
  //   }
  // });


  // Catch clicks on contact list item to open the contact information drawer
  $('.contact-name').click(function() {
    if($(window).width() < 655) {
        // If it is already active, remove the class (closing the drawer)
      if ($(this).hasClass('active')) {
        $(this).removeClass('active');
        $( this ).siblings('.contact-drawer').removeClass('is-active');
      }
        // else add the classes to open the drawer
      else {
        $(this).addClass('active');
        $( this ).siblings('.contact-drawer').addClass('is-active');
      }
      // Prevent the click.
      return false;
    }
  });

// Change image on click
$('.contact-name').click(function(){
  // if the image is a down arrow
  if ( $(this).children('.button').attr('src') == 'http://www.eimerstahl.com/assets/imgs/site/arrow-down.png' ) {
      // change it to an X
      $(this).children('.button').attr('src','http://www.eimerstahl.com/assets/imgs/site/navigation-x.png');
  }
      // otherwise, change it to a down arrow.
    else {
      $(this).children('.button').attr('src','http://www.eimerstahl.com/assets/imgs/site/arrow-down.png');
    }
    });


  // Catch clicks on email to open popup dialogue
  $('.email').click(function(event) {
      $( this ).addClass('active');
      $( this ).parent().siblings('.black_overlay').addClass('is-active');
      $( this ).parent().siblings('.black_overlay').children('.card').addClass('is-active');
  });

  // Catch clicks on pop up x to close the dialogue.
  $('.card-x').click(function(event) {
      $('.email').removeClass('active');
      $('.black_overlay').removeClass('is-active');
      $('.card').removeClass('is-active');
  });


// active classes on main navigation and sidebar for landing pages

$(function() {

    var pathArray = window.location.pathname.split( '/' );
  if (pathArray[1] == 'firm') {
        $('#firm').addClass('is-current');
        $('#menu-icon').attr('src','http://www.eimerstahl.com/assets/imgs/site/firm-menu.png');
  }
    else if  (pathArray[1] == 'lawyers') {
        $('#lawyers').addClass('is-current');
        $('#menu-icon').attr('src','http://www.eimerstahl.com/assets/imgs/site/lawyers-menu.png');
    }
    else if (pathArray[1] == 'clients') {
        $('#clients').addClass('is-current');
        $('#menu-icon').attr('src','http://www.eimerstahl.com/assets/imgs/site/clients-menu.png');
    }
    else if (pathArray[1] == 'contact') {
        $('#contact').addClass('is-current');
        $('#menu-icon').attr('src','http://www.eimerstahl.com/assets/imgs/site/contact-menu.png');
    }
    else if (pathArray[1] == 'news') {
        $('#media').addClass('is-current');
        $('#menu-icon').attr('src','http://www.eimerstahl.com/assets/imgs/site/media-menu.png');
    }
});

  /**
   * Homepage Article rotation (no fade) display.
   *
   * @param object element
   *   jQuery element object.
   * @param int speed
   *   Speed of the rotation in milliseconds.
   * @param int numToShow
   *   Number of items to show in a given rotation.
   */
  function rotateItems(element, speed, numToShow) {
    // Variable defaults.
    if (typeof speed == 'undefined') {
      var speed = 5000;
    }
    if (typeof numToShow == 'undefined') {
      var numToShow = 1;
    }

    // Start with the first child of this element.
    var current = 0;
    var items = element.children();

    // Hide the element's children on page load.
    element.children().hide();
    // Show the first item.
    element.children().first().show();

    setInterval(function() {
      for (var i = current; i < (current + numToShow ); i++ ) {
        items.eq(i).fadeOut(300, function() {
          var idx = (items.index(this) + numToShow) % items.length;
          items.eq(idx).fadeIn(300);
        });
      }
      current += numToShow;

      if (current >= items.length) current = 0;

    }, speed);
  }

  // Rotate the news items on the front page.
  rotateItems($('.home__news-list'), 6000);

});

// homepage image rotation (fade) only at tablet/desktop sizes.

function cycleImages(){
      if($(window).width() > 600) {
      var $active = $('#cycler .active');
      var $next = ($active.next().length > 0) ? $active.next() : $('#cycler div:first');
      $next.css('z-index',-3);//move the next image up the pile
      $active.fadeOut(1500,function(){//fade out the top image
      $active.css('z-index',-4).show().removeClass('active');//reset the z-index and unhide the image
          $next.css('z-index',-2).addClass('active');//make the next image the top one
      });
    }
  }

$(document).ready(function(){
// run every 7s
setInterval('cycleImages()', 8000);
})


