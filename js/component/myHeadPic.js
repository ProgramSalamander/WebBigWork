class MyHeadPic {
    constructor(picUrl, size, title) {
        this.picUrl = picUrl;
        this.size = size;
        this.title = title;
    }

    render() {
        let img = $(`<img src="${this.picUrl}"/>`);
        adapt(img);
        return $(`<div style="width: ${this.size}px; height: ${this.size}px;overflow: hidden; position: relative;"; class="uk-border-circle">
                  </div>`).append(img);
    }

    renderWithTooltip(){
        let img = $(`<img src="${this.picUrl}" title="${this.title}" uk-tooltip/>`);
        adapt(img);
        return $(`<div style="width: ${this.size}px; height: ${this.size}px;overflow: hidden; position: relative;"; class="uk-border-circle">
                  </div>`).append(img);
    }
}