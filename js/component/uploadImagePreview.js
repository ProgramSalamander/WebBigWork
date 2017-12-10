class UploadImagePreview {
    constructor(source, progressBar) {
        this.source = source;
        this.progressBar = progressBar;
        this.uploadFiles = [];
    }

    render() {
        let previewContainer = $(`<div style="height: 300px; overflow: scroll" class="uk-width-2-3 uk-child-width-1-4 uk-grid-small" uk-grid></div>`);
        let progressBar = this.progressBar;

        let uploadFiles = this.uploadFiles;

        UIkit.util.on('.js-upload', 'upload', function (e, files) {
            let newFiles = [];
            //图片去重
            for (let i = 0; i < files.length;i++){
                let isRepeat = false;
                if (files[i].type === 'image/jpeg') {
                    for (let j = 0; j < uploadFiles.length; j++) {
                        if (uploadFiles[j].name === files[i].name) {
                            isRepeat = true;
                            break;
                        }
                    }
                    if (!isRepeat) {
                        uploadFiles.push(files[i]);
                        newFiles.push(files[i]);
                    }
                }
                else {
                    notification('请上传jpg格式的图片。','warning');
                }
            }
            $.each(newFiles, function (index, element) {
                let preview = $(`<div class="uk-position-relative uk-overflow-hidden uk-text-center"></div>`);
                let imgContainer = $(`<div style="height: 140px" class="uk-inline-clip uk-transition-toggle uk-dark" title="${element.name}" uk-tooltip></div>`);
                let img = $('<img src=""/>');
                let deleteBtn = $(`<div style="cursor: pointer" class="uk-transition-fade uk-position-center uk-overlay uk-overlay-default">
                                <span class="uk-icon">移除</span>
                            </div>`);
                let fr = new FileReader();
                fr.onloadstart = function (ev) {
                    progressBar.start();
                };
                fr.onprogress = function (ev) {
                    progressBar.process();
                };
                fr.onloadend = function (ev) {
                    progressBar.end();
                    img.attr('src', ev.target.result);
                };
                fr.readAsDataURL(element);
                deleteBtn.click(function () {
                    uploadFiles.splice(index, 1);
                    preview.remove();
                });
                adapt(img);
                imgContainer.append(img).append(deleteBtn);
                preview.append(imgContainer);
                previewContainer.append(preview);
            });
        });
        return previewContainer;
    }

    getImages(){
        return this.uploadFiles;
    }
}