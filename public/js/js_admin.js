jQuery(document).ready(function ($) {
    $('#selectall').click(function () {
        var checkboxes = $('#table_index').find(':checkbox');
        if ($(this).is(':checked')) {
            //checkboxes.attr('checked', 'checked');
            $(':checkbox').prop('checked', true);
        } else {
            //checkboxes.removeAttr('checked');
            $(':checkbox').prop('checked', false);
        }
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //js upload avatar admin
    var readURL = function (input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('.profile-pic').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $(".file-upload").on('change', function () {
        readURL(this);
    });

    $(".upload-button").on('click', function () {
        $(".file-upload").click();
    });
});

function select_all() {
    (function ($) {
        var checkboxes = $('#table_index').find(':checkbox').each(function () {
            if ($(this).is(':checked')) {
                //checkboxes.attr('checked', 'checked');
                $(':checkbox').prop('checked', true);
            } else {
                //checkboxes.removeAttr('checked');
                $(':checkbox').prop('checked', false);
            }
        });
    })(jQuery);
}

function delete_id(type) {
    (function ($) {
        Swal.fire({
            title: 'Xoá',
            text: 'Bạn muốn xoá nội dung đã chọn?',
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: 'Huỷ',
            confirmButtonColor: '#0d6efd',
            confirmButtonText: 'OK',
        }).then((result) => {
            if (result.isConfirmed) {
                let body = $('body');
                let arr = new Array();
                var con = 0;
                $('input[name="seq_list[]"]:checked').each(function () {
                    arr = $('input:checkbox').serializeArray();
                    arr.push({name: "_token", value: getMetaContentByName('csrf-token')});
                    arr.push({name: "type", value: type});
                }); //each
                $.ajax({
                    type: "POST",
                    url: admin_url + "/delete-id",
                    data: arr,//pass the array to the ajax call
                    cache: false,
                    beforeSend: function () {
                        body.addClass("loading");
                        $('#waiting-delete').show();
                    },
                    success: function () {
                        body.removeClass("loading");
                        location.reload();
                    }
                });//ajax
            }
        });
    })(jQuery);
}

function confirmPayment(code) {
    (function ($) {
        if (window.confirm('Bạn muốn xác nhận cộng Coin cho giao dịch "' + code + '"?')) {
            var arr = {
                "_token": getMetaContentByName("csrf-token"),
                "code": code
            };
            let body = $('body');
            $.ajax({
                type: "POST",
                url: admin_url + "/payment/confirm",
                data: arr,//pass the array to the ajax call
                cache: false,
                beforeSend: function () {
                    body.addClass("loading");
                },
                success: function (result) {
                    body.removeClass("loading");
                    if (result.success) {
                        Swal.fire({
                            title: 'Thành công',
                            text: result.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    } else {
                        Swal.fire(
                            'Oops...',
                            result.message,
                            'error'
                        );
                    }
                }
            });//ajax
        } else {
            return false;
        }
    })(jQuery);
}

function cancelPayment(code) {
    (function ($) {
        if (window.confirm('Bạn muốn huỷ giao dịch "' + code + '"?')) {
            var arr = {
                "_token": getMetaContentByName("csrf-token"),
                "code": code
            };
            let body = $('body');
            $.ajax({
                type: "POST",
                url: admin_url + "/payment/cancel",
                data: arr,//pass the array to the ajax call
                cache: false,
                beforeSend: function () {
                    body.addClass("loading");
                },
                success: function (result) {
                    body.removeClass("loading");
                    if (result.success) {
                        Swal.fire({
                            title: 'Thành công',
                            text: result.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    } else {
                        Swal.fire(
                            'Oops...',
                            result.message,
                            'error'
                        );
                    }
                }
            });//ajax
        } else {
            return false;
        }
    })(jQuery);
}

function confirmWithdrawRequest(code) {
    (function ($) {
        if (window.confirm('Bạn muốn xác nhận rút xu cho yêu cầu "' + code + '"?')) {
            var arr = {
                "_token": getMetaContentByName("csrf-token"),
                "code": code
            };
            let body = $('body');
            $.ajax({
                type: "POST",
                url: admin_url + "/withdraw-request/confirm",
                data: arr,//pass the array to the ajax call
                cache: false,
                beforeSend: function () {
                    body.addClass("loading");
                },
                success: function (result) {
                    body.removeClass("loading");
                    if (result.success) {
                        Swal.fire({
                            title: 'Thành công',
                            text: result.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    } else {
                        Swal.fire(
                            'Oops...',
                            result.message,
                            'error'
                        );
                    }
                }
            });//ajax
        } else {
            return false;
        }
    })(jQuery);
}

function cancelWithdrawRequest(code) {
    (function ($) {
        if (window.confirm('Bạn muốn huỷ yêu cầu rút xu "' + code + '"?')) {
            var arr = {
                "_token": getMetaContentByName("csrf-token"),
                "code": code
            };
            let body = $('body');
            $.ajax({
                type: "POST",
                url: admin_url + "/withdraw-request/cancel",
                data: arr,//pass the array to the ajax call
                cache: false,
                beforeSend: function () {
                    body.addClass("loading");
                },
                success: function (result) {
                    body.removeClass("loading");
                    if (result.success) {
                        Swal.fire({
                            title: 'Thành công',
                            text: result.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    } else {
                        Swal.fire(
                            'Oops...',
                            result.message,
                            'error'
                        );
                    }
                }
            });//ajax
        } else {
            return false;
        }
    })(jQuery);
}

function confirmError(id) {
    (function ($) {
        if (window.confirm('Bạn muốn xác nhận đã sửa lỗi cho Báo cáo lỗi "#' + id + '"?')) {
            var arr = {
                "_token": getMetaContentByName("csrf-token"),
                "id": id
            };
            let body = $('body');
            $.ajax({
                type: "POST",
                url: admin_url + "/report/confirm",
                data: arr,//pass the array to the ajax call
                cache: false,
                beforeSend: function () {
                    body.addClass("loading");
                },
                success: function (result) {
                    body.removeClass("loading");
                    if (result.success) {
                        Swal.fire({
                            title: 'Thành công',
                            text: result.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    } else {
                        Swal.fire(
                            'Oops...',
                            result.message,
                            'error'
                        );
                    }
                }
            });//ajax
        } else {
            return false;
        }
    })(jQuery);
}

function cancelError(id) {
    (function ($) {
        if (window.confirm('Bạn muốn huỷ báo cáo lỗi "#' + id + '"?')) {
            var arr = {
                "_token": getMetaContentByName("csrf-token"),
                "id": id
            };
            let body = $('body');
            $.ajax({
                type: "POST",
                url: admin_url + "/report/cancel",
                data: arr,//pass the array to the ajax call
                cache: false,
                beforeSend: function () {
                    body.addClass("loading");
                },
                success: function (result) {
                    body.removeClass("loading");
                    if (result.success) {
                        Swal.fire({
                            title: 'Thành công',
                            text: result.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    } else {
                        Swal.fire(
                            'Oops...',
                            result.message,
                            'error'
                        );
                    }
                }
            });//ajax
        } else {
            return false;
        }
    })(jQuery);
}

function confirmRequestChangeType(id) {
    (function ($) {
        if (window.confirm('Bạn muốn xác nhận yêu cầu đóng góp truyện "#' + id + '"?')) {
            var arr = {
                "_token": getMetaContentByName("csrf-token"),
                "id": id
            };
            let body = $('body');
            $.ajax({
                type: "POST",
                url: admin_url + "/request-change-user-type/confirm",
                data: arr,//pass the array to the ajax call
                cache: false,
                beforeSend: function () {
                    body.addClass("loading");
                },
                success: function (result) {
                    body.removeClass("loading");
                    if (result.success) {
                        Swal.fire({
                            title: 'Thành công',
                            text: result.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    } else {
                        Swal.fire(
                            'Oops...',
                            result.message,
                            'error'
                        );
                    }
                }
            });//ajax
        } else {
            return false;
        }
    })(jQuery);
}

function cancelRequestChangeType(id) {
    (function ($) {
        if (window.confirm('Bạn muốn huỷ yêu cầu đóng góp truyện "#' + id + '"?')) {
            var arr = {
                "_token": getMetaContentByName("csrf-token"),
                "id": id
            };
            let body = $('body');
            $.ajax({
                type: "POST",
                url: admin_url + "/request-change-user-type/cancel",
                data: arr,//pass the array to the ajax call
                cache: false,
                beforeSend: function () {
                    body.addClass("loading");
                },
                success: function (result) {
                    body.removeClass("loading");
                    if (result.success) {
                        Swal.fire({
                            title: 'Thành công',
                            text: result.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    } else {
                        Swal.fire(
                            'Oops...',
                            result.message,
                            'error'
                        );
                    }
                }
            });//ajax
        } else {
            return false;
        }
    })(jQuery);
}

function loadFile(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
}

function loadFileIcon(event) {
    var output = document.getElementById('output_icon');
    output.src = URL.createObjectURL(event.target.files[0]);
}

function loadFileSlishow_pc(event) {
    var output = document.getElementById('output_slishow_pc');
    output.src = URL.createObjectURL(event.target.files[0]);
}

function loadFileSlishow_mobile(event) {
    var output = document.getElementById('output_slishow_mobile');
    output.src = URL.createObjectURL(event.target.files[0]);
}

function getMetaContentByName(name, content) {
    var content = (content == null) ? 'content' : content;
    return document.querySelector("meta[name='" + name + "']").getAttribute(content);
}

function number_format(number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function getFormattedDate(data) {
    const date = new Date(data);

    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Tháng trong JS bắt đầu từ 0
    const year = date.getFullYear();

    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');

    return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
}
