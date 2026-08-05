// TEMP
import data from './streets.json' with {type: "json"}

export default async function aStar(genesis, terminus) {
  //TEMP: Uncomment for prod
  // const context = await getData();
  let counter = 0;
  const context = data;
  const streets = context.streets;
  const start = getNode(genesis, streets);
  const destination = getNode(terminus, streets);
  let open = [], closed = [];
  setF(start, start, destination);
  open.push(start);

  while (open.length > 0 && counter < 11) {
    const current = open.reduce((prev, cur) => { return cur.f < prev.f ? cur : prev });

    let [street, index] = findCoords(current, streets);
    closed.push(open.splice(open.indexOf(current), 1)[0]);
    if (current.x === destination.x && current.y === destination.y) return getPath(current, []);

    let neighbours = getNeighbours(street, index, current, streets);
    neighbours.push(...getIntersection(current));

    neighbours.forEach(n => {
      if (closed.indexOf(n) >= 0) return;
      if (open.indexOf(n) === -1 || n.g > current + getDistance(current, n)) {
        try {
          setF(n, current, destination);
        } catch (err) {
          console.warn(err, neighbours, current);
        }
      }
      if (open.indexOf(n) === -1) open.push(n);
    });
    counter++;
  }
  return [];
}

function getPath(n, path) {
  if (n.prev) getPath(n.prev, path);
  path.push(n);
  return path;
}

function getNode(coords, streets) {
  const indices = findCoords({ x: coords.split(",")[0], y: coords.split(",")[1] }, streets);
  return streets[indices[0]].coordinates[indices[1]];
}

function setF(node, prev, destination) {
  node.prev = prev === node ? null : prev;
  node.g = node.prev ? prev.g + getDistance(node, prev) : 0;
  node.h = getDistance(destination, node);
  node.f = node.g + node.h;
}

function getDistance(node1, node2) {
  return Math.sqrt((node2.x - node1.x) ** 2 + (node2.y - node1.y) ** 2);
}

function findCoords(coords, streets) {
  for (let i = 0; i < streets.length; i++) {
    for (let j = 0; j < streets[i].coordinates.length; j++) {
      if (streets[i].coordinates[j].x == coords.x && streets[i].coordinates[j].y == coords.y) {
        return [i, j];
      }
    }
  }
  return undefined;
}

async function getData() {
  const res = await fetch("streets.json");
  return await res.json();
}

function getNeighbours(street, index, current, streets) {
  let n = [];
  if (index > 0) n.push(streets[street].coordinates[index - 1]);
  if (streets[street].coordinates.length - 1 > index) n.push(streets[street].coordinates[index + 1]);
  n.forEach((e, i) => {
    let c = 1;
    while (n[i].x == current.x && n[i].y == current.y) {
      c++;
      n[i] = streets[street].coordinates[index + (!(i % 2) ? -1 : 1) * c];
    }
  });
  return n;
}

function findIntersection(intersections, current) {
  for (let i = 0; i < intersections.length; i++) {
    for (let j = 0; j < intersections[i].nodes.length; j++) {
      if (intersections[i].nodes[j].x == current.x && intersections[i].nodes[j].y == current.y) return [i, j];
    }
  }
  return [];
}

function getIntersection(current) {
  const res = findIntersection(data.intersections, current);
  if (!res.length) return [];
  const [index, j] = res;
  return data.intersections[index].nodes.filter((_, i) => i !== j);
}

// TEMP
// let res = aStar("723,-2482", "724,-2482");
let res = getIntersection({ x: -1460, y: -217 });
console.log(res);
