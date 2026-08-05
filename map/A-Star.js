// TEMP
import data from './streets.json' with {type: "json"}

export default function aStar(genesis, terminus, context) {
  const streets = context.streets, intersections = context.intersections;
  const getNodeByIndexString = str => getNodeByIndices(toIndexArray(str), streets);
  let open = new Set(), closed = new Set();
  const start = getNodeFromString(genesis, streets), destination = getNodeFromString(terminus, streets);
  setF(getNodeByIndexString(start), getNodeByIndexString(start), getNodeByIndexString(destination));
  open.add(start)

  while (open.size) {
    const current = [...open].reduce((min, cur) => cur.f < min.f ? min : cur);
    open.delete(current);
    closed.add(current);
    if (current == destination) return getPath(getNodeByIndexString(current), []);
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
  for (let i = 0; i < streets.length; i++) {
    for (let j = 0; j < streets[i].coordinates.length; j++) {
      if (coords.x == streets[i].coordinates[j].x && coords.y == streets[i].coordinates[j].y) return `${i},${j}`;
    }
  }
  return [];
}

// "x,y"
function getNodeFromString(string, streets) {
  const [x, y] = toIndexArray(string);
  return getNodeByCoords({ x: x, y: y }, streets);
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
    while (getNodeByIndices(neighbour, streets).x == currNode.x && getNodeByIndices(neighbour, streets).y == currNode.y) {
      c++;
      neighbour = [i, j + (!(index % 2) ? -1 : 1) * c];
    }
    return `${neighbour[0]},${neighbour[1]}`;
  });
}

function getPath(n, path) {
  if (n.prev) getPath(n.prev, path);
  path.push(n);
  return path;
}

function getIntersection(node, streets, intersections) {
  const [i, j] = toIndexArray(node);
  const res = findIntersection(streets[i].coordinates[j], intersections);
  if (!res) return [];
  return intersections[res[0]].nodes.flatMap((_, i) => i !== res[1] ? `${res[0]},${i}` : []);
  // return intersections[res[0]].nodes.flatMap((_, i) => i !== res[1] ? [res[0], i] : []);
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

const res = aStar("722,-2482", "726,-2483", data);
console.log(res)
