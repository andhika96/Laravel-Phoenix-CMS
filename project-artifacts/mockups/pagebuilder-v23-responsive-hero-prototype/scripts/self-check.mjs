import assert from "node:assert/strict";
import {
  isSafeMediaUrl,
  moveItem,
  normalizeButtons,
  resolveResponsive,
  resolveVideoMedia,
} from "../src/hero-model.js";
import { anchorTransform, positionStyle } from "../src/positioning.js";

assert.equal(anchorTransform("top-left"), "translate(0, 0)");
assert.equal(anchorTransform("center"), "translate(-50%, -50%)");
assert.equal(anchorTransform("bottom-right"), "translate(-100%, -100%)");
assert.deepEqual(positionStyle({ anchor: "top-center", x: 50, y: 12, width: 84, align: "center" }), {
  "--content-align": "center",
  left: "50%",
  top: "12%",
  width: "84%",
  textAlign: "center",
  transform: "translate(-50%, 0)",
});

assert.deepEqual(moveItem(["title", "subtitle", "buttons"], "subtitle", "up"), ["subtitle", "title", "buttons"]);
assert.deepEqual(moveItem(["title", "subtitle", "buttons"], "title", "up"), ["title", "subtitle", "buttons"]);

assert.equal(normalizeButtons([]).length, 1);
assert.equal(normalizeButtons([{ id: "one" }, { id: "two" }, { id: "three" }, { id: "four" }]).length, 3);

assert.deepEqual(resolveResponsive({ desktop: "D", tablet: "T", mobile: null }, "mobile"), { value: "T", inheritedFrom: "tablet" });
assert.deepEqual(resolveResponsive({ desktop: "D", tablet: null, mobile: null }, "mobile"), { value: "D", inheritedFrom: "desktop" });

assert.equal(isSafeMediaUrl("https://example.test/hero.webp"), true);
assert.equal(isSafeMediaUrl("/assets/hero.webp"), true);
assert.equal(isSafeMediaUrl("//example.test/hero.webp"), false);
assert.equal(isSafeMediaUrl("javascript:alert(1)"), false);

assert.deepEqual(resolveVideoMedia("youtube", "https://youtu.be/h529sg3pEV4"), {
  kind: "iframe",
  src: "https://www.youtube.com/embed/h529sg3pEV4?autoplay=1",
});
assert.deepEqual(resolveVideoMedia("vimeo", "https://vimeo.com/76979871"), {
  kind: "iframe",
  src: "https://player.vimeo.com/video/76979871?autoplay=1",
});
assert.deepEqual(resolveVideoMedia("dailymotion", "https://www.dailymotion.com/video/x8abc12"), {
  kind: "iframe",
  src: "https://www.dailymotion.com/embed/video/x8abc12?autoplay=1",
});
assert.deepEqual(resolveVideoMedia("self_hosted", "https://example.test/video.mp4"), {
  kind: "video",
  src: "https://example.test/video.mp4",
});
assert.equal(resolveVideoMedia("youtube", "javascript:alert(1)"), null);

console.log("responsive hero self-check passed");
