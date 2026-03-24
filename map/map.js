import { setupListener } from "./settings.js";
import { setupMeasure, points, mode, drawPoints } from "./measure.js";
import { mouseDown, checkBrightness } from "./map_functions.js";

export const mapDiv = document.getElementById("map");
export const copyCoords = document.getElementById("copyCoords");

let transformData = [0, 0];

let currentStyles = window.getComputedStyle(mapDiv);
export let currentZoomLevel = +currentStyles.getPropertyValue("scale");

let labelDataArray = [];
let streetDataArray = [];

// map div ---------------------------------------------------------------------------

const genMap = type => {

  type == undefined ? type = "maps" : "maps";

  for(let i = -6; i < 4; i++){
    for(let j = -6; j < 4; j++){
      let mapCanvasE = document.createElement("div");
      mapCanvasE.id = `${j},${i}`;
      mapCanvasE.className = "mapCanvas";
      mapDiv.appendChild(mapCanvasE);
      const imgSrc = `../img/${type}/${j},${i}.png`
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

// ------------------------------------------------------------------------------------------
window.onload = async () => {
  await fetchData();
  drawLabels(true);
  genMap();
  setupListener();
  setupMeasure();
  adaptPanoLinks();
  adaptStreets();
  adaptPolygonsText();
  mapDiv.addEventListener("mousedown", mouseDown);
  mapDiv.addEventListener("wheel", mouseScroll, {passive: false});

  document.getElementById("maps").addEventListener("click", () => {genMap("maps");});
  document.getElementById("nightMaps").addEventListener("click", () => {genMap("nightMaps")});
  document.getElementById("terrainMaps").addEventListener("click", () => {genMap("terrainMaps")});
  document.getElementById("biomeMaps").addEventListener("click", () => {genMap("biomeMaps")});
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
  currentZoomLevel < 0.17 ? currentZoomLevel = .17 : currentZoomLevel > 10 ? currentZoomLevel = 10 : null;
  currentZoomLevel = Math.round(currentZoomLevel * 100) / 100;
  mapDiv.style.scale = currentZoomLevel;

  drawLabels(false);
  drawStreetLabels(false);
  adaptBorders();
  adaptStreets();
  adaptPanoLinks();
  adaptPolygonsText();

  //mouse pos after scale
  let mouseX2 = getMousePos(e, currentZoomLevel).x;
  let mouseY2 = getMousePos(e, currentZoomLevel).y;

  //transforms canvas based on mouse positions
  mapDiv.style.transform = `translateX(${(mouseX2 - mouseX + transformData[transformData.length - 2])}px) translateY(${(mouseY2 - mouseY + transformData[transformData.length - 1])}px)`;

  //arrays stores current scale data ([x, y])
  transformData.push(mouseX2 - mouseX + transformData[transformData.length - 2], mouseY2 - mouseY + transformData[transformData.length - 1]);
  transformData.splice(0, 2);

  drawPoints(mode);
}

export const getMousePos = (e, currentZoomLevel) => {
  let rect = mapDiv.getBoundingClientRect();
  let x = (e.clientX - rect.left - mapDiv.clientLeft) / currentZoomLevel;
  let y = (e.clientY - rect.top - mapDiv.clientTop) / currentZoomLevel;
  return {x, y};
}

const fetchData = async () => {
  await fetch('../api/label_data.php')
    .then(response => response.json())
    .then(data => {
      for(let i = 0; i < data.length; i++){
        labelDataArray.push(data[i]);
      }
    })
    .catch(error => console.error('Error while loading data: ', error));

  await fetch('../api/polygon_data.php')
    .then(response => response.json())
    .then(data => {
      for(let i = 0; i < data.length; i++){

        let polygonCoordsArray = data[i][2].split(" ");

        for(let k = 0; k < polygonCoordsArray.length; k++){
          polygonCoordsArray[k] = polygonCoordsArray[k].split(",");
        }
        let maxCoords = [+polygonCoordsArray[0][0], +polygonCoordsArray[0][1], +polygonCoordsArray[0][0], +polygonCoordsArray[0][1]];
        for(let j = 0; j < polygonCoordsArray.length; j++){
          if(+polygonCoordsArray[j][0] < maxCoords[0]) maxCoords[0] = +polygonCoordsArray[j][0];
          if(+polygonCoordsArray[j][1] < maxCoords[1]) maxCoords[1] = +polygonCoordsArray[j][1];
          if(+polygonCoordsArray[j][0] > maxCoords[2]) maxCoords[2] = +polygonCoordsArray[j][0];
          if(+polygonCoordsArray[j][1] > maxCoords[3]) maxCoords[3] = +polygonCoordsArray[j][1];
        }
        labelDataArray.push([data[i][0], data[i][1], `${(maxCoords[0] + 0.5 * (maxCoords[2] - maxCoords[0]))}`, `${(maxCoords[1] + 0.5 * (maxCoords[3] - maxCoords[1]))}`, data[i][3]]);
      }
    })
    .catch(error => console.error('Error while loading data: ', error));

  await fetch('../api/street_data.php')
    .then(respones => respones.json())
    .then(data => {
      for(let j = 0; j < data.length; j++){
        streetDataArray.push(data[j]);
      }
      drawStreetLabels(true);
      coordsArray();
    })
    .catch(error => console.error('Error while loading data: ', error));
}

export const drawLabels = create => {
    for(var i = 0; i < labelDataArray.length; i++){

      //creates div only once
      if(create){
        let labelDiv = document.createElement("div");
        labelDiv.className = "labelDiv";
        labelDiv.id = `mapLabel_${+labelDataArray[i][0]}_${labelDataArray[i][4]}`;
        mapDiv.appendChild(labelDiv);
      }

      //positions divs and sets font-size
      let labelDiv = document.getElementById(`mapLabel_${+labelDataArray[i][0]}_${labelDataArray[i][4]}`);
      labelDiv.style.left = `${+labelDataArray[i][2] + 512 * 6}px`;
      labelDiv.style.top = `${+labelDataArray[i][3] + 512 * 6}px`;
      labelDiv.style.fontSize = `${20 / currentZoomLevel**.7}px`;

      //styles/displays different types differently
      switch(labelDataArray[i][4]){
        case "province":
          if(document.getElementById("province").checked && currentZoomLevel <= .35){
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
          if(document.getElementById("town").checked && currentZoomLevel > .2){
            labelDiv.style.color = "black";
            labelDiv.innerHTML = `${labelDataArray[i][1]}`;
            labelDiv.style.translate = "-50% 0"
          }else{
            labelDiv.innerHTML = ``;
          }
          break;
        case "waters":
          if(document.getElementById("waters").checked && currentZoomLevel > .3){
            labelDiv.style.color = "blue";
            labelDiv.innerHTML = `${labelDataArray[i][1]}`;
            labelDiv.style.translate = "-50% 0"
          }
          else{
            labelDiv.innerHTML = ``;
          }
          break;
        case "landscape":
          if(document.getElementById("landscape").checked && currentZoomLevel > .4){
            labelDiv.style.color = "green";
            labelDiv.innerHTML = `${labelDataArray[i][1]}`;
            labelDiv.style.translate = "-50% 0"
          }
          else{
            labelDiv.innerHTML = ``;
          }
          break;
        case "point":
          if(document.getElementById("point").checked && currentZoomLevel > .6){
            labelDiv.style.color = "black";
            labelDiv.innerHTML = `▪${labelDataArray[i][1]}`;
          }
          else{
            labelDiv.innerHTML = ``;
          }
          break;
        case "np":
          if(document.getElementById("nationalParks").checked && currentZoomLevel > .2){
            labelDiv.style.color = "#CBBA9F";
            labelDiv.innerHTML = `${labelDataArray[i][1]}`;
            labelDiv.style.translate = "-50% 0"
            labelDiv.innerHTML = `${labelDataArray[i][1].toUpperCase()}`;
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
  currentZoomLevel < 10 ? checkCollision() : null;
}

//check collision

const checkCollision = () => {
  for(let i = 0; i < labelDataArray.length; i++){
    let div = document.getElementById(`mapLabel_${+labelDataArray[i][0]}_${labelDataArray[i][4]}`);
    let size = div.getBoundingClientRect();

    for(let j = i + 1; j < labelDataArray.length; j++){
      if(j == i) continue;
      let compareDiv = document.getElementById(`mapLabel_${+labelDataArray[j][0]}_${labelDataArray[j][4]}`);
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

//street labels

export const drawStreetLabels = create => {

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

      currentZoomLevel < 1.4 || !document.getElementById("street").checked ? streetLabelDiv.style.display = "none" : streetLabelDiv.style.display = "flex";

      streetLabelDiv.style.fontSize = `${14 / currentZoomLevel**.7}px`;
      streetLabelDiv.style.padding = `0 ${2 / currentZoomLevel**.7}px`;
    }
  }
}

//hide borders

document.getElementById("border").addEventListener("change", () => {
  const borders = document.getElementsByClassName("borderPolyline");
    if(!document.getElementById("border").checked){
      [].forEach.call(borders, (e) => {
        e.style.visibility = "hidden";
      })
    }else{
      [].forEach.call(borders, (e) => {
        e.style.visibility = "visible";
      })
    }
});

//adapt borders in size

const adaptBorders = () => {
  const borders = document.getElementsByClassName("borderPolyline");
  [].forEach.call(borders, (e) => {
    e.style.strokeWidth = 4 / currentZoomLevel**.6;
  })
}

//adapt pano links in size

const adaptPanoLinks = () => {
  if(document.getElementById("panorama").checked){
    const panos = document.getElementsByClassName("panoLink");
    [].forEach.call(panos, (e) => {
      e.style.transform = `translate(-5px, -5px) scale(${1 / currentZoomLevel ** .5})`;
    })
  }
}

//hide streets

document.getElementById("streetLine").addEventListener("change", () => {
  const streetLines = document.getElementsByClassName("streetPolyline");
    if(!document.getElementById("streetLine").checked){
      [].forEach.call(streetLines, (e) => {
        e.style.visibility = "hidden";
      })
    }else{
      [].forEach.call(streetLines, (e) => {
        e.style.visibility = "visible";
      })
    }
});

//hide np
document.getElementById("nationalParks").addEventListener("change", () => {
  const nationalParks = document.getElementsByClassName("np_polygons");
    if(!document.getElementById("nationalParks").checked){
      [].forEach.call(nationalParks, (e) => {
        e.style.visibility = "hidden";
      })
    }else{
      [].forEach.call(nationalParks, (e) => {
        e.style.visibility = "visible";
      })
    }
});

//adapt streets in size

const adaptStreets = () => {
  const streetLines = document.getElementsByClassName("streetPolyline");
  [].forEach.call(streetLines, (e) => {
    e.style.strokeWidth = 4 / currentZoomLevel**.6;
  })
}

const adaptPolygonsText = () => {
  const polygonsText = document.getElementsByClassName("polygonText");
  [].forEach.call(polygonsText, (e) => {
    e.style.fontSize = `${15 / currentZoomLevel ** .6}px`;
    e.style.transform = `translateX(-${e.getBoundingClientRect().width * 0.5 / currentZoomLevel}px)`;
  })
}

document.getElementById("panorama").addEventListener("change", () => {
  const panorama = document.getElementById("panorama");
  const panoramaDivContainer = document.getElementById("panoramaDivContainer");

  if(panorama.checked){
    panoramaDivContainer.style.display = "block";
    adaptPanoLinks();
  }else{
    panoramaDivContainer.style.display = "none";
  }
});

//hide map tiles

document.getElementById("mapTile").addEventListener("change", () => {
  const mapCanvas = document.getElementsByClassName("mapCanvas");

  if(!document.getElementById("mapTile").checked){
      [].forEach.call(mapCanvas, (e) => {
        e.style.visibility = "hidden";
      })
    }else{
      [].forEach.call(mapCanvas, (e) => {
        e.style.visibility = "visible";
      })
    }
})

//show ingame coords

const showCoords = e => {
  const rect = mapDiv.getBoundingClientRect();
  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;

  if (x >= 0 && y >= 0 && x <= rect.width && y <= rect.height) {
    document.getElementById("coords").innerHTML =
    document.getElementById("coords").innerHTML = `X: ${Math.round(getMousePos(e, currentZoomLevel).x) - 512 * 6} | Y: ${Math.round(getMousePos(e, currentZoomLevel).y)  - 512 * 6}`;
  }
}

mapDiv.addEventListener("mousemove", showCoords);

//copy coords

export const updateCoords = () => {
  let fullString = "";
  for(let i = 0; i < points.length; i++){
    let substringX = `${points[i].x - 6 * 512},`;
    let substringY = `${points[i].y - 6 * 512}`;
    fullString = fullString.concat(substringX, substringY, " ");
  }
  document.getElementById("coordsValue").value = fullString.trim();
}

copyCoords.onclick = () => {
  const input = document.getElementById("coordsValue");
  navigator.clipboard.writeText(input.value);
  alert("Copied the text: " + input.value);
}

const coordsArray = () => {
  const cA = document.getElementById("coordsArray");
  cA.style.display = "none";
  cA.textContent = streetDataArray;
}