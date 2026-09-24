import test from "node:test";
import assert from "node:assert/strict";
import { matchCoords, find } from "./streets.js";

test("matchCoords", () => {
  assert.strictEqual(
    matchCoords(
      [{ x: 1, y: 2 }, { x: 3, y: 4 }],
      [{ x: 1, y: 2 }, { x: 3, y: 4 }],
      2
    ),
    true
  );

  assert.strictEqual(
    matchCoords([{ x: 1, y: 2 }], [{ x: 9, y: 2 }], 1),
    false
  );

  assert.strictEqual(
    matchCoords([{ x: 1, y: 2 }], [{ x: 1, y: 9 }], 1),
    false
  );

  assert.strictEqual(
    matchCoords([{ x: 1, y: 2 }], [{ x: 9, y: 9 }], 1),
    false
  );

  assert.strictEqual(
    matchCoords([], [], 5),
    true
  );

  assert.strictEqual(
    matchCoords([{ x: 1, y: 2 }], [{ x: 9, y: 9 }], 0),
    true
  );
});


test("find", () => {
  const streets = [
    {
      coordinates: [
        { x: 1, y: 1 },
        { x: 2, y: 2 },
        { x: 3, y: 3 },
        { x: 4, y: 4 },
        { x: 5, y: 5 },
        { x: 6, y: 6 },
      ],
    },
    {
      coordinates: [
        { x: 1, y: 1 },
        { x: 2, y: 2 },
        { x: 3, y: 3 },
        { x: 4, y: 4 },
        { x: 5, y: 5 },
        { x: 9, y: 9 },
      ],
    },
    {
      coordinates: [
        { x: 1, y: 1 },
        { x: 2, y: 2 },
        { x: 3, y: 3 },
        { x: 4, y: 4 },
        { x: 5, y: 8 },
        { x: 6, y: 6 },
      ],
    },
    {
      coordinates: [
        { x: 9, y: 9 },
        { x: 8, y: 8 },
        { x: 7, y: 7 },
        { x: 6, y: 6 },
        { x: 5, y: 5 },
        { x: 4, y: 4 },
      ],
    },
  ];

  assert.deepStrictEqual(
    find([
      { x: 1, y: 1 },
      { x: 2, y: 2 },
      { x: 3, y: 3 },
      { x: 4, y: 4 },
      { x: 5, y: 5 },
      { x: 6, y: 6 },
    ], streets),
    [0]
  );

  assert.deepStrictEqual(
    find([
      { x: 1, y: 1 },
      { x: 2, y: 2 },
      { x: 3, y: 3 },
      { x: 4, y: 4 },
      { x: 5, y: 5 },
      { x: 9, y: 9 },
    ], streets),
    [1]
  );

  assert.deepStrictEqual(
    find([
      { x: 9, y: 9 },
      { x: 8, y: 8 },
      { x: 7, y: 7 },
      { x: 6, y: 6 },
      { x: 5, y: 5 },
      { x: 4, y: 4 },
    ], streets),
    [3]
  );

  assert.deepStrictEqual(
    find([
      { x: 0, y: 0 },
      { x: 0, y: 0 },
      { x: 0, y: 0 },
      { x: 0, y: 0 },
      { x: 0, y: 0 },
      { x: 0, y: 0 },
    ], streets),
    []
  );
});
