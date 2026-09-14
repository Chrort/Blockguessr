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
// write("./inter.json", new Intersection(...args));

function mvIntersections() {
  BigData.intersections = data.intersections;
  fs.writeFile("./streets.json", JSON.stringify(BigData, null, 2));
}
