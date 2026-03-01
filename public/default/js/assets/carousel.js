const Carousel = {
    responsiveNoAutoPlay: {
        3: {
            0: {
                items: 1,
                nav: true
            },
            1200: {
                items: 3
            },
            1024: {
                items: 3
            },
            768: {
                items: 2
            },
            480: {
                items: 1
            },
            380: {
                items: 1
            }
        },
        4: {
            0: {
                items: 2,
                nav: true
            },
            1200: {
                items: 4
            },
            1024: {
                items: 4
            },
            768: {
                items: 3
            },
            480: {
                items: 2
            },
            380: {
                items: 2
            }
        },
        5: {
            0: {
                items: 2,
                nav: true
            },
            1200: {
                items: 5
            },
            1024: {
                items: 5
            },
            768: {
                items: 4
            },
            480: {
                items: 2
            },
            380: {
                items: 2
            }
        },
        9: {
            0: {
                items: 2,
                nav: true
            },
            1200: {
                items: 8
            },
            1024: {
                items: 7
            },
            768: {
                items: 5
            },
            480: {
                items: 3
            },
            380: {
                items: 3
            }
        },
        10: {
            0: {
                items: 3,
                nav: true
            },
            1200: {
                items: 10
            },
            1024: {
                items: 7
            },
            768: {
                items: 5
            },
            480: {
                items: 3
            }
        }
    },
    responsiveAutoPlay: {
        5: {
            0: {
                items: 2
            },
            1200: {
                items: 5
            },
            1024: {
                items: 4
            },
            768: {
                items: 3
            },
            480: {
                items: 2
            }

        }
    },
    oneItemAutoPlay: function (selector) {
        if ($(selector).length) {
            $(selector).owlCarousel({
                nav: true,
                items: 1,
                lazyLoad: true,
                loop: true,
                autoplay: true,
                autoplayTimeout: 8500,
                autoplayHoverPause: true
            });
        }
    },
    oneItemNoAutoPlay: function (selector) {
        if ($(selector).length) {
            $(selector).owlCarousel({
                nav: true,
                lazyLoad: true,
                items: 1,
                loop: true
            });
        }
    },
    responsiveItemsNoAutoPlay: function (selector, number, margin = 15) {
        var responsive = this.responsiveNoAutoPlay[number];
        if ($(selector).length) {
            $(selector).owlCarousel({
                nav: true,
                items: number,
                margin: margin,
                lazyLoad: true,
                responsive: responsive
            });
    }
    },
    responsiveItemsAutoPlay: function (selector, number) {
        var responsive = this.responsiveAutoPlay[number];
        if ($(selector).length) {
            $(selector).owlCarousel({
                nav: true,
                items: number,
                loop: true,
                autoplay: true,
                autoplayTimeout: 3000,
                autoplayHoverPause: true,
                margin: 15,
                lazyLoad: true,
                responsive: responsive
            });
        }
    },
    start: function () {
        this.oneItemAutoPlay("#owl-slider");
        this.oneItemNoAutoPlay(".owl-slider-1x");
        this.responsiveItemsNoAutoPlay(".owl-items-3x", 3);
        this.responsiveItemsNoAutoPlay(".owl-items-4x", 4);
        this.responsiveItemsNoAutoPlay(".owl-items-5x", 5);
        this.responsiveItemsNoAutoPlay(".owl-items-5x0", 5, 0);
        this.responsiveItemsNoAutoPlay(".owl-items-8x", 8);
        this.responsiveItemsNoAutoPlay("#list-category-home", 9);
        this.responsiveItemsAutoPlay(".partners-logos", 5);
    }
};