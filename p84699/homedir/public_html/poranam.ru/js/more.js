window.onresize = navigationresize;
navigationresize();
function navigationresize() {
    $('#nav li.more').before($('#overflow > li'));
    var navItemMore = $('#nav > li.more'),
        navItems = $('#nav > li:not(.more)'),
        navItemWidth = navItemMore.width(),
        windowWidth = $('#nav').parent().width(),
        navOverflowWidth;
    navItems.each(function() {
        navItemWidth += $(this).width();
    });
    navItemWidth > windowWidth ? navItemMore.show() : navItemMore.hide();
    while (navItemWidth > windowWidth) {
        navItemWidth -= navItems.last().width();
        navItems.last().prependTo('#overflow');
        navItems.splice(-1, 1);
    }
    navOverflowWidth = $('#overflow').width();
}