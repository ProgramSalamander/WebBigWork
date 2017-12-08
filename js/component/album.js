class Album {
    constructor(data) {
        this.data = data;
    }

    render() {
        let album = $(`<div class="uk-text-center">
                    <a href="albumContent.php?id=${this.data.id}" class="uk-inline-clip uk-transition-toggle">
                        <div style="height: 200px;overflow: hidden; position: relative;">
                            <img src="${this.data.coverUrl}"/>
                        </div>
                        <div class="uk-transition-slide-bottom uk-position-bottom uk-overlay uk-overlay-default">
                            <p class="uk-h4 uk-margin-remove">${this.data.name}</p>
                        </div>
                    </a>
                </div>`);
        adapt(album.find('img'));
        return album;
    }

}