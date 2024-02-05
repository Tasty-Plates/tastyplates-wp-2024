var is_rtlz;
if (search_strings.rtlz !== "" && search_strings.rtlz === "1") {
    is_rtlz = true;
} else {
    is_rtlz = false;
}
(function($) {
    "use strict";
    var ajax_url = $("input#dwt_listing_ajax_url").val();
    /*For Title & Location*/
    $('#get_title,#l_loc,#status_success,#rated').on('click', function() {
        $('.sk-circle').show();
        $(".masonery_wrap").html('');
        $(".feat_slider").html('');
        $("#listing_ajax_pagination").html('');
        $.post(ajax_url, {
            action: 'dwt_ajax_search',
            collect_data: $("form#search_form_ajax").serialize(),
        }).done(function(response) {
            $('.sk-circle').hide();
            if ($.trim(response) != "") {
                var res = response.split("|");
                $(".result-area h4.pull-left strong").html(res[0]);
                $(".masonery_wrap").html(res[1]);
                $("#result_reset").html(res[2]);
                $("#listing_ajax_pagination").html(res[3]);
                $(".feat_slider").html(res[4]);
                map_regenerate();
                regenerate_masnory();
                regenerate_tiksy();
                regenerate_slider();
            }
        });
    });

    /*For Category Change*/
    $('#l_category,#l_tag, #region,#order_by').on('change', function() {
        $("#selected_loc").val($("#region").val());
        $('.sk-circle').show();
        $(".masonery_wrap").html('');
        $(".feat_slider").html('');
        $("#listing_ajax_pagination").html('');
        var cat_id = $('#l_category').val();
        var sort_by = $("#order_by").val();
        $('#accordionz').hide();
        $.post(ajax_url, {
            action: 'dwt_ajax_search',
            collect_data: $("form#search_form_ajax").serialize(),
            sort_by: sort_by,
            search_filter_type: 'map_sidebar_filter'
        }).done(function(response) {
            $('.sk-circle').hide();
            if ($.trim(response) != "") {
                var res = response.split("|");
                $(".result-area h4.pull-left strong").html(res[0]);
                $(".masonery_wrap").html(res[1]);
                $("#result_reset").html(res[2]);
                $("#listing_ajax_pagination").html(res[3]);
                $(".feat_slider").html(res[4]);

                $("form#search_form_ajax #dynamic_fields_map_on").html('');
                $("form#search_form_ajax #dynamic_fields_map_on").html(res[5]);
                $('.custom-checkbox').iCheck({ checkboxClass: 'icheckbox_flat', radioClass: 'iradio_flat' });
                $('.dynamic_field_select').select2({ allowClear: true });
                map_regenerate();
                regenerate_masnory();
                regenerate_tiksy();
                regenerate_slider();
            }
        });
        dwt_listing_get_category_features(cat_id);
    });

    //for filters
    $(document).on('click', '#d_getfilters', function() {
        $('.sk-circle').show();
        $(".masonery_wrap").html('');
        $(".feat_slider").html('');
        $("#listing_ajax_pagination").html('');
        var sort_by = $("#order_by").val();
        $.post(ajax_url, {
            action: 'dwt_ajax_search',
            collect_data: $("form#search_form_ajax").serialize(),
            sort_by: sort_by
        }).done(function(response) {
            $('.sk-circle').hide();
            if ($.trim(response) != "") {
                var res = response.split("|");
                $(".result-area h4.pull-left strong").html(res[0]);
                $(".masonery_wrap").html(res[1]);
                $("#result_reset").html(res[2]);
                $("#listing_ajax_pagination").html(res[3]);
                $(".feat_slider").html(res[4]);
                map_regenerate();
                regenerate_masnory();
                regenerate_tiksy();
                regenerate_slider();
            }
        });
    });

    /*
    Custom fields for checkbox, radio filter
     */
    $(document).on('ifChecked', '.multicheckbox_custom, .checkbox_radio', function() {
        dwt_dynamic_fields_search_filter_();
    });
    /*Custom fields for uncheck checkbox, radio filter*/
    $(document).on('ifUnchecked', '.multicheckbox_custom', function() {
        dwt_dynamic_fields_search_filter_();
    });
    /* Custom fields for Select filter */
    $(document).on('change', '.dynamic_field_select', function() {
        dwt_dynamic_fields_search_filter_();
    });

    /* function for handling ajax for checkbox, radio, select (filter) */
    function dwt_dynamic_fields_search_filter_() {
        $.post(ajax_url, {
            action: 'dwt_ajax_search',
            collect_data: $("form#search_form_ajax").serialize(),
        }).done(function(response) {
            $('.sk-circle').hide();
            if ($.trim(response) != "") {
                var res = response.split("|");
                $(".result-area h4.pull-left strong").html(res[0]);
                $(".masonery_wrap").html(res[1]);
                $("#result_reset").html(res[2]);
                $("#listing_ajax_pagination").html(res[3]);
                $(".feat_slider").html(res[4]);
                map_regenerate();
                regenerate_masnory();
                regenerate_tiksy();
                regenerate_slider();
            }
        });
    }

    /*
    Custom fields radio filter
     */
    $(document).on('ifChecked', '.cost_range', function() {
        /*For Cost Range*/
        $('.sk-circle').show();
        var ptype_id = $(this).val();
        $("#listing_ajax_pagination").html('');
        $('input[name=l_price_type]').val(ptype_id);
        $(".masonery_wrap").html('');
        $(".feat_slider").html('');
        $.post(ajax_url, {
            action: 'dwt_ajax_search',
            collect_data: $("form#search_form_ajax").serialize(),
        }).done(function(response) {
            $('.sk-circle').hide();
            if ($.trim(response) != "") {
                var res = response.split("|");
                $(".result-area h4.pull-left strong").html(res[0]);
                $(".masonery_wrap").html(res[1]);
                $("#result_reset").html(res[2]);
                $("#listing_ajax_pagination").html(res[3]);
                $(".feat_slider").html(res[4]);
                map_regenerate();
                regenerate_masnory();
                regenerate_tiksy();
                regenerate_slider();
            }
        });
    });

    /*Reset All Resutl*/
    $(document).on('click', '#reset_ajax_reslut', function() {
        $("#listing_ajax_pagination").html('');
        $("select").select2('destroy').val('').select2({ width: '100%' });
        $('input[name="cost_range"]').attr('checked', false);
        $('input[name="l_price_type"]').val('');
        $('input[name="by_title"]').val('');
        $("input#r_map_lat").val('');
        $("input#r_map_long").val('');
        $('input[name="street_address"]').val('');
        $('.has-clear input[type="text"]').val('');
        $('input[type="radio"]').removeAttr('checked').iCheck('update');
        $('input[type="checkbox"]').removeAttr('checked').iCheck('update');
        $('.sk-circle').show();
        dwt_listing_get_category_features('');
        $(".masonery_wrap").html('');
        $(".feat_slider").html('');
        $('.btn').removeClass('active');
        $.post(ajax_url, {
            action: 'dwt_ajax_search',
            collect_data: $("form#search_form_ajax").serialize(),
        }).done(function(response) {
            $('.sk-circle').hide();
            if ($.trim(response) != "") {
                var res = response.split("|");
                $(".result-area h4.pull-left strong").html(res[0]);
                $(".masonery_wrap").html(res[1]);
                $("#result_reset").html(res[2]);
                $("#listing_ajax_pagination").html(res[3]);
                $(".feat_slider").html(res[4]);
                map_regenerate();
                regenerate_masnory();
                regenerate_tiksy();
                reinit_regions();
                regenerate_slider();
            }
        });
        return false;
    });

    //pagination
    $(document).on('click', '.fetch_result', function() {
        var page_no = $(this).attr("data-page-no");
        $('.masonery_wrap').height('auto');
        $('.sk-circle').show();
        $(".feat_slider").html('');
        $(".masonery_wrap").html('');
        $("#listing_ajax_pagination").html('');
        var sort_by = $("#order_by").val();
        $.post(ajax_url, {
            action: 'dwt_ajax_search',
            collect_data: $("form#search_form_ajax").serialize(),
            page_no: page_no,
            sort_by: sort_by
        }).done(function(response) {
            $('.sk-circle').hide();
            if ($.trim(response) != "") {
                var res = response.split("|");
                $(".result-area h4.pull-left strong").html(res[0]);
                $(".masonery_wrap").html(res[1]);
                $("#result_reset").html(res[2]);
                $("#listing_ajax_pagination").html(res[3]);
                $(".feat_slider").html(res[4]);
                map_regenerate();
                regenerate_masnory();
                regenerate_tiksy();
                regenerate_slider();
            }
        });
    });


    if ($('.for_search_pages ').is('.specific_search')) {
        $('.for_search_pages').typeahead({
            minLength: 1,
            hint: true,
            maxItem: 15,
            order: "asc",
            dynamic: true,
            delay: 200,
            emptyTemplate: $("#no_s_result").val() + "{{query}}",
            source: {
                ajax: {
                    type: "GET",
                    url: ajax_url,
                    data: {
                        q: '{{query}}',
                        action: 'fetch_listing_suggestions_search',
                        loc_id: $("#selected_loc").val(),
                    }
                },
            },
            callback: {
                onCancel: function(node, event) {
                    $('.sk-circle').show();
                    $(".masonery_wrap").html('');
                    $(".feat_slider").html('');
                    $("#listing_ajax_pagination").html('');
                    $.post(ajax_url, {
                        action: 'dwt_ajax_search',
                        collect_data: $("form#search_form_ajax").serialize(),
                    }).done(function(response) {
                        $('.sk-circle').hide();
                        if ($.trim(response) != "") {
                            var res = response.split("|");
                            $(".result-area h4.pull-left strong").html(res[0]);
                            $(".masonery_wrap").html(res[1]);
                            $("#result_reset").html(res[2]);
                            $("#listing_ajax_pagination").html(res[3]);
                            $(".feat_slider").html(res[4]);
                            map_regenerate();
                            regenerate_masnory();
                            regenerate_tiksy();
                            regenerate_slider();
                        }
                    });
                }
            },
        });
    }


    //Region

    $('#region').select2({

        allowClear: true,
        width: '100%',
        rtl: is_rtlz,
        ajax: {
            url: ajax_url,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search query
                    action: 'dwt_listingzones'
                };
            },
            processResults: function(data) {
                var options = [];
                if (data) {

                    $.each(data, function(index, text) {
                        options.push({ id: text[0], text: text[1] });
                    });
                }
                return {
                    results: options
                };
            },
            cache: true
        },
        "language": {
            "errorLoading": function() {
                return search_strings.errorLoading;
            },
            "inputTooShort": function() {
                return search_strings.inputTooShort;
            },
            "searching": function() {
                return search_strings.searching;
            },
            "noResults": function() {
                return search_strings.noResults;
            }
        },
        minimumInputLength: 3
    });


    var map_lat = parseFloat($('#map_lat').val());
    var map_long = parseFloat($('#map_long').val());
    if (search_strings.s_lat != '' && search_strings.s_lon != '') {
        map_lat = search_strings.s_lat;
        map_long = search_strings.s_lon;
    }
    if (map_lat && map_long) {
        var my_icons = document.getElementById('theme_path').value + "assets/images/map-pin.png";
        if ($('#mapid').length) {
            var map = L.map('mapid').setView([map_lat, map_long], 12);
            L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}{r}.png').addTo(map);

            function onLocationFound(e) {
                $("#loc_mez span").addClass('fa-circle-o-notch fa-spin');
                $("#loc_mez span").removeClass('fa-map-marker');
                var radius = e.accuracy / 2;
                var location = e.latlng
                    // L.marker(location).addTo(map).bindPopup("You are within " + radius + " meters from this point").openPopup();
                    // L.circle(location, radius).addTo(map);
                $('.sk-circle').show();
                $(".masonery_wrap").html('');
                $(".feat_slider").html('');
                $("#listing_ajax_pagination").html('');
                $("input#r_map_lat").val(e.latitude);
                $("input#r_map_long").val(e.longitude);
                $.post(ajax_url, {
                    action: 'dwt_ajax_search',
                    collect_data: $("form#search_form_ajax").serialize(),
                    e_lat: e.latitude,
                    e_long: e.longitude
                }).done(function(response) {
                    $('.sk-circle').hide();
                    $("#loc_mez span").addClass('fa-map-marker');
                    $("#loc_mez span").removeClass('fa-circle-o-notch fa-spin');
                    if ($.trim(response) != "") {
                        var res = response.split("|");
                        $(".result-area h4.pull-left strong").html(res[0]);
                        $(".masonery_wrap").html(res[1]);
                        $("#result_reset").html(res[2]);
                        $("#listing_ajax_pagination").html(res[3]);
                        $(".feat_slider").html(res[4]);
                        map_regenerate();
                        regenerate_masnory();
                        regenerate_tiksy();
                        regenerate_slider();
                    }
                });

            }

            function onLocationError(e) {
                alert(e.message);
            }

            $(document).on('click', '#loc_mez', function() {
                map.on('locationfound', onLocationFound);
                map.on('locationerror', onLocationError);
                map.locate();
            });
            var markerClusters = L.markerClusterGroup();
            for (var i = 0; i < listing_markers.length; ++i) {
                var icon = L.divIcon({
                    html: listing_markers[i].icon,
                    iconSize: [50, 50],
                    iconAnchor: [25, 50],
                    popupAnchor: [0, -60]
                });
                var popup = '<div class="map-in-listings"><div class="list-thumbnail"><a href="' + listing_markers[i].listing_link + '"><img class="img-responsive" src="' + listing_markers[i].img + '" alt=""></a>' + listing_markers[i].is_featured + '</div><div class="entry-header"><h3 class="entry-title"><a href="' + listing_markers[i].listing_link + '">' + listing_markers[i].title + ' </a></h3><div class="entry-meta">' + listing_markers[i].ratings + '<span class="posted-date">' + listing_markers[i].posted_on + '</span></div></div></div>';
                var m = L.marker([listing_markers[i].lat, listing_markers[i].lng], { icon: icon }).bindPopup(popup, {
                    minWidth: 270,
                    maxWidth: 270
                });
                markerClusters.addLayer(m);
                map.fitBounds(markerClusters.getBounds());
                map.addLayer(markerClusters);
            }
            var loader = L.control.loader().addTo(map);
            setTimeout(function() {
                loader.hide();
            }, 1500);
            map.scrollWheelZoom.disable();
            map.invalidateSize();
        }
    }

    /*fetch category features*/
    function dwt_listing_get_category_features(cat_id) {
        var category_id = cat_id;
        if (category_id != 'all') {
            $.post(ajax_url, {
                action: 'dwt_listing_get_cat_featurez',
                category_id: category_id,
            }).done(function(response) {
                $('#show_on_request').html("");
                if ($.trim(response) != "") {
                    $(".amenties_ajax").show();
                    $(".amenties_ajax").html(response);
                    $('.custom-checkbox').iCheck({ checkboxClass: 'icheckbox_flat', radioClass: 'iradio_flat' });
                } else {
                    $("#accordionz").hide();
                    $(".amenties_ajax").hide();
                    $('#show_on_request').html("");
                }
            });
        }
    }


})(jQuery);

function map_regenerate() {
    var ajax_urlz = $("input#dwt_listing_ajax_url").val();
    $('.left-area').html('');
    $('.left-area').html('<div id="mapid" class="map"></div><div class="leaf-radius-search"><a id="loc_mez" href="javascript:void(0)"><span class="fa fa-map-marker"></span></a></div>');
    var map_lat = parseFloat($('#map_lat').val());
    var map_long = parseFloat($('#map_long').val());
    if (search_strings.s_lat != '' && search_strings.s_lon != '') {
        map_lat = search_strings.s_lat;
        map_long = search_strings.s_lon;
    }
    if (map_lat && map_long) {
        var my_icons = document.getElementById('theme_path').value + "assets/images/map-pin.png";
        if ($('#mapid').length) {
            var map = L.map('mapid').setView([map_lat, map_long], 12);
            L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}{r}.png').addTo(map);

            function onLocationFound1(e) {
                $("#loc_mez span").addClass('fa-circle-o-notch fa-spin');
                $("#loc_mez span").removeClass('fa-map-marker');
                var radius = e.accuracy / 2;
                var location = e.latlng
                    // L.marker(location).addTo(map).bindPopup("You are within " + radius + " meters from this point").openPopup();
                    // L.circle(location, radius).addTo(map);
                $('.sk-circle').show();
                $(".masonery_wrap").html('');
                $(".feat_slider").html('');
                $("#listing_ajax_pagination").html('');
                $("input#r_map_lat").val(e.latitude);
                $("input#r_map_long").val(e.longitude);
                $.post(ajax_urlz, {
                    action: 'dwt_ajax_search',
                    collect_data: $("form#search_form_ajax").serialize(),
                    e_lat: e.latitude,
                    e_long: e.longitude
                }).done(function(response) {
                    $('.sk-circle').hide();
                    $("#loc_mez span").addClass('fa-map-marker');
                    $("#loc_mez span").removeClass('fa-circle-o-notch fa-spin');
                    if ($.trim(response) != "") {
                        var res = response.split("|");
                        $(".result-area h4.pull-left strong").html(res[0]);
                        $(".masonery_wrap").html(res[1]);
                        $("#result_reset").html(res[2]);
                        $("#listing_ajax_pagination").html(res[3]);
                        $(".feat_slider").html(res[4]);
                        map_regenerate();
                        regenerate_masnory();
                        regenerate_tiksy();
                        regenerate_slider();
                    }
                });

            }

            function onLocationError1(e) {
                alert(e.message);
            }

            $(document).on('click', '#loc_mez', function() {
                map.on('locationfoundz', onLocationFound1);
                map.on('locationerrorz', onLocationError1);
                map.locate();
            });
            var markerClusters = L.markerClusterGroup();
            var current_marker = listing_markersz;
            for (var i = 0; i < current_marker.length; ++i) {
                var icon = L.divIcon({
                    html: current_marker[i].icon,
                    iconSize: [50, 50],
                    iconAnchor: [25, 50],
                    popupAnchor: [0, -60]
                });
                var popup = '<div class="map-in-listings"><div class="list-thumbnail"><a href="' + current_marker[i].listing_link + '"><img class="img-responsive" src="' + current_marker[i].img + '" alt=""></a>' + current_marker[i].is_featured + '</div><div class="entry-header"><h3 class="entry-title"><a href="' + current_marker[i].listing_link + '">' + current_marker[i].title + ' </a></h3><div class="entry-meta">' + current_marker[i].ratings + '<span class="posted-date">' + current_marker[i].posted_on + '</span></div></div></div>';
                var m = L.marker([current_marker[i].lat, current_marker[i].lng], { icon: icon }).bindPopup(popup, {
                    minWidth: 270,
                    maxWidth: 270
                });
                markerClusters.addLayer(m);
                map.addLayer(markerClusters);
                map.fitBounds(markerClusters.getBounds());
            }
            var loader = L.control.loader().addTo(map);
            setTimeout(function() {
                loader.hide();
            }, 1500);
            map.scrollWheelZoom.disable();
            map.invalidateSize();
        }
    }
}


function regenerate_masnory() {
    "use strict";
    // init Isotope
    var $item = $('.masonery_wrap');
    $item.isotope('destroy');
    $item.imagesLoaded(function() {
        $item.isotope({
            itemSelector: '.masonery_item',
            percentPosition: true,
            originLeft: 'is_rtlz',
            layoutMode: 'fitRows',
            transitionDuration: '0.7s',
            masonry: {
                columnWidth: '.masonery_item'
            }
        });
    });
}

function regenerate_tiksy() {
    "use strict";
    $('.tool-tip').tipsy({
        arrowWidth: 10,
        attr: 'data-tipsy',
        cls: null,
        duration: 150,
        offset: 7,
        position: 'top-center',
        trigger: 'hover',
    });
}

function reinit_regions() {
    "use strict";
    var ajax_urlz = $("input#dwt_listing_ajax_url").val();
    $('#region').select2({
        allowClear: true,
        width: '100%',
        rtl: is_rtlz,
        ajax: {
            url: ajax_urlz,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search query
                    action: 'dwt_listingzones'
                };
            },
            processResults: function(data) {
                var options = [];
                if (data) {
                    $.each(data, function(index, text) {
                        options.push({ id: text[0], text: text[1] });
                    });
                }
                return {
                    results: options
                };
            },
            cache: true
        },
        "language": {
            "errorLoading": function() {
                return search_strings.errorLoading;
            },
            "inputTooShort": function() {
                return search_strings.inputTooShort;
            },
            "searching": function() {
                return search_strings.searching;
            },
            "noResults": function() {
                return search_strings.noResults;
            }
        },
        minimumInputLength: 3
    });
}

function regenerate_slider() {
    "use strict";
    if ($('#papular-listing-2-slider').length) {
        $("#papular-listing-2-slider").owlCarousel({
            rtl: is_rtlz,
            loop: true,
            margin: 10,
            dots: false,
            responsiveClass: true,
            navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
            nav: true,
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 2,
                },
                1000: {
                    items: 2,
                }
            }
        });
    }
}