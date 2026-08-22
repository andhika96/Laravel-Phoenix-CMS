import { useEffect, useMemo, useRef, useState } from "react";
import {
  isSafeMediaUrl,
  moveItem,
  normalizeButtons,
  resolveResponsive,
  resolveVideoMedia,
} from "./hero-model.js";
import { positionStyle } from "./positioning.js";

const DEVICES = [
  { id: "desktop", label: "Desktop", icon: "bi-display", width: "1440 px" },
  { id: "tablet", label: "Tablet", icon: "bi-tablet", width: "768 px" },
  { id: "mobile", label: "Mobile", icon: "bi-phone", width: "390 px" },
];

const ANCHORS = [
  "top-left", "top-center", "top-right",
  "center-left", "center", "center-right",
  "bottom-left", "bottom-center", "bottom-right",
];

const LABELS = {
  group: "Content group",
  title: "Title",
  subtitle: "Subtitle",
  buttons: "Button Group",
};

const INITIAL_POSITION = {
  desktop: {
    group: { anchor: "center-left", x: 17, y: 54, width: 30, align: "left" },
    title: { anchor: "top-left", x: 17, y: 52, width: 28, align: "left" },
    subtitle: { anchor: "top-left", x: 17, y: 65, width: 28, align: "left" },
    buttons: { anchor: "top-left", x: 17, y: 77, width: 30, align: "left" },
  },
  tablet: {
    group: { anchor: "center-left", x: 11, y: 55, width: 42, align: "left" },
    title: { anchor: "top-left", x: 11, y: 48, width: 42, align: "left" },
    subtitle: { anchor: "top-left", x: 11, y: 62, width: 42, align: "left" },
    buttons: { anchor: "top-left", x: 11, y: 75, width: 40, align: "left" },
  },
  mobile: {
    group: { anchor: "top-center", x: 50, y: 13, width: 84, align: "center" },
    title: { anchor: "top-center", x: 50, y: 12, width: 84, align: "center" },
    subtitle: { anchor: "top-center", x: 50, y: 20, width: 84, align: "center" },
    buttons: { anchor: "top-center", x: 50, y: 27, width: 72, align: "center" },
  },
};

const INITIAL_LAYOUT = {
  desktop: { direction: "row", align: "left", gap: 10, wrap: true },
  tablet: null,
  mobile: { direction: "column", align: "center", gap: 9, wrap: true },
};

const INITIAL_MEDIA = {
  desktop: {
    source: "ckfinder",
    url: "/assets/mg5gt-hero-desktop.webp",
    alt: "Yellow MG5 GT in a white studio",
    objectFit: "cover",
    objectPosition: "center center",
  },
  tablet: null,
  mobile: {
    source: "ckfinder",
    url: "/assets/mg5gt-hero-mobile.webp",
    alt: "Yellow MG5 GT mobile hero",
    objectFit: "cover",
    objectPosition: "center top",
  },
};

const MEDIA_ASSETS = [
  { url: "/assets/mg5gt-hero-desktop.webp", alt: "Yellow MG5 GT in a white studio", label: "MG5 GT — Desktop" },
  { url: "/assets/mg5gt-hero-mobile.webp", alt: "Yellow MG5 GT mobile hero", label: "MG5 GT — Mobile" },
];

const INITIAL_BUTTONS = normalizeButtons([{
  id: "button-1",
  text: "Watch Video",
  actionType: "video_popup",
  videoSource: "youtube",
  videoUrl: "https://www.youtube.com/watch?v=h529sg3pEV4",
}]);

function Field({ label, children, hint }) {
  return (
    <label className="field">
      <span>{label}</span>
      {children}
      {hint && <small>{hint}</small>}
    </label>
  );
}

function NumberControl({ label, value, onChange, min = 0, max = 100, unit = "%" }) {
  return (
    <Field label={label}>
      <div className="number-control">
        <input
          type="number"
          min={min}
          max={max}
          value={value}
          onChange={(event) => onChange(Math.min(max, Math.max(min, Number(event.target.value))))}
        />
        <span>{unit}</span>
      </div>
    </Field>
  );
}

function StatusBadge({ device, inheritedFrom }) {
  const custom = device === inheritedFrom;
  return <span className={`inheritance-badge ${custom ? "custom" : ""}`}>{custom ? "Custom override" : `Inherited from ${LABELS_DEVICE[inheritedFrom]}`}</span>;
}

const LABELS_DEVICE = { desktop: "Desktop", tablet: "Tablet", mobile: "Mobile" };

function App() {
  const [device, setDevice] = useState("desktop");
  const [mode, setMode] = useState("grouped");
  const [tab, setTab] = useState("content");
  const [selected, setSelected] = useState("title");
  const [positions, setPositions] = useState(INITIAL_POSITION);
  const [copy, setCopy] = useState({ title: "MG 5 GT", subtitle: "Light Up Desire" });
  const [contentOrder, setContentOrder] = useState(["title", "subtitle", "buttons"]);
  const [visibility, setVisibility] = useState({ title: true, subtitle: true, buttons: true });
  const [buttons, setButtons] = useState(INITIAL_BUTTONS);
  const [selectedButtonId, setSelectedButtonId] = useState(INITIAL_BUTTONS[0].id);
  const [buttonLayout, setButtonLayout] = useState(INITIAL_LAYOUT);
  const [media, setMedia] = useState(INITIAL_MEDIA);
  const [modal, setModal] = useState(null);
  const [mediaError, setMediaError] = useState(false);
  const closeRef = useRef(null);
  const triggerRef = useRef(null);

  const deviceMeta = DEVICES.find((item) => item.id === device);
  const target = mode === "grouped" ? "group" : selected;
  const position = positions[device][target];
  const activeButton = buttons.find((button) => button.id === selectedButtonId) || buttons[0];
  const resolvedLayout = resolveResponsive(buttonLayout, device);
  const resolvedMedia = resolveResponsive(media, device);
  const layout = resolvedLayout.value;
  const heroMedia = resolvedMedia.value;

  useEffect(() => setMediaError(false), [heroMedia.url]);

  useEffect(() => {
    if (!modal) return undefined;
    const previousOverflow = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    const onKeyDown = (event) => event.key === "Escape" && setModal(null);
    document.addEventListener("keydown", onKeyDown);
    requestAnimationFrame(() => closeRef.current?.focus());
    return () => {
      document.body.style.overflow = previousOverflow;
      document.removeEventListener("keydown", onKeyDown);
      requestAnimationFrame(() => triggerRef.current?.focus());
    };
  }, [modal]);

  const updatePosition = (key, value) => {
    setPositions((current) => ({
      ...current,
      [device]: {
        ...current[device],
        [target]: { ...current[device][target], [key]: value },
      },
    }));
  };

  const updateButton = (key, value) => {
    setButtons((current) => current.map((button) => button.id === activeButton.id ? { ...button, [key]: value } : button));
  };

  const addButton = (source = {}) => {
    if (buttons.length >= 3) return;
    const next = normalizeButtons([{ ...source, id: `button-${Date.now()}`, text: source.text ? `${source.text} Copy` : `Button ${buttons.length + 1}` }])[0];
    setButtons((current) => [...current, next]);
    setSelectedButtonId(next.id);
  };

  const removeButton = (id) => {
    if (buttons.length === 1) return;
    const next = buttons.filter((button) => button.id !== id);
    setButtons(next);
    if (selectedButtonId === id) setSelectedButtonId(next[0].id);
  };

  const updateLayout = (key, value) => {
    setButtonLayout((current) => ({ ...current, [device]: { ...resolveResponsive(current, device).value, [key]: value } }));
  };

  const updateMedia = (changes) => {
    setMedia((current) => ({ ...current, [device]: { ...resolveResponsive(current, device).value, ...changes } }));
  };

  const openModal = (nextModal, trigger) => {
    triggerRef.current = trigger;
    setModal(nextModal);
  };

  const openButtonAction = (button, event) => {
    if (button.actionType === "video_popup") {
      const video = resolveVideoMedia(button.videoSource, button.videoUrl);
      if (video) openModal({ type: "video", ...video, title: button.text || "Video" }, event.currentTarget);
    }
    if (button.actionType === "image_popup" && isSafeMediaUrl(button.imageUrl)) {
      openModal({ type: "image", src: button.imageUrl, alt: button.imageAlt || button.text || "Popup image" }, event.currentTarget);
    }
  };

  const buttonGroupStyle = useMemo(() => ({
    flexDirection: layout.direction,
    gap: `${layout.gap}px`,
    flexWrap: layout.wrap ? "wrap" : "nowrap",
    justifyContent: layout.direction === "row" ? ({ left: "flex-start", center: "center", right: "flex-end" }[layout.align]) : undefined,
    alignItems: layout.direction === "column" ? ({ left: "flex-start", center: "center", right: "flex-end" }[layout.align]) : undefined,
  }), [layout]);

  const renderButton = (button) => {
    const icon = button.actionType === "video_popup" ? "bi-play-fill" : button.actionType === "image_popup" ? "bi-image" : "bi-arrow-up-right";
    const label = button.text || "Button";
    if (button.actionType === "link") {
      const valid = isSafeMediaUrl(button.url);
      const rel = [button.target === "_blank" && "noopener noreferrer", button.nofollow && "nofollow"].filter(Boolean).join(" ") || undefined;
      return <a key={button.id} className={`hero-button ${valid ? "" : "disabled"}`} href={valid ? button.url : "#"} target={button.target || undefined} rel={rel} onClick={(event) => !valid && event.preventDefault()}><span>{label}</span><i className={`bi ${icon}`} /></a>;
    }
    return <button key={button.id} className="hero-button" onClick={(event) => openButtonAction(button, event)}><span>{label}</span><i className={`bi ${icon}`} /></button>;
  };

  const previewElements = {
    title: <h1>{copy.title || "Untitled hero"}</h1>,
    subtitle: <p>{copy.subtitle || "Add a subtitle"}</p>,
    buttons: <div className="hero-button-group" style={buttonGroupStyle}>{buttons.map(renderButton)}</div>,
  };

  const renderVisible = (key) => visibility[key] ? previewElements[key] : null;

  return (
    <div className="builder-shell">
      <header className="topbar">
        <div className="topbar-left">
          <div className="brand-mark"><i className="bi bi-grid-1x2-fill" /></div>
          <div className="brand-copy"><strong>Phoenix</strong><span>Page Builder 2.3</span></div>
          <div className="crumb"><span>Website</span><i className="bi bi-chevron-right" /><strong>MG5 GT Hero Prototype</strong></div>
        </div>
        <div className="device-switcher" aria-label="Responsive preview">
          {DEVICES.map((item) => (
            <button key={item.id} className={device === item.id ? "active" : ""} title={item.label} aria-label={item.label} onClick={() => setDevice(item.id)}>
              <i className={`bi ${item.icon}`} /><span>{item.label}</span>
            </button>
          ))}
        </div>
        <div className="topbar-actions">
          <button className="icon-button" title="Undo is disabled in this prototype" disabled><i className="bi bi-arrow-counterclockwise" /></button>
          <button className="secondary-button" disabled title="Preview navigation is outside this prototype"><i className="bi bi-play-circle" /> Preview</button>
          <button className="primary-button" disabled title="This prototype does not persist data"><i className="bi bi-cloud-arrow-up" /> Save</button>
        </div>
      </header>

      <main className="workspace">
        <aside className="inspector">
          <div className="panel-heading">
            <button className="back-button" aria-label="Back"><i className="bi bi-chevron-left" /></button>
            <div><strong>Responsive Hero Banner</strong><span>Widget settings</span></div>
            <span className="prototype-badge">Prototype</span>
          </div>

          <nav className="tabs" aria-label="Widget settings">
            {["content", "style", "advanced"].map((item) => <button key={item} className={tab === item ? "active" : ""} onClick={() => setTab(item)}>{item}</button>)}
          </nav>

          <div className="panel-scroll">
            {tab === "content" && (
              <>
                <section className="panel-section first">
                  <div className="section-title"><span>Content behavior</span><i className="bi bi-chevron-up" /></div>
                  <div className="mode-toggle" role="group" aria-label="Content positioning mode">
                    <button className={mode === "grouped" ? "active" : ""} onClick={() => setMode("grouped")}><i className="bi bi-bounding-box" />Grouped</button>
                    <button className={mode === "independent" ? "active" : ""} onClick={() => setMode("independent")}><i className="bi bi-layers" />Independent</button>
                  </div>
                  <p className="helper-copy">{mode === "grouped" ? "Title, Subtitle, dan Button Group mengikuti satu flow." : "Setiap blok memiliki posisi responsif sendiri."}</p>
                </section>

                <section className="panel-section">
                  <div className="section-title"><span>Content</span><i className="bi bi-chevron-up" /></div>
                  <Field label="Title"><input value={copy.title} onChange={(event) => setCopy({ ...copy, title: event.target.value })} /></Field>
                  <Field label="Subtitle"><input value={copy.subtitle} onChange={(event) => setCopy({ ...copy, subtitle: event.target.value })} /></Field>
                  {mode === "grouped" && (
                    <div className="content-order">
                      <div className="control-label">Content order</div>
                      {contentOrder.map((item, index) => (
                        <div className="order-row" key={item}>
                          <i className="bi bi-grip-vertical" />
                          <strong>{LABELS[item]}</strong>
                          <button aria-label={`Toggle ${LABELS[item]}`} className={visibility[item] ? "visible" : ""} onClick={() => setVisibility({ ...visibility, [item]: !visibility[item] })}><i className={`bi ${visibility[item] ? "bi-eye" : "bi-eye-slash"}`} /></button>
                          <button aria-label={`Move ${LABELS[item]} up`} disabled={index === 0} onClick={() => setContentOrder(moveItem(contentOrder, item, "up"))}><i className="bi bi-arrow-up" /></button>
                          <button aria-label={`Move ${LABELS[item]} down`} disabled={index === contentOrder.length - 1} onClick={() => setContentOrder(moveItem(contentOrder, item, "down"))}><i className="bi bi-arrow-down" /></button>
                        </div>
                      ))}
                    </div>
                  )}
                </section>

                <section className="panel-section">
                  <div className="section-title"><span>Buttons</span><span className="count-badge">{buttons.length} / 3</span></div>
                  <div className="button-list">
                    {buttons.map((button, index) => (
                      <div key={button.id} className={`button-row ${activeButton.id === button.id ? "active" : ""}`} onClick={() => setSelectedButtonId(button.id)}>
                        <i className="bi bi-grip-vertical" /><strong>{button.text || `Button ${index + 1}`}</strong>
                        <button aria-label={`Duplicate ${button.text || "button"}`} disabled={buttons.length >= 3} onClick={(event) => { event.stopPropagation(); addButton(button); }}><i className="bi bi-copy" /></button>
                        <button aria-label={`Remove ${button.text || "button"}`} disabled={buttons.length === 1} onClick={(event) => { event.stopPropagation(); removeButton(button.id); }}><i className="bi bi-trash3" /></button>
                      </div>
                    ))}
                  </div>
                  <button className="add-button" disabled={buttons.length >= 3} onClick={() => addButton()}><i className="bi bi-plus-lg" /> Add Button</button>

                  <div className="button-editor">
                    <Field label="Button text"><input value={activeButton.text} onChange={(event) => updateButton("text", event.target.value)} /></Field>
                    <Field label="Action type">
                      <select value={activeButton.actionType} onChange={(event) => updateButton("actionType", event.target.value)}>
                        <option value="link">Link</option>
                        <option value="video_popup">Video Popup</option>
                        <option value="image_popup">Image Popup</option>
                      </select>
                    </Field>

                    {activeButton.actionType === "link" && (
                      <>
                        <Field label="URL" hint="HTTP(S) atau local path."><input type="url" placeholder="https://example.com/page" value={activeButton.url} onChange={(event) => updateButton("url", event.target.value)} /></Field>
                        <label className="check-row"><input type="checkbox" checked={activeButton.target === "_blank"} onChange={(event) => updateButton("target", event.target.checked ? "_blank" : "")} /><span>Open in new window</span></label>
                        <label className="check-row"><input type="checkbox" checked={activeButton.nofollow} onChange={(event) => updateButton("nofollow", event.target.checked)} /><span>Add nofollow</span></label>
                        <Field label="Custom attributes" hint="Disimpan sebagai data prototype; belum dirender."><input placeholder="key|value" value={activeButton.customAttributes || ""} onChange={(event) => updateButton("customAttributes", event.target.value)} /></Field>
                      </>
                    )}

                    {activeButton.actionType === "video_popup" && (
                      <>
                        <Field label="Video source">
                          <select value={activeButton.videoSource} onChange={(event) => updateButton("videoSource", event.target.value)}>
                            <option value="youtube">YouTube</option><option value="vimeo">Vimeo</option><option value="dailymotion">Dailymotion</option><option value="self_hosted">Self-hosted</option>
                          </select>
                        </Field>
                        <Field label="Video URL" hint={resolveVideoMedia(activeButton.videoSource, activeButton.videoUrl) ? "Source recognized." : "Masukkan URL video yang valid."}><input type="url" value={activeButton.videoUrl} onChange={(event) => updateButton("videoUrl", event.target.value)} /></Field>
                      </>
                    )}

                    {activeButton.actionType === "image_popup" && (
                      <>
                        <Field label="Image source">
                          <select value={activeButton.imageSource} onChange={(event) => updateButton("imageSource", event.target.value)}><option value="ckfinder">CKFinder / Media Library</option><option value="url">External URL</option></select>
                        </Field>
                        {activeButton.imageSource === "ckfinder" ? (
                          <button className="media-picker-button" onClick={(event) => openModal({ type: "picker", target: "button" }, event.currentTarget)}><i className="bi bi-folder2-open" /> Choose popup image</button>
                        ) : (
                          <Field label="External image URL" hint="Remote host dapat menolak hotlinking."><input type="url" value={activeButton.imageUrl} onChange={(event) => updateButton("imageUrl", event.target.value)} /></Field>
                        )}
                        {activeButton.imageUrl && <div className="selected-media"><img src={activeButton.imageUrl} alt="Selected popup" /><span>{activeButton.imageUrl}</span></div>}
                        <Field label="Alt text"><input value={activeButton.imageAlt} onChange={(event) => updateButton("imageAlt", event.target.value)} /></Field>
                      </>
                    )}
                  </div>
                </section>

                <section className="panel-section">
                  <div className="section-title"><span>Responsive position</span><span className="device-chip"><i className={`bi ${deviceMeta.icon}`} />{deviceMeta.label}</span></div>
                  {mode === "independent" && (
                    <div className="target-tabs">
                      {["title", "subtitle", "buttons"].map((item) => <button key={item} className={selected === item ? "active" : ""} onClick={() => setSelected(item)}>{LABELS[item]}</button>)}
                    </div>
                  )}
                  <div className="active-target"><span>Editing</span><strong>{LABELS[target]}</strong></div>
                  <div className="control-label">Anchor point</div>
                  <div className="anchor-grid" aria-label="Anchor point">
                    {ANCHORS.map((anchor) => <button key={anchor} className={position.anchor === anchor ? "active" : ""} aria-label={anchor} title={anchor} onClick={() => updatePosition("anchor", anchor)}><span /></button>)}
                  </div>
                  <div className="control-grid">
                    <NumberControl label="Horizontal (X)" value={position.x} onChange={(value) => updatePosition("x", value)} />
                    <NumberControl label="Vertical (Y)" value={position.y} onChange={(value) => updatePosition("y", value)} />
                  </div>
                  <NumberControl label="Content width" value={position.width} min={10} onChange={(value) => updatePosition("width", value)} />
                  <Field label="Alignment"><div className="alignment-control">{["left", "center", "right"].map((align) => <button key={align} className={position.align === align ? "active" : ""} title={align} aria-label={`${align} alignment`} onClick={() => updatePosition("align", align)}><i className={`bi bi-text-${align}`} /></button>)}</div></Field>
                  <button className="reset-button" onClick={() => setPositions((current) => ({ ...current, [device]: { ...current[device], [target]: { ...INITIAL_POSITION[device][target] } } }))}><i className="bi bi-arrow-counterclockwise" /> Reset {deviceMeta.label}</button>
                </section>

                <section className="panel-section">
                  <div className="section-title"><span>Button Group layout</span><StatusBadge device={device} inheritedFrom={resolvedLayout.inheritedFrom} /></div>
                  <Field label="Direction"><div className="segmented-control">{["row", "column"].map((value) => <button key={value} className={layout.direction === value ? "active" : ""} onClick={() => updateLayout("direction", value)}>{value}</button>)}</div></Field>
                  <Field label="Alignment"><div className="segmented-control three">{["left", "center", "right"].map((value) => <button key={value} className={layout.align === value ? "active" : ""} onClick={() => updateLayout("align", value)}>{value}</button>)}</div></Field>
                  <NumberControl label="Gap" value={layout.gap} min={0} max={48} unit="px" onChange={(value) => updateLayout("gap", value)} />
                  <label className="check-row"><input type="checkbox" checked={layout.wrap} onChange={(event) => updateLayout("wrap", event.target.checked)} /><span>Wrap buttons</span></label>
                  {device !== "desktop" && <button className="reset-button" disabled={buttonLayout[device] == null} onClick={() => setButtonLayout((current) => ({ ...current, [device]: null }))}><i className="bi bi-arrow-counterclockwise" /> Reset override</button>}
                </section>

                <section className="panel-section">
                  <div className="section-title"><span>Responsive media</span><StatusBadge device={device} inheritedFrom={resolvedMedia.inheritedFrom} /></div>
                  <Field label="Image source"><select value={heroMedia.source} onChange={(event) => updateMedia({ source: event.target.value })}><option value="ckfinder">CKFinder / Media Library</option><option value="url">External URL</option></select></Field>
                  {heroMedia.source === "ckfinder" ? (
                    <button className="media-picker-button" onClick={(event) => openModal({ type: "picker", target: "hero" }, event.currentTarget)}><i className="bi bi-folder2-open" /> Choose from Media Library</button>
                  ) : (
                    <Field label="External image URL" hint="Hanya HTTP(S); remote host dapat menolak hotlinking."><input type="url" value={heroMedia.url} onChange={(event) => updateMedia({ url: event.target.value })} /></Field>
                  )}
                  <div className="selected-media"><img src={heroMedia.url} alt="Selected hero" /><span>{heroMedia.url}</span></div>
                  <Field label="Alt text"><input value={heroMedia.alt} onChange={(event) => updateMedia({ alt: event.target.value })} /></Field>
                  <div className="control-grid">
                    <Field label="Object fit"><select value={heroMedia.objectFit} onChange={(event) => updateMedia({ objectFit: event.target.value })}><option value="cover">Cover</option><option value="contain">Contain</option><option value="fill">Fill</option></select></Field>
                    <Field label="Object position"><select value={heroMedia.objectPosition} onChange={(event) => updateMedia({ objectPosition: event.target.value })}><option value="left center">Left</option><option value="center center">Center</option><option value="right center">Right</option><option value="center top">Top</option><option value="center bottom">Bottom</option></select></Field>
                  </div>
                  {device !== "desktop" && <button className="reset-button" disabled={media[device] == null} onClick={() => setMedia((current) => ({ ...current, [device]: null }))}><i className="bi bi-arrow-counterclockwise" /> Reset override</button>}
                </section>
              </>
            )}

            {tab === "style" && <section className="panel-section first"><div className="section-title"><span>Style preview</span><i className="bi bi-chevron-up" /></div><p className="empty-tab-copy">Typography, colors, spacing, button Normal/Hover, dan modal styling akan mengikuti pola kontrol v2.3 pada implementasi produksi.</p><div className="style-swatch-row"><span>Text color</span><span className="swatch dark" />#292d32</div><div className="style-swatch-row"><span>Button</span><span className="swatch button" />#30343a</div></section>}
            {tab === "advanced" && <section className="panel-section first"><div className="section-title"><span>Advanced</span><i className="bi bi-chevron-up" /></div><p className="empty-tab-copy">Widget produksi tetap mewarisi margin, padding, motion effects, background, border, responsive visibility, attributes, dan custom CSS v2.3.</p></section>}
          </div>
        </aside>

        <section className="canvas-region">
          <div className="canvas-toolbar">
            <div className="canvas-meta"><span>{deviceMeta.label}</span><i className="bi bi-dot" /><span>{deviceMeta.width}</span><em>Live canvas</em></div>
            <div className="canvas-breadcrumb"><i className="bi bi-file-earmark" />Page<i className="bi bi-chevron-right" /><strong>Responsive Hero Banner</strong></div>
            <div className="canvas-tools"><button title="Canvas grid" aria-label="Canvas grid"><i className="bi bi-grid-3x3-gap" /></button><button title="Reset zoom" aria-label="Reset zoom"><i className="bi bi-aspect-ratio" /></button><span>100%</span></div>
          </div>

          <div className="stage">
            <div className={`webpage-frame ${device}`}>
              <div className={`hero-canvas ${device}`}>
                {!mediaError && isSafeMediaUrl(heroMedia.url) ? <img className="hero-media" src={heroMedia.url} alt={heroMedia.alt} style={{ objectFit: heroMedia.objectFit, objectPosition: heroMedia.objectPosition }} onError={() => setMediaError(true)} /> : <div className="media-error"><i className="bi bi-image" /><strong>Hero image unavailable</strong><span>Check the selected media URL.</span></div>}

                {mode === "grouped" ? (
                  <div className="content-group selection-outline" style={positionStyle(positions[device].group)}>
                    <span className="selection-label">Content group</span>
                    {contentOrder.map((key) => <div className={`content-block ${key}`} key={key}>{renderVisible(key)}</div>)}
                  </div>
                ) : (
                  ["title", "subtitle", "buttons"].filter((key) => visibility[key]).map((key) => (
                    <div key={key} className={`independent-element ${selected === key ? "selection-outline" : ""}`} style={positionStyle(positions[device][key])} onClick={() => setSelected(key)}>
                      {selected === key && <span className="selection-label">{LABELS[key]}</span>}
                      {previewElements[key]}
                    </div>
                  ))
                )}
              </div>
            </div>
          </div>

          <div className="decision-bar">
            <div><i className={`bi ${mode === "grouped" ? "bi-bounding-box" : "bi-layers"}`} /><span>Current behavior</span><strong>{mode === "grouped" ? "Grouped positioning" : "Independent positioning"}</strong></div>
            <p>{mode === "grouped" ? "Order mengikuti flow; satu positioning block per device." : "Title, Subtitle, dan Button Group diposisikan terpisah."}</p>
          </div>
        </section>
      </main>

      {modal && (
        <div className="modal-backdrop" onMouseDown={(event) => event.target === event.currentTarget && setModal(null)}>
          <div className={`prototype-modal ${modal.type === "picker" ? "picker-modal" : "media-modal"}`} role="dialog" aria-modal="true" aria-label={modal.type === "picker" ? "Choose media" : modal.title || "Media preview"}>
            <button ref={closeRef} className="modal-close" aria-label="Close modal" onClick={() => setModal(null)}><i className="bi bi-x-lg" /></button>
            {modal.type === "picker" && (
              <><div className="modal-heading"><span>CKFinder simulation</span><h2>Choose media</h2><p>Prototype ini memakai aset lokal tanpa membuka storage produksi.</p></div><div className="asset-grid">{MEDIA_ASSETS.map((asset) => <button key={asset.url} onClick={() => { if (modal.target === "hero") updateMedia({ source: "ckfinder", url: asset.url, alt: asset.alt }); else { updateButton("imageSource", "ckfinder"); updateButton("imageUrl", asset.url); updateButton("imageAlt", asset.alt); } setModal(null); }}><img src={asset.url} alt={asset.alt} /><span>{asset.label}</span></button>)}</div></>
            )}
            {modal.type === "image" && <img className="modal-image" src={modal.src} alt={modal.alt} />}
            {modal.type === "video" && modal.kind === "iframe" && <iframe className="modal-video" src={modal.src} title={modal.title} allow="autoplay; fullscreen; picture-in-picture" allowFullScreen />}
            {modal.type === "video" && modal.kind === "video" && <video className="modal-video" src={modal.src} controls autoPlay />}
          </div>
        </div>
      )}
    </div>
  );
}

export { App };
