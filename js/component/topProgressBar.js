class TopProgressBar{

    constructor(){
        this.progressBar = $('<div id="progressBar"></div>')
            .css('width','0')
            .css('position','fixed')
            .css('top','0')
            .css('height','3px')
            .css('transition','1s')
            .css('z-index','999')
            .addClass('uk-background-primary');
    }

    init(){
        $('header').append(this.progressBar);
    }

    start(callback){
        this.progressBar.show();
        callback && callback();
    }

    process(callback){
        this.progressBar.css('width','20%');
        callback && callback();
    }

    end(callback){
        let progressBar = this.progressBar;
        setTimeout(function () {
            progressBar.css('width','100%');
            setTimeout(function () {
                progressBar.hide().css('width','0');
                callback && callback();
            },1000);
        },1000);

    }
}