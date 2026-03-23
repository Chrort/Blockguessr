import {mouseDown, checkBrightness} from '../../map/map_functions.js';

let username = document.getElementById("usernameMeta").content;

const guessContainer = document.getElementById("guessContainer");
const mapContainer = document.getElementById("mapContainer");
const guessBtn = document.getElementById("guess");
const mapDiv = document.getElementById("map");
const resultScreen = document.getElementById("resultScreen");
const gameInfo = document.getElementById("gameInfo");
const location = document.getElementById("location");
const idLine = document.getElementById("idLine");

let transformData = [0, 0];
let currentStyles = window.getComputedStyle(mapDiv);
let currentZoomLevel = +currentStyles.getPropertyValue("scale");

let labelDataArray = [];
let streetDataArray = [];
let locations = [];

let pinIsDown = false;
let movedMap = false;
let locScreen = true;
let currentRound = 0;
let minScale;
let maxScale = 30;

let expansion;
let roundData = Array(5);
let totalPoints = 0;

const cB4 = window.getComputedStyle(document.getElementById("cB4"));
let additionalXTransform = +cB4.getPropertyValue("width").slice(0, cB4.getPropertyValue("width").length - 2);

const cB1 = window.getComputedStyle(document.getElementById("cB1"));
let additionalYTransform = +cB1.getPropertyValue("height").slice(0, cB1.getPropertyValue("height").length - 2);

const fetchData = async () => {
    try{
        const response = await fetch('../../api/location_data.php');
        const data = await response.json();

        let randomYaw = Math.random() < .5 ? -1 : 1;

        pannellum.viewer('panorama', {
            "type": "cubemap",
            "autoLoad": true,
            "yaw": 90 * randomYaw,
            "showControls": false,
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
    fetch('../../api/label_data.php')
        .then(response => response.json())
        .then(data => {
          for(let i = 0; i < data.length; i++){
            labelDataArray.push(data[i]);
          }
          drawLabels(true);
        })
        .catch(error => console.error('Error while loading data: ', error))
    
      fetch('../../api/street_data.php')
        .then(respones => respones.json())
        .then(data => {
          for(let j = 0; j < data.length; j++){
            streetDataArray.push(data[j]);
          }
          drawStreetLabels(true);
        })
        .catch(error => console.error('Error while loading data: ', error))

        fetch('../../api/get_map_expansion.php')
        .then(response => response.json())
        .then(data => {
          expansion = data;
        })
        .catch(error => console.error('Error while loading data: ', error))
}

window.onload = () => {
    fetchData();
    genMap();
    adaptSize();
    mapDiv.addEventListener("mousedown", mouseDown);
    mapDiv.addEventListener("click", setPin)
    mapDiv.addEventListener("wheel", mouseScroll, {passive: false});
    document.addEventListener("keydown", spaceEvent);
    guessBtn.addEventListener("click", () => {
      if(pinIsDown) guess();
    });
}

const guess = () => {
  locScreen = false;

  let d = distance();
  let p = calculateScore(d[0]);

  resultScreen.style.display = "block";
  mapContainer.classList.add("resultMapContainer");
  gameInfo.classList.add("resultGameInfo");
  guessBtn.style.display = "none";

  location.style.top = `${locations[currentRound][3] + 512 * 6}px`;
  location.style.left = `${locations[currentRound][2] + 512 * 6}px`;
  location.style.scale = 1 / currentZoomLevel;
  location.style.display = "flex";

  let size = calcSize(d[1], d[2], d[3], d[4]);

  transformData = [0, 0];
  mapDiv.style.scale = size;
  currentZoomLevel = size;

  let pTransformData = calcTransform(parseInt(d[1]), parseInt(d[2]), parseInt(d[3]), parseInt(d[4]));

  mapDiv.style.top = `${pTransformData[1]}px`;
  mapDiv.style.left = `${pTransformData[0]}px`;
  mapDiv.style.transform = `translateX(-${additionalXTransform}px) translateY(-${additionalYTransform}px)`;

  drawLabels(false);
  drawStreetLabels(false);
  adaptBorders();
  adaptIdLine();
  adjustPins();

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

  let locX = locations[currentRound][2] + 512 * 6;
  let locY = locations[currentRound][3] + 512 * 6;

  let d = Math.sqrt((pinX - locX)**2 + (pinY - locY)**2);

  return [d, pinX.toString(), pinY.toString(), locX.toString(), locY.toString()];
}

const calculateScore = d => {
  let radius = 5 * (1 + 0.0001 * expansion);
  console.log(radius)
  if(d < radius) return 5000;
  let p = 5000 * Math.E**(-10 * d / (expansion * 5));
  return Math.round(p);
}

const calcSize = (x1, y1, x2, y2) => {
  let w; //max expansion

  Math.abs(x1 - x2) > Math.abs(y1 - y2) ? w = Math.abs(x1 - x2) : w = Math.abs(y1 - y2);

  let p = w / (window.innerHeight * 0.85);

  if(p**-1 * 0.75 > maxScale) return maxScale;

  return p**-1 * 0.75;
}

const calcTransform = (x1, y1, x2, y2) => {
  let x = Math.abs((x1 + x2) / 2);
  let y = Math.abs((y1 + y2) / 2);
  return [(x * -1 + additionalXTransform) * currentZoomLevel + .5 * .85 * window.innerHeight, (y * -1 + additionalYTransform) * currentZoomLevel + .5 * .85 * window.innerHeight];
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
  mapDiv.style.transform = `translateX(-${additionalXTransform}px) translateY(-${additionalYTransform}px)`;
  transformData = [0, 0];
  idLine.setAttribute("x1", "0");
  idLine.setAttribute("y1", "0");
  idLine.setAttribute("x2", "0");
  idLine.setAttribute("y2", "0");
  adaptSize();

  let randomYaw = Math.random() < .5 ? -1 : 1;

  pannellum.viewer('panorama', {
            "type": "cubemap",
            "autoLoad": true,
            "yaw": 90 * randomYaw,
            "showControls": false,
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
}

const btnAction = () => {
  if(locScreen && pinIsDown){
      guess();
    }else if(currentRound >= 4 && locScreen == false){
      localStorage.setItem("totalP", totalPoints);
      localStorage.setItem("roundData", JSON.stringify(roundData));
      window.location.href = "./summary.php";
    }else if(!locScreen && currentRound < 4){
      nextRound();
    }
}

const genMap = type => {

  type == undefined ? type = "maps" : "maps";

  for(let i = -6; i < 4; i++){
    for(let j = -6; j < 4; j++){
      let mapCanvasE = document.createElement("div");
      mapCanvasE.id = `${j},${i}`;
      mapCanvasE.className = "mapCanvas";
      mapDiv.appendChild(mapCanvasE);
      const imgSrc = `../../img/${type}/${j},${i}.png`
      const mapImg = document.createElement("img");
      mapImg.src = imgSrc;
      mapImg.onload = () => {
          mapImg.className = "mapImgs"
          try{
            document.getElementById(`${j},${i}`).removeChild(document.getElementById(`${j},${i}`).firstChild);
          }
          catch{
            console.log("First load");
          }
          document.getElementById(`${j},${i}`).appendChild(mapImg);
      };
    }
  }
}

// Zoom ---------------------------------------------------------------------------

const mouseScroll = e => {
  let zoomStep = 1.4;
  let y = e.deltaY;

  //mouse pos before scale
  let mouseX = getMousePos(e, currentZoomLevel).x;
  let mouseY = getMousePos(e, currentZoomLevel).y;

  //check if scrolled up/down & max/min zoom level 
  y > 0 ? currentZoomLevel /= zoomStep : currentZoomLevel *= zoomStep;
  currentZoomLevel < minScale ? currentZoomLevel = minScale : currentZoomLevel > maxScale ? currentZoomLevel = maxScale : null;
  currentZoomLevel = Math.round(currentZoomLevel * 100) / 100;
  mapDiv.style.scale = currentZoomLevel;

  drawLabels(false);
  drawStreetLabels(false);
  adaptBorders();
  adaptIdLine();
  adjustPins();

  //mouse pos after scale
  let mouseX2 = getMousePos(e, currentZoomLevel).x;
  let mouseY2 = getMousePos(e, currentZoomLevel).y;

  //transforms canvas based on mouse positions
  mapDiv.style.transform = `translateX(${(mouseX2 - mouseX + transformData[transformData.length - 2] - additionalXTransform)}px) translateY(${(mouseY2 - mouseY + transformData[transformData.length - 1] - additionalYTransform)}px)`;

  //arrays stores current scale data ([x, y])
  transformData.push(mouseX2 - mouseX + transformData[transformData.length - 2], mouseY2 - mouseY + transformData[transformData.length - 1]);
  transformData.splice(0, 2);
}

//street labels
const drawStreetLabels = create => {

  //distance between labels
  let frequency = 150;

  for(let i = 0; i < streetDataArray.length; i++){
    if(create){
      for(let j = 0; j < streetDataArray[i][3].split(" ").length; j+=frequency){
        let streetLabelDiv = document.createElement("div");
        streetLabelDiv.classList.add(`${streetDataArray[i][1]}_label`, 'streetLabel');
        streetLabelDiv.id = `${streetDataArray[i][1]}_${streetDataArray[i][0]}_label_${j}`;  
        streetLabelDiv.innerHTML = streetDataArray[i][1];
        streetLabelDiv.style.backgroundColor = streetDataArray[i][2];
        streetLabelDiv.style.color = checkBrightness(streetDataArray[i][2]);

        let coords = streetDataArray[i][3].split(" ");
        let xCoord;
        let yCoord;

        //checks for too short roads
        if(streetDataArray[i][3].split(" ").length > frequency){
          try{
            xCoord = +coords[j + Math.round(frequency / 2)].split(",")[0];
            yCoord = +coords[j + Math.round(frequency / 2)].split(",")[1];
          } catch{
            null;
          }
        }else{
          xCoord = +coords[Math.round(streetDataArray[i][3].split(" ").length / 2)].split(",")[0];
          yCoord = +coords[Math.round(streetDataArray[i][3].split(" ").length / 2)].split(",")[1];
        }
        streetLabelDiv.style.top = `${yCoord + 512 * 6}px`;
        streetLabelDiv.style.left = `${xCoord + 512 * 6}px`;

        document.getElementById("streetLabelDivContainer").appendChild(streetLabelDiv);
      }
    }

    for(let k = 0; k < streetDataArray[i][3].split(" ").length; k+=frequency){
      let streetLabelDiv = document.getElementById(`${streetDataArray[i][1]}_${streetDataArray[i][0]}_label_${k}`);

      currentZoomLevel < 1.5 ? streetLabelDiv.style.display = "none" : streetLabelDiv.style.display = "flex";

      streetLabelDiv.style.fontSize = `${14 / currentZoomLevel**.7}px`;
      streetLabelDiv.style.padding = `0 ${2 / currentZoomLevel**.7}px`;
    }
  }
}

const drawLabels = create => {
    for(var i = 0; i < labelDataArray.length; i++){

      //creates div only once
      if(create){
        let labelDiv = document.createElement("div");
        labelDiv.className = "labelDiv";
        labelDiv.id = `mapLabel_${+labelDataArray[i][0]}`;
        mapDiv.appendChild(labelDiv);
      }

      //positions divs and sets font-size
      let labelDiv = document.getElementById(`mapLabel_${+labelDataArray[i][0]}`);
      labelDiv.style.left = `${+labelDataArray[i][2] + 512 * 6}px`;
      labelDiv.style.top = `${+labelDataArray[i][3] + 512 * 6}px`;
      labelDiv.style.fontSize = `${20 / currentZoomLevel**.7}px`;

      //styles/displays different types differently
      switch(labelDataArray[i][4]){
        case "province":
          if(currentZoomLevel <= .35){
            labelDiv.style.color = "black";
            labelDiv.style.fontWeight = "bolder";
            labelDiv.style.fontSize = `${25 / currentZoomLevel**.7}px`;
            labelDiv.innerHTML = `${labelDataArray[i][1].toUpperCase()}`;
            labelDiv.style.translate = "-50% 0"
          }else{
            labelDiv.innerHTML = ``;
          }
          break;
        case "town":
          if(currentZoomLevel > .2){
            labelDiv.style.color = "black";
            labelDiv.innerHTML = `${labelDataArray[i][1]}`;
            labelDiv.style.translate = "-50% 0"
          }else{
            labelDiv.innerHTML = ``;
          }
          break;
        case "waters":
          if(currentZoomLevel > .3){
            labelDiv.style.color = "blue";
            labelDiv.innerHTML = `${labelDataArray[i][1]}`;
            labelDiv.style.translate = "-50% 0"
          }
          else{
            labelDiv.innerHTML = ``;
          }
          break;
        case "landscape":
          if(currentZoomLevel > .4){
            labelDiv.style.color = "green";
            labelDiv.innerHTML = `${labelDataArray[i][1]}`;
            labelDiv.style.translate = "-50% 0"
          }
          else{
            labelDiv.innerHTML = ``;
          }
          break;
        case "point":
          if(currentZoomLevel > .6){
            labelDiv.style.color = "black";
            labelDiv.innerHTML = `▪${labelDataArray[i][1]}`;
          }
          else{
            labelDiv.innerHTML = ``;
          }
          break;
        default:
      }
      //adjust postion
      labelDiv.style.transform = `translateY(-${parseFloat(window.getComputedStyle(labelDiv).getPropertyValue('font-size')) / 2}px)`;
  }
  currentZoomLevel < maxScale ? checkCollision() : null;
}

const checkCollision = () => {
  for(let i = 0; i < labelDataArray.length; i++){
    let div = document.getElementById(`mapLabel_${+labelDataArray[i][0]}`);
    let size = div.getBoundingClientRect();

    for(let j = i + 1; j < labelDataArray.length; j++){
      if(j == i) continue;
      let compareDiv = document.getElementById(`mapLabel_${+labelDataArray[j][0]}`);
      let compareSize = compareDiv.getBoundingClientRect();
      
      if(size.top < compareSize.bottom && size.bottom > compareSize.top && size.left < compareSize.right && size.right > compareSize.left){
        deleteDecider(i, j, div, compareDiv).innerHTML = ``;
      }
    }
  }
}
const deleteDecider = (i, j, div, compareDiv) => {
  switch(labelDataArray[i][4]){
    case "province":
      return compareDiv;
    case "town":
      if(labelDataArray[j][4] == "province"){
        return div;
      }else{
        return compareDiv;
      }
    case "waters":
      if(labelDataArray[j][4] == "province" || labelDataArray[j][4] == "town"){
        return div;
      }
      else{
        return compareDiv;
      }
    case "landscape":
      if(labelDataArray[j][4] == "province" || labelDataArray[j][4] == "town" || labelDataArray[j][4] == "waters"){
        return div;
      }
      else{
        return compareDiv;
      }
    case "point":
      return div;
    default:
      return div;
  }
}

//adapt borders
const adaptBorders = () => {
  const borders = document.getElementsByClassName("borderPolyline");
  [].forEach.call(borders, (e) => {
    e.style.strokeWidth = 4 / currentZoomLevel**.4;
  })
}

//adapt map zoom

const adaptSize = () => {
    let size = window.innerHeight * 0.8 / (512 * 10 - 661);
    minScale = size;
    mapDiv.style.scale = size;
    currentZoomLevel = size;
    drawLabels();
}

const setPin = e => {
  if(movedMap) return;
  if(locScreen){
    let x = getMousePos(e, currentZoomLevel).x;
    let y = getMousePos(e, currentZoomLevel).y;

    let pin = document.getElementById("pin");

    pin.style.scale = 1 / currentZoomLevel;
    pin.style.left = `${x}px`;
    pin.style.top = `${y}px`;
    pin.style.display = "block";

    pinIsDown = true;
  }
}

const adjustPins = () => {
  let pin = document.getElementById("pin");
  pin.style.scale = 1 / currentZoomLevel;
  location.style.scale = 1 / currentZoomLevel;
}

const getMousePos = (e, currentZoomLevel) => {
  let rect = mapDiv.getBoundingClientRect();
  let x = (e.clientX - rect.left - mapDiv.clientLeft) / currentZoomLevel;
  let y = (e.clientY - rect.top - mapDiv.clientTop) / currentZoomLevel;
  return {x, y};
}

const adaptIdLine = () => {
  idLine.style.strokeWidth = 5 / currentZoomLevel**.6;
  idLine.style.strokeDasharray = `${10 / currentZoomLevel} ${5 / currentZoomLevel}`;
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
