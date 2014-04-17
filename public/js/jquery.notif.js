(function($){
   $.fn.notif = function(options){
      var settings={
        html : '<div class="notification animated fadeInLeft {{cls}}">\
                <div class="notif-left">\
                    <div class="icon">\
                    {{{icon}}}\
                    </div>\
                </div>\
                <div class="notif-right">\
                    <h2>{{title}}</h2>\
                    <p>{{content}}</p>\
                </div>\
            </div>',
         icon:'i',
         timeout : false
        }
        if (options.cls == 'error'){
         settings.icon = 'e';
        }
       if (options.cls == 'success'){
         settings.icon = 'v';
        }
      var options = $.extend(settings,options);
      return this.each(function(){
            var $this = $(this);
            var $notifs = $('> .notifications', this);
            var $notif = $(Mustache.render(options.html,options));
            if ($notifs.length == 0){
                $notifs = $('<div class="notifications"/>'
                    );
                $this.prepend($notifs);
            }   
            $notifs.append($notif);
            if (options.timeout){
               setTimeout(function(){
                  $notif.trigger('click');
               },options.timeout)
            }
            $notif.click(function(event){
               event.preventDefault();
               $notif.addClass('fadeOutLeft').delay(0.1).slideUp(200, function(){
               if( $notif.siblings().length == 0) {
                  $notifs.remove();
               }
               $notif.remove();            
            });
         })
            
      })
   }
   $('.add').click(function(event){
      event.preventDefault();
      $('body').notif({title:'Information', content:'Lorem ipsum dolor sit amet,!', icon:'', timeout:2000})
      })

    
})(jQuery);

$('.animable').each(function(i,e){
  $e = $(e);
  $e.hide();
  $('body').notif({title:$e.attr("data-type"), content:$e.html(), icon:'',cls:$e.attr("data-class"), timeout:3000});
});
   

