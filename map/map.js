import { setupListener } from "./settings.js";
import { setupMeasure, points, mode, drawPoints } from "./measure.js";
import { Map } from "./map_class.js";

export const mapDiv = document.getElementById("map");
export const copyCoords = document.getElementById("copyCoords");

export let map = new Map(mapDiv, [-6, 4, -6, 4]);

window.onload = async () => {
  map.genMap();
  await map.fetchData();
  map.drawLabels(true);
  map.adaptPanoLinks();
  map.adaptStreets();
  map.adaptPolygonsText();

  setupListener();
  setupMeasure();

  document.getElementById("maps").addEventListener("click", () => {map.genMap("maps");});
  document.getElementById("nightMaps").addEventListener("click", () => {map.genMap("nightMaps")});
  document.getElementById("terrainMaps").addEventListener("click", () => {map.genMap("terrainMaps")});
  document.getElementById("biomeMaps").addEventListener("click", () => {map.genMap("biomeMaps")});
}

const toggleVisibility = (className, checkBoxId) => {
  const elements = document.getElementsByClassName(className);
  if(!document.getElementById(checkBoxId).checked){
      [].forEach.call(elements, (e) => {
        e.style.display = "none";
      })
    }else{
      [].forEach.call(elements, (e) => {
        e.style.display = "block";
      })
    }
}

//hide borders
document.getElementById("border").addEventListener("change", () => {
  toggleVisibility("borderPolyline", "border");
});

//hide streets
document.getElementById("streetLine").addEventListener("change", () => {
  toggleVisibility("streetPolyline", "streetLine");
});

//hide np
document.getElementById("nationalParks").addEventListener("change", () => {
  toggleVisibility("np_polygons", "nationalParks");
  toggleVisibility("np", "nationalParks");
});

//hide map tiles
document.getElementById("mapTile").addEventListener("change", () => {
  toggleVisibility("mapCanvas", "mapTile");
})

//hide province
document.getElementById("province").addEventListener("change", () => {
  toggleVisibility("labelDiv_province", "province")
})

//hide town
document.getElementById("town").addEventListener("change", () => {
  toggleVisibility("labelDiv_town", "town")
})

//hide landscape
document.getElementById("landscape").addEventListener("change", () => {
  toggleVisibility("labelDiv_landscape", "landscape")
})

//hide waters
document.getElementById("waters").addEventListener("change", () => {
  toggleVisibility("labelDiv_waters", "waters")
})

//hide point
document.getElementById("point").addEventListener("change", () => {
  toggleVisibility("labelDiv_point", "point")
})

document.getElementById("panorama").addEventListener("change", () => {
  const panorama = document.getElementById("panorama");
  const panoramaDivContainer = document.getElementById("panoramaDivContainer");

  if(panorama.checked){
    panoramaDivContainer.style.display = "block";
    map.adaptPanoLinks();
  }else{
    panoramaDivContainer.style.display = "none";
  }
});

//show ingame coords
const showCoords = e => {
  const rect = mapDiv.getBoundingClientRect();
  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;

  if (x >= 0 && y >= 0 && x <= rect.width && y <= rect.height) {
    document.getElementById("coords").innerHTML = `X: ${Math.round(map.getMousePos(e).x) - map.xTransform} | Y: ${Math.round(map.getMousePos(e).y) - map.yTransform}`;
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