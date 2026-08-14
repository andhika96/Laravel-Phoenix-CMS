function anchorTransform(anchor) {
  const [vertical, horizontal] = anchor === "center"
    ? ["center", "center"]
    : anchor.split("-");
  const x = horizontal === "center" ? "-50%" : horizontal === "right" ? "-100%" : "0";
  const y = vertical === "center" ? "-50%" : vertical === "bottom" ? "-100%" : "0";

  return `translate(${x}, ${y})`;
}

function positionStyle(position) {
  const contentAlign = position.align === "center"
    ? "center"
    : position.align === "right" ? "flex-end" : "flex-start";

  return {
    "--content-align": contentAlign,
    left: `${position.x}%`,
    top: `${position.y}%`,
    width: `${position.width}%`,
    textAlign: position.align,
    transform: anchorTransform(position.anchor),
  };
}

export { anchorTransform, positionStyle };
