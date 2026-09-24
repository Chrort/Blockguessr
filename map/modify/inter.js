//INFO: node inter -m copies intersections from inter.json into streets.json
//node inter -i s1 s2 s1.x s1.y s2.x s2.y adds {sreets:[s1,s2], nodes: [{s1.x, s1.y}, {s2.x, s2.y}]} to inter.json
//node inter -i s1 s2 s.x s.y adds adds {sreets:[s1,s2], nodes: [{s1.x, s1.y}, {s1.x, s1.y}]} to inter.json
//node inter -s -streets s1...sn outputs all intersections that include s1...sn in intersection.streets in any order

import BigData from "../streets.json" with {type: "json"};
import data from "../inter.json" with {type: "json"}
import fs from "node:fs/promises";

async function write(filepath, newData) {
  data.intersections.push(newData);
  await fs.writeFile(filepath, JSON.stringify(data, null, 2));
}

class Intersection {
  constructor(...args) {
    this.streets = args.splice(0, 2);
    this.nodes = [];
    for (let i = 0; i < args.length; i += 2) {
      this.nodes.push({ x: args[i], y: args[i + 1] });
    }
    if (this.nodes.length === 1) this.nodes.push({ x: args[0], y: args[1] });
  }
}

const mode = process.argv[2].slice(1);
switch (mode) {
  case "s":
    const type = process.argv[3].slice(1);
    switch (type) {
      case "streets":
        const s1 = Number(process.argv[4]), s2 = Number(process.argv[5]);
        let res = searchByStreets(s1, s2);
        console.log(res);
        break;
      case "coords":

        break;
    }
    break;
  case "i":
    const args = process.argv.slice(3).map(Number);
    if (args.length >= 4) write("../inter.json", new Intersection(...args));
    else throw new Error("expected at least 4 arguments for insert mode. Do node inter --help for help");
    break;
  case "m":
    mvIntersections();
    break;
  case "-help":
  case "h":
    console.log(`
node inter -m       Copies intersections from inter.json into streets.json.

node inter -i s1 s2 s1.x s1.y s2.x s2.y
  Adds:
    {
      streets: [s1, s2],
      nodes: [
        { x: s1.x, y: s1.y },
        { x: s2.x, y: s2.y }
      ]
    }
  to inter.json.

node inter -i s1 s2 s.x s.y
  Adds:
    {
      streets: [s1, s2],
      nodes: [
        { x: s.x, y: s.y },
        { x: s.x, y: s.y }
      ]
    }
  to inter.json.

node inter -s -streets s1 ... sn      Outputs all intersections whose streets include s1 ... sn, in any order.
`);
    break;
}

function mvIntersections() {
  BigData.intersections = data.intersections;
  fs.writeFile("../streets.json", JSON.stringify(BigData, null, 2));
}

function searchByStreets(s1, s2) {
  let res = [];
  for (let i = 0; i < data.intersections.length; i++) {
    if (data.intersections[i].streets.includes(s1) && (!s2 || data.intersections[i].streets.includes(s2))) {
      res.push(data.intersections[i]);
    }
  }
  return res;
}
