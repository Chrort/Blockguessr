import {getMousePos, updateCoords, copyCoords, currentZoomLevel, mapDiv} from "./map.js";

//measure area
const canvas = document.getElementById("measureCanvas");
const ctx = canvas.getContext("2d");
const result = document.getElementById("result");
const escapeInfo = document.getElementById("escape");
const measureArea = document.getElementById("measureArea");
const measureDistance = document.getElementById("measureDistance");

export let points = [];
export let mode = "";

const keydownHandler = e => {
  if (e.key === 'Escape'){
    leaveMode();
    window.addEventListener('keydown', keydownHandler)
  }
}

export const setupMeasure = () => {
    measureArea.addEventListener("click", () => {
        setPoints("area");
    });
    window.addEventListener('keydown', keydownHandler)
    //measure Distance
    measureDistance.addEventListener("click", () => {
        setPoints("distance");
    });
}

export const leaveMode = () => {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  points = [];
  escapeInfo.style.display = "none";
  result.style.display = "none";
  copyCoords.style.display = "none";
  canvas.style.backgroundColor = "transparent";
  mapDiv.removeEventListener("click", pushPoint);
  window.removeEventListener('keydown', keydownHandler)
}

export const setPoints = m => {
  mode = m;
  points = [];
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  copyCoords.style.display = "none";
  escapeInfo.style.display = "block";
  result.style.display = "block";
  canvas.style.backgroundColor = "rgba(255, 255, 255, .2)";

  if(mode == "area"){
    result.innerHTML = `Surface Area: 0m²`;
  }else if(mode == "distance"){
    result.innerHTML = `Total Distance: 0m`;
    copyCoords.style.display = "block";
  }

  mapDiv.removeEventListener("click", pushPoint);
  mapDiv.addEventListener("click", pushPoint);

  window.removeEventListener("keydown", removePoint)
  window.addEventListener("keydown", removePoint);
}

export const removePoint = e => {
  if(e.key === "Backspace" || e.key === "Delete"){
    points.pop();
    drawPoints();
  }
}

export const pushPoint = e => {
  let x = Math.round(getMousePos(e, currentZoomLevel).x);
  let y = Math.round(getMousePos(e, currentZoomLevel).y);
  points.push({x, y});
  drawPoints();
}

export const drawPoints = () => {
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  for(let i = 0; i < points.length; i++){
    ctx.beginPath();
    ctx.fillStyle = "red";
    ctx.fillRect(points[i].x - (8 / currentZoomLevel**.7) * .5, points[i].y - (8 / currentZoomLevel**.7) * .5, 8 / currentZoomLevel**.7, 8 / currentZoomLevel**.7)
    ctx.stroke();
  }
  connectPoints();
}

export const connectPoints = () => {
  ctx.lineWidth = 3 / currentZoomLevel**.7 + .5;
  ctx.strokeStyle = "red";
  switch(mode){
    case "area":
      for(let i = 0; i < points.length; i++){
          ctx.beginPath();
          ctx.moveTo(points[i].x, points[i].y);
          i == points.length - 1 ? i = -1 : null;
          ctx.lineTo(points[i + 1].x, points[i + 1].y);
          ctx.stroke();
          if(i == - 1) break;
        }
        calculateArea();
      break;
    case "distance":
      for(let i = 0; i < points.length - 1; i++){
          ctx.beginPath();
          ctx.moveTo(points[i].x, points[i].y);
          i == points.length - 1 ? i = -1 : null;
          ctx.lineTo(points[i + 1].x, points[i + 1].y);
          ctx.stroke();
          if(i == -1) break;
        }
        calculateDistance();
        updateCoords();
      break;
  }
}

export const calculateArea = () => {
  let pointsCopy = [];
  pointsCopy = points.slice();
  pointsCopy.push(points[0]);

  let firstSum = 0;
  let secondSum = 0;
  for(let i = 0; i < points.length; i++){
    firstSum += pointsCopy[i].x * -pointsCopy[i + 1].y;
    secondSum += -pointsCopy[i].y * pointsCopy[i + 1].x;
  }
  let area = .5 * Math.abs(firstSum - secondSum);
  result.innerHTML = `Surface Area: ${area}m²`
}

export const calculateDistance = () => {
  let sum = 0;
  for(let i = 0; i < points.length - 1; i++){
    let cat1 = Math.abs(points[i].x - points[i + 1].x);
    let cat2 = Math.abs(points[i].y - points[i + 1].y);
    let hyp = Math.sqrt(cat1*cat1 + cat2*cat2);
    sum += hyp;
  }
  result.innerHTML = `Total Distance: ${Math.round(sum)}m`;
} 