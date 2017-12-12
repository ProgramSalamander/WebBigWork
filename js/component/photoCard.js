class PhotoCard {

    constructor(data, width){
        this.data = data;
        this.width = width;
    }

    render(){
        let photoCard = $(`<div class="uk-card uk-card-hover uk-card-default uk-margin-bottom">\
                        <a target="_blank" href="photoContent.php?id=${this.data.photoId}">\
                            <div class="uk-inline-clip uk-transition-toggle">\
                                <img style="width: ${this.width}px; height: ${this.width / this.data.photoWHRate}px;" class="uk-transition-scale-up uk-transition-opaque"\
                                                                     src="${this.data.photoUrl}">\
                            </div>\
                        </a>\
                        <div class="uk-grid-small uk-padding-small" uk-grid>\
                            <div class="uk-width-auto">\
                                <span class="uk-icon" uk-icon="icon: user"></span><a href="homepage.php?username=${this.data.photoUsername}" class="uk-text-small" title="去ta的主页" uk-tooltip>${this.data.photoAuthor}</a>\
                            </div>\
                            <div class="uk-width-expand uk-text-center">\
                                <span class="uk-label">${this.data.photoLabel}</span>\
                            </div>\
                            <div class="uk-width-auto">\
                                <a target="_blank" href="photoContent.php?id=${this.data.photoId}" class="uk-icon-link" uk-icon="icon:heart" title="喜欢" uk-tooltip></a>\
                                <a target="_blank" href="photoContent.php?id=${this.data.photoId}" class="uk-margin-left uk-icon-link" uk-icon="icon:commenting" title="评论" uk-tooltip></a>\
                            </div>\
                        </div>\
                    </div>`);
        photoCard.find('.uk-label').addClass(this.data.photoLabelClass);
        return photoCard;
    }
}