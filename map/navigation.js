// import { map } from './map.js';
import aStar from "./A-Star.js";
import data from "./streets.json" with  { type: "json"}
// const data = await fetch("./streets.json").then(res => res.json());
// const navigationBar = document.querySelector("#navigationBar");
// const navigateBtn = document.querySelector("#navigateBtn");
// const startInput = document.querySelector("#start");
// const destinationInput = document.querySelector("#destination");
// const errorLog = document.querySelector("#errorLog");
//
// navigationToggleBtn.addEventListener("click", () => {
//   navigationBar.classList.toggle("hideNavbar");
// })
//
// document.addEventListener("keydown", e => {
//   if (e.key == "n" && document.activeElement !== startInput && document.activeElement !== destinationInput) navigationBar.classList.toggle("hideNavbar");
// });
//
// const isValidInput = input => {
//   for (let i = 0; i < map.labelDataArray.length; i++) if (input == map.labelDataArray[i][1]) return true;
//   if (input.match(/^-?\d+,-?\d+$/)) return true;
//   return false;
// }
//
// const convertToCoordsPair = input => {
//   if (input.match(/^-?\d+,-?\d+$/)) return input;
//   for (let i = 0; i < map.labelDataArray.length; i++) if (input == map.labelDataArray[i][1]) return `${map.labelDataArray[i][2]},${map.labelDataArray[i][3]}`;
// }
//
// navigateBtn.addEventListener("click", async () => {
//   if (!isValidInput(startInput.value) || !isValidInput(destinationInput.value)) {
//     errorLog.style.display = "flex";
//     errorLog.innerHTML = "Invalid Input"
//     setTimeout(() => {
//       errorLog.style.display = "none";
//     }, 5000);
//     return;
//   };
//   errorLog.style.display = "none";
//
//   let startCoords = convertToCoordsPair(startInput.value);
//   let destinationCoords = convertToCoordsPair(destinationInput.value);
//
//   startInput.value = "";
//   destinationInput.value = "";

const path = aStar("-2429,-2653", "1114,887", data);
// for (let i = 0; i < path.intersections.length; i++) {
//   console.log(`${path.intersections[i].streets[0]}, ${path.intersections[i].streets[1]} | ${path.intersections[i].nodes[0].x},${path.intersections[i].nodes[0].y}  ${path.intersections[i].nodes[1].x},${path.intersections[i].nodes[1].y}`);
// }
console.log(path);
// });
