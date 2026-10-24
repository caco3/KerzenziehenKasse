/**
 * Firework displays short notifications at top of page,
 * then fades out a few seconds later (no user interaction)
 * Source: https://www.jqueryscript.net/other/Simple-Top-Notification-Plugin-with-jQuery-firework-js.html
 *         https://github.com/smalldogs/fireworkjs
 * @param   m   string    message
 * @param   t   string    (optional) message type ('success', 'warning', 'error')
 * @param   l   number    (optional) length of time to display message in milliseconds
 * @return  id unique element id
 */
/**
 * Firework plugin IIFE; defines the window.firework notification API.
 *
 * @param {jQuery} $ - The jQuery object.
 * @param {Window} window - The global window object.
 */
;(function ($, window) {
  "use strict";

  window.firework = {
    /**
     * Shows a notification at the top of the page which fades out
     * automatically after a few seconds.
     *
     * @param {string} m - Message to display.
     * @param {string} [t] - Message type ('success', 'warning', 'error').
     * @param {number} [l] - Display duration in milliseconds (default 1500).
     * @returns {string|boolean} The unique element id of the notification,
     *          or false when called without a message string.
     */
    launch: function(m, t, l) {
      if (typeof m != 'string') {
        console.error('Error: Call to firework() without a message');
        return false
      }

      var c = 'firework' // css class(es)
        , p = 10 // pixels from top or page to display
        , d = new Date()
        , s = d.getTime() // used to create unique element ids
        , fid = "firework-"+ s; // firework id

      if (typeof t !== 'undefined') c += ' '+ t; // add any user defined classes

      $('.firework').each(
        /**
         * Stacks the new notification below existing firework elements by
         * accumulating their heights into the top offset.
         */
        function(){ // account for existing fireworks and move new one below
        p += parseInt($(this).height()) + 30
      });

      $('<div id="'+ fid +'" class="'+ c +'">'+ m +'<span class="closingHandle"><a onclick="firework.remove(\'#'+ fid +'\')">x</a></span></div>')
        .appendTo('body')
        .animate({
          opacity: 1,
          top: p +'px'
        });

      setTimeout(
        /** Removes the notification after its display duration elapsed. */
        function(){ firework.remove("#"+ fid) }, typeof l == "number" ? l : 1500);
      return fid;
    },

    /**
     * Fades out and removes a firework notification.
     *
     * @param {string} t - jQuery selector of the notification to remove (e.g. '#firework-123').
     */
    remove : function(t) {
      $(t)
        .animate({
          opacity: 0
        })
        .promise()
        .done(
          /** Removes the notification element after the fade-out animation. */
          function(){
          $(t).remove()
        })
    },

    /**
     * Stores a notification in a cookie so it is shown after the next
     * page load.
     *
     * @param {string} m - Message to display.
     * @param {string} [t] - Message type ('success', 'warning', 'error').
     * @param {number} [l] - Display duration in milliseconds.
     */
    sticky : function(m, t, l) {
      $.cookie("firework", '{ "message" : "'+ m +'", "type" : "'+ t +'", "display" : "'+ l +'" }', { path: '/' })
    }
  };

  // checks for firework cookie on dom ready
  /**
   * On DOM ready, shows a stored 'firework' cookie notification (if any)
   * and clears the cookie.
   */
  $(function() {
    if (typeof $.cookie == "function") {
      if ($.cookie("firework")) {
        var ex = $.parseJSON($.cookie("firework"))
        setTimeout(
          /** Launches the notification stored in the firework cookie. */
          function(){ firework.launch(ex.message, ex.type, parseInt(ex.display) > 0 ? parseInt(ex.display) : null) }, 1000)
        $.cookie("firework", null, { path: '/'})
      }
    }
  });
})(jQuery, window);
