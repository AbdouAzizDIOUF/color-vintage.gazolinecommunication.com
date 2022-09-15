jQuery(function ($) {
  /**
   * Permet d'afficher une barre de
   * scroll et d'accéder aux items
   * éventuellement masqués.
   */
  var sidebar = $('#sidebar-admin');
  var menu = $('#MenuList');

  $(window).resize(function () {
    var toolbar = ($('.toolbar-tray-open.toolbar-horizontal').length) ? 78 : 39;
    var height = $(window).height() - ($('.bg', sidebar).height() + toolbar);

    menu.height(height);
  }).resize();

  $('.toolbar-icon.toolbar-icon-toggle-vertical, .toolbar-icon.toolbar-icon-toggle-horizontal').click(function () {
    setTimeout(function () {
      $(window).resize();
    }, 50);
  });

  /**
   * Affichage des enfants d'un
   * item au hover.
   */
  $('.menu-item').not('.active').hover(function () {
    if ($('.childs', this).length) {
      var height = 0;
      var childs = $('.childs', this);

      $('.menu-item-child', childs).each(function () {
        height += $(this).height();
      });

      childs.height(height);
    }
  }, function () {
    if ($('.childs', this).length) {
      $('.childs', this).height(0);
    }
  });

  /**
   * Taille dynamique des éléments
   * des menu-item-special du menu
   * au hover.
   */
  var items = $('.menu-item-special', menu);
  var nb_items = items.length;

  items.hover(function () {
    switch (nb_items) {
      case 3:
          items.not($(this)).width('25%');
          $(this).width('50%');
        break;
      case 2:
        items.not($(this)).width('25%');
        $(this).width('75%');
        break;
    }
  }, function () {
    switch (nb_items) {
      case 3:
          items.width('33.33%');
        break;
      case 2:
          items.width('50%');
        break;
    }
  });
});