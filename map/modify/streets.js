import fs from "fs";
import data from "../streets.json" with {type: "json"}
let streets = data.streets;

class Street {
  constructor(name, color, coordinates) {
    this.name = name;
    this.color = color;
    this.coordinates = toNodeArray(coordinates);
  }
}

class Node {
  constructor(x, y) {
    this.x = x;
    this.y = y;
  }
}

let args = process.argv.slice(2);
const mode = getFlag(args);

switch (mode) {
  case "s":
    if (getFlag(args) === "coords") {
      console.log(searchCoords(Number(args[0]), Number(args[1])));
    }
    break;
  case "i":
    insert(args);
    break;
  case "help":
  case "h":
    const message = `
node <file> [option] [arguments]

Options:
  -s -coords <x> <y>    Search for streets containing a node with the given coordinates.

  -i -n <name> <color> <"x1,y1 x2,y2 ...">
                       Create a Street with the given name, color, and coordinates.

  -i -c <idNode.x> <idNode.y> <name> <color> <"x1,y1 x2,y2 ...">
                       Update a Street that includes/included the idNode and set it to name, color, and coordinates.

  -h, --help           Show this help message.

Examples:
  node streets.js -s -coords 10 20
  node streets.js -i -n "Main Street" "#FFFFFF" "10,20 15,25 20,30"
  node streets.js -i -c 10 20 "Main Street" "#FFFFFF" "10,20 15,25 20,30"
`
    console.log(message);
    break;
}

function getFlag(args) {
  return args[0]?.startsWith("-") ? args.shift().replace(/^-+/, "") : undefined;
}

function searchCoords(x, y) {
  let res = [];
  outer:
  for (let i = 0; i < streets.length; i++) {
    for (let j = 0; j < streets[i].coordinates.length; j++) {
      const node = streets[i].coordinates[j];
      if (node.x == x && node.y == y) {
        res.push({ streetName: streets[i].name, indices: [i, j] });
        continue outer;
      }
    }
  }
  return res;
}

function toNodeArray(coords) {
  let nodes = [];
  const stringPairs = coords.split(" ");
  for (let pair of stringPairs) {
    pair = pair.split(",").map(Number);
    nodes.push(new Node(...pair));
  }
  return nodes;
}

function insert() {
  const flag = getFlag(args);
  switch (flag) {
    case "c":
      updateStreet();
      break;
    case "n":
      addStreet();
      break;
  }
  fs.writeFile(new URL("../streets.json", import.meta.url), JSON.stringify(data, null, 2), err => {
    if (err) {
      console.error("Failed to write file:", err);
      return;
    }

    console.log("streets.json written successfully");
  });
}

function updateStreet() {
  let searchRes = searchCoords(...args.splice(0, 2).map(Number));
  if (searchRes.length != 1) throw new Error("Id Node can not be an Intersection and must be on a Road");
  const index = searchRes[0].indices[0];
  streets[index] = new Street(...args);
}

function addStreet() {
  streets.push(new Street(...args))
}

