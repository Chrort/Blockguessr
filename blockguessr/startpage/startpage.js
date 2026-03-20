let yaw = Math.random() * 360;

const loadingScreen = () => {
    const loadingScreenDiv = document.getElementById("loadingScreenDiv");
    loadingScreenDiv.style.display = "flex";
    let dotString = "."
    document.body.style.overflow = "hidden";
    setInterval(() => {
        loadingScreenDiv.innerHTML = `Loading locations ${dotString}`;
        dotString += ".";
        dotString.length > 5 ? dotString = "." : null;
    }, 1000);
}

pannellum.viewer('panorama', {
            "type": "cubemap",
            "autoLoad": true,
            "showControls": false,
            "draggable": false,
            "mouseZoom": false,
            "yaw": yaw,
            "pitch": -20,
            "autoRotate": -2,
            "cubeMap": [
                `../../img/fullServerPano/panorama_0.jpg`,
                `../../img/fullServerPano/panorama_1.jpg`,
                `../../img/fullServerPano/panorama_2.jpg`,
                `../../img/fullServerPano/panorama_3.jpg`,
                `../../img/fullServerPano/panorama_4.jpg`,
                `../../img/fullServerPano/panorama_5.jpg`,
            ]
        })

document.querySelector('header').style.setProperty(
    "background",
    "rgba(121, 121, 121, 0.6)",
    "important"
);

document.getElementById('dropdown').style.setProperty(
    "background",
    "rgba(121, 121, 121, 0.6)",
    "important"
);

document.getElementById("goBack").setAttribute("href", "../../index.php");