//INFO: node inter copies intersections from inter.json into streets.json
//node inter s1 s2 s1.x s1.y s2.x s2.y adds {sreets:[s1,s2], nodes: [{s1.x, s1.y}, {s2.x, s2.y}]} to inter.json
//node inter s1 s2 s.x s.y adds adds {sreets:[s1,s2], nodes: [{s1.x, s1.y}, {s1.x, s1.y}]} to inter.json

import BigData from "./streets.json" with {type: "json"};
import data from "./inter.json" with {type: "json"}
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

const args = process.argv.slice(2).map(Number);
if (args.length > 0) write("./inter.json", new Intersection(...args));
else mvIntersections();

function mvIntersections() {
  BigData.intersections = data.intersections;
  fs.writeFile("./streets.json", JSON.stringify(BigData, null, 2));
}
