(function($) {
    "use strict";
    var ajax_url = $("input#dwt_listing_ajax_url").val();
    var yes_rtl;
    if ($('#is_rtl').val() !== "" && $('#is_rtl').val() === "1") {
        yes_rtl = true;
    } else {
        yes_rtl = false;
    }


    if ($('.admin-panel-scroll').length > 0) {
        $('.admin-panel-scroll .panel-body').slimScroll({
            height: '800px',
            wheelStep: 12,
        });
    }

    if ($('#sidebar-nav .sidebar-scroll').length > 0) {
        $('#sidebar-nav .sidebar-scroll').slimScroll({
            height: 'auto',
            wheelStep: 10,
            color: '#ccc',
        });
    }

    $('.collaspe-btn-admin').on('click', function() {

        $(this).find('.lnr').toggleClass('lnr-menu lnr-cross');

        if ($(window).innerWidth() < 1025) {
            if (!$('body').hasClass('admin-sidebar-active')) {
                $('body').addClass('admin-sidebar-active');
            } else {
                $('body').removeClass('admin-sidebar-active');
            }
        }
    });


    var readURL = function(input) {
        if (input.files && input.files[0]) {

            var fd = new FormData();
            var files_data = $('.profile-file-upload');
            $.each($(files_data), function(i, obj) {
                $.each(obj.files, function(j, file) {
                    fd.append('my_file_upload[' + j + ']', file);
                });
            });

            fd.append('action', 'upload_user_pic');
            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: fd,
                contentType: false,
                processData: false,
                success: function(res) {
                    var res_arr = res.split("|");
                    if ($.trim(res_arr[0]) == "1") {
                        $('.profile-pic').attr('src', res_arr[1]);
                        $('.resize').attr('src', res_arr[1]);
                        $('#img-upload-success').show();
                    } else if ($.trim(res_arr[0]) == "0") {
                        $('#max-img-size').show();
                    } else {
                        $('#error-messages').show();
                    }

                }
            });


        }
    }
    $(".profile-file-upload").on('change', function() {
        readURL(this);
    });

    $(".profile-upload-button").on('click', function() {
        $(".profile-file-upload").click();
    });


    /*--- Reset Password ---*/
    $('#resetPassword').validator().on('submit', function(e) {
        if (e.isDefaultPrevented()) {
            return false;
        } else {
            $('.sonu-button-reset').button('loading');
            $.post(ajax_url, { action: 'dwt_listing_resetmy', collect_data: $("form#resetPassword").serialize(), }).done(function(response) {
                $('.sonu-button-reset').button('reset');
                if ($.trim(response) == '1') {
                    $('#password-success').show();
                }
            });
        }
        return false;
    });


    /*--- Registration Form Action ---*/
    $('#profile-update').validator().on('submit', function(e) {
        if (e.isDefaultPrevented()) {
            return false;
        } else {
            $('#profile-update').button('loading');
            $.post(ajax_url, {
                action: 'dwt_listing_profile_update',
                collect_data: $("form#profile-update").serialize(),
            }).done(function(response) {
                $('#profile-update').button('reset');
                if ($.trim(response) == '1') {
                    $('#p_up').hide();
                    $('#success-messages').show();
                    window.setTimeout(function() {
                        window.location.reload(true);
                    }, 1000);
                }

            });
            /*--- stop submitting ---*/
            return false;
        }
    });


    /*--- Events  ---*/
    function dwt_listing_eventz_zone() {
        Dropzone.autoDiscover = false;
        var acceptedFileTypes = "image/*";
        var fileList = new Array;
        var i = 0;
        $("#event_dropzone").dropzone({
            addRemoveLinks: true,
            paramName: "my_file_upload",
            maxFiles: $('#event_upload_limit').val(),
            gallery_limit: $('#event_img_size').val(),
            acceptedFiles: '.jpeg,.jpg,.png',
            dictMaxFilesExceeded: $('#max_upload_reach').val(),
            url: ajax_url + "?action=upload_dwt_listing_events_images&is_update=" + $('#is_update').val(),
            parallelUploads: 1,
            dictDefaultMessage: $('#dictDefaultMessage').val(),
            dictFallbackMessage: $('#dictFallbackMessage').val(),
            dictFallbackText: $('#dictFallbackText').val(),
            dictFileTooBig: $('#dictFileTooBig').val(),
            dictInvalidFileType: $('#dictInvalidFileType').val(),
            dictResponseError: $('#dictResponseError').val(),
            dictCancelUpload: $('#dictCancelUpload').val(),
            dictCancelUploadConfirmation: $('#dictCancelUploadConfirmation').val(),
            dictRemoveFile: $('#dictRemoveFile').val(),
            dictRemoveFileConfirmation: null,
            init: function() {
                var thisDropzone = this;
                $.post(ajax_url, { action: 'get_event_images', is_update: $('#is_update').val() }).done(function(data) {
                    if (data != 0) {
                        $.each(data, function(key, value) {

                            var mockFile = { name: value.dispaly_name, size: value.size };

                            thisDropzone.options.addedfile.call(thisDropzone, mockFile);

                            thisDropzone.options.thumbnail.call(thisDropzone, mockFile, value.name);
                            $('a.dz-remove:eq(' + i + ')').attr("data-dz-remove", value.id);
                            i++;
                            $(".dz-progress").remove();
                        });
                    }
                    if (i > 0)
                        $('.dz-message').hide();
                    else
                        $('.dz-message').show();
                });

                this.on("addedfile", function(file) {
                    $('.dz-message').hide();
                });
                this.on("success", function(file, responseText) {
                    var res_arr = responseText.split("|");
                    if ($.trim(res_arr[0]) != "0") {
                        $('a.dz-remove:eq(' + i + ')').attr("data-dz-remove", responseText);
                        i++;
                        $('.dz-message').hide();
                    } else {
                        if (i == 0)
                            $('.dz-message').show();
                        this.removeFile(file);
                        $("#listing_msgz").show();
                        $("#listing_msgz .custom-alert__content").text(res_arr[1]);
                    }

                });
                this.on("removedfile", function(file) {

                    var img_id = file._removeLink.attributes[2].value;
                    if (img_id != "") {
                        i--;
                        if (i == 0)
                            $('.dz-message').show();
                        $.post(ajax_url, { action: 'delete_event_image', img: img_id, is_update: $('#is_update').val(), }).done(function(response) {
                            if ($.trim(response) == "1") {
                                $("#listing_msgz").hide();
                                /*this.removeFile(file);*/
                            }
                        });
                    }
                });

            },
        });
    }
    dwt_listing_eventz_zone();


    /*Create Event*/
    $('#event_title').on('blur', function() {

        $(".loader-field").css("display", "block");
        $.post(ajax_url, {
            action: 'create_new_event',
            event_title: $('#event_title').val(),
            is_update: $('#is_update').val(),
        }).done(function(response) {
            $(".loader-field").css("display", "none");
        });
    });

    /*--- Registration Form Action ---*/
    $('#my-events').validator().on('submit', function(e) {
        if (e.isDefaultPrevented()) {
            return false;
        } else {
            var is_update = $('#is_update').val();
            var curent_lang = $('#lang').val();
            $('.sonu-button').button('loading');
            $.post(ajax_url, {
                action: 'my_new_event',
                lang: curent_lang,
                collect_data: $("form#my-events").serialize(),
                is_update: $('#is_update').val(),
            }).done(function(response) {
                $('.sonu-button').button('reset');
                if (is_update) {
                    $.alert({
                        title: get_strings.congratulations,
                        backgroundDismiss: true,
                        rtl: yes_rtl,
                        icon: 'fa fa-smile-o',
                        theme: 'modern',
                        animation: 'scale',
                        type: 'green',
                        content: profile_strings.updated,
                        buttons: {
                            ShowEvent: function() {
                                window.open(response);
                            },
                            Cancel: function() {

                            }
                        }

                    });


                } else {
                    $.alert({
                        title: get_strings.congratulations,
                        rtl: yes_rtl,
                        icon: 'fa fa-smile-o',
                        theme: 'modern',
                        animation: 'scale',
                        type: 'green',
                        content: profile_strings.created,
                        buttons: {
                            ShowEvent: function() {
                                window.open(response);
                            },
                            Cancel: function() {

                            }
                        }
                    });

                }
            });
            /*--- stop submitting ---*/
            return false;
        }
    });


    /* Delete My Events */
    $('.delete-my-events').on('click', function() {
        var event_id = $(this).attr("data-myevent-id");
        $.confirm({
            title: get_strings.confirmation,
            icon: 'fa fa-question-circle',
            theme: 'supervan',
            animation: 'scale',
            content: get_strings.content,
            closeAnimation: 'scale',
            type: 'red',
            buttons: {
                'confirm': {
                    text: get_strings.ok,
                    action: function() {
                        $.post(ajax_url, { action: 'remove_my_events', event_id: event_id, }).done(function(response) {
                            var get_r = response.split('|');
                            if ($.trim(get_r[0]) == '1') {
                                $.alert({ title: get_strings.congratulations, backgroundDismiss: true, rtl: yes_rtl, icon: 'fa fa-smile-o', theme: 'modern', animation: 'scale', type: 'green', content: get_r[1], buttons: { okay: { btnClass: 'btn-blue' } } });
                                window.setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                $.alert({ title: get_strings.whoops, rtl: yes_rtl, icon: 'fa fa-frown-o', theme: 'modern', animation: 'scale', type: 'red', closeIcon: true, backgroundDismiss: true, content: get_r[1], buttons: { okay: { btnClass: 'btn-blue' } } });
                            }
                        });
                    }
                },
                cancle: {
                    text: get_strings.cancle,
                }
            }
        });
        return false;
    });


    /* Expire My Listing */
    $('.expire-my-events').on('click', function() {
        var event_id = $(this).attr("data-myevent-id");
        $.confirm({
            title: get_strings.confirmation,
            icon: 'fa fa-question-circle',
            theme: 'supervan',
            animation: 'scale',
            content: get_strings.event_expiry,
            closeAnimation: 'scale',
            type: 'red',
            buttons: {
                'confirm': {
                    text: get_strings.ok,
                    action: function() {
                        $.post(ajax_url, {
                            action: 'expire_my_events',
                            event_id: event_id
                        }).done(function(response) {
                            var get_r = response.split('|');
                            if ($.trim(get_r[0]) == '1') {
                                $.alert({
                                    title: get_strings.congratulations,
                                    rtl: yes_rtl,
                                    icon: 'fa fa-smile-o',
                                    theme: 'modern',
                                    animation: 'scale',
                                    type: 'green',
                                    content: get_r[1],
                                    buttons: { okay: { btnClass: 'btn-blue' } }
                                });
                                window.setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                $.alert({ title: get_strings.whoops, rtl: yes_rtl, icon: 'fa fa-frown-o', theme: 'modern', animation: 'scale', type: 'red', closeIcon: true, backgroundDismiss: true, content: get_r[1], buttons: { okay: { btnClass: 'btn-blue' } } });
                            }
                        });
                    }
                },
                cancle: {
                    text: get_strings.cancle,
                }
            }
        });
        return false;
    });

    /* Expire My Listing */
    $('.reactive-my-events').on('click', function() {
        var event_id = $(this).attr("data-myevent-id");
        $.confirm({
            title: get_strings.confirmation,
            icon: 'fa fa-question-circle',
            theme: 'supervan',
            animation: 'scale',
            content: get_strings.event_reactive,
            closeAnimation: 'scale',
            type: 'red',
            buttons: {
                'confirm': {
                    text: get_strings.ok,
                    action: function() {
                        $.post(ajax_url, { action: 'reactive_my_events', event_id: event_id, }).done(function(response) {
                            var get_r = response.split('|');
                            if ($.trim(get_r[0]) == '1') {
                                $.alert({ title: get_strings.congratulations, rtl: yes_rtl, icon: 'fa fa-smile-o', theme: 'modern', animation: 'scale', type: 'green', content: get_r[1], buttons: { okay: { btnClass: 'btn-blue' } } });
                                window.setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                $.alert({ title: get_strings.whoops, rtl: yes_rtl, icon: 'fa fa-frown-o', theme: 'modern', animation: 'scale', type: 'red', closeIcon: true, backgroundDismiss: true, content: get_r[1], buttons: { okay: { btnClass: 'btn-blue' } } });
                            }
                        });
                    }
                },
                cancle: {
                    text: get_strings.cancle,
                }
            }
        });
        return false;
    });



    $('#food_menu').validator().on('submit', function(e) {
        if (e.isDefaultPrevented()) {
            return false;
        } else {
            $('.sonu-button').button('loading');
            $.post(ajax_url, {
                action: 'my_new_menu',
                collect_data: $("form#food_menu").serialize()
            }).done(function(response) {
                $('.sonu-button').button('reset');
            });
        }
        return false;
    });


    $('#dwt_create_menu').validator().on('submit', function(e) {
        if (e.isDefaultPrevented()) {
            return false;
        } else {
            $('.sonu-button').button('loading');
            var listing_id = $("#menu_parent_listingz").val();
            $.post(ajax_url, { action: 'dwt_create_menutype', collect_data: $("form#dwt_create_menu").serialize(), listing_id: listing_id }).done(function(response) {
                $("#dwt_create_menu")[0].reset();
                $('.sonu-button').button('reset');
                $('.menu_modalz').modal('hide');
                $("#append_result").html(response);
            });
            return false;
        }
    });


    /*--- edit menu ---*/
    $(document).on('click', '.edit-lmenu', function() {
        var listing_id = $(this).attr("data-id");
        var key = $(this).attr("data-key");
        $('.edit-button-' + key).button('loading');
        var trid = $(this).closest('tr').attr('id');
        $.post(ajax_url, { action: 'dwt_edit_menutype', listing_id: listing_id, key: key, trid: trid }).done(function(response) {
            $('.edit-button-' + key).button('reset');
            $('.edit_modal_menu').html(response);
            $('.menu_modalz1').modal('show');
            $('#dwt_update_menu').validator();
        });
        return false;
    });


    $(document).on('submit', '#dwt_update_menu', function(e) {
        if (e.isDefaultPrevented()) {
            return false;
        } else {
            $('.sonu-button').button('loading');
            $.post(ajax_url, { action: 'dwt_update_menutype', collect_data: $("form#dwt_update_menu").serialize() }).done(function(response) {
                $('.sonu-button').button('reset');
                $('.menu_modalz1').modal('hide');
                var res = response.split("|");
                $("#" + res[1] + " span.menu_name").html(res[0]);
            });
            return false;
        }
    });




    /*--- Add new items in menu ---*/
    $(document).on('click', '.menu_items_addition', function() {
        $('.menu_modalz_itemz').modal('show');
        var k_ref = $(this).attr("data-key-ref");
        var k_list = $(this).attr("data-key-id");
        $("input#reference_key").val(k_ref);
        $("input#reference_listing").val(k_list);
        return false;
    });

    /*--- Inserting menu items ---*/
    $('#ad_menu_listz').validator().on('submit', function(e) {
        if (e.isDefaultPrevented()) {
            return false;
        } else {
            $('.sonu-button').button('loading');
            $.post(ajax_url, { action: 'dwt_ad_new_menu_listz', collect_data: $("form#ad_menu_listz").serialize() }).done(function(response) {
                $("#ad_menu_listz")[0].reset();
                $('.sonu-button').button('reset');
                $('.menu_modalz_itemz').modal('hide');
                $('.show_inner_menuz').html(response);
                $('.menu_item_historyz').modal('show');
            });
            return false;
        }
    });


    /*--- edit inner menu type ---*/
    $(document).on('click', '.inner-menu-edit', function() {
        var listing_id = $(this).attr("data-listing_id");
        var key = $(this).attr("data-refer_key");
        $('.lmenu-edit-' + key).button('loading');
        var trid = $(this).closest('tr').attr('id');
        $.post(ajax_url, { action: 'dwt_edit_inner_menugroup', listing_id: listing_id, key: key, trid: trid }).done(function(response) {
            $('.lmenu-edit-' + key).button('reset');
            $('.show_updated_modal').html(response);
            $('.fetch_inner_form').modal('show');
            $('#update_inner_itemz_menu').validator();
        });
        return false;
    });


    /*--- update menu ---*/
    $(document).on('submit', '#update_inner_itemz_menu', function(e) {
        if (e.isDefaultPrevented()) {
            return false;
        } else {
            $('.sonu-button').button('loading');
            $.post(ajax_url, { action: 'dwt_update_current_menu', collect_data: $("form#update_inner_itemz_menu").serialize() }).done(function(response) {
                $('.sonu-button').button('reset');
                $('.fetch_inner_form').modal('hide');
                var res = response.split("|");
                $("#" + res[1] + " span.menu_name").html(res[0]);
                $("#" + res[1] + " span.menu_price").html(res[2]);
            });
            return false;
        }
    });


    /*--- View All menu Items ---*/
    $(document).on('click', '.l_view_collection', function() {
        var listing_id = $(this).attr("data-id");
        var key = $(this).attr("data-key");
        $('.view-button-' + key).button('loading');
        $.post(ajax_url, {
            action: 'dwt_fetch_inner_menugroupz',
            listing_id: listing_id,
            key: key
        }).done(function(response) {
            $('.view-button-' + key).button('reset');
            $('.show_inner_menuz').html(response);
            $('.menu_item_historyz').modal('show');
        });
        return false;
    });

    $('#menu_parent_listingz').on('change', function() {
        $(".menu-btn").show();
    });

    var m_id = $('#conditional_id').val();
    if (typeof m_id != 'undefined') {
        $('.sk-circle').show();
        //$("#menu_parent_listingz").trigger("change");
        $(".menu-btn").show();
        $.post(ajax_url, { action: 'dwt_fetchmenu_against_listing', listing_id: m_id }).done(function(response) {
            $('.sk-circle').hide();
            $("#append_result").html(response);
        });
        return false;
    }




    /*--- Update profile settings ---*/
    $('#profile-settings').validator().on('submit', function(e) {
        if (e.isDefaultPrevented()) {
            return false;
        } else {
            $('.sonu-button').button('loading');
            $.post(ajax_url, {
                action: 'save_profile_settings',
                collect_data: $("form#profile-settings").serialize()
            }).done(function(response) {
                $('.sonu-button').button('reset');
                if ($.trim(response) != "") {
                    $.alert({ title: get_strings.congratulations, rtl: yes_rtl, icon: 'fa fa-smile-o', theme: 'modern', animation: 'scale', type: 'green', content: response, buttons: { okay: { btnClass: 'btn-blue' } } });
                    window.setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $.alert({ title: get_strings.whoops, closeIcon: true, rtl: yes_rtl, backgroundDismiss: true, icon: 'fa fa-frown-o', theme: 'modern', animation: 'scale', type: 'red', content: response, buttons: { okay: { btnClass: 'btn-blue' } } });
                }
            });
        }
        return false;
    });

    //addding ma new ajax for calendar form

    $('#reservation_information').validator().on('submit', function(e) {
        if (e.isDefaultPrevented()) {
            return false;
        } else {
            var is_update = $('#is_update').val();
            var curent_lang = $('#lang').val();
            $('.sonu-button').button('loading');
            $.post(ajax_url, {
                action: 'my_new_reservation',
                lang: curent_lang,
                collect_data: $("form#reservation_information").serialize(),
                is_update: $('#is_update').val(),
            }).done(function(response) {
                $('.sonu-button').button('reset');
                $.alert({
                    title: get_strings.congratulations,
                    rtl: yes_rtl,
                    icon: 'fa fa-smile-o',
                    theme: 'modern',
                    animation: 'scale',
                    type: 'green',
                    content: profile_strings.reservation,
                    buttons: {
                        Thank_You: function() {
                            location.reload();

                        },
                    }
                });
            });
            /*--- stop submitting ---*/
            return false;
        }
    });

    //enable reservation on listings

    $('#reservations').on('submit', function(e) {

        if (e.isDefaultPrevented()) {
            return false;
        } else {

            $('.sonu-button').button('loading');
            var booked_days = $('#disabled_dates').datepicker().val();
            var listing_id = $('#menu_parent_listingz').val();

            $.post(ajax_url, {
                action: 'my_reservations',
                collect_data: $("form#reservations").serialize(),
                restricted_days: booked_days,
                listing_id: listing_id,
            }).done(function(response) {
                $('.sonu-button').button('reset');
                alert("Reservation created Succefully!");


            });
        }
        return false;
    });


    //for post status

    $('.booking_status').on('change', function() {
        var booking_status = $(this).val();
        var booking_id = $(this).attr("data-id");
        $('#current_booking_id').val(booking_id);
        $('#current_booking_status').val(booking_status);
        $('#exampleModal').modal({
            show: true
        });
    });

    $('.form_booking_email').on('submit', function(e) {
        e.preventDefault();
        var booking_id = $('#current_booking_id').val();
        var booking_status = $('#current_booking_status').val();
        var message_text = $('#message-text').val();
        $.post(ajax_url, {
            action: 'booked_listing_status',
            staus: booking_status,
            collect_data: $("form#event_attendies").serialize(),
            booking_id: booking_id,
            message_text: message_text,
        }).done(function(response) {
            $('.sonu-button').button('reset');
            return false;
        });
    });

    //adding event attendees
    $('#attending_event').on('click', function() {
        var event_id = $("#attending_event").attr("event-data-id");
        $.post(ajax_url, {
            action: 'event_attendees_status',
            event_id: event_id,
        }).done(function(response) {
            location.reload();
        });
    });

    //deleting meta of not attending event

    $("#cancel_attendance").on('click', function() {
        var event_id = $("#cancel_attendance").attr("event-data-delete-id");

        $.post(ajax_url, {
            action: 'dwt_delete_event_attendance',
            event_id: event_id,

        }).done(function(response) {
            location.reload();
        });

    });

})(jQuery);