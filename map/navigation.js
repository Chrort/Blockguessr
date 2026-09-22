<<<<<<< HEAD
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
=======
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

    console.log(startCoords, destinationCoords);

    let path = await aStar(startCoords, destinationCoords, data);
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

    let streetsOnRoute = "";

    for(let i = 0; i < path.streets.length; i++){
      if(+path.streets[i - 1] == +path.streets[i]) continue;
      let bgColor = document.querySelector(`.${CSS.escape(path.streets[i])}_label`).style.backgroundColor;
      let color = document.querySelector(`.${CSS.escape(path.streets[i])}_label`).style.color;
      console.log(color)
      streetsOnRoute += `<span style='background-color:${bgColor}; color:${color}'>${path.streets[i]}</span>`;
    }

    streetsOnRoute += "";

    document.querySelector("#distanceInfo").innerHTML = `${Math.round(path.distance)}m`;
    document.querySelector("#streetsInfo").innerHTML = streetsOnRoute;
    document.querySelector("#routeInfo>#sprintTimeInfo").innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" height="3vh" viewBox="0 -960 960 960" width="3vh" fill="#000000"><path d="m216-160-56-56 384-384H440v80h-80v-160h233q16 0 31 6t26 17l120 119q27 27 66 42t84 16v80q-62 0-112.5-19T718-476l-40-42-88 88 90 90-262 151-40-69 172-99-68-68-266 265Zm-96-280v-80h200v80H120ZM40-560v-80h200v80H40Zm739-80q-33 0-57-23.5T698-720q0-33 24-56.5t57-23.5q33 0 57 23.5t24 56.5q0 33-24 56.5T779-640Zm-659-40v-80h200v80H120Z" /></svg>${path.times.sprinting}`;
    document.querySelector("#routeInfo>#horseTimeInfo").innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" height="3vh" viewBox="0 -960 960 960" width="3vh" fill="#000000"><path d="M216-96v-171q0-21.47 11-38.73Q238-323 257-332l175-85v-63l-111 59q-11.29 6-23.53 9-12.23 3-24.47 3-28.12 0-53.06-15T181-466q-11-23-10.5-49t14.5-49l115-192-84-108h240q119.7 0 203.85 84Q744-696 744-576v480H216Zm72-72h384v-408q0-90-63-153t-153-63h-93l24 30-140 234q-4 7.27-4.5 15t3.5 15q5 9 12.48 13t14.52 4q4 0 14-4l217-115v228L288-267v99Zm144-312Z" /></svg>${path.times.horse}`;
    document.querySelector("#routeInfo>#walkTimeInfo").innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" height="3vh" viewBox="0 -960 960 960" width="3vh" fill="#000000"><path d="m298-96 93-476-79 34v106h-72v-154l185-78q10-4 19.5-6t18.5-2q26 0 46 11.5t34 31.5l9 13q23 35 51.5 73.5T720-504v72q-65 0-115.5-24T527-522l-21 114 70 70v242h-72v-215l-73-56-62 271h-71Zm170.5-624.5Q444-745 444-780t24.5-59.5Q493-864 528-864t59.5 24.5Q612-815 612-780t-24.5 59.5Q563-696 528-696t-59.5-24.5Z" /></svg>${path.times.walking}`;
    document.querySelector("#executionTimeInfo").innerHTML = `${Math.round(path.executionTime * 100) / 100}ms`;
});
>>>>>>> e8f6b9d8bd2a0fc1683856b29b65948d6da680ad
