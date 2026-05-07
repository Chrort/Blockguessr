export class Map{
  constructor(mapDiv, boundaries = [0, 0, 0, 0]){
    this.mapDiv = mapDiv;

    this.additionalXTransform = 460;
    this.additionalYTransform = 330;

    this.labelDataArray = [];
    this.streetDataArray = [];
    this.transformData = [this.additionalXTransform, this.additionalYTransform];
    this.boundaries = boundaries;

    this.xTransform = Math.abs(boundaries[0] * 512);
    this.yTransform = Math.abs(boundaries[2] * 512);

    this.currentStyles = window.getComputedStyle(this.mapDiv);
    this.currentZoomLevel = +this.currentStyles.getPropertyValue("scale");
    this.maxScale = 50;
    this.minScale = 0.05;

    this.startX = 0;
    this.startY = 0;
    this.newX = 0;
    this.newY = 0;

    this.mouseMove = this.mouseMove.bind(this);
    this.mouseUp = this.mouseUp.bind(this);
    this.mouseDown = this.mouseDown.bind(this);
    this.zoom = this.zoom.bind(this);

    this.mapDiv.addEventListener("pointerdown", this.mouseDown);
    this.mapDiv.addEventListener("wheel", this.zoom, {passive: true});
  }

  genMap(type, imgPath = "../"){
    type == undefined ? type = "maps" : "maps";
    
    for(let i = this.boundaries[0]; i < this.boundaries[1]; i++){
      for(let j = this.boundaries[2]; j < this.boundaries[3]; j++){
        let mapCanvasE = document.createElement("div");
        mapCanvasE.id = `${j},${i}`;
        mapCanvasE.className = "mapCanvas";
        this.mapDiv.appendChild(mapCanvasE);
        const imgSrc = `${imgPath}img/${type}/${j},${i}.png`
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

  async fetchData(dataSpecification = [true, true, true], filePath = "../"){
      if(dataSpecification[0]){
        await fetch(`${filePath}api/label_data.php`)
          .then(response => response.json())
          .then(data => {
          for(let i = 0; i < data.length; i++){
              this.labelDataArray.push(data[i]);
          }
          })
          .catch(error => console.error('Error while loading data: ', error));
        }
      if(dataSpecification[1]){
          await fetch(`${filePath}api/polygon_data.php`)
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
                  this.labelDataArray.push([data[i][0], data[i][1], `${(maxCoords[0] + 0.5 * (maxCoords[2] - maxCoords[0]))}`, `${(maxCoords[1] + 0.5 * (maxCoords[3] - maxCoords[1]))}`, data[i][3]]);
              }
          })
          .catch(error => console.error('Error while loading data: ', error));
      }
      if(dataSpecification[2]){
          await fetch(`${filePath}api/street_data.php`)
              .then(respones => respones.json())
              .then(data => {
                  for(let j = 0; j < data.length; j++){
                      this.streetDataArray.push(data[j]);
                  }
              this.drawStreetLabels(true);
              })
              .catch(error => console.error('Error while loading data: ', error));
      }
  }

  drawLabels(create){
    for(var i = 0; i < this.labelDataArray.length; i++){
      let center = "-50%";

      //creates div only once
      if(create){
        let labelDiv = document.createElement("div");
        labelDiv.className = `labelDiv labelDiv_${this.labelDataArray[i][4]}`;
        labelDiv.id = `mapLabel_${+this.labelDataArray[i][0]}_${this.labelDataArray[i][4]}`;
        this.mapDiv.appendChild(labelDiv);
      }

      //positions divs and sets font-size
      let labelDiv = document.getElementById(`mapLabel_${+this.labelDataArray[i][0]}_${this.labelDataArray[i][4]}`);
      labelDiv.style.left = `${+this.labelDataArray[i][2] + this.xTransform}px`;
      labelDiv.style.top = `${+this.labelDataArray[i][3] + this.yTransform}px`;
      labelDiv.style.fontSize = `${20 / this.currentZoomLevel**.7}px`;

      //styles/displays different types differently
      switch(this.labelDataArray[i][4]){
        case "province":
          if(this.currentZoomLevel <= .35){
            labelDiv.style.visibility = "visible";
            labelDiv.style.color = "black";
            labelDiv.style.fontWeight = "bolder";
            labelDiv.style.fontSize = `${25 / this.currentZoomLevel**.7}px`;
            labelDiv.innerHTML = `${this.labelDataArray[i][1].toUpperCase()}`;
          }else{
            labelDiv.style.visibility = "hidden";
          }
          break;
        case "town":
          if(this.currentZoomLevel > .2){
            labelDiv.style.visibility = "visible";
            labelDiv.style.color = "black";
            labelDiv.innerHTML = `${this.labelDataArray[i][1]}`;
          }else{
            labelDiv.style.visibility = "hidden";
          }
          break;
        case "waters":
          if(this.currentZoomLevel > .3){
            labelDiv.style.visibility = "visible";
            labelDiv.style.color = "blue";
            labelDiv.innerHTML = `${this.labelDataArray[i][1]}`;
          }
          else{
            labelDiv.style.visibility = "hidden";
          }
          break;
        case "landscape":
          if(this.currentZoomLevel > .4){
            labelDiv.style.visibility = "visible";
            labelDiv.style.color = "green";
            labelDiv.innerHTML = `${this.labelDataArray[i][1]}`;
          }
          else{
            labelDiv.style.visibility = "hidden";
          }
          break;
        case "point":
          if(this.currentZoomLevel > .6){
            labelDiv.style.visibility = "visible";
            labelDiv.style.color = "black";
            center = "0%";
            labelDiv.innerHTML = `▪${this.labelDataArray[i][1]}`;
          }
          else{
            labelDiv.style.visibility = "hidden";
          }
          break;
        case "np":
          if(this.currentZoomLevel > .2){
            labelDiv.style.visibility = "visible";
            labelDiv.style.color = "#CBBA9F";
            labelDiv.innerHTML = `${this.labelDataArray[i][1]}`;
            labelDiv.innerHTML = `${this.labelDataArray[i][1].toUpperCase()}`;
          }
          else{
            labelDiv.style.visibility = "hidden";
          }
          break;
        default:
      }
      //adjust postion
      labelDiv.style.transform = `translateX(${center}) translateY(-${parseFloat(window.getComputedStyle(labelDiv).getPropertyValue('font-size')) / 2}px)`;
    }
    if(this.currentZoomLevel < 11) this.checkCollision();
  }

  drawStreetLabels(create){
    let frequency = 150;
    
    for(let i = 0; i < this.streetDataArray.length; i++){
      const container = document.getElementById("streetLabelDivContainer");
      const streetCheckbox = document.getElementById("street");
      let coordsLength = this.streetDataArray[i][3].split(" ").length;
      if(create){
        for(let j = 0; j < coordsLength; j+=frequency){
          let streetLabelDiv = document.createElement("div");
          streetLabelDiv.classList.add(`${this.streetDataArray[i][1]}_label`, 'streetLabel');
          streetLabelDiv.id = `${this.streetDataArray[i][1]}_${this.streetDataArray[i][0]}_label_${j}`;  
          streetLabelDiv.innerHTML = this.streetDataArray[i][1];
          streetLabelDiv.style.backgroundColor = this.streetDataArray[i][2];
          streetLabelDiv.style.color = this.checkBrightness(this.streetDataArray[i][2]);
  
          let coords = this.streetDataArray[i][3].split(" ");
          let xCoord;
          let yCoord;
  
          //checks for too short roads
          if(coordsLength > frequency){
            try{
              xCoord = +coords[j + Math.round(frequency / 2)].split(",")[0];
              yCoord = +coords[j + Math.round(frequency / 2)].split(",")[1];
            } catch{
              null;
            }
          }else{
            xCoord = +coords[Math.round(coordsLength / 2)].split(",")[0];
            yCoord = +coords[Math.round(coordsLength / 2)].split(",")[1];
          }
          streetLabelDiv.style.top = `${yCoord + this.yTransform}px`;
          streetLabelDiv.style.left = `${xCoord + this.xTransform}px`;
  
          container.appendChild(streetLabelDiv);
        }
      }

      for(let k = 0; k < coordsLength; k+=frequency){
        let streetLabelDiv = document.getElementById(`${this.streetDataArray[i][1]}_${this.streetDataArray[i][0]}_label_${k}`);
        this.currentZoomLevel < 1.4 || (!streetCheckbox?.checked && streetCheckbox != undefined) ? streetLabelDiv.style.display = "none" : streetLabelDiv.style.display = "flex";
  
        if(streetLabelDiv.style.display !== "none"){
          streetLabelDiv.innerHTML = this.streetDataArray[i][1];
          streetLabelDiv.style.fontSize = `${14 / this.currentZoomLevel**.7}px`;
          streetLabelDiv.style.padding = `0 ${2 / this.currentZoomLevel**.7}px`;
        }
      }
    }
  }

  getMousePos(e){
    let rect = this.mapDiv.getBoundingClientRect();
      let x = (e.clientX - rect.left - this.mapDiv.clientLeft) / this.currentZoomLevel;
      let y = (e.clientY - rect.top - this.mapDiv.clientTop) / this.currentZoomLevel;
      return {x, y};
  }

  mouseDown(e){
    this.mapDiv.addEventListener("pointermove", this.mouseMove);
    this.mapDiv.addEventListener("pointerup", this.mouseUp);
  }

  mouseMove(e){
    this.mapDiv.style.top = `${this.mapDiv.offsetTop - e.movementY * -1}px`;
    this.mapDiv.style.left = `${this.mapDiv.offsetLeft - e.movementX * -1}px`;
  }

  mouseUp(){
    this.mapDiv.removeEventListener("pointermove", this.mouseMove);
    this.mapDiv.removeEventListener("pointerup", this.mouseUp);
  }

  zoom(e){
    let zoomStep = 1.4;
    let y = e.deltaY;
  
    //mouse pos before scale
    let mouseX = this.getMousePos(e).x;
    let mouseY = this.getMousePos(e).y;
  
    //check if scrolled up/down & max/min zoom level 
    y > 0 ? this.currentZoomLevel /= zoomStep : this.currentZoomLevel *= zoomStep;
    this.currentZoomLevel < this.minScale ? this.currentZoomLevel = this.minScale : this.currentZoomLevel > this.maxScale ? this.currentZoomLevel = this.maxScale : null;
    this.currentZoomLevel = Math.round(this.currentZoomLevel * 100) / 100;
    this.mapDiv.style.scale = this.currentZoomLevel;
  
    //mouse pos after scale
    let mouseX2 = this.getMousePos(e).x;
    let mouseY2 = this.getMousePos(e).y;
  
    //transforms canvas based on mouse positions
    this.mapDiv.style.transform = `translateX(${(mouseX2 - mouseX + this.transformData[0] - this.additionalXTransform)}px) translateY(${(mouseY2 - mouseY + this.transformData[1] - this.additionalYTransform)}px)`;
  
    //arrays stores current scale data ([x, y])
    this.transformData.push(mouseX2 - mouseX + this.transformData[0], mouseY2 - mouseY + this.transformData[1]);
    this.transformData.splice(0, 2);

    this.drawStreetLabels(false);
    this.drawLabels(false);
    this.adaptBorders();
    this.adaptStreets();
    this.adaptPanoLinks();
    this.adaptPolygonsText();
    this.adaptIdLine();
    this.adjustPins();
  }

  checkCollision(){

    const boxes = [];

    for(let i = 0; i < this.labelDataArray.length; i++){
        const div = document.getElementById(`mapLabel_${+this.labelDataArray[i][0]}_${this.labelDataArray[i][4]}`);

        if(window.getComputedStyle(div).visibility == "visible") boxes.push([div, div.getBoundingClientRect(), this.labelDataArray[i][4]]);
    }

    [].forEach.call(document.querySelectorAll('.streetLabel'), e => {
      if(window.getComputedStyle(e).display == "flex") boxes.push([e, e.getBoundingClientRect(), "street"]);
    })

    let c = 0;
    for(let i = 0; i < boxes.length; i++){
      for(let j = i + 1; j < boxes.length; j++){
        if(Math.abs(boxes[i][1].top - boxes[j][1].top) > 70){
          continue;
        }

        const size = boxes[i][1];
        const compareSize = boxes[j][1];
        
        if(size.top < compareSize.bottom && size.bottom > compareSize.top && size.left < compareSize.right && size.right > compareSize.left){
          this.deleteDecider(boxes[i][0], boxes[j][0], boxes[i][2], boxes[j][2]).innerHTML = ``;
        }
      }
    }
  }

  deleteDecider(div, compareDiv, typeI, typeJ){
    const priority = {
      province: 5,
      town: 4,
      waters: 3,
      landscape: 2,
      point: 1,
      street: 0
    }

    return priority[typeI] >= priority[typeJ] ? compareDiv : div;
  }

  adaptBorders(){
    const borders = document.getElementsByClassName("borderPolyline");
    [].forEach.call(borders, e => {
      e.style.strokeWidth = 4 / this.currentZoomLevel**.6;
    })
  }

  adaptPanoLinks(){
    if(document.getElementById("panorama").checked){
      document.documentElement.style.setProperty("--panoScale", 1 / (this.currentZoomLevel ** 0.5));
    }
  }

  adaptStreets(){
    const streetLines = document.getElementsByClassName("streetPolyline");
    [].forEach.call(streetLines, (e) => {
      e.style.strokeWidth = 4 / this.currentZoomLevel**.6;
    })
  }

  adaptPolygonsText(){
    const polygonsText = document.getElementsByClassName("polygonText");
    [].forEach.call(polygonsText, (e) => {
      e.style.fontSize = `${15 / this.currentZoomLevel ** .6}px`;
      e.style.transform = `translateX(-${e.getBoundingClientRect().width * 0.5 / this.currentZoomLevel}px)`;
    })
  }

  adaptIdLine(){
    const idLine = document.getElementById("idLine");
    if(!idLine) return;
    idLine.style.strokeWidth = 5 / this.currentZoomLevel**.6;
    idLine.style.strokeDasharray = `${10 / this.currentZoomLevel} ${5 / this.currentZoomLevel}`;
  }

  adjustPins(){
    const location = document.getElementById("location");
    if(!location) return;
    let pin = document.querySelector("#pin");
    pin.style.scale = 1 / this.currentZoomLevel;
    location.style.scale = 1 / this.currentZoomLevel;
  }

  checkBrightness(hex){
    hex = hex.replace("#", "");

    let r = parseInt(hex.substr(0, 2), 16);
    let g = parseInt(hex.substr(2, 2), 16);
    let b = parseInt(hex.substr(4, 2), 16);

    let brightness = (r * 299 + g * 587 + b * 144) / 1000;

    return (brightness < 128) ? 'white' : 'black';
  }
}