(function ($) {
    'use strict';

    // Add Color Picker to all inputs that have 'color-field' class
    $(
        function () {
            jQuery('.color-field').wpColorPicker();
        }
    );

    // Select all files list compress
    $('#check-all-compress').on(
        'click',
        function () {
            if (this.checked) {
                $('.nslb-input').each(
                    function () {
                        this.checked = true;
                    }
                );
            } else {
                $('.nslb-input').each(
                    function () {
                        this.checked = false;
                    }
                );
            }
        }
    );

    $('.nslb-input').on(
        'click',
        function () {
            if ($('.nslb-input:checked').length == $('.nslb-input').length) {
                $('#check-all-compress').prop('checked', true);
            } else {
                $('#check-all-compress').prop('checked', false);
            }
        }
    );

    // Select all files list watermark
    $('#check-all-watermark').on(
        'click',
        function () {
            if (this.checked) {
                $('.nslb-input').each(
                    function () {
                        this.checked = true;
                    }
                );
            } else {
                $('.nslb-input').each(
                    function () {
                        this.checked = false;
                    }
                );
            }
        }
    );

    $('.nslb-input').on(
        'click',
        function () {
            if ($('.nslb-input:checked').length == $('.nslb-input').length) {
                $('#check-all-watermark').prop('checked', true);
            } else {
                $('#check-all-watermark').prop('checked', false);
            }
        }
    );

    // Watermark Format Mode
    $("input[name$='ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_mode]']").on(
        'change',
        function () {
            var test = $(this).val();

            $("div.watermark-mode").hide();
            $("#div-mode" + test).show();
        }
    );

    var xhr = true;

    $('.btn-cancel').on(
        'click',
        function () {
            xhr = false;
        }
    );

    var tmp_count = 0;
    var array_lenght = 0;

    $('.btn-compress-all').on(
        'click',
        function () {
            $('#cancel-compress').css('display', 'inline-block');
            // $('.all-compressing').show();
            $.ajax({
                type: 'POST',
                url: 'admin-post.php?action=ilovepdf_compress_list',
                success: function (data) {
                    var response = $.parseJSON(data);
                    if (response != '' && response.status == 1) {
                        array_lenght = response.list_pdf.length;
                        compressMultiPDF(response.list_pdf)

                    } else {
                        // Strip HTML tags
                        var div = document.createElement("div");
                        div.innerHTML = response;

                        $('.compress-error').html(div.innerText).show();
                    }
                }
            });
        }
    );

    function compressMultiPDF(list_pdf) {
        if (tmp_count == 0) {
            array_lenght = list_pdf.length;
        }
        var elem = $('#file-row-' + list_pdf[0]).find('td:eq(3)');
        elem.find('a').hide();
        $('.all-compressing').show();
        elem.find('.compressing').show();
        tmp_count++;
        $('.all-compressing span').html(tmp_count + '/' + array_lenght);
        $('.all-compressing .progress-percent').width((tmp_count * 100) / array_lenght + "%");
        $.post(
            'admin-post.php?action=ilovepdf_compress&id=' + list_pdf[0] + '&ajax=1',
            function (response) {
                if (response != '' && response.status == 1 && !response.api_error) {
                    elem.find('.compressing').hide();
                    elem.find('.success').show();
                    $('#file-row-' + list_pdf[0]).find('td:eq(2)').html(response.compress_size);

                    list_pdf.splice(0, 1);
                    if (!jQuery.isEmptyObject(list_pdf) && xhr) {

                        compressMultiPDF(list_pdf);

                    } else {
                        $('.all-compressing').hide();
                        if (xhr) {
                            $('.compress-success').show();
                        } else {
                            $('.compress-abort').show();
                        }
                        $('#cancel-compress').hide();
                        window.setTimeout(
                            function () {
                                $('#stats_total_files_compressed').html(response.total_files);
                                $('#stats_initial_size').html(response.initial_size);
                                $('#stats_current_size').html(response.current_size);
                                $('#stats_total_percentage').html(response.percentage + "%");
                                $('#stats_total_percentage').parent('.c100').addClass('p' + response.percentage);
                                $('#compress-pdf-list').load(document.URL + ' #compress-pdf-list');
                            },
                            2000
                        );
                    }
                } else {

                    if (response.api_error == 'error_auth') {

                        tb_show("HAI", "#TB_inline?height=240&amp;width=405&amp;inlineId=pricing_ilovepdf&amp;modal=true", null);
                        elem.show();

                    } else {

                        elem.find('.compressing').hide();
                        // Strip HTML tags
                        var div = document.createElement("div");
                        div.innerHTML = response.api_error;
                        elem.find('.error').html(div.innerText).show();

                        list_pdf.splice(0, 1);
                        if (!jQuery.isEmptyObject(list_pdf) && xhr) {

                            compressMultiPDF(list_pdf);

                        } else {
                            $('.all-compressing').hide();
                            if (xhr) {
                                $('.compress-success').show();
                            } else {
                                $('.compress-abort').show();
                            }
                            $('#cancel-compress').hide();
                        }

                    }
                }

            },
            'json'
        );
    }
    window.exportedCompressMultiPDF = compressMultiPDF;

    $('.btn-watermark-all').on(
        'click',
        function () {
            $('#cancel-watermark').css('display', 'inline-block');
            // $('.all-applying-watermark').show();
            $.ajax({
                type: 'POST',
                url: 'admin-post.php?action=ilovepdf_watermark_list',
                success: function (data) {
                    var response = $.parseJSON(data);
                    if (response != '' && response.status == 1) {
                        array_lenght = response.list_pdf.length;
                        watermarkMultiPDF(response.list_pdf)

                    } else {
                        // Strip HTML tags
                        var div = document.createElement("div");
                        div.innerHTML = response;

                        $('.applied-error').html(div.innerText).show();
                    }
                }
            });
        }
    );

    function watermarkMultiPDF(list_pdf) {
        if (tmp_count == 0) {
            array_lenght = list_pdf.length;
        }
        var elem = $('#file-row-' + list_pdf[0]).find('td:eq(2)');
        elem.find('a').hide();
        $('.all-applying-watermark').show();
        elem.find('.applying-watermark').show();
        tmp_count++;
        $('.all-applying-watermark span').html(tmp_count + '/' + array_lenght);
        $('.all-applying-watermark .progress-percent').width((tmp_count * 100) / array_lenght + "%");
        $.post(
            'admin-post.php?action=ilovepdf_watermark&id=' + list_pdf[0] + '&ajax=1',
            function (response) {
                if (response != '' && response.status == 1 && !response.api_error) {
                    elem.find('.applying-watermark').hide();
                    elem.find('.success').show();

                    list_pdf.splice(0, 1);
                    if (!jQuery.isEmptyObject(list_pdf) && xhr) {

                        watermarkMultiPDF(list_pdf);

                    } else {
                        $('.all-applying-watermark').hide();
                        if (xhr) {
                            $('.applied-success').show();
                        } else {
                            $('.applied-abort').show();
                        }
                        $('#cancel-watermark').hide();
                        window.setTimeout(
                            function () {
                                $('#stats_total_files_watermarked').html(response.total_files);
                                $('#watermark-pdf-list').load(document.URL + ' #watermark-pdf-list');
                            },
                            2000
                        );
                    }
                } else {

                    if (response.api_error == 'error_auth') {

                        tb_show("HAI", "#TB_inline?height=240&amp;width=405&amp;inlineId=pricing_ilovepdf&amp;modal=true", null);
                        elem.show();

                    } else {

                        elem.find('.applying-watermark').hide();
                        // Strip HTML tags
                        var div = document.createElement("div");
                        div.innerHTML = response.api_error;
                        elem.find('.error').html(div.innerText).show();

                        list_pdf.splice(0, 1);
                        if (!jQuery.isEmptyObject(list_pdf) && xhr) {

                            watermarkMultiPDF(list_pdf);

                        } else {
                            $('.all-applying-watermark').hide();
                            if (xhr) {
                                $('.applied-success').show();
                            } else {
                                $('.applied-abort').show();
                            }
                            $('#cancel-watermark').hide();
                        }

                    }
                }
            },
            'json'
        );
    }
    window.exportedWatermarkMultiPDF = watermarkMultiPDF;

    $('.btn-compress').on(
        'click',
        function (e) {
            var elem = $(this);
            var size_compressed = $(this).parent();
            var btn_watermark_visible = false;

            e.preventDefault();

            if (elem.closest(".row-library").find('.btn-watermark').is(":visible")) {
                btn_watermark_visible = true;
            }
            elem.closest(".row-library").find('.btn-watermark').hide();

            $(this).hide();
            $(this).nextAll('.compressing').show();
            $(this).nextAll('.success').hide();
            $.post(
                $(this).prop('href') + '&ajax=1',
                function (response) {
                    elem.nextAll('.compressing').hide();
                    if (btn_watermark_visible) {
                        elem.closest(".row-library").find('.btn-watermark').show();
                    }
                    if (response != '' && response.status == 1 && !response.api_error) {
                        console.log(response.api_error);
                        elem.nextAll('.success').show();
                        $('#stats_total_files_compressed').html(parseInt($('#stats_total_files_compressed').html()) + 1);
                        $('#stats_initial_size').html(response.initial_size);
                        $('#stats_current_size').html(response.current_size);
                        $('#stats_total_percentage').html(response.percentage + "%");
                        $('#stats_total_percentage').parent('.c100').addClass('p' + response.percentage);
                        window.setTimeout(
                            function () {
                                elem.nextAll('.success').hide();
                                if (response.library == 1) {
                                    elem.nextAll('.stats-compress').html('<i class="fa fa-check" aria-hidden="true"></i> Compressed<br />Savings ' + response.percent + '%</span>');
                                } else if (response.editpdf == 1) {
                                    elem.prevAll('#current-size').find('strong').html(response.compress_size);
                                } else {
                                    size_compressed.html(response.percent + '%');
                                    size_compressed.closest('td').prev('td').html(response.compress_size);
                                }
                            },
                            3000
                        );
                    } else {

                        if (response.api_error == 'error_auth') {

                            tb_show("HAI", "#TB_inline?height=240&amp;width=405&amp;inlineId=pricing_ilovepdf&amp;modal=true", null);
                            elem.show();

                        } else {
                            // Strip HTML tags
                            var div = document.createElement("div");
                            div.innerHTML = response.api_error;
                            if (response.editpdf == 1) {
                                elem.nextAll('.error').before('<br /><br />');
                            }
                            elem.nextAll('.error').html(div.innerText).show();
                        }

                    }
                },
                'json'
            );
        }
    );

    $('.btn-watermark').on(
        'click',
        function (e) {
            var elem = $(this);
            var parent_elem = $(this).parent();
            var btn_compress_visible = false;

            e.preventDefault();
            if (elem.closest(".row-library").find('.btn-compress').is(":visible")) {
                btn_compress_visible = true;
            }
            elem.closest(".row-library").find('.btn-compress').hide();
            $(this).hide();
            $(this).nextAll('.applying-watermark').show();
            $(this).prevAll('.stats-compress').hide();
            $(this).nextAll('.success').hide();
            $.post(
                $(this).prop('href') + '&ajax=1',
                function (response) {
                    elem.nextAll('.applying-watermark').hide();
                    if (btn_compress_visible) {
                        elem.closest(".row-library").find('.btn-compress').show();
                    }
                    if (response != '' && response.status == 1 && !response.api_error) {
                        elem.nextAll('.success').show();
                        $('#stats_total_files_watermarked').html(parseInt($('#stats_total_files_watermarked').html()) + 1);
                        window.setTimeout(
                            function () {
                                elem.nextAll('.success').hide();
                                if (response.library == 1) {
                                    parent_elem.html('<i class="fa fa-check" aria-hidden="true"></i> Stamped');
                                } else if (response.editpdf == 1) {
                                    parent_elem.append('<i class="fa fa-check" aria-hidden="true"></i> Stamped');
                                }
                            },
                            3000
                        );
                    } else {
                        if (response.api_error == 'error_auth') {

                            tb_show("HAI", "#TB_inline?height=240&amp;width=405&amp;inlineId=pricing_ilovepdf&amp;modal=true", null);
                            elem.show();

                        } else {
                            // Strip HTML tags
                            var div = document.createElement("div");
                            div.innerHTML = response.api_error;
                            if (response.editpdf == 1) {
                                elem.nextAll('.error').before('<br /><br />');
                            }
                            elem.nextAll('.error').html(div.innerText).show();
                        }
                    }
                },
                'json'
            );
        }
    );

    $('.btn-restore').on(
        'click',
        function (e) {
            var elem = $(this);

            e.preventDefault();

            Swal.fire({
                title: 'Attention!',
                text: 'The changes applied by all the tools will be lost. Do you want to continue?',
                icon: 'warning',
                confirmButtonText: 'Yes',
                showCloseButton: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'button-primary',
                },
            }).then(
                (result) => {
                    if (result.isConfirmed) {
                        $(this).hide();
                        $(this).nextAll('.loading').show();
                        $(this).parent().prevAll('.row-compress-tool').hide();
                        $(this).parent().prevAll('.row-watermark-tool').hide();

                        $.post(
                            $(this).prop('href') + '&ajax=1',
                            function (response) {
                                elem.nextAll('.loading').hide();
                                if (response == '') {
                                    elem.nextAll('.success').show();
                                } else {
                                    // Strip HTML tags
                                    var div = document.createElement("div");
                                    div.innerHTML = response;

                                    elem.nextAll('.error').html(div.innerText).show();
                                }
                            }
                        );
                    }
                }
            );
        }
    );

    // trigger on File Single Edit page
    $('.ilovepdf--meta-box-container .link-restore, .compat-field-iLovePDF-tools .link-restore').on(
        'click',
        function (e) {
            var elem = $(this);
            const hrefUrl = elem[0].href;

            e.preventDefault();

            Swal.fire({
                title: 'Attention!',
                text: 'The changes applied by all the tools will be lost. Do you want to continue?',
                icon: 'warning',
                confirmButtonText: 'Yes',
                showCloseButton: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'button-primary',
                },
            }).then(
                (result) => {
                    if (result.isConfirmed) {
                        location.href = hrefUrl;
                    }
                }
            );
        }
    );

})(jQuery);