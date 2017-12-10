function getShortestColumn(...columns) {
    let index = 0;
    let height = columns[index].height();
    for (let i = 1; i < columns.length; i++) {
        if (columns[i].height() < height) {
            index = i;
            height = columns[i].height();
        }
    }
    return columns[index];
}