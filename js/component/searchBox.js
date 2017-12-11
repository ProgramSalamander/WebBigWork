class SearchBox {
    constructor(container) {
        this.container = container;
    }

    init() {
        let searchBox = $(`<a class="uk-navbar-toggle" uk-search-icon></a>
                        <div class="uk-drop" uk-drop="mode: click; pos: left-center; offset: 0">
                            <form action="search.php" method="get" class="uk-search uk-search-navbar uk-width-1-1">
                                <input class="uk-search-input" type="search" placeholder="搜索用户/照片/摄影活动..." autofocus>
                            </form>
                        </div>`);
        this.container.append(searchBox);
    }
}