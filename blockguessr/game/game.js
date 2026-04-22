import { Map } from '../../map/map_class.js';

let username = document.getElementById("usernameMeta").content;

let viewer;

const guessContainer = document.getElementById("guessContainer");
const mapContainer = document.getElementById("mapContainer");
const guessBtn = document.getElementById("guess");
const mapDiv = document.getElementById("map");
const resultScreen = document.getElementById("resultScreen");
const gameInfo = document.getElementById("gameInfo");
const location = document.getElementById("location");
const idLine = document.getElementById("idLine");

let map = new Map(mapDiv, [-6, 4, -6, 4]);

let mapTransforming = map.xTransform > map.yTransform ? map.xTransform : map.yTransform;
let mapTranslateX, mapTranslateY;

let locations = [];

let pinIsDown = false;
let locScreen = true;
let currentRound = 0;
let timePlayed = 0;

let expansion = [];
let roundData = Array(5);
let totalPoints = 0;

const cB4 = window.getComputedStyle(document.getElementById("cB4"));
let additionalXTransform = +cB4.getPropertyValue("width").slice(0, cB4.getPropertyValue("width").length - 2);

const cB1 = window.getComputedStyle(document.getElementById("cB1"));
let additionalYTransform = +cB1.getPropertyValue("height").slice(0, cB1.getPropertyValue("height").length - 2);

const fetchGameData = async () => {
    try{
        const response = await fetch('../../api/location_data.php');
        const data = await response.json();

        viewer = pannellum.viewer('panorama', {
            "type": "cubemap",
            "autoLoad": true,
            "yaw": 180,
            "showControls": false,
            "compass": true,
            "northOffset": 0,
            "cubeMap": [
                `./panoramas/${username}/${data[currentRound][1]}panorama_0.png`,
                `./panoramas/${username}/${data[currentRound][1]}panorama_1.png`,
                `./panoramas/${username}/${data[currentRound][1]}panorama_2.png`,
                `./panoramas/${username}/${data[currentRound][1]}panorama_3.png`,
                `./panoramas/${username}/${data[currentRound][1]}panorama_4.png`,
                `./panoramas/${username}/${data[currentRound][1]}panorama_5.png`,
            ]
        })

        locations = data;

    }catch(error){
        console.error('Error while loading data:', error);
    }

    await fetch('../../api/get_map_expansion.php')
    .then(response => response.json())
    .then(data => {
      for(let k = 0; k < data.length; k++){
        expansion.push(data[k]);
      }
    })
    .catch(error => console.error('Error while loading data: ', error));
}

window.onload = async () => {
  map.genMap(undefined, "../../");
  await map.fetchData([true, false, true], "../../");
  await fetchGameData();

  adaptSize();
  map.drawLabels(true);
  map.drawStreetLabels(false);
  map.adaptBorders();

  mapDiv.addEventListener("click", setPin);

  mapTranslateX = -1 * ((expansion[1] + expansion[4]) / 2 + mapTransforming - window.innerHeight * 0.4 / map.currentZoomLevel);
  mapTranslateY = -1 * ((expansion[2] + expansion[5]) / 2 + mapTransforming - window.innerHeight * 0.4 / map.currentZoomLevel);
  mapDiv.style.transform = `translateX(${mapTranslateX}px) translateY(${mapTranslateY}px)`;
  map.transformData = [mapTranslateX + additionalXTransform, mapTranslateY + additionalYTransform];

  document.addEventListener("keydown", spaceEvent);
  guessBtn.addEventListener("click", () => {
    if(pinIsDown) guess();
  });
  setInterval(() => {
    timePlayed++;
  }, 100);
}

const guess = () => {
  locScreen = false;

  viewer.destroy();

  let d = distance();
  let p = calculateScore(d[0]);

  resultScreen.style.display = "block";
  mapContainer.classList.add("resultMapContainer");
  gameInfo.classList.add("resultGameInfo");
  guessBtn.style.display = "none";

  location.style.top = `${locations[currentRound][3] + mapTransforming}px`;
  location.style.left = `${locations[currentRound][2] + mapTransforming}px`;
  location.style.scale = 1 / map.currentZoomLevel;
  location.style.display = "flex";

  let size = calcSize(d[1], d[2], d[3], d[4]);

  map.transformData = [0, 0];
  mapDiv.style.scale = size;
  map.currentZoomLevel = size;

  let pTransformData = calcTransform(parseInt(d[1]), parseInt(d[2]), parseInt(d[3]), parseInt(d[4]));

  mapDiv.style.top = `${pTransformData[1]}px`;
  mapDiv.style.left = `${pTransformData[0]}px`;
  mapDiv.style.transform = `translateX(-${additionalXTransform}px) translateY(-${additionalYTransform}px)`;

  map.drawLabels(false);
  map.drawStreetLabels(false);
  map.adaptBorders();
  map.adjustPins(location);
  map.adaptIdLine(idLine);

  guessContainer.style.width = "300px";
  guessContainer.style.height = "345px";

  document.getElementById("nextRoundBtn").addEventListener("click", btnAction);

  idLine.setAttribute("x1", d[1]);
  idLine.setAttribute("y1", d[2]);
  idLine.setAttribute("x2", d[3]);
  idLine.setAttribute("y2", d[4]);

  document.getElementById("distance").innerHTML = `${Math.round(d[0])} m`;
  document.getElementById("score").innerHTML = `${p} points`;
  document.getElementById("scoreInfo").innerHTML = `Score: ${totalPoints + p}`
  totalPoints += p;

  roundData[currentRound] = {d: Math.round(d[0]), p: p};

  currentRound == 4 ? document.getElementById("nextRoundBtn").innerHTML = "Finish game" : document.getElementById("nextRoundBtn").innerHTML = "Next Round";
}

const distance = () => {
  const pinStyles = window.getComputedStyle(document.getElementById("pin"));
  let pinX = +pinStyles.getPropertyValue("left").slice(0, pinStyles.getPropertyValue("left").length - 2);
  let pinY = +pinStyles.getPropertyValue("top").slice(0, pinStyles.getPropertyValue("top").length - 2);

  let locX = locations[currentRound][2] + mapTransforming;
  let locY = locations[currentRound][3] + mapTransforming;

  let d = Math.sqrt((pinX - locX)**2 + (pinY - locY)**2);

  return [d, pinX.toString(), pinY.toString(), locX.toString(), locY.toString()];
}

const calculateScore = d => {
  let radius = 5 * (1 + 0.0002 * expansion[0]);
  if(d < radius) return 5000;
  let p = 5000 * Math.E**(-20 * (d - 5) / (expansion[0] * 5));
  return Math.round(p);
}

const calcSize = (x1, y1, x2, y2) => {
  let w; //max expansion

  Math.abs(x1 - x2) > Math.abs(y1 - y2) ? w = Math.abs(x1 - x2) : w = Math.abs(y1 - y2);

  let p = w / (window.innerHeight * 0.85);

  if(p**-1 * 0.75 > map.maxScale) return map.maxScale;

  return p**-1 * 0.75;
}

const calcTransform = (x1, y1, x2, y2) => {
  let x = Math.abs((x1 + x2) / 2);
  let y = Math.abs((y1 + y2) / 2);
  return [(x * -1 + additionalXTransform) * map.currentZoomLevel + .5 * .85 * window.innerHeight, (y * -1 + additionalYTransform) * map.currentZoomLevel + .5 * .85 * window.innerHeight];
}

const nextRound = () => {
  locScreen = true;
  pinIsDown = false;
  currentRound++;

  resultScreen.style.display = "none";
  mapContainer.classList.remove("resultMapContainer");
  gameInfo.classList.remove("resultGameInfo");
  document.getElementById("roundInfo").innerHTML = `Round: ${currentRound + 1}/5`;
  guessBtn.style.display = "block";
  location.style.display = "none";
  pin.style.display = "none";
  mapDiv.style.top = "0";
  mapDiv.style.left = "0";
  mapDiv.style.transform = `translateX(${mapTranslateX}px) translateY(${mapTranslateY}px)`;
  map.transformData = [mapTranslateX + additionalXTransform, mapTranslateY + additionalYTransform];
  idLine.setAttribute("x1", "0");
  idLine.setAttribute("y1", "0");
  idLine.setAttribute("x2", "0");
  idLine.setAttribute("y2", "0");
  adaptSize();
  map.drawLabels(false);
  map.drawStreetLabels(false);
  map.adaptBorders();

  viewer = pannellum.viewer('panorama', {
            "type": "cubemap",
            "autoLoad": true,
            "yaw": 180,
            "showControls": false,
            "compass": true,
            "northOffset": 0,
            "cubeMap": [
                `./panoramas/${username}/${locations[currentRound][1]}panorama_0.png`,
                `./panoramas/${username}/${locations[currentRound][1]}panorama_1.png`,
                `./panoramas/${username}/${locations[currentRound][1]}panorama_2.png`,
                `./panoramas/${username}/${locations[currentRound][1]}panorama_3.png`,
                `./panoramas/${username}/${locations[currentRound][1]}panorama_4.png`,
                `./panoramas/${username}/${locations[currentRound][1]}panorama_5.png`,
            ]
        })
}

const spaceEvent = (e) => {
  if(e.code == "Space") btnAction();
  if(e.code == "KeyN") align();
}

const btnAction = () => {
  if(locScreen && pinIsDown){
      guess();
    }else if(currentRound >= 4 && locScreen == false){
      localStorage.setItem("timePlayed", timePlayed / 10);
      localStorage.setItem("totalP", totalPoints);
      localStorage.setItem("roundData", JSON.stringify(roundData));
      window.location.href = "./summary.php";
    }else if(!locScreen && currentRound < 4){
      nextRound();
    }
}

const align = () => {
  viewer.setYaw(180);
  viewer.setNorthOffset(0);
}

//adapt map zoom
const adaptSize = () => {
    let denumerator = expansion[3] * 1.1 > (512 * 10 - 661) ? (512 * 10 - 661) : expansion[3] * 1.1;
    map.minScale = window.innerHeight * 0.8 / (512 * 10 - 661);
    let size = (window.innerHeight * 0.8 / denumerator) > map.maxScale ?map. maxScale : (window.innerHeight * 0.8 / denumerator);
    mapDiv.style.scale = size;
    map.currentZoomLevel = size;
}

const setPin = e => {
  if(locScreen){
    let x = map.getMousePos(e).x;
    let y = map.getMousePos(e).y;

    let pin = document.getElementById("pin");

    pin.style.scale = 1 / map.currentZoomLevel;
    pin.style.left = `${x}px`;
    pin.style.top = `${y}px`;
    pin.style.display = "block";

    pinIsDown = true;
  }
}

//guess container animations

guessContainer.addEventListener("mouseover", () => {
  guessContainer.style.width = "80vh";
  guessContainer.style.height = "calc(80vh + 45px)";

  mapContainer.style.width = "80vh";
  mapContainer.style.height = "80vh";

  guessBtn.style.width = "80vh";
})

guessContainer.addEventListener("mouseleave", () => {
  guessContainer.style.width = "300px";
  guessContainer.style.height = "345px";

  mapContainer.style.width = "300px";
  mapContainer.style.height = "300px";

  guessBtn.style.width = "300px";
})
