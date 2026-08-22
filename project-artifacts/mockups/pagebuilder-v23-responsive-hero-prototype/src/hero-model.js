const DEFAULT_BUTTON = {
  text: "Button",
  actionType: "link",
  url: "",
  target: "",
  nofollow: false,
  customAttributes: "",
  videoSource: "youtube",
  videoUrl: "",
  imageSource: "ckfinder",
  imageUrl: "",
  imageAlt: "",
};

function moveItem(order, id, direction) {
  const from = order.indexOf(id);
  const to = direction === "up" ? from - 1 : from + 1;
  if (from < 0 || to < 0 || to >= order.length) return order;

  const next = [...order];
  [next[from], next[to]] = [next[to], next[from]];
  return next;
}

function normalizeButtons(buttons, max = 3) {
  const source = Array.isArray(buttons) && buttons.length ? buttons : [{}];
  return source.slice(0, max).map((button, index) => ({
    ...DEFAULT_BUTTON,
    ...button,
    id: button.id || `button-${index + 1}`,
  }));
}

function resolveResponsive(values, device) {
  const chain = device === "mobile"
    ? ["mobile", "tablet", "desktop"]
    : device === "tablet" ? ["tablet", "desktop"] : ["desktop"];
  const inheritedFrom = chain.find((key) => values[key] != null) || "desktop";
  return { value: values[inheritedFrom], inheritedFrom };
}

function isSafeMediaUrl(value) {
  const url = String(value || "").trim();
  if (url.startsWith("/") && !url.startsWith("//")) return true;
  try {
    return ["http:", "https:"].includes(new URL(url).protocol);
  } catch {
    return false;
  }
}

function remoteUrl(value) {
  if (!isSafeMediaUrl(value) || String(value).startsWith("/")) return null;
  try {
    return new URL(value);
  } catch {
    return null;
  }
}

function resolveVideoMedia(source, value) {
  const url = String(value || "").trim();
  if (!isSafeMediaUrl(url)) return null;
  if (source === "self_hosted") return { kind: "video", src: url };

  const parsed = remoteUrl(url);
  if (!parsed) return null;
  const host = parsed.hostname.replace(/^www\./, "");
  let id = "";

  if (source === "youtube" && ["youtube.com", "youtu.be"].includes(host)) {
    id = host === "youtu.be" ? parsed.pathname.split("/")[1] : parsed.searchParams.get("v") || parsed.pathname.split("/").filter(Boolean).pop();
    return id ? { kind: "iframe", src: `https://www.youtube.com/embed/${id}?autoplay=1` } : null;
  }

  if (source === "vimeo" && host === "vimeo.com") {
    id = parsed.pathname.split("/").filter(Boolean).pop();
    return id ? { kind: "iframe", src: `https://player.vimeo.com/video/${id}?autoplay=1` } : null;
  }

  if (source === "dailymotion" && ["dailymotion.com", "dai.ly"].includes(host)) {
    id = parsed.pathname.split("/").filter(Boolean).pop();
    return id ? { kind: "iframe", src: `https://www.dailymotion.com/embed/video/${id}?autoplay=1` } : null;
  }

  return null;
}

export { isSafeMediaUrl, moveItem, normalizeButtons, resolveResponsive, resolveVideoMedia };
