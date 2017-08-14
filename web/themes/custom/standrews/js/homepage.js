(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.homepage = {
        attach: function (context, settings) {
            var resizeTimer;

            $(window, context).resize(function(){
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(resizeFunctions(context), 250);
            });

            jQuery.support.transition = false;

            $(context).imagesLoaded(function(){
                loadFunctions(context);
            });

            function loadFunctions(context) {
                homepagePositioning(context);
                homepageSearch(context);
                homepageTimeline(context);
                homepageIdentify(context);
                homepageHeights(context);
            }

            function resizeFunctions(context){
                homepagePositioning(context);
                homepageHeights(context);
            }

            $('.collapse', context).on('shown.bs.collapse', function(){
                homepageHeights(context);
            });

            $('.collapse', context).on('hidden.bs.collapse', function(){
                homepageHeights(context);
            });

            function homepagePositioning(context){
                var $navbar_container = $('.navbar > .container', context);
                var $right_side = $('.home-identify', context);

                if($navbar_container.length > 0 && $right_side.length > 0){
                    var offset = $navbar_container.offset().left - 15;
                    var right_side_offset = $right_side.offset().left - 30;
                    $('.home-search > .home-section-content, .home-browse > .home-section-content', context).css('left', offset);
                    $('.home-section-content', context).css('visibility', 'visible');
                    $('.home-browse .home-section-content, .home-search .home-section-content', context).css('max-width', parseInt(right_side_offset - offset,10));
                    $('#home-right .home-section-content', context).css('max-width', parseInt(($navbar_container.offset().left + $navbar_container.width() + 15) - $right_side.offset().left,10));
                }
            }

            function homepageHeights(context){
                var $home_browse = $('.home-browse', context);
                var $body = $('body', context);

                $home_browse.css('height', '');

                if($body.attr('data-breakpoint') === 'md' || $body.attr('data-breakpoint') === 'lg'){
                    var $home_left = $('#home-left', context);
                    var $home_right = $('#home-right', context);

                    if($home_right.height() > $home_left.height()){
                        $home_browse.css('height', $home_browse.height() + ($home_right.height() - $home_left.height()));
                    }
                    else{
                        $home_browse.css('height', '');
                    }
                }
                else{
                    $home_browse.css('height', '');
                }
            }

            function homepageSearch(context){
                $('#homepage-search', context).submit(function(e){
                    e.preventDefault();

                    var $form = $('#advanced-search-text-search-form', context);
                    $('input[type="search"]', $form).val($('input[type="search"]', this).val());
                    $form.submit();
                });
            }

            function homepageTimeline(context){
                var $timeline_slider = $('#timeline-slider', context);
                var $from = $('#edit-date-from', context);
                var $to = $('#edit-date-to', context);

                $timeline_slider.rangeSlider({
                    arrows: false,
                    bounds: {
                        min: 1920,
                        max: new Date().getFullYear()
                    },
                    step: 1,
                    defaultValues: {
                        min: $from.val(),
                        max: $to.val()
                    },
                    symmetricPositioning: true,
                    scales: [
                        {
                            first: function(val){ return val; },
                            next:  function(val){ return val + 10; },
                            stop:  function(val){ return false; },
                            label: function(val){ return val; }
                        },
                        // Secondary scale
                        {
                            first: function(val){ return val; },
                            next: function(val){
                                if (val % 10 === 9){
                                    return val + 2;
                                }
                                return val + 1;
                            },
                            stop: function(val){ return false; },
                            label: function(){ return null; }
                        }
                    ]
                });

                timelineLabelOffset(1921, 1925);

                $timeline_slider.on('valuesChanged', function(e, data){
                    $from.val(data.values.min);
                    $to.val(data.values.max);
                });

                $timeline_slider.on('valuesChanging', function(e, data){
                    timelineLabelOffset(data.values.min, data.values.max);
                });
            }

            function timelineLabelOffset(min, max){
                if(max - min < 9){
                    $('.ui-rangeSlider-label').addClass('close-label');
                }
                else{
                    $('.ui-rangeSlider-label').removeClass('close-label');
                }
            }

            function homepageIdentify(context) {
                var $carousel = $('#identify-carousel', context);

                $('#identify-prev', context).click(function(e){
                    e.preventDefault();
                    $carousel.carousel('prev');
                });

                $('#identify-pause', context).click(function(e){
                    e.preventDefault();
                    $carousel.carousel('pause');
                    $(this).addClass('hidden');
                    $('#identify-resume').removeClass('hidden');
                });

                $('#identify-resume', context).click(function(e){
                    e.preventDefault();
                    $carousel.carousel('cycle');
                    $(this).addClass('hidden');
                    $('#identify-pause').removeClass('hidden');
                });

                $('#identify-next', context).click(function(e){
                    e.preventDefault();
                    $carousel.carousel('next');
                });

                $('#identify-submit', context).click(function(e){
                    window.location = $('.item.active .node-slide').attr('href');
                });
            }
        }
    };

})(jQuery, Drupal);