$(function () {
	"use strict";
    lazyload();

	/* perfect scrol bar */
    if ($('.scroll-menu').length) {
        new PerfectScrollbar(".scroll-menu");
    }

	// search bar
	$(".mobile-search-icon").on("click", function () {
		$(".search-bar").addClass("full-search-bar");
	});
	$(".search-close").on("click", function () {
		$(".search-bar").removeClass("full-search-bar");
	});


	$(".mobile-toggle-menu").on("click", function () {
		$(".wrapper").addClass("toggled");
	});

	$(function() {
		for (var e = window.location, o = $(".navbar-nav .dropdown-item").filter(function() {
				return this.href == e
			}).addClass("active").parent().addClass("active"); o.is("li");) o = o.parent("").addClass("").parent("").addClass("")
	}),

    $('.list-story-categories .btn-genres').on('click', function () {
        if ($('.show-list-categories').hasClass('active')) {
            $('.show-list-categories').removeClass('active');
            $(this).removeClass('active');
        } else {
            $('.show-list-categories').addClass('active');
            $(this).removeClass('active');
        }
    });

	$(".dark-mode").on("click", function() {
        let darkmode = 0;
		if($(".dark-mode-icon i").attr("class") == 'bx bx-sun') {
			$(".dark-mode-icon i").attr("class", "bx bx-moon");
			$("html").attr("class", "light-theme");
            darkmode = 0;
		} else {
			$(".dark-mode-icon i").attr("class", "bx bx-sun");
			$("html").attr("class", "dark-theme");
            darkmode = 1;
		}

        let data = {
            'darkmode': darkmode,
            '_token': getMetaContentByName("_token")
        };
        $.ajax({
            url: site + '/ajax/dark-mode',
            type: "POST",
            data: data,
            success: function (result) {
                console.log('Change layout success');
                if ($('.chapter-content .content-container #0x4v9k98').length) {
                    window.location.reload();
                }
            }
        });
	}),

	// toggle menu button
	$(".toggle-icon").click(function () {
		if ($(".wrapper").hasClass("toggled")) {
			// unpin sidebar when hovered
			$(".wrapper").removeClass("toggled");
			$(".sidebar-wrapper").unbind("hover");
		} else {
			$(".wrapper").addClass("toggled");
			$(".sidebar-wrapper").hover(function () {
				$(".wrapper").addClass("sidebar-hovered");
			}, function () {
				$(".wrapper").removeClass("sidebar-hovered");
			})
		}
	});
	/* Back To Top */
	$(document).ready(function () {
		$(window).on("scroll", function () {
			if ($(this).scrollTop() > 300) {
				$('.back-to-top').fadeIn();
			} else {
				$('.back-to-top').fadeOut();
			}
		});
		$('.back-to-top').on("click", function () {
			$("html, body").animate({
				scrollTop: 0
			}, 600);
			return false;
		});
	});
	$(function () {
		for (var i = window.location, o = $(".metismenu li a").filter(function () {
			return this.href == i;
		}).addClass("").parent().addClass("");;) {
			if (!o.is("li")) break;
			o = o.parent("").addClass("").parent("").addClass("");
		}
	}),



	$('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
		if (!$(this).next().hasClass('show')) {
		  $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
		}
		var $subMenu = $(this).next(".dropdown-menu");
		$subMenu.toggleClass('show');

		$(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
		  $('.submenu .show').removeClass("show");
		});

		return false;
    });

	// chat toggle
	$(".chat-toggle-btn").on("click", function () {
		$(".chat-wrapper").toggleClass("chat-toggled");
	});
	$(".chat-toggle-btn-mobile").on("click", function () {
		$(".chat-wrapper").removeClass("chat-toggled");
	});
	// email toggle
	$(".email-toggle-btn").on("click", function () {
		$(".email-wrapper").toggleClass("email-toggled");
	});
	$(".email-toggle-btn-mobile").on("click", function () {
		$(".email-wrapper").removeClass("email-toggled");
	});
	// compose mail
	$(".compose-mail-btn").on("click", function () {
		$(".compose-mail-popup").show();
	});
	$(".compose-mail-close").on("click", function () {
		$(".compose-mail-popup").hide();
	});
	/*switcher*/
	$(".switcher-btn").on("click", function () {
		$(".switcher-wrapper").toggleClass("switcher-toggled");
	});
	$(".close-switcher").on("click", function () {
		$(".switcher-wrapper").removeClass("switcher-toggled");
	});
	$("#lightmode").on("click", function () {
		$('html').attr('class', 'light-theme');
	});
	$("#darkmode").on("click", function () {
		$('html').attr('class', 'dark-theme');
	});
	$("#semidark").on("click", function () {
		$('html').attr('class', 'semi-dark');
	});
	$("#minimaltheme").on("click", function () {
		$('html').attr('class', 'minimal-theme');
	});
	$("#headercolor1").on("click", function () {
		$("html").addClass("color-header headercolor1");
		$("html").removeClass("headercolor2 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8");
	});
	$("#headercolor2").on("click", function () {
		$("html").addClass("color-header headercolor2");
		$("html").removeClass("headercolor1 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8");
	});
	$("#headercolor3").on("click", function () {
		$("html").addClass("color-header headercolor3");
		$("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8");
	});
	$("#headercolor4").on("click", function () {
		$("html").addClass("color-header headercolor4");
		$("html").removeClass("headercolor1 headercolor2 headercolor3 headercolor5 headercolor6 headercolor7 headercolor8");
	});
	$("#headercolor5").on("click", function () {
		$("html").addClass("color-header headercolor5");
		$("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor3 headercolor6 headercolor7 headercolor8");
	});
	$("#headercolor6").on("click", function () {
		$("html").addClass("color-header headercolor6");
		$("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor3 headercolor7 headercolor8");
	});
	$("#headercolor7").on("click", function () {
		$("html").addClass("color-header headercolor7");
		$("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor3 headercolor8");
	});
	$("#headercolor8").on("click", function () {
		$("html").addClass("color-header headercolor8");
		$("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor3");
	});

    //chat
    // if ($('.group-chat .content_box_chat').length > 0) {
    //     $('.group-chat .content_box_chat')[0].scrollTo(0, $('.group-chat .content_box_chat .list-chat').height());
    //     $('.group-chat .content_box_chat').scroll(function() {
    //         if($('.group-chat .content_box_chat').scrollTop() == 0) {
    //             let offset = $('#offset').val();
    //             let currentPosition = $('.group-chat .content_box_chat .list-chat').height();
    //             $.ajax({
    //                 url: "/ajax/load-more-chat",
    //                 dataType: "html",
    //                 data: {
    //                     offset: offset
    //                 },
    //                 beforeSend: function () {
    //                     $('.group-chat .content_box_chat .list-chat').prepend('<div id="chat-loading" class="text-center my-3">Đang tải dữ liệu...</div>');
    //                 },
    //                 success: function (result) {
    //                     $('.group-chat .content_box_chat .list-chat').prepend(result);
    //                     $('#offset').val(parseInt(offset) + 15);
    //                     $('.group-chat .content_box_chat .list-chat #chat-loading').remove();
    //                     $('.group-chat .content_box_chat').scrollTop(currentPosition);
    //                 }
    //             });
    //         }
    //     });
    //
    //     // chat
    //     $('#chat_text').keypress(function(e) {
    //         if (e.which === 13) {
    //             if (!islogin) {
    //                 Swal.fire(
    //                     'Oops...',
    //                     'Bạn cần đăng nhập để có thể tham gia trò chuyện.',
    //                     'error'
    //                 );
    //                 return false;
    //             } else {
    //                 e.preventDefault();
    //                 let chat_text = $('#chat_text').val();
    //                 if (chat_text !== '') {
    //                     $('#chat_text').val('');
    //
    //                     let data = {
    //                         'message': chat_text,
    //                         '_token': getMetaContentByName("_token")
    //                     };
    //                     let body = $("body");
    //                     $.ajax({
    //                         url: site + '/ajax/chat',
    //                         type: "POST",
    //                         data: data,
    //                         beforeSend: function () {
    //                             body.addClass("loading");
    //                         },
    //                         success: function (result) {
    //                             body.removeClass("loading");
    //                             if (result.success) {
    //                                 $('.list-chat').append(result.data);
    //                             } else {
    //                                 Swal.fire(
    //                                     'Oops...',
    //                                     result.message,
    //                                     'error'
    //                                 );
    //                             }
    //                         }
    //                     });
    //                 }
    //             }
    //         }
    //     });
    // }

    if ($('#userNotification').length) {
        $.ajax({
            type: "GET",
            url: site + "/ajax/get-user-header-info",
            cache: false,
            beforeSend: function () {
            },
            success: function (result) {
                if (result.success) {
                    $('#userNotification').html(result.notification);
                    $('#userBox').html(result.userMenu);

                    if ($('.header-notifications-list').length) {
                        new PerfectScrollbar('.header-notifications-list');
                    }
                }
            }
        });
    }

    $("input[data-type='currency']").on({
        keyup: function() {
            formatCurrency($(this));
        }
    });

    // search autocomplete for PC
    const searchInputPC = document.getElementById('searchInputPC');
    searchInputPC.addEventListener('input', debounce(function() {
        if ($(this).val() == "") {
            $('.search-bar #search-autocomplete').removeClass('active');
        } else {
            $('.search-bar #search-autocomplete').addClass('active');
            if ($(this).val().length > 3) {
                let arr = {
                    "search": $(this).val()
                };
                $.ajax({
                    type: "GET",
                    url: site + "/ajax/autocomplete",
                    data: arr,
                    cache: false,
                    beforeSend: function () {
                        $('#search-autocomplete').html('');
                    },
                    success: function (result) {
                        if (result.success) {
                            let story = result.data;
                            let item = '';
                            for (let i = 0; i < story.length; i++) {
                                item += '<li class="d-flex justify-content-start align-items-center mb-2 border-bottom">' +
                                    '<a href="/' + story[i].slug + '.html" class="m-2"><img src="/images/story/thumbs/230/' + story[i].thumbnail + '" alt="' + story[i].name + '" class="img-search"></a>' +
                                    '<a href="/' + story[i].slug + '.html" class="m-2 search-item-title">' + story[i].name + '</a>' +
                                    '</li>';
                            }
                            $('#search-autocomplete').html(item);
                        } else {
                            console.log(result.message)
                        }
                    }
                });
            }
        }
    }, 500));

    // search autocomplete for mobile
    const searchInputMobile = document.getElementById('searchInputMobile');
    searchInputMobile.addEventListener('input', debounce(function() {
        if ($(this).val() != "") {
            if ($(this).val().length > 3) {
                let arr = {
                    "search": $(this).val()
                };
                $.ajax({
                    type: "GET",
                    url: site + "/ajax/autocomplete",
                    data: arr,
                    cache: false,
                    beforeSend: function () {
                        $('#search-autocomplete-mobile').html('');
                    },
                    success: function (result) {
                        if (result.success) {
                            let story = result.data;
                            let item = '';
                            for (let i = 0; i < story.length; i++) {
                                item += '<li class="d-flex justify-content-start align-items-center mb-2 border-bottom">' +
                                    '<a href="/' + story[i].slug + '.html" class="m-2"><img src="/images/story/thumbs/230/' + story[i].thumbnail + '" alt="' + story[i].name + '" class="img-search"></a>' +
                                    '<a href="/' + story[i].slug + '.html" class="m-2 search-item-title">' + story[i].name + '</a>' +
                                    '</li>';
                            }
                            $('#search-autocomplete-mobile').html(item);
                        } else {
                            console.log(result.message)
                        }
                    }
                });
            }
        }
    }, 500));

    $("#reportLicenseForm").submit(function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        let form = $(this);
        let actionUrl = form.attr('action');

        let body = $("body");
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: form.serialize(), // serializes the form's elements.
            beforeSend: function () {
                body.addClass("loading");
            },
            success: function(result)
            {
                body.removeClass("loading");
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        text: result.message,
                        title: 'Báo cáo thành công',
                        showCancelButton: false,
                        confirmButtonColor: '#0d6efd',
                        confirmButtonText: 'OK',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: result.message,
                        showConfirmButton: false,
                        timer: 500
                    });
                }
            }
        });
    });
});

function favourite(story_id, slug, is_favourite) {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để có thể gửi lượt yêu thích đến truyện này.',
                'error'
            );
            return false;
        } else {
            if (is_favourite) {
                Swal.fire(
                    'Oops...',
                    'Bạn đã từng gửi lượt yêu thích đến truyện này.',
                    'error'
                );
                return false;
            }

            Swal.fire({
                title: 'Yêu thích',
                text: 'Bạn muốn gửi một lượt yêu thích đến truyện này?',
                icon: 'question',
                showCancelButton: true,
                cancelButtonText: 'Huỷ',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK',
            }).then((result) => {
                if (result.isConfirmed) {
                    var arr = {
                        "_token": getMetaContentByName("_token"),
                        "story_id": story_id,
                        'slug': slug
                    };

                    let body = $("body");
                    $.ajax({
                        type: "POST",
                        url: site + "/ajax/favourite",
                        data: arr,
                        cache: false,
                        beforeSend: function () {
                            body.addClass("loading");
                        },
                        success: function (data) {
                            body.removeClass("loading");
                            if (data) {
                                Swal.fire({
                                    title: 'Thành công',
                                    text: 'Cảm ơn bạn đã gửi một lượt yêu thích đến truyện ^^!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: '#0d6efd',
                                    confirmButtonText: 'OK',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // location.reload();
                                    }
                                })
                            } else {
                                Swal.fire(
                                    'Oops...',
                                    'Bạn đã từng gửi lượt yêu thích đến truyện này.',
                                    'error'
                                );
                            }
                        }
                    });
                }
            });
        }
    })(jQuery);
}

function bookmark(story_id, slug) {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để có thể lưu truyện này.',
                'error'
            );
            return false;
        } else {
            Swal.fire({
                title: 'Theo dõi truyện',
                text: 'Bạn muốn theo dõi truyện này?',
                icon: 'question',
                showCancelButton: true,
                cancelButtonText: 'Huỷ',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK',
            }).then((result) => {
                if (result.isConfirmed) {
                    var arr = {
                        "_token": getMetaContentByName("_token"),
                        "story_id": story_id,
                        'slug': slug
                    };

                    let body = $("body");
                    $.ajax({
                        type: "POST",
                        url: site + "/ajax/bookmark",
                        data: arr,
                        cache: false,
                        beforeSend: function () {
                            body.addClass("loading");
                        },
                        success: function (data) {
                            body.removeClass("loading");
                            if (data) {
                                Swal.fire({
                                    title: 'Thành công',
                                    text: 'Lưu truyện thành công',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: '#0d6efd',
                                    confirmButtonText: 'OK',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // location.reload();
                                    }
                                })
                            } else {
                                Swal.fire(
                                    'Oops...',
                                    'Bạn đã lưu truyện này.',
                                    'error'
                                );
                            }
                        }
                    });
                }
            });
        }
    })(jQuery);
}

function removeBookmark(story_id, slug) {
    (function ($) {
        Swal.fire({
            title: 'Huỷ theo dõi truyện',
            text: 'Bạn muốn huỷ theo dõi truyện này?',
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: 'Huỷ',
            confirmButtonColor: '#0d6efd',
            confirmButtonText: 'OK',
        }).then((result) => {
            if (result.isConfirmed) {
                var arr = {
                    "_token": getMetaContentByName("_token"),
                    "story_id": story_id,
                    "slug": slug
                };
                let body = $("body");
                $.ajax({
                    type: "POST",
                    url: site + "/ajax/remove-bookmark",
                    data: arr,
                    cache: false,
                    beforeSend: function () {
                        body.addClass("loading");
                    },
                    success: function (data) {
                        body.removeClass("loading");
                        Swal.fire({
                            title: 'Thành công',
                            text: 'Đã bỏ lưu truyện này',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // location.reload();
                            }
                        })
                    }
                });
            }
        });
    })(jQuery);
}

function followAuthor(author_id) {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để có thể theo dõi người này.',
                'error'
            );
            return false;
        } else {
            Swal.fire({
                title: 'Theo dõi',
                text: 'Bạn muốn theo dõi người này?',
                icon: 'question',
                showCancelButton: true,
                cancelButtonText: 'Huỷ',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK',
            }).then((result) => {
                if (result.isConfirmed) {
                    var arr = {
                        "_token": getMetaContentByName("_token"),
                        "author_id": author_id,
                    };

                    let body = $("body");
                    $.ajax({
                        type: "POST",
                        url: site + "/ajax/follow-author",
                        data: arr,
                        cache: false,
                        beforeSend: function () {
                            body.addClass("loading");
                        },
                        success: function (response) {
                            body.removeClass("loading");
                            if (response.success) {
                                Swal.fire({
                                    title: 'Thành công',
                                    text: response.message,
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
                                    response.message,
                                    'error'
                                );
                            }
                        }
                    });
                }
            });
        }
    })(jQuery);
}

function unfollowAuthor(author_id) {
    (function ($) {
        Swal.fire({
            title: 'Huỷ theo dõi',
            text: 'Bạn muốn huỷ theo dõi người này?',
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: 'Huỷ',
            confirmButtonColor: '#0d6efd',
            confirmButtonText: 'OK',
        }).then((result) => {
            if (result.isConfirmed) {
                var arr = {
                    "_token": getMetaContentByName("_token"),
                    "author_id": author_id,
                };
                let body = $("body");
                $.ajax({
                    type: "POST",
                    url: site + "/ajax/unfollow-author",
                    data: arr,
                    cache: false,
                    beforeSend: function () {
                        body.addClass("loading");
                    },
                    success: function (response) {
                        body.removeClass("loading");
                        if (response.success) {
                            Swal.fire({
                                title: 'Thành công',
                                text: response.message,
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
                                response.message,
                                'error'
                            );
                        }
                    }
                });
            }
        });
    })(jQuery);
}

function increaseViewChapter(story_id, chapter_id) {
    (function ($) {
        var arr = {
            "_token": getMetaContentByName("_token"),
            "story_id": story_id,
            "chapter_id": chapter_id
        };
        $.ajax({
            type: "POST",
            url: site + "/ajax/increase-view-chapter",
            data: arr,
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {

            }
        });
    })(jQuery);
}

function report() {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để có thể báo cáo lỗi.',
                'error'
            );
            return false;
        } else {
            $('#reportModal').modal('show');
        }
    })(jQuery);
}

function reportError() {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để có thể báo cáo lỗi.',
                'error'
            );
            return false;
        } else {
            if ($('#error').val() !== 'Truyện không chính chủ' && $('#error_chapter').val() === '') {
                Swal.fire(
                    'Oops...',
                    'Vui lòng chọn chapter bị lỗi!',
                    'error'
                );
                return false;
            }

            var arr = {
                "_token": getMetaContentByName("_token"),
                "error": $('#error').val(),
                "error_chapter": $('#error_chapter').val(),
                "story_id": $('#report_story_id').val(),
                "error_note": $('#error_note').val(),
            };
            let body = $("body");
            $.ajax({
                type: "POST",
                url: site + "/ajax/error",
                data: arr,
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
                                $('#reportModal').modal('hide');
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
            });
        }
    })(jQuery);
}

function reportLicense() {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để có thể báo cáo lỗi.',
                'error'
            );
            return false;
        } else {
            $('#reportLicenseModal').modal('show');
        }
    })(jQuery);
}

function readReviewComment(review_id) {
    (function ($) {
        $.ajax({
            type: "GET",
            url: site + "/ajax/get-review-comments?review_id=" + review_id,
            cache: false,
            success: function (result) {
                $('#review-' + review_id).append(result);
                $('#read-comment-' + review_id).remove();
            }
        });
    })(jQuery);
}

function donate(user_id) {
    (function ($) {
        let body = $("body");
        $.ajax({
            type: "GET",
            url: site + "/ajax/donate-info?user_id=" + user_id,
            cache: false,
            beforeSend: function () {
                body.addClass("loading");
            },
            success: function (result) {
                body.removeClass("loading");
                if (result.success) {
                    let data = result.data;
                    if (data.method === 'bank') {
                        $('#qrCodeDonate').html('<div class="my-3"><img src="' + data.qr_code + '" alt="QR Code" class="img-fluid"></div>');
                    } else {
                        $('#qrCodeDonate').html('<div class="my-3"><img src="' + data.qr_code + '" alt="QR Code" class="img-fluid"></div><hr><div><p><b>MoMo: </b>' + data.account_number + '</p><p><b>Tên TK: </b>' + data.account_name + '</p></div>');
                    }
                    $('#donateModal').modal('show');
                } else {
                    Swal.fire(
                        'Oops...',
                        result.message,
                        'error'
                    );
                }
            }
        });
    })(jQuery);
}

function processDonate() {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để có thể Donate cho team.',
                'error'
            );
            return false;
        } else {
            let amount = $('#money_donate').val().replace(/(<([^>]+)>)/gi, "");
            let arr = {
                "_token": getMetaContentByName("_token"),
                "amount": amount,
                "receiver": $('#receiver').val(),
                "story_id": $('#story_id').val()
            };
            let body = $("body");
            $.ajax({
                type: "POST",
                url: site + "/user/donate",
                data: arr,
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
                                $('#donateModal').modal('hide');
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
            });
        }
    })(jQuery);
}

function processDonateAuthor() {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để có thể Donate cho team.',
                'error'
            );
            return false;
        } else {
            let amount = $('#money_donate').val().replace(/(<([^>]+)>)/gi, "");
            let arr = {
                "_token": getMetaContentByName("_token"),
                "amount": amount,
                "author_id": $('#author_id').val()
            };
            let body = $("body");
            $.ajax({
                type: "POST",
                url: site + "/user/donate-to-author",
                data: arr,
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
                                $('#donateModal').modal('hide');
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
            });
        }
    })(jQuery);
}

function loadComments(story_id) {
    (function ($) {
        $.ajax({
            type: "GET",
            url: site + "/ajax/showComment?story_id=" + story_id,
            cache: false,
            success: function (result) {
                $('.ajax_load_cmt').html(result);
                $('#loadCommentBtn').removeAttr('onclick');
                $('#blockComments').show();
                $('#chapterLoadComment').remove();
            }
        });
    })(jQuery);
}

function commentReply(media_id, parent, avatar) {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để có thể tham gia bình luận.',
                'error'
            );
            return false;
        } else {
            $("<ul class=\"reply2\"><li class=\"clearfix\"><div class=\"avt_user\"><img class=\"avatar\" src=\"" + avatar + "\" width=\"32\"/></div><div class=\"post-comments\"><p class=\"meta\"><textarea class='form-control' style=\"width:100%\" onblur=\"if(this.value==''){(function($) {$('.reply2').remove();})(jQuery);}\"  name=\"txtContent" + parent + "\" id=\"txtContent" + parent + "\" placeholder=\"Viết bình luận\"></textarea></p><p class=\"buttons\"><button type=\"submit\" class=\"btn btn-read\" name=\"btnComment\" id=\"btnComment\" onclick='comment(" + media_id + "," + parent + ",5)'>Đăng</button></p></div></li></ul>").appendTo('.comment_' + parent);
            $('#txtContent' + parent).focus();
            return false;
        }
    })(jQuery);
}

function comment(media_id, parent, num) {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để có thể tham gia bình luận.',
                'error'
            );
            return false;
        }
        var txtContent = $("#txtContent" + parent).val();
        if (!txtContent) {
            $("#txtContent" + parent).focus();
            $("#txtContent" + parent).addClass("frmerror");
            return false;
        } else {
            $("#txtContent" + parent).removeClass("frmerror");
        }
        $("#comment_loading").css("display", "block");
        $("#btnComment").attr("disabled", true);

        let chapter_id = 0;
        if ($('#chapter_id').length > 0) {
            chapter_id = $('#chapter_id').val();
        }

        var sendInfo = {
            '_token': getMetaContentByName("_token"),
            'btnComment': '1',
            'media_id': media_id,
            'num': num,
            'txtContent': txtContent,
            'parent': parent,
            'chapter_id': chapter_id
        };
        let body = $("body");
        $.ajax({
            url: site + '/ajax/post-comment',
            type: "POST",
            data: sendInfo,
            cache: false,
            beforeSend: function () {
                body.addClass("loading");
            },
            success: function (html) {
                body.removeClass("loading");
                $("#comment_loading").hide();
                if (html == 1) {
                    showComment(media_id, num, 1, '');
                    $("#txtContent").val('');
                } else if (html) {
                    Swal.fire(
                        'Oops...',
                        html,
                        'error'
                    );
                } else {
                    Swal.fire(
                        'Oops...',
                        'Sorry, unexpected error. Please try again later.',
                        'error'
                    );
                }
                $("#btnComment").attr("disabled", false);
            }
        });
        return false;
    })(jQuery);
}

function showComment(media_id, num, page, type) {
    (function ($) {
        var sendInfo = {
            'btnComment': '1',
            'story_id': media_id,
            'num': num,
            'page': page,
            'type': 'comment'
        };
        let body = $("body");
        $.ajax({
            url: site + '/ajax/showComment',
            type: "GET",
            data: sendInfo,
            cache: false,
            beforeSend: function () {
                body.addClass("loading");
            },
            success: function (html) {
                body.removeClass("loading");
                if (html) {
                    $(".comment-website").html(html);
                } else {
                    Swal.fire(
                        'Oops...',
                        'Sorry, unexpected error. Please try again later.',
                        'error'
                    );
                }
            }
        });
        return false;
    })(jQuery);
}

function more_comments(media_id, current_page, next_page) {
    (function ($) {
        var sendInfo = {
            '_token': getMetaContentByName("_token"),
            'media_id': media_id,
            'current_page': current_page,
            'next_page': next_page
        };
        let body = $("body");
        $.ajax({
            url: site + '/ajax/more-comments',
            type: "POST",
            data: sendInfo,
            cache: false,
            beforeSend: function () {
                body.addClass("loading");
            },
            success: function (html) {
                body.removeClass("loading");
                if (html) {
                    $(".paging").remove();
                    $("#comment-done").append(html);
                } else {
                    Swal.fire(
                        'Oops...',
                        'Sorry, unexpected error. Please try again later.',
                        'error'
                    );
                }
            }
        });
        return false;
    })(jQuery);
}

function actionChangeChapter(stringSlug) {
    (function ($) {
        $('#selected_chapter').val(stringSlug).change();
    })(jQuery);
}

function changeChapter(storySlug, chapterSlug) {
    (function ($) {
        let body = $("body");
        $.ajax({
            url: site + '/ajax/get-chapter?story=' + storySlug + '&chapter=' + chapterSlug,
            type: "GET",
            cache: false,
            beforeSend: function () {
                body.addClass("loading");
            },
            success: function (result) {
                body.removeClass("loading");
                if (result.success === true) {
                    $("#chapterContentLoading").html(result.html);

                    window.scroll({
                        top: 0,
                        left: 0,
                        behavior: 'smooth'
                    });

                    if (typeof (Storage) !== 'undefined') {
                        //Setting font
                        let settings;
                        if (localStorage.getItem('otruyenChapterSetting') !== null) {
                            let data = localStorage.getItem('otruyenChapterSetting');
                            settings = JSON.parse(data);
                        } else {
                            settings = {
                                "font": 'Roboto, sans-serif',
                                "size": '20',
                                "line": '140',
                            }
                        }

                        let contentContainer = $('.content-container');
                        contentContainer.css('font-family', settings.font);
                        contentContainer.css('font-size', settings.size + 'px');
                        contentContainer.css('line-height', settings.line + '%');
                    }

                    if (result.prev_chapter !== '') {
                        $("#prev_chapter_btn").removeClass('disabled');
                        $("#prev_chapter_btn").attr('onclick', 'actionChangeChapter(\'' + result.prev_chapter.storySlug + ',' + result.prev_chapter.chapterSlug + '\')');
                    } else {
                        $("#prev_chapter_btn").attr('onclick', 'javascript:void(0)');
                        $("#prev_chapter_btn").addClass('disabled');
                    }

                    if (result.next_chapter !== '') {
                        $("#next_chapter_btn").removeClass('disabled');
                        $("#next_chapter_btn").attr('onclick', 'actionChangeChapter(\'' + result.next_chapter.storySlug + ',' + result.next_chapter.chapterSlug + '\')');
                    } else {
                        $("#next_chapter_btn").attr('onclick', 'javascript:void(0)');
                        $("#next_chapter_btn").addClass('disabled');
                    }

                    $('.breadcrumb-item.active').html(result.chapterName);
                    window.history.pushState(
                        { data: jQuery('.page-wrapper .page-content .container').html() },
                        result.chapterName,
                        result.url
                    );
                    clearTimeout(parseInt(sessionStorage.getItem('_0vx57u3v2')));

                    let timeOut = setTimeout(function () {
                        var sendInfo = {
                            '_token': getMetaContentByName("_token"),
                            'chapter_id': $('input[name="chapter_id"]').val(),
                            'story_id': $('input[name="story_id"]').val(),
                            'story_slug': result.storySlug,
                        };
                        $.ajax({
                            url: site + '/ajax/story-add-view',
                            type: 'POST',
                            data: sendInfo,
                            dataType: 'html',
                            beforeSend: function () {
                            },
                            success: function (data) {
                            },
                            complete: function () {
                            },
                            error: function (errorThrown) {
                                console.log(errorThrown);
                            }
                        });
                    }, 10.5e4);
                    sessionStorage.setItem("_0vx57u3v2", timeOut);
                } else {
                    Swal.fire(
                        'Oops...',
                        'Đã xãy ra lỗi trong quá trình tải chương, vui lòng thử lại.',
                        'error'
                    );
                }
            }
        });
        return false;
    })(jQuery);
}

function readNotification(e, id) {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để có thể đọc thông báo này.',
                'error'
            );
            return false;
        } else {
            let arr = {
                "_token": getMetaContentByName("_token"),
                "id": id,
            };
            let body = $("body");
            $.ajax({
                type: "POST",
                url: site + "/ajax/read-notification",
                data: arr,
                cache: false,
                beforeSend: function () {
                    body.addClass("loading");
                },
                success: function (result) {
                    body.removeClass("loading");
                    if (result.success) {
                        window.location.href = $(e).attr('data-href');
                    }
                }
            });
        }
    })(jQuery);
}

function makeAllRead() {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để có thể thực hiện hành động này.',
                'error'
            );
            return false;
        } else {
            let arr = {
                "_token": getMetaContentByName("_token"),
            };
            let body = $("body");
            $.ajax({
                type: "POST",
                url: site + "/ajax/make-all-read-notification",
                data: arr,
                cache: false,
                beforeSend: function () {
                    body.addClass("loading");
                },
                success: function (result) {
                    body.removeClass("loading");
                    if (result.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Thành công",
                            text: result.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                }
            });
        }
    })(jQuery);
}

function buyChapter(chapter_id, story_id) {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để mở khoá chương này.',
                'error'
            );
            return false;
        } else {
            Swal.fire({
                title: 'Mở khoá chương',
                text: 'Bạn muốn mở khoá chương này?',
                icon: 'question',
                showCancelButton: true,
                cancelButtonText: 'Huỷ',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK',
            }).then((result) => {
                if (result.isConfirmed) {
                    let arr = {
                        "_token": getMetaContentByName("_token"),
                        "chapter_id": chapter_id,
                        "story_id": story_id
                    };
                    let body = $("body");
                    $.ajax({
                        type: "POST",
                        url: site + "/ajax/buy-chapter",
                        data: arr,
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
                                        window.location.reload();
                                    }
                                })
                            } else {
                                Swal.fire({
                                    title: 'Lỗi',
                                    text: result.message,
                                    icon: 'error',
                                    showCancelButton: true,
                                    cancelButtonText: 'OK',
                                    confirmButtonColor: '#0d6efd',
                                    confirmButtonText: 'Nạp ngay',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = site + '/user/nap-tien';
                                    }
                                })
                            }
                        }
                    });
                }
            });
        }
    })(jQuery);
}

function historiesRender() {
    (function ($) {
        if (typeof(Storage) !== 'undefined') {
            //Nếu có hỗ trợ
            //Thực hiện thao tác với Storage
            if (localStorage.getItem('otruyenHistories') !== null) {
                let data = localStorage.getItem('otruyenHistories');
                let histories = JSON.parse(data);
                let historyItems = '';
                if (histories.length > 0) {
                    let count = histories.length;
                    if (count > 50) {
                        count = 50;
                    }
                    for (let i = 0; i < count; i++) {
                        historyItems += `<div class="col-md-3 col-6">
                            <div class="card">
                                <div class="position-relative">
                                    <a href="${histories[i]['link']}">
                                        <img data-original="${histories[i]['thumbnail']}" alt="${histories[i]['title']}" class="card-img-top"
                                             width="200" height="260" src="${histories[i]['thumbnail']}" onerror="this.src='/img/no-image.png'">
                                    </a>
                                    <div class="">
                                        <div class="position-absolute top-0 end-0 m-1 product-discount">
                                            <a href="javascript:;" onclick="deleteHistoryItem(${histories[i]['id']})" class="ms-3">
                                                <i class='bx bxs-trash'></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <a href="${histories[i]['link']}">
                                        <h3 class="card-title cursor-pointer story-item-title">${histories[i]['title']}</h3>
                                    </a>
                                    <div class="d-flex justify-content-between">
                                        <span class="chapter font-meta">
                                            <a href="${histories[i]['lastChapterLink']}">
                                                Đã xem ${histories[i]['lastChapterTitle']}
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    }
                }
                $('#readHistoriesResult').html(historyItems);
            }
        } else {
            //Nếu không hỗ trợ
            console.log('Trình duyệt của bạn không hỗ trợ Storage');
        }
    })(jQuery);
}

function deleteHistoryItem(id) {
    (function ($) {
        Swal.fire({
            title: 'Xoá truyện',
            text: 'Bạn muốn xoá này khỏi danh sách?',
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: 'Huỷ',
            confirmButtonColor: '#0d6efd',
            confirmButtonText: 'OK',
        }).then((result) => {
            if (result.isConfirmed) {
                if (typeof(Storage) !== 'undefined') {
                    //Nếu có hỗ trợ
                    if (localStorage.getItem('otruyenHistories') !== null) {
                        let data = localStorage.getItem('otruyenHistories');
                        let histories = JSON.parse(data);
                        for (let i = 0; i < histories.length; i++) {
                            if (histories[i]['id'] == id) {
                                histories.splice(i, 1);
                            }
                        }
                        localStorage.setItem(
                            'otruyenHistories',
                            JSON.stringify(histories)
                        );
                        historiesRender();
                        Swal.fire({
                            icon: "success",
                            title: "Đã xoá truyện khỏi danh sách",
                            text: result.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                } else {
                    //Nếu không hỗ trợ
                    console.log('Trình duyệt của bạn không hỗ trợ Storage');
                }
            }
        });
    })(jQuery);
}

function sendReview() {
    (function ($) {
        if (!islogin) {
            Swal.fire(
                'Oops...',
                'Bạn cần đăng nhập để đánh giá truyện này.',
                'error'
            );
            return false;
        } else {
            Swal.fire({
                title: 'Gửi đánh giá',
                text: 'Bạn muốn gửi đánh giá cho truyện này?',
                icon: 'question',
                showCancelButton: true,
                cancelButtonText: 'Huỷ',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK',
            }).then((result) => {
                if (result.isConfirmed) {
                    let story_id = $('#story_id').val();
                    let ratingOnly = 0;
                    if ($('#ratingOnly').is(':checked')) {
                        ratingOnly = 1;
                    }
                    let reviewRating = $('#reviewRating').val();
                    let reviewContent = '';
                    if (!ratingOnly) {
                        reviewContent = $('#reviewContent').val();
                        if (wordCount(reviewContent) < 30) {
                            Swal.fire(
                                'Oops...',
                                'Đánh giá phải có ít nhất 50 từ!',
                                'error'
                            );
                            return;
                        }
                    }
                    let currentLink = window.location.href;

                    let arr = {
                        "_token": getMetaContentByName("_token"),
                        "ratingOnly": ratingOnly,
                        "reviewRating": reviewRating,
                        "reviewContent": reviewContent,
                        "story_id": story_id,
                        "currentLink": currentLink
                    };
                    let body = $("body");
                    $.ajax({
                        type: "POST",
                        url: site + "/ajax/sent-story-review",
                        data: arr,
                        cache: false,
                        beforeSend: function () {
                            body.addClass("loading");
                        },
                        success: function (result) {
                            body.removeClass("loading");
                            if (result.success) {
                                Swal.fire({
                                    title: 'Đã gửi đánh giá',
                                    text: result.message,
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: '#0d6efd',
                                    confirmButtonText: 'OK',
                                }).then((result) => {
                                    if (result.isConfirmed) {
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
                    });
                }
            });
        }
    })(jQuery);
}

function replyReview(name, review_id) {
    (function ($) {
        $('#replyReviewChild-' + review_id).val('@' + name + ': ');
        $('#replyReviewChild-' + review_id).focus();
    })(jQuery);
}

function sentReplyReview(review_id) {
    (function ($) {
        let content = $('#replyReviewChild-' + review_id).val();
        if (content === '') {
            Swal.fire(
                'Oops...',
                'Vui lòng nhập nội dung trả lời!',
                'error'
            );
            return;
        }

        let story_id = $('#story_id').val();
        let arr = {
            "_token": getMetaContentByName("_token"),
            "review_id": review_id,
            "replyContent": content,
            "story_id": story_id,
        };
        let body = $("body");
        $.ajax({
            type: "POST",
            url: site + "/ajax/sent-reply-review",
            data: arr,
            cache: false,
            beforeSend: function () {
                body.addClass("loading");
            },
            success: function (result) {
                body.removeClass("loading");
                if (result.success) {
                    $('#blockReplyReview-' + review_id).remove();
                    $('#review-' + review_id).append(result.html);
                } else {
                    Swal.fire(
                        'Oops...',
                        'Sorry, unexpected error. Please try again later.',
                        'error'
                    );
                }
            }
        });
    })(jQuery);
}

function loadMoreReview(page) {
    (function ($) {
        let story_id = $('#story_id').val();
        let body = $("body");
        $.ajax({
            type: "GET",
            url: site + "/ajax/load-more-review?page=" + page + '&story_id=' + story_id,
            cache: false,
            beforeSend: function () {
                body.addClass("loading");
            },
            success: function (result) {
                body.removeClass("loading");
                if (result.success) {
                    $('#btnMoreReview').remove();
                    $('#reviewResult').append(result.html);
                } else {
                    Swal.fire(
                        'Oops...',
                        'Sorry, unexpected error. Please try again later.',
                        'error'
                    );
                }
            }
        });
    })(jQuery);
}

function wordCount(str) {
    return str.split(" ").length;
}

function deleteCookie(name) {
    document.cookie = name + '=; Max-Age=-99999999; path=/';
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

// Định nghĩa hàm debounce
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        if (timeoutId) clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
}

function _0x51v42ae9(passphrase, encrypted_json_string) {
    var obj_json = JSON.parse(encrypted_json_string);

    var encrypted = obj_json._4xhbkt98c5v;
    var salt = CryptoJS.enc.Hex.parse(obj_json._0x67br4ff);
    var iv = CryptoJS.enc.Hex.parse(obj_json._0x4tvbv6vcr);

    var key = CryptoJS.PBKDF2(passphrase, salt, { hasher: CryptoJS.algo.SHA512, keySize: 64/8, iterations: 999});

    var decrypted = CryptoJS.AES.decrypt(encrypted.toString(), key, { iv: iv});
    return decrypted.toString(CryptoJS.enc.Utf8);
}

function getMetaContentByName(name, content) {
    var content = (content == null) ? 'content' : content;
    return document.querySelector("meta[name='" + name + "']").getAttribute(content);
}

function formatNumber(n) {
    // format number 1000000 to 1,234,567
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}

function formatCurrency(input, blur) {
    // appends $ to value, validates decimal side
    // and puts cursor back in right position.

    // get input value
    var input_val = input.val();

    // don't validate empty input
    if (input_val === "") { return; }

    // original length
    var original_len = input_val.length;

    // initial caret position
    var caret_pos = input.prop("selectionStart");

    // check for decimal
    if (input_val.indexOf(".") >= 0) {

        // get position of first decimal
        // this prevents multiple decimals from
        // being entered
        var decimal_pos = input_val.indexOf(".");

        // split number by decimal point
        var left_side = input_val.substring(0, decimal_pos);
        var right_side = input_val.substring(decimal_pos);

        // add commas to left side of number
        left_side = formatNumber(left_side);

        // validate right side
        right_side = formatNumber(right_side);

        // Limit decimal to only 2 digits
        right_side = right_side.substring(0, 2);

        // join number by .
        input_val = left_side + "." + right_side;

    } else {
        // no decimal entered
        // add commas to number
        // remove all non-digits
        input_val = formatNumber(input_val);
        input_val = input_val;
    }

    // send updated string to input
    input.val(input_val);

    // put caret back in the right position
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
}
