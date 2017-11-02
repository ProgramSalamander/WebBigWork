class FriendNews {

    constructor(data) {
        this.data = data;
    }

    render() {
        let newsList = $('<ul></ul>').addClass('uk-list uk-list-divider friends-news-list');
        for (let i = 0; i < this.data.length; i++) {
            let newsData = this.data[i];
            let news = $('<li></li>').addClass('friends-news').html(`<div class="uk-grid-small friend-news-header" uk-grid>\
                                <div class="uk-width-auto uk-align-center">\
                                    <img class="uk-border-circle friend-head-pic" src="${newsData.friendHeadPicUrl}"/>\
                                </div>\
                                <div class="uk-width-expand uk-align-center">\
                                    <p class="uk-width-large">\
                                        <a href="${newsData.friendHomePageUrl}" class="uk-text-success" title="去Ta的主页" uk-tooltip>${this.data[i].friendName}</a>: ${newsData.friendNewsContent}\
                                    </p>\
                                </div>\
                                <div class="uk-width-auto uk-align-center">\
                                    <span class="uk-text-muted uk-text-small">${newsData.friendNewsTime}</span>\
                                </div>\
                              </div>`);

            //照片
            let photoContainer = $('<div class="uk-card uk-card-default uk-position-relative uk-visible-toggle uk-light friend-news-body" uk-slideshow></div>');
            let photos = $('<ul class="friend-news-photo-slide"></ul>').addClass('uk-slideshow-items');
            for (let i = 0; i < newsData.friendNewsPhotos.length; i++) {
                photos.append($(`<li><img class="friend-news-photo" src="${newsData.friendNewsPhotos[i]}" uk-cover/></li>`))
            }
            photoContainer.append(photos);
            if(newsData.friendNewsPhotos.length > 1) {
                photoContainer.append($('<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>'));
                photoContainer.append($('<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>'));
            }
            news.append(photoContainer);

            //添加动态交互按钮（喜欢、评论、转发）
            let buttonGroup = $('<div></div>').addClass('uk-margin uk-text-right').html(`<a class="uk-display-inline friend-news-btn-like" title="喜欢" uk-tooltip><img src="../../imgs/icon/like.svg" /></a> <span class="uk-badge like-badge uk-margin-right">${this.data[i].friendNewsLikes}</span>\
                                    <a class="uk-display-inline uk-icon-link uk-margin-right friend-news-btn-comment" uk-icon="icon: commenting" title="评论" uk-tooltip></a>\
                                    <a class="uk-display-inline uk-icon-link friend-news-btn-forward" uk-icon="icon: forward" title="转发" uk-tooltip></a>`);

            let likeBtn = buttonGroup.find('a').first();
            let likeBadge = likeBtn.next();
            let commentBtn = likeBadge.next();
            let forwardBtn = commentBtn.next();

            //喜欢
            likeBtn.click(function () {
                let newVal;
                if (!newsData.friendNewsIsLiked) {
                    newVal = parseInt(likeBadge.text()) + 1;
                    likeBadge.text(newVal);
                    likeBtn.html("<img src='../../imgs/icon/like_active.svg'>");
                    newsData.friendNewsIsLiked = true;
                }
                else {
                    newVal = parseInt(likeBadge.text()) - 1;
                    likeBadge.text(newVal);
                    likeBtn.html("<img src='../../imgs/icon/like.svg'>");
                    newsData.friendNewsIsLiked = false;
                }
            });
            //评论
            commentBtn.click(function () {
                myComment.toggle();
                // getCookie('user_head_pic');
            });
            //转发
            forwardBtn.click(function () {
                alert('a');
            });

            news.append(buttonGroup);

            //评论列表
            let commentList = $('<ul></ul>').addClass('friend-news-comment-list');
            let commentData = newsData.commentData;

            //我的评论
            let myComment = $('<li></li>').addClass('friend-news-my-comment').html(`<div class="uk-grid-small friend-news-comment-body friend-news-my-comment" uk-grid>\
                                        <div class="uk-width-auto">\
                                            <img class="friend-news-comment-head-pic uk-border-circle" src="../imgs/index/bg2.jpg"/>\
                                        </div>\
                                        <div class="uk-width-expand">\
                                            <input class="uk-input friend-news-my-comment-input" type="text" placeholder="请输入您的评论"/>\
                                        </div>\
                                        <div class="uk-width-auto">\
                                            <a class="uk-display-inline uk-icon" uk-icon="icon:plus" title="发表评论" uk-tooltip></a>\
                                        </div>\
                                    </div>`).hide().css('transition', '1s');
            commentList.append(myComment);

            myComment.find('a').first().click(function () {
                let content = myComment.find('input').first().val();
                if (content === ''){
                    notification('评论不能为空哦','warning');
                }
                else {
                    let date = new Date();
                    let comment = $('<li></li>').html(`<div class="uk-grid-small friend-news-comment-body" uk-grid>\
                                        <div class="uk-width-auto">\
                                            <img class="friend-news-comment-head-pic uk-border-circle" src="../../imgs/index/bg2.jpg"/>\
                                        </div>\
                                        <div class="uk-width-expand">\
                                            <a href="hisPage.html" class="friend-news-comment-user" class="uk-text-primary" title="去Ta的主页" uk-tooltip>${commentData[i].commentUsername}</a>:\
                                            <span class="friend-news-comment-content">${content}</span>\
                                        </div>\
                                        <div class="uk-width-auto">\
                                            <span class="friend-news-comment-time uk-text-meta">${date.getHours() + ":" + date.getMinutes()}</span>\
                                        </div>\
                                     </div>`);
                    commentList.append(comment);
                    notification('评论成功！', 'success');
                    myComment.hide();
                }
            });

            for (let i = 0; i < commentData.length; i++) {
                let comment = $('<li></li>').html(`<div class="uk-grid-small friend-news-comment-body" uk-grid>\
                                        <div class="uk-width-auto">\
                                            <img class="friend-news-comment-head-pic uk-border-circle" src="${commentData[i].commentHeadPicUrl}"/>\
                                        </div>\
                                        <div class="uk-width-expand">\
                                            <a href="hisPage.html" class="friend-news-comment-user" class="uk-text-primary" title="去Ta的主页" uk-tooltip>${commentData[i].commentUsername}</a>:\
                                            <span class="friend-news-comment-content">${commentData[i].commentContent}</span>\
                                        </div>\
                                        <div class="uk-width-auto">\
                                            <span class="friend-news-comment-time uk-text-meta">${commentData[i].commentTime}</span>\
                                        </div>\
                                     </div>`);
                commentList.append(comment);
            }

            news.append(commentList);

            newsList.append(news);
        }

        return newsList;
    }
}