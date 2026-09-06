export class Path {
  constructor(genesis, terminus) {
    this.genesis = genesis, this.terminus = terminus;
    this.coordsString = "";
    this.streets = [];
    this.intersections = [];
    this.path = [];
  }

  formatCoordsString() {
    this.coordsString += this.genesis + " ";
    for (let i = 0; i < this.path.length; i++) {
      this.coordsString += toCoordsString(this.path[i]) + " ";
    }
    this.coordsString += this.terminus;
    return this.coordsString.trim();
  }

  setPath(endNode) {
    let node = endNode;
    this.path.push(endNode);
    while (node.prev) {
      node = node.prev;
      this.path.unshift(node);
    }
  }

  setIntersections(intersections) {
    for (let i = 0; i < this.path.length; i++) {
      let inter = findIntersection(this.path[i], intersections);
      if (inter) {
        let next = intersections[inter[0]].nodes.some(e =>
          e.x == this.path[i + 1]?.x &&
          e.y == this.path[i + 1]?.y
        );
        if (next) this.intersections.push(intersections[inter[0]]);
      }
    }
  }

  setStreets(start, destination, streets) {
    this.streets = this.intersections.slice(0, -1).map((intersection, i) => {
      const next = this.intersections[i + 1];
      return intersection.streets.find(street => next.streets.includes(street));
    });
    this.streets.unshift(streets[toIndexArray(start)[0]].name);
    this.streets.push(streets[toIndexArray(destination)[0]].name);
  }

  populatePath(endNode, start, destination, intersections, streets) {
    endNode = getNodeByIndices(toIndexArray(endNode), streets)
    this.setPath(endNode);
    this.formatCoordsString();
    this.setIntersections(intersections, streets);
    this.setStreets(start, destination, streets);
  }
}

export default function aStar(genesis, terminus, context) {
  const streets = context.streets, intersections = context.intersections, path = new Path(genesis, terminus);
  const getNodeByIndexString = str => getNodeByIndices(toIndexArray(str), streets);
  //TODO: open into PriorityQueue?
  let open = new Set(), closed = new Set();
  const start = findNearestStreetCoords(genesis, streets), destination = findNearestStreetCoords(terminus, streets);
  console.log(getNodeByIndexString(start), getNodeByIndexString(destination));
  setF(getNodeByIndexString(start), getNodeByIndexString(start), getNodeByIndexString(destination));
  open.add(start)

  while (open.size) {
    const current = getLowestF(open, streets);
    open.delete(current);
    closed.add(current);
    if (current == destination) {
      path.populatePath(current, start, destination, intersections, streets);
      break;
    }
    let neighbours = getNeighbours(current, streets);
    neighbours.push(...getIntersection(current, streets, intersections));

    neighbours.forEach(neighbour => {
      const neighbourNode = getNodeByIndexString(neighbour), curNode = getNodeByIndexString(current);
      if (closed.has(neighbour)) return;
      if (!open.has(neighbour) || neighbourNode.g > curNode.g + getDistance(curNode, neighbourNode)) {
        setF(neighbourNode, curNode, getNodeByIndexString(destination));
        open.add(neighbour);
      }
    });
  }

  return path;
}

function getLowestF(open, streets) {
  let lowest = { f: Infinity }, lowestIndex;
  for (const nodeIndexString of open) {
    const node = getNodeByIndices(toIndexArray(nodeIndexString), streets);
    if (lowest.f > node.f) {
      lowestIndex = nodeIndexString;
      lowest = node;
    }
  }
  return lowestIndex;
}

function setF(node, prev, destination) {
  node.prev = node === prev ? null : prev;
  node.g = node.prev ? getDistance(node, prev) + prev.g : 0;
  node.f = getDistance(node, destination) + node.g;
}

function getDistance(node1, node2) {
  return Math.sqrt((node1.x - node2.x) ** 2 + (node1.y - node2.y) ** 2);
}

function getNodeByCoords(coords, streets) {
  let res = [];
  for (let i = 0; i < streets.length; i++) {
    for (let j = 0; j < streets[i].coordinates.length; j++) {
      if (coords.x == streets[i].coordinates[j].x && coords.y == streets[i].coordinates[j].y) res.push(`${i},${j}`);
    }
  }
  return res;
}

// "x,y"
function getNodeFromString(string) {
  const [x, y] = toIndexArray(string);
  return { x: x, y: y };
}

function getNodeByIndices([i, j], streets) {
  return streets[i].coordinates[j];
}

function getNeighbours(current, streets) {
  const [i, j] = toIndexArray(current);
  const currNode = getNodeByIndices([i, j], streets);
  let n = [];
  if (j > 0) n.push([i, j - 1]);
  if (streets[i].coordinates.length - 1 > j) n.push([i, j + 1]);
  return n.map((neighbour, index) => {
    let c = 1;
    while (getNodeByIndices([i, j + (!(index % 2) ? -1 : 1) * (c + 1)], streets) && getNodeByIndices(neighbour, streets).x == currNode.x && getNodeByIndices(neighbour, streets).y == currNode.y) {
      c++;
      neighbour = [i, j + (!(index % 2) ? -1 : 1) * c];
    }
    return `${neighbour[0]},${neighbour[1]}`;
  });
}


function getIntersection(node, streets, intersections) {
  const [i, j] = toIndexArray(node);
  const res = findIntersection(streets[i].coordinates[j], intersections);
  if (!res) return [];
  const foundIntersections = intersections[res[0]].nodes.flatMap((e, i) => i !== res[1] ? getNodeByCoords(e, streets) : []).filter(e => e !== node);
  return foundIntersections;
}
//TODO: make a lookup Map
function findIntersection(node, intersections) {
  for (let i = 0; i < intersections.length; i++) {
    for (let j = 0; j < intersections[i].nodes.length; j++) {
      if (intersections[i].nodes[j].x == node.x && intersections[i].nodes[j].y == node.y) return [i, j];
    }
  }
  return null;
}

function toIndexArray(str) {
  return str.split(",").map(e => Number(e));
}

function toCoordsString(node) {
  return `${node.x},${node.y}`;
}



export function format() { }

export function findNearestStreetCoords(inputCoords, streets) {
  let nearest = [0, 0], coords = getNodeFromString(inputCoords, streets), distance = getDistance(getNodeByIndices(nearest, streets), coords);
  for (let i = 0; i < streets.length; i++) {
    for (let j = 0; j < streets[i].coordinates.length; j++) {
      const node = streets[i].coordinates[j];
      if (node.x == coords.x && node.y == coords.y) return `${i},${j}`;
      let tempDistance = getDistance(node, coords);
      if (tempDistance < distance) {
        nearest = [i, j];
        distance = tempDistance;
      }
    }
  }
  return `${nearest[0]},${nearest[1]}`;
}
