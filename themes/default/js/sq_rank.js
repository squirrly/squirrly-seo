(function() {
    var SQ = this;

    if (typeof this.sq_dashurl == 'undefined')
        if (typeof sq_dashurl !== 'undefined')
            this.sq_dashurl = sq_dashurl;
        else
            this.sq_dashurl = 'http://my.squirrly.co/';

    if (typeof this.__token == 'undefined' && typeof __token !== 'undefined')
        this.__token = __token;
    if (typeof this.typenow == 'undefined' && typeof typenow !== 'undefined')
        this.typenow = typenow;

    /**
     * Search item in array
     *
     * @param id integer
     * @param array array
     * @return boolean
     */
    this.inArray = function(id, array) {
        if (array.length == 0)
            return false;

        for (var i = 0; i < array.length; i++) {
            if (array[i] == id) {
                return true;
            }
        }
        return false;
    };

    this.getHashParam = function(key) {
        if (location.href.indexOf("#") != -1 && window.location.href.split('#')[1] != '') {
            var results = new RegExp('[\\?&#]' + key + '=([^&#]*)').exec(window.location.href);

            if (results)
                return results[1] || 0;
        }

        return false;
    };

    this.setHashParam = function(key, val) {
        var separator = '';

        if (!this.getHashParam(key)) {
            if (location.href.indexOf("#") != -1) {
                if (location.href.split('#')[1] != '')
                    separator = '&';
            } else {
                separator = '#';
            }
            window.location.href = window.location.href + separator + key + '=' + val;
        } else {
            window.location.href = window.location.href.replace(key + '=' + this.getHashParam(key), key + '=' + val);

        }

        return false;
    };

    /**
     * Get posts Analytics
     **/
    this.sq_rank = {
        getRanks: function(posts) {
            jQuery.getJSON(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_posts_rank',
                        posts: posts,
                        nonce: sqQuery.nonce
                    }
            ).success(function(response) {
                // if (typeof response.posts == Array){
                //If global information then show it before posts
                if (response)
                    if (typeof response.error !== 'undefined') {
                        if (response.error == 'subscription_expired')
                            jQuery('.sq_rank_column_row').each(function() {
                                jQuery(this).removeClass('sq_minloading').html('<span class="sq_rank"><span class="sq_expired">For Squirrly Rank<br /><a href="' + SQ.sq_dashurl + 'login/?token=' + SQ.__token + '&redirect_to=' + escape(SQ.sq_dashurl + 'user/plans') + '" target="_blank">upgrade your plan</a></span></span>');
                            });
                    } else {

                        if (typeof response.global !== 'undefined') {
                            jQuery('.wrap').before(response.global);
                        }

                        if (typeof response.status !== 'undefined') {
                            jQuery('.wrap').before(response.status);

                            jQuery('.sq_status_close').bind('click', function() {
                                jQuery('#sq_status').remove();
                                jQuery.getJSON(
                                        sqQuery.ajaxurl,
                                        {
                                            action: 'sq_posts_status_close',
                                            nonce: sqQuery.nonce
                                        }
                                );
                            });
                        }

                        var post_id;
                        //console.log(response.posts);
                        //Show responce from each post
                        jQuery('.sq_rank_column_row').each(function() {
                            jQuery(this).removeClass('sq_minloading');
                            if (jQuery.inArray(jQuery(this).attr('ref'), posts) !== -1) {
                                post_id = jQuery(this).attr('ref');
                                //console.log(post_id,response.posts[post_id]);
                                jQuery(this).removeClass('sq_minloading').html('<span class="sq_rank" ref="' + post_id + '"></span>');

                                response.posts[jQuery(this).attr('ref')].offpage = ((response.posts[post_id].offpage > 0) ? '+' : '') + response.posts[post_id].offpage;
                                jQuery(this).find('.sq_rank').append('<span class="sq_rank_text"><span id="sq_rank_global-' + post_id + '" class="sq_rank_value ' + ((response.posts[post_id].offpage > 0) ? 'sq_rank_progress_value' : '') + '">' + response.posts[post_id].offpage + '</span> ' + __sq_rankglobal_text + '</span>');

                                if (response.posts[post_id].onpage > 0) {
                                    //jQuery(this).find('.sq_rank').append('<span class="sq_rank_text sq_optimized"><span class="sq_rank_value">'+response.posts[post_id].onpage+'%</span> '+__sq_rankoptimized_text+'</span>');
                                    jQuery(this).find('.sq_rank').append('<span class="sq_rank_column_button sq_show_more" ref="' + post_id + '">' + __sq_rankseemore_text + '</span>');
                                    SQ.sq_eventsrank.addClick(jQuery(this).find('.sq_show_more'));
                                } else {
                                    // jQuery(this).find('.sq_rank').append('<span class="sq_rank_text sq_optimized"><span class="sq_rank_value" >0%</span> '+__sq_rankoptimized_text+'</span>');
                                    jQuery(this).find('.sq_rank').append('<span class="sq_rank_column_button sq_optimize" ref="' + post_id + '">' + __sq_optimize_text + '</span>');
                                }
                            } else {
                                jQuery(this).removeClass('sq_minloading').html('<span class="sq_no_rank" ref="' + post_id + '">' + __sq_ranknotpublic_text + '</span>');
                            }
                        });

                        jQuery('.sq_optimize').bind('click', function() {
                            if (typeof sqQuery.adminposturl !== 'undefined')
                                location.href = sqQuery.adminposturl + "?post=" + jQuery(this).attr('ref') + "&action=edit";
                            else
                                location.href = "/wp-admin/post.php?post=" + jQuery(this).attr('ref') + "&action=edit";
                        });
                    }
                // }
            });
            jQuery('.sq_rank_column_row').each(function() {

            });
        }
    };

    /**
     * Listen the posts and call the Analytics (sq_rank) function
     */
    this.sq_eventsrank = {
        listen: function(posts) {
            SQ.sq_rank.getRanks(posts);
        },
        addClick: function(arrow) {
            arrow.unbind('click').bind('click', function() {
                var post_id = jQuery(this).attr('ref');
                //var _this = jQuery(this);
                SQ.sq_eventsrank.arrowClick(post_id, jQuery(this), true);
            });

            if (SQ.getHashParam('sq_brief') != '') {
                var posts = [];

                if (SQ.getHashParam('sq_brief').indexOf(",") != -1) {
                    posts = SQ.getHashParam('sq_brief').split(",");
                } else {
                    posts[0] = SQ.getHashParam('sq_brief');
                }

                for (var i = 0; i < posts.length; i++) {
                    jQuery('.sq_rank_column_row[ref=' + posts[0] + ']').find('.sq_show_more').trigger('click');
                    //SQ.sq_eventsrank.loadBrief(posts[i]);
                }

            }

        },
        arrowClick: function(post_id, _this, scroll) {
            //Load the brief information
            SQ.sq_eventsrank.loadBrief(post_id);

            if (jQuery('#sq_post-' + post_id).is(":visible")) {
                jQuery('#sq_post-' + post_id).hide();
                if (jQuery('#sq_post-' + post_id).length != 0)
                    jQuery('#sq_post_details-' + post_id).hide();
                if (typeof _this !== 'undefined') {
                    _this.find('.sq_arrow_down').removeClass('sq_arrow_up');
                    _this.find('.sq_more_detail_text').html(__sq_more_details);
                }
            } else {
                jQuery('#sq_post-' + post_id).show();
                if (typeof _this !== 'undefined') {
                    _this.find('.sq_arrow_down').addClass('sq_arrow_up');
                    _this.find('.sq_more_detail_text').html(__sq_less_details);

                    if (scroll)
                        var rowtop = jQuery('#sq_post-' + post_id).find('.sq_post_rank_content').offset().top - 200;
                    var scrolltop = jQuery(window).scrollTop();
                    if (scrolltop < rowtop) {
                        jQuery('html,body').animate({scrollTop: rowtop}, 1000, function() {
                        });
                    }
                }
            }



            if (typeof _this !== 'undefined') {
                jQuery('#sq_post-' + post_id).find('.sq_post_rank_close').unbind('click').bind('click', function() {
                    _this.trigger('click');
                });
            }
            jQuery('#sq_post-' + post_id).find('.sq_post_rank_refresh').unbind('click').bind('click', function() {
                SQ.sq_eventsrank.loadBrief(post_id);
            });
        },
        loadBrief: function(post_id) {
            if (jQuery('#sq_post-' + post_id).length == 0) {

                jQuery('#post-' + post_id).after('<tr id="sq_post-' + post_id + '" style="display:none"><td colspan="' + (jQuery('#post-' + post_id + ' td').length + 1) + '" class="sq_post_rank_row"><div  class="sq_post_rank_title">' + __sq_article_rank + '</div><div class="sq_post_rank_content sq_loading"></div><div class="sq_post_arrow_details"></div><div class="sq_post_rank_close">x</div><div class="sq_post_rank_refresh">' + __sq_refresh + '</div></td></tr>');
            } else {
                jQuery('#sq_post-' + post_id).find('.sq_post_rank_content').html('');
                jQuery('#sq_post-' + post_id).find('.sq_post_arrow_details').html('');
                jQuery('#sq_post_details-' + post_id).hide();
                jQuery('#sq_post-' + post_id).find('.sq_post_rank_content').addClass('sq_loading');
            }

            jQuery.getJSON(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_post_rank_brief',
                        post: post_id,
                        nonce: sqQuery.nonce
                    }
            ).success(function(response) {
                jQuery('#sq_post-' + post_id).find('.sq_post_rank_content').removeClass('sq_loading');
                jQuery('#sq_rank_default_text').remove();
                if (response)
                    if (typeof response.error !== 'undefined') {
                        jQuery('#sq_post-' + post_id).find('.sq_post_rank_content').html('<ul class="sq_no_information"><li>' + response.message + '</li></ul>');
                    } else {
                        jQuery('#sq_post-' + post_id).find('.sq_post_arrow_details').html('<span class="sq_arrow_button"><span class="sq_more_detail_text">' + __sq_more_details + '</span><span class="sq_arrow_down"></span></span>');
                        jQuery('#sq_post-' + post_id).find('.sq_post_rank_content').html(response);

                        jQuery('#sq_post-' + post_id).find('.sq_post_arrow_details').prepend('<div class="sq_report_interval">' + __sq_interval_text + '<select id="sq_interval-' + post_id + '"><option value="day">' + __sq_interval_day + '</option><option value="week">' + __sq_interval_week + '</option><option value="month" selected="selected" >' + __sq_interval_month + '</option></select></div>');
                        jQuery('#sq_interval-' + post_id).bind('change', function() {
                            jQuery('#sq_post_details-' + post_id).hide();
                            jQuery('#sq_post-' + post_id).find('.sq_arrow_button').trigger('click');
                        });
                        jQuery('#sq_post-' + post_id).find('.sq_rank_history_text').bind('click', function() {
                            if (jQuery(this).attr('ref') != '') {
                                jQuery('#sq_post_details-' + post_id).hide();
                                jQuery('#sq_interval-' + post_id + ' option[value=' + jQuery(this).attr('ref') + ']').attr("selected", "selected");
                                jQuery('#sq_post-' + post_id).find('.sq_arrow_button').trigger('click');
                            }
                        });
                        /* Add the click event for more rank detais*/
                        SQ.sq_eventsrank.detailsClick(jQuery('#sq_post-' + post_id).find('.sq_arrow_button'), post_id);

                        //For incorporated post
                        jQuery('#sq_post-' + post_id).find('.sq_post_arrow_details').append('<span class="sq_allposts_button" style="display:none"><span class="sq_allposts_text">' + __sq_goto_allposts + '</span></span>');
                        jQuery('#sq_post-' + post_id).find('.sq_allposts_button').bind('click', function() {
                            if (typeof sqQuery.adminlisturl !== 'undefined')
                                location.href = sqQuery.adminlisturl + "?post_type=" + SQ.typenow + "&post_status=publish&sq_post_id=" + post_id + "#sq_brief=" + post_id;
                            else
                                location.href = "/wp-admin/edit.php?post_type=" + SQ.typenow + "&post_status=publish&sq_post_id=" + post_id + "#sq_brief=" + post_id;
                        });

                        var originalFontSize = 12;
                        jQuery('.sq_rank_total_value').each(function() {
                            var spanHeight = jQuery(this).outerHeight();
                            var sectionHeight = 50;
                            var ratio = (sectionHeight / spanHeight);
                            if (ratio < 1)
                                ratio = ratio / 1.10;
                            //console.log(sectionHeight, spanHeight);
                            var newFontSize = ratio * originalFontSize;
                            //console.log("font-size: "+newFontSize);
                            jQuery(this).css({"font-size": newFontSize});
                        });
                    }
            });

        },
        loadDetails: function(post_id) {
            var sq_interval = 'month';

            if (jQuery('#sq_post_details-' + post_id).length == 0) {
                jQuery('#sq_post-' + post_id).after('<tr id="sq_post_details-' + post_id + '" style="display:none"><td colspan="' + (jQuery('#post-' + post_id + ' td').length + 1) + '" class="sq_post_rank_row"><div class="sq_post_rank_content sq_loading"></div></td></tr>');
            } else {
                jQuery('#sq_post_details-' + post_id).find('.sq_post_rank_content').html('');
                jQuery('#sq_post_details-' + post_id).find('.sq_post_rank_content').addClass('sq_loading');
            }

            if (jQuery('#sq_interval-' + post_id).length != 0 && jQuery('#sq_interval-' + post_id + ' :selected').val() != '')
                sq_interval = jQuery('#sq_interval-' + post_id + ' :selected').val();

            jQuery.getJSON(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_post_rank',
                        post: post_id,
                        interval: sq_interval,
                        nonce: sqQuery.nonce
                    }
            ).success(function(response) {
                jQuery('#sq_post_details-' + post_id).find('.sq_post_rank_content').removeClass('sq_loading');
                jQuery('#sq_post_details-' + post_id).find('.sq_post_rank_content').html(response.rank);

            });
        },
        detailsClick: function(details, post_id) {
            details.unbind('click').bind('click', function() {
                SQ.sq_eventsrank.loadDetails(post_id);

                if (jQuery('#sq_post_details-' + post_id).is(":visible")) {
                    jQuery('#sq_post_details-' + post_id).hide();
                    jQuery(this).find('.sq_arrow_down').removeClass('sq_arrow_up');
                    jQuery(this).find('.sq_more_detail_text').html(__sq_more_details);

                } else {
                    jQuery('#sq_post_details-' + post_id).show();
                    jQuery(this).find('.sq_arrow_down').addClass('sq_arrow_up');
                    jQuery(this).find('.sq_more_detail_text').html(__sq_less_details);

                    var rowtop = jQuery(this).offset().top - 50;
                    var scrolltop = jQuery(window).scrollTop();
                    if (scrolltop < rowtop) {
                        jQuery('html,body').animate({scrollTop: rowtop}, 1000, function() {
                        });
                    }
                }


            });
        }



    };
})(window);

jQuery(document).ready(function() {
    if (typeof sq_posts !== 'undefined')
        window.sq_eventsrank.listen(sq_posts);
});
