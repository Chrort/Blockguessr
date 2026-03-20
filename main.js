let slideshowImgCount, i;
let imgArray = [];
let time = 15000;

window.onload = () => {

    fetch("./count.php")
        .then(response => response.json())
        .then(data => {
            slideshowImgCount = data;

            i = Math.ceil(Math.random() * slideshowImgCount);

            fillImgArray();
            changeImg();
            fillImg()
        })
        .catch(error => console.error('Error while reading data: ', error));
}

const fillImgArray = () => {
    for (var i = 0; i < slideshowImgCount; i++) {
        imgArray[i] = `./img/slideshow/slideshow_${i + 1}.jpg`;
    }
}

const changeImg = () => {
    document.slideshow.src = imgArray[i - 1];
    i++;

    i > slideshowImgCount ? i = 1 : null;
}

setInterval(() => {
    changeImg();
}, time);

const fillImg = () => {
    let img = new Image();
    img.src = './/img/fullServerMap.png';
    img.onload = () => {
        document.getElementById("mapBtn").classList.add("mapBtnClass")
    }
}