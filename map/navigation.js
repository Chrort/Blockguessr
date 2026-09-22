import { map } from './map.js';
import aStar from "./A-Star.js";
const data = await fetch("./streets.json").then(res => res.json());
const navigationBar = document.querySelector("#navigationBar");
const navigateBtn = document.querySelector("#navigateBtn");
const navigationToggle = document.querySelector("#navigation");
const startInput = document.querySelector("#start");
const destinationInput = document.querySelector("#destination");
const errorLog = document.querySelector("#errorLog");
const deleteRoute = document.querySelector("#deleteRoute");
const routeInfo = document.querySelector("#routeInfo");

navigationToggle.addEventListener("click", () => {
  navigationBar.classList.toggle("hideNavbar");
})

document.addEventListener("keydown", e => {
  if (e.key == "n" && document.activeElement !== startInput && document.activeElement !== destinationInput) navigationBar.classList.toggle("hideNavbar");
});

const isValidInput = input => {
  for (let i = 0; i < map.labelDataArray.length; i++) if (input == map.labelDataArray[i][1]) return true;
  if (input.match(/^-?\d+,-?\d+$/)) return true;
  return false;
}

const convertToCoordsPair = input => {
  if (input.match(/^-?\d+,-?\d+$/)) return input;
  for (let i = 0; i < map.labelDataArray.length; i++) if (input == map.labelDataArray[i][1]) return `${map.labelDataArray[i][2]},${map.labelDataArray[i][3]}`;
}

deleteRoute.addEventListener("click", () => {
  startInput.value = "";
  destinationInput.value = "";
  document.querySelector("polyline.routes").remove();
  document.querySelector("line.routes").remove();
  document.querySelector("line.routes").remove();
  deleteRoute.style.display = "none";
  routeInfo.style.display = "none";
})

navigateBtn.addEventListener("click", async () => {
    if (!isValidInput(startInput.value) || !isValidInput(destinationInput.value)) {
        errorLog.style.display = "flex";
        errorLog.innerHTML = "Invalid Input"
        setTimeout(() => {
        errorLog.style.display = "none";
        }, 5000);
        return;
    };
    errorLog.style.display = "none";

    deleteRoute.style.display = "flex";
    routeInfo.style.display = "flex";

    let startCoords = convertToCoordsPair(startInput.value);
    let destinationCoords = convertToCoordsPair(destinationInput.value);

    let path = aStar(startCoords, destinationCoords, data);
    path.setHorseTime(0.34);

    console.log(path);
    
    path.coordsString = path.coordsString.replace(/\s+/g, " ");
    
    let coordsArray = path.coordsString.split(" ");
    for(let i = 0; i < coordsArray.length; i++){
      let x = (+coordsArray[i].split(",")[0]) + 6 * 512;
      let y = (+coordsArray[i].split(",")[1]) + 6 * 512;
      coordsArray[i] = `${x},${y}`;
    }

    let firstPair = coordsArray.shift();
    let lastPair = coordsArray.pop();
    path.coordsString = coordsArray.join(" ");

    const svgContainer = document.querySelector(".borderSvg");
    if(document.querySelector("polyline.routes")){
      document.querySelector("polyline.routes").remove();
      document.querySelector("line.routes").remove();
      document.querySelector("line.routes").remove();
    }
    const route = `<polyline points='${path.coordsString}' class='routes' stroke='purple' fill='none' stroke-linecap='round' stroke-linejoin='round'>`;
    const startRoute = `<line x1='${firstPair.split(",")[0]}' y1='${firstPair.split(",")[1]}' x2='${coordsArray[0].split(",")[0]}' y2='${coordsArray[0].split(",")[1]}' class='routes offroad' fill='none' stroke-linecap='round' stroke-dasharray='5,5' stroke='plum'>`;
    const endRoute = `<line x1='${lastPair.split(",")[0]}' y1='${lastPair.split(",")[1]}' x2='${coordsArray[coordsArray.length - 1].split(",")[0]}' y2='${coordsArray[coordsArray.length - 1].split(",")[1]}' class='routes offroad' fill='none' stroke-linecap='round' stroke-dasharray='5,5' stroke='plum'>`;
    svgContainer.insertAdjacentHTML("beforeend", startRoute);
    svgContainer.insertAdjacentHTML("beforeend", route);
    svgContainer.insertAdjacentHTML("beforeend", endRoute);

    map.adaptRoute();

    let streetsOnRoute = "Start →";

    for(let i = 0; i < path.streets.length; i++){
      if(+path.streets[i - 1] == +path.streets[i]) continue;
      streetsOnRoute += ` ${path.streets[i]} →`;
    }

    streetsOnRoute += " End"

    document.querySelector("#routeInfo>#distanceInfo").innerHTML = `Distance: ${Math.round(path.distance)}m`;
    document.querySelector("#routeInfo>#streetsInfo").innerHTML = streetsOnRoute;
    document.querySelector("#routeInfo>#travelTimeInfo").innerHTML = `Walk: ${path.times.walking} <br>Sprint: ${path.times.sprinting} <br>Horse: ${path.times.horse}`;
    document.querySelector("#routeInfo>#executionTimeInfo").innerHTML = `Took: ${Math.round(path.executionTime * 100) / 100}ms`;
});
