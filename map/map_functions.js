const mapDiv = document.getElementById("map");

let startX = 0;
let startY = 0;
let newX = 0;
let newY = 0;

export const mouseDown = e => {
  startX = e.clientX;
  startY = e.clientY;
  
  document.addEventListener("mousemove", mouseMove);
  document.addEventListener("mouseup", mouseUp);
}

const mouseMove = e => {
  newX = startX - e.clientX;
  newY = startY - e.clientY;

  startX = e.clientX;
  startY = e.clientY;

  mapDiv.style.top = `${mapDiv.offsetTop - newY}px`;
  mapDiv.style.left = `${mapDiv.offsetLeft - newX}px`;
}

const mouseUp = () => {
  document.removeEventListener("mousemove", mouseMove);
}

//color decider
export const checkBrightness = hex => {
  hex = hex.replace("#", "");

  let r = parseInt(hex.substr(0, 2), 16);
  let g = parseInt(hex.substr(2, 2), 16);
  let b = parseInt(hex.substr(4, 2), 16);

  let brightness = (r * 299 + g * 587 + b * 144) / 1000;

  return (brightness < 128) ? 'white' : 'black';
}