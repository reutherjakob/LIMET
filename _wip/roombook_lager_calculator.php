<?php
require_once '_utils/_utils.php';
check_login();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Regalplaner — Lager &amp; Archiv</title>
    <style>
        :root{
            --bg:#EAEEF2; --paper:#F7F9FB; --ink:#1E2A33; --line:#B7C4CE;
            --grid:#DCE4EB; --shelf:#C97A2B; --shelf-edge:#9C5C1B;
            --shelf2:#3E7CB1; --shelf2-edge:#2C5D87;
            --column:#2B3742; --door:#2563EB; --swing:#2563EB;
            --panel:#ffffff; --muted:#64748b; --border:#cbd5e1; --aisle:#E2ECF4;
        }
        *{box-sizing:border-box}
        html,body{margin:0}
        body{background:var(--bg);color:var(--ink);
            font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;padding:16px;}
        .wrap{max-width:1120px;margin:0 auto}
        h1{font-size:20px;margin:0 0 2px;letter-spacing:-.01em}
        .sub{font-size:13px;color:var(--muted);margin:0 0 16px}
        .layout{display:grid;grid-template-columns:320px 1fr;gap:16px}
        @media(max-width:880px){.layout{grid-template-columns:1fr}}
        .panel{background:var(--panel);border:1px solid #e2e8f0;border-radius:10px;padding:12px}
        .panel.soft{background:rgba(255,255,255,.7)}
        .cap{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin:0 0 10px}
        .grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
        label.fld{display:flex;flex-direction:column;gap:4px}
        label.fld span{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted)}
        .inp{display:flex;align-items:center;border:1px solid var(--border);border-radius:6px;background:#fff}
        .inp:focus-within{border-color:#64748b}
        .inp input{width:100%;border:0;background:transparent;padding:7px 8px;font:13px/1.2 ui-monospace,SFMono-Regular,Menlo,monospace;color:var(--ink);outline:none}
        .inp .suf{padding-right:8px;font-size:11px;color:#94a3b8}
        select{width:100%;border:1px solid var(--border);border-radius:6px;background:#fff;padding:7px 8px;font-size:12px;color:#334155;margin-top:8px}
        .btnrow{display:flex;gap:6px;flex-wrap:wrap;align-items:center}
        .btn{border:1px solid var(--border);border-radius:6px;background:#fff;color:#475569;padding:7px 10px;font-size:12px;cursor:pointer;transition:.15s}
        .btn:hover{border-color:#94a3b8}
        .btn.active{border-color:#334155;background:#1E2A33;color:#fff}
        .btn.sm{padding:5px 8px;font-size:11px}
        .btn.disabled{opacity:.4;pointer-events:none}
        .seg{display:inline-flex;gap:4px;align-items:center;padding:0 2px}
        .seg .lab{font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-right:2px}
        .typehead{display:flex;align-items:center;justify-content:space-between;margin:2px 0 6px}
        .typehead .cap{margin:0}
        .switch{display:inline-flex;align-items:center;gap:6px;font-size:11px;color:var(--muted);cursor:pointer;user-select:none}
        .switch input{accent-color:var(--shelf2)}
        .typeB{opacity:.45;pointer-events:none;transition:.15s}
        .typeB.on{opacity:1;pointer-events:auto}
        .swatch{display:inline-block;width:10px;height:10px;border-radius:2px;vertical-align:-1px;margin-right:5px}
        .walls{display:grid;grid-template-columns:1fr 1fr;gap:8px}
        .wallchk{display:flex;align-items:center;gap:7px;border:1px solid var(--border);border-radius:6px;padding:7px 9px;font-size:12px;color:#334155;cursor:pointer;user-select:none}
        .wallchk input{accent-color:#334155}
        .wallchk.on{border-color:#334155;background:#f1f5f9}
        .vcard{display:block;width:100%;text-align:left;border:1px solid var(--border);border-radius:8px;background:#fff;padding:8px 10px;margin-bottom:8px;cursor:pointer;transition:.15s}
        .vcard:hover{border-color:#94a3b8}
        .vcard.active{border-color:#334155;box-shadow:0 0 0 1px #334155 inset}
        .vrow{display:flex;align-items:center;gap:8px;margin-bottom:3px}
        .vrank{display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:5px;background:#1E2A33;color:#fff;font-size:11px;font-weight:600;flex:none}
        .vlabel{font-size:12px;color:#334155;font-weight:600}
        .vstats{font-size:11px;color:var(--muted)}
        .vstats b{font-family:ui-monospace,monospace;color:#334155}
        .vtag{display:inline-block;font-size:10px;font-weight:600;color:#334155;background:#e2e8f0;border-radius:4px;padding:1px 5px;margin-left:6px}
        .planhead{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px}
        .hint{font-size:11px;color:#94a3b8}
        .note{font-size:12px;color:#8a5a1a;background:#fdf3e6;border:1px solid #f0dcc0;border-radius:6px;padding:7px 9px;margin-bottom:8px}
        .note.ok{color:#3f6212;background:#f2f8ea;border-color:#dcebc7}
        .stage{display:flex;justify-content:center;overflow:auto;border-radius:6px;background:var(--paper);user-select:none}
        svg{display:block}
        #plan{touch-action:none}
        .legend{display:flex;flex-wrap:wrap;gap:6px 16px;margin-top:8px;font-size:11px;color:var(--muted)}
        .legend .sw{display:inline-block;width:12px;height:12px;border-radius:3px;vertical-align:-2px;margin-right:5px}
        .cmp{width:100%;border-collapse:collapse;font-size:12px}
        .cmp th,.cmp td{padding:5px 8px;text-align:right;border-bottom:1px solid #eef2f6}
        .cmp th{font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);font-weight:600}
        .cmp td:first-child,.cmp th:first-child{text-align:left;color:#475569}
        .cmp .num{font-family:ui-monospace,monospace;color:#334155}
        .delta{font-family:ui-monospace,monospace;font-weight:600}
        .delta.up{color:#3f6212}
        .delta.down{color:#b91c1c}
        .delta.zero{color:#94a3b8}
        .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:16px}
        @media(max-width:560px){.stats{grid-template-columns:1fr 1fr}}
        .stat{border:1px solid #e2e8f0;background:#fff;border-radius:6px;padding:8px 12px}
        .stat .k{font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted)}
        .stat .v{font:18px/1.2 ui-monospace,monospace;color:#334155}
        .stat .v .u{font-size:12px;color:#94a3b8;margin-left:4px;font-family:inherit}
        .tools{display:flex;gap:6px;flex-wrap:wrap}
        @media print{
            body{background:#fff;padding:0}
            .col-controls,.planhead .tools,.planhead .btnrow,.hint,.legend{display:none}
            .layout{grid-template-columns:1fr}
        }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Regalplaner</h1>
    <p class="sub">Raum maximal mit Regalen füllen — erreichbar, Gangbreite eingehalten, um Säulen herum geplant. Zwei Regaltypen mischbar. Automatik <b>oder</b> von Hand planen und beides direkt vergleichen.</p>

    <div class="layout">
        <div class="col-controls" style="display:flex;flex-direction:column;gap:16px">
            <section class="panel soft">
                <p class="cap">Raum</p>
                <div class="grid2">
                    <label class="fld"><span>Breite</span><div class="inp"><input id="roomW" type="number" min="1000" step="10"><span class="suf">mm</span></div></label>
                    <label class="fld"><span>Tiefe</span><div class="inp"><input id="roomH" type="number" min="1000" step="10"><span class="suf">mm</span></div></label>
                    <label class="fld"><span>Wandabstand</span><div class="inp"><input id="wallGap" type="number" min="0" step="10"><span class="suf">mm</span></div></label>
                    <label class="fld"><span>Säulen-Freiraum</span><div class="inp"><input id="colClear" type="number" min="0" step="10"><span class="suf">mm</span></div></label>
                </div>
            </section>

            <section class="panel soft">
                <p class="cap"><span class="swatch" style="background:var(--shelf)"></span>Regaltyp A</p>
                <div class="grid2">
                    <label class="fld"><span>Breite</span><div class="inp"><input id="type1W" type="number" min="200" step="10"><span class="suf">mm</span></div></label>
                    <label class="fld"><span>Tiefe</span><div class="inp"><input id="type1D" type="number" min="200" step="10"><span class="suf">mm</span></div></label>
                </div>
                <select id="shelfPreset" style="margin-top:10px"><option value="" disabled selected>Vorlage für Typ A…</option></select>

                <div class="typehead" style="margin-top:14px">
                    <p class="cap"><span class="swatch" style="background:var(--shelf2)"></span>Regaltyp B</p>
                    <label class="switch"><input type="checkbox" id="useType2"> aktiv</label>
                </div>
                <div class="grid2 typeB" id="typeBBox">
                    <label class="fld"><span>Breite</span><div class="inp"><input id="type2W" type="number" min="200" step="10"><span class="suf">mm</span></div></label>
                    <label class="fld"><span>Tiefe</span><div class="inp"><input id="type2D" type="number" min="200" step="10"><span class="suf">mm</span></div></label>
                </div>
                <div style="font-size:11px;color:var(--muted);margin-top:6px">Gleiche Tiefe → beide Breiten mischen sich in einem Band. Andere Tiefe → eigene Bänder.</div>
            </section>

            <section class="panel soft">
                <p class="cap">Gang</p>
                <label class="fld"><span>Gangbreite</span><div class="inp"><input id="aisle" type="number" min="400" step="10"><span class="suf">mm</span></div></label>
                <select id="aislePreset"><option value="" disabled selected>Gang-Vorlage wählen…</option></select>
            </section>

            <section class="panel soft">
                <p class="cap">Entlang Wand möblieren</p>
                <div class="walls" id="wallBox">
                    <label class="wallchk" data-wall="top"><input type="checkbox">Oben</label>
                    <label class="wallchk" data-wall="bottom"><input type="checkbox">Unten</label>
                    <label class="wallchk" data-wall="left"><input type="checkbox">Links</label>
                    <label class="wallchk" data-wall="right"><input type="checkbox">Rechts</label>
                </div>
                <div style="font-size:11px;color:var(--muted);margin-top:8px"><b>Nichts angekreuzt</b> = Automatik prüft frei auch die Perimeter-Varianten (alle / Längs / Quer) und zeigt die beste. <b>Angekreuzt</b> = harte Vorgabe: es werden nur Layouts gezeigt, die genau diese Wände durchmöblieren. Restecken an den Wandenden füllt die Automatik mit Eckregalen (Kennzeichen „+N Eck").</div>
            </section>

            <section class="panel soft">
                <p class="cap">Varianten · Top 3 (Auto)</p>
                <div id="variants"></div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px">Erst maximale Regalfläche, dann kürzester Weg. Antippen zum Anzeigen.</div>
            </section>

            <section class="panel soft">
                <p class="cap">Manueller Grundriss</p>
                <div style="font-size:12px;color:#475569;line-height:1.55">
                    <b>＋ Regal</b>: Klick setzt ein Regal.&nbsp; <b>＋ Reihe</b>: ziehen füllt eine Reihe.&nbsp; <b>✥ Verschieben</b>: Regal ziehen.&nbsp; <b>✕ Löschen</b>: entfernt Regal/Säule/Tür. Typ &amp; Achse oben wählen.
                </div>
                <div style="font-size:11px;color:var(--muted);margin-top:8px">Tipp: <b>„Auto → Manuell"</b> übernimmt die aktuell gewählte Auto-Variante als Startpunkt — dann von Hand feilen und im Vergleich unten sehen, ob du sie schlägst.</div>
            </section>

            <section class="panel soft">
                <p class="cap">Daten · Export / Import</p>
                <div class="btnrow" style="margin-bottom:8px">
                    <button class="btn sm" id="btnExport">Export</button>
                    <button class="btn sm" id="btnCopy">Kopieren</button>
                    <button class="btn sm" id="btnImport">Import</button>
                </div>
                <textarea id="dataBox" spellcheck="false" placeholder="Hier erscheint der Grundriss als JSON (Export) — oder JSON einfügen und Import drücken."
                          style="width:100%;height:120px;border:1px solid var(--border);border-radius:6px;padding:8px;font:11px/1.4 ui-monospace,SFMono-Regular,Menlo,monospace;color:#334155;resize:vertical;background:#fff"></textarea>
                <div id="dataMsg" style="font-size:11px;color:var(--muted);margin-top:6px">Export erzeugt den kompletten Grundriss inkl. aktuell angezeigter Regale. Diesen Text kannst du mir zum Fehler-Aufzeigen geben.</div>
            </section>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px">
            <div class="panel">
                <div class="planhead">
                    <div class="btnrow" id="modeSeg">
                        <button class="btn" data-mode="column">＋ Säule</button>
                        <button class="btn" data-mode="door">＋ Tür</button>
                        <button class="btn" data-mode="shelf">＋ Regal</button>
                        <button class="btn" data-mode="row">＋ Reihe</button>
                        <button class="btn" data-mode="move">✥ Verschieben</button>
                        <button class="btn" data-mode="delete">✕ Löschen</button>
                    </div>
                    <div class="tools">
                        <button class="btn" id="btnPng">PNG</button>
                        <button class="btn" id="btnPrint">Drucken</button>
                    </div>
                </div>
                <div class="planhead" style="margin-top:-2px">
                    <div class="btnrow">
                        <span class="seg"><span class="lab">Typ</span>
                            <button class="btn sm active" data-ptype="0">A</button>
                            <button class="btn sm" data-ptype="1" id="ptypeB">B</button></span>
                        <span class="seg"><span class="lab">Achse</span>
                            <button class="btn sm active" data-orient="h">↔</button>
                            <button class="btn sm" data-orient="v">↕</button></span>
                        <span class="seg"><span class="lab">Ansicht</span>
                            <button class="btn sm active" data-shown="auto">Auto</button>
                            <button class="btn sm" data-shown="manual">Manuell</button></span>
                    </div>
                    <div class="tools">
                        <button class="btn sm" id="btnAdopt">Auto → Manuell</button>
                        <button class="btn sm" id="btnClearManual">Manuell leeren</button>
                    </div>
                </div>
                <div id="note" class="note ok" style="display:none"></div>
                <div class="hint" id="hint" style="margin-bottom:8px"></div>
                <div class="stage"><svg id="plan" xmlns="http://www.w3.org/2000/svg"></svg></div>
                <div class="legend">
                    <span><span class="sw" style="background:var(--shelf)"></span>Regal A</span>
                    <span><span class="sw" style="background:var(--shelf2)"></span>Regal B</span>
                    <span><span class="sw" style="background:#fff;border:1px dashed #b08">…</span>nicht erreichbar</span>
                    <span><span class="sw" style="background:var(--column)"></span>Säule</span>
                    <span><span class="sw" style="background:var(--door)"></span>Tür + Schwenk</span>
                    <span><span class="sw" style="background:var(--aisle)"></span>Quergang</span>
                </div>
            </div>

            <div class="panel">
                <p class="cap" style="margin-bottom:8px">Vergleich · Auto ↔ Manuell</p>
                <table class="cmp" id="compare"></table>
                <div style="font-size:11px;color:var(--muted);margin-top:8px">Δ grün = Manuell besser. Auto = aktuell gewählte Variante links.</div>
            </div>

            <div class="stats">
                <div class="stat"><div class="k">Regale erreichbar</div><div class="v"><span id="stCount">0</span><span class="u">Stk</span></div></div>
                <div class="stat"><div class="k">Flächennutzung</div><div class="v"><span id="stUtil">0</span><span class="u">%</span></div></div>
                <div class="stat"><div class="k">Regalmeter</div><div class="v"><span id="stLin">0</span><span class="u">m</span></div></div>
                <div class="stat"><div class="k">Ø Weg zur Tür</div><div class="v"><span id="stWalk">0</span><span class="u">m</span></div></div>
            </div>
        </div>
    </div>
</div>

<script>
    "use strict";

    /* ---------------------------------------------------------------------
       PRESETS + STATE
    --------------------------------------------------------------------- */
    const SHELF_PRESETS = [
        { label: "Fachbodenregal 1000 × 500", w: 1000, d: 500 },
        { label: "Fachbodenregal 1300 × 600", w: 1300, d: 600 },
        { label: "Weitspannregal 1500 × 600", w: 1500, d: 600 },
        { label: "Archivregal 1000 × 300", w: 1000, d: 300 },
        { label: "Palettenregal 2700 × 1100", w: 2700, d: 1100 },
    ];
    const AISLE_PRESETS = [
        { label: "Person (800)", v: 800 },
        { label: "Sackkarre (1000)", v: 1000 },
        { label: "Hubwagen (1200)", v: 1200 },
        { label: "Gabelstapler (1500)", v: 1500 },
        { label: "Stapler breit (2800)", v: 2800 },
    ];

    const SNAP = 50; // mm-Raster fürs manuelle Setzen
    const DEFAULT_HINT = "In den Plan klicken/ziehen zum Bearbeiten. Variante links oder Ansicht oben wählen.";

    const state = {
        roomW: 12000, roomH: 8000, aisle: 1200,
        type1W: 1200, type1D: 600,
        type2W: 800,  type2D: 600, useType2: false,
        wallGap: 0, colClear: 0,
        walls: { top:false, bottom:false, left:false, right:false },
        columns: [],
        doors: [{ id: 1, wall: "left", pos: 4000, width: 1200 }],
        mode: "column", selected: 0,
        // manuell:
        manual: [], shown: "auto", placeType: 0, placeOrient: "h",
    };
    let nextId = 2;

    /* ---------------------------------------------------------------------
       SMALL HELPERS
    --------------------------------------------------------------------- */
    const $ = (id) => document.getElementById(id);
    const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
    const snap = (v) => Math.round(v/SNAP)*SNAP;
    const rectsOverlap = (a,b) => a.x < b.x+b.w && a.x+a.w > b.x && a.y < b.y+b.h && a.y+a.h > b.y;
    const pointInRect = (px,py,r) => px>=r.x && px<=r.x+r.w && py>=r.y && py<=r.y+r.h;

    function enabledTypes(p){
        const t=[{w:p.type1W, d:p.type1D, type:0}];
        if(p.useType2) t.push({w:p.type2W, d:p.type2D, type:1});
        return t;
    }
    function typesForDepth(p, bd){
        return enabledTypes(p).filter(t=>Math.abs(t.d-bd)<0.5).map(t=>({w:t.w,type:t.type}));
    }
    function typeDims(p, t){
        return t===1 ? {w:p.type2W, d:p.type2D} : {w:p.type1W, d:p.type1D};
    }

    function doorZone(d, W, H, depth){
        const h = d.width/2;
        if(d.wall==="top")    return {x:d.pos-h, y:0, w:d.width, h:depth};
        if(d.wall==="bottom") return {x:d.pos-h, y:H-depth, w:d.width, h:depth};
        if(d.wall==="left")   return {x:0, y:d.pos-h, w:depth, h:d.width};
        return {x:W-depth, y:d.pos-h, w:depth, h:d.width};
    }

    // Türschwenk (Viertelkreis): Mittelpunkt am Türangel, Radius = Türbreite
    function doorSwing(d, p){
        const r=d.width; let cx,cy,dirX,dirY;
        if(d.wall==="top"){    cx=d.pos-r/2; cy=0;        dirX=1;  dirY=1; }
        else if(d.wall==="bottom"){ cx=d.pos-r/2; cy=p.roomH; dirX=1; dirY=-1; }
        else if(d.wall==="left"){   cx=0;        cy=d.pos-r/2; dirX=1; dirY=1; }
        else {                 cx=p.roomW;  cy=d.pos-r/2; dirX=-1; dirY=1; }
        return {cx,cy,r,dirX,dirY,wall:d.wall};
    }
    function rectHitsSwing(s, sw){
        const nx=clamp(sw.cx, s.x, s.x+s.w), ny=clamp(sw.cy, s.y, s.y+s.h);
        const dx=nx-sw.cx, dy=ny-sw.cy;
        if(dx*dx+dy*dy > sw.r*sw.r) return false;                 // außerhalb des Radius
        const qx = sw.dirX>0 ? (s.x+s.w>sw.cx) : (s.x<sw.cx);     // richtiger Quadrant?
        const qy = sw.dirY>0 ? (s.y+s.h>sw.cy) : (s.y<sw.cy);
        return qx && qy;
    }

    /* ---------------------------------------------------------------------
       1D INTERVALL-WERKZEUGE + PACKER (zwei Breiten optimal füllen)
    --------------------------------------------------------------------- */
    function subtractIntervals(range, blockers){
        let free=[[range[0],range[1]]];
        const bs=blockers.filter(b=>b[1]>b[0]).sort((x,y)=>x[0]-y[0]);
        for(const [b0,b1] of bs){
            const next=[];
            for(const [s0,s1] of free){
                if(b1<=s0 || b0>=s1){ next.push([s0,s1]); continue; }
                if(b0>s0) next.push([s0, Math.min(b0,s1)]);
                if(b1<s1) next.push([Math.max(b1,s0), s1]);
            }
            free=next;
        }
        return free.filter(s=>s[1]-s[0] > 1e-6);
    }

    // Füllt [start,end] mit bis zu zwei Breiten so, dass die belegte Länge maximal ist.
    function packInterval(start, end, types){
        const L=end-start; if(L<=0 || !types.length) return [];
        const sorted=[...types].sort((a,b)=>b.w-a.w);
        let counts;
        if(sorted.length===1){
            counts=[Math.floor(L/sorted[0].w)];
        } else {
            const [t1,t2]=sorted; let best=-1, bc=[0,0];
            const maxB=Math.floor(L/t2.w);
            for(let b=0;b<=maxB;b++){
                const rem=L-b*t2.w; if(rem<0) break;
                const a=Math.floor(rem/t1.w);
                const cov=a*t1.w + b*t2.w;
                if(cov>best){ best=cov; bc=[a,b]; }
            }
            counts=bc;
        }
        const seq=[];
        sorted.forEach((t,i)=>{ for(let k=0;k<counts[i];k++) seq.push(t); });
        seq.sort((a,b)=>b.w-a.w);              // breite zuerst, dann schmale (füllt Restlänge)
        const out=[]; let a=start;
        for(const t of seq){ out.push({a0:a, w:t.w, type:t.type}); a+=t.w; }
        return out;
    }

    /* ---------------------------------------------------------------------
       KOORDINATEN A (Laufrichtung) / C (Tiefe)  <->  x / y
    --------------------------------------------------------------------- */
    function toAC(r, orient){
        return orient==="h"
            ? {a0:r.x, a1:r.x+r.w, c0:r.y, c1:r.y+r.h}
            : {a0:r.y, a1:r.y+r.h, c0:r.x, c1:r.x+r.w};
    }
    function fromAC(a0, len, cPos, depth, orient, type){
        return orient==="h"
            ? {x:a0, y:cPos, w:len, h:depth, len, type}
            : {x:cPos, y:a0, w:depth, h:len, len, type};
    }

    // Verbotene Rechtecke (Regale dürfen NICHT rein): Säulen(+Freiraum) und lokaler Türfreiraum
    function forbiddenAC(p, orient){
        const out=[];
        for(const c of p.columns)
            out.push(toAC({x:c.x-p.colClear, y:c.y-p.colClear, w:c.w+2*p.colClear, h:c.h+2*p.colClear}, orient));
        for(const d of p.doors)
            out.push(toAC(doorZone(d,p.roomW,p.roomH,p.aisle), orient));
        return out;
    }

    // Bänder in Tiefenrichtung: Einzel + k×(Gang+Doppel) + Gang + Einzel — feste Tiefe bd
    function buildBands(usableC, aisle, bd, lead){
        const bands=[]; const avail=usableC-lead;
        const k=Math.floor((avail-2*bd-aisle)/(aisle+2*bd));
        if(k>=0){ let c=lead; bands.push({c0:c,rows:1}); c+=bd;
            for(let i=0;i<k;i++){ c+=aisle; bands.push({c0:c,rows:2}); c+=2*bd; }
            c+=aisle; bands.push({c0:c,rows:1});
        } else if(avail>=bd){ bands.push({c0:lead,rows:1}); }
        return bands;
    }

    /* ---------------------------------------------------------------------
       KERN-GENERATOR: Bänder + Quergänge, säulen-/türbewusst je Lane
    --------------------------------------------------------------------- */
    function generateBands(opts, p){
        const {orient, lead=0, spines=[], bd, region} = opts;
        const {roomW,roomH,aisle,wallGap} = p;
        const A = orient==="h" ? roomW : roomH;
        const C = orient==="h" ? roomH : roomW;
        const aStart = region ? region.aStart : wallGap;
        const aEnd   = region ? region.aEnd   : A-wallGap;
        const cStart = region ? region.cStart : wallGap;
        const cEnd   = region ? region.cEnd   : C-wallGap;
        const usableC=cEnd-cStart, usableA=aEnd-aStart;
        const types=typesForDepth(p, bd);
        if(!types.length) return {shelves:[], cross:[], bandsCount:0};
        const minW=Math.min(...types.map(t=>t.w));
        if(usableC<bd || usableA<minW) return {shelves:[], cross:[], bandsCount:0};

        const bands=buildBands(usableC, aisle, bd, lead);

        const spineA=spines.map(rc=>clamp(rc-aisle/2, aStart, aEnd-aisle)).sort((a,b)=>a-b);
        const forb=forbiddenAC(p, orient);

        const cross=spineA.map(s => orient==="h"
            ? {x:s,y:0,w:aisle,h:roomH} : {x:0,y:s,w:roomW,h:aisle});

        const shelves=[];
        for(const band of bands){
            for(let r=0;r<band.rows;r++){
                const cPos=cStart + band.c0 + r*bd;
                const cLo=cPos, cHi=cPos+bd;
                const blk=[];
                for(const s of spineA) blk.push([s, s+aisle]);
                for(const f of forb) if(f.c0<cHi && f.c1>cLo) blk.push([f.a0, f.a1]);
                for(const [f0,f1] of subtractIntervals([aStart,aEnd], blk)){
                    for(const pc of packInterval(f0, f1, types))
                        shelves.push(fromAC(pc.a0, pc.w, cPos, bd, orient, pc.type));
                }
            }
        }
        return {shelves, cross, bandsCount:bands.length};
    }

    /* ---------------------------------------------------------------------
       PERIMETER-GENERATOR (harte Wandreihen) + Innenfüllung
    --------------------------------------------------------------------- */
    function stripBlockers(runIsX, lo, hi, p){
        const rects=[];
        for(const c of p.columns) rects.push({x:c.x-p.colClear, y:c.y-p.colClear, w:c.w+2*p.colClear, h:c.h+2*p.colClear});
        for(const d of p.doors) rects.push(doorZone(d,p.roomW,p.roomH,p.aisle));
        const out=[];
        for(const r of rects){
            if(runIsX){ if(r.y<hi && r.y+r.h>lo) out.push([r.x, r.x+r.w]); }
            else      { if(r.x<hi && r.x+r.w>lo) out.push([r.y, r.y+r.h]); }
        }
        return out;
    }

    function generatePerimeter(opts, p){
        const {orient, lead=0, bd, walls} = opts;
        const {roomW,roomH,aisle,wallGap} = p;
        const types=typesForDepth(p, bd);
        if(!types.length) return {shelves:[], cross:[], bandsCount:0, walls};

        const sel={top:false,bottom:false,left:false,right:false};
        walls.forEach(w=>sel[w]=true);
        const shelves=[];

        const xLo = sel.left  ? wallGap+bd+aisle : wallGap;
        const xHi = sel.right ? roomW-wallGap-bd-aisle : roomW-wallGap;
        const yLo = sel.top   ? wallGap+bd+aisle : wallGap;
        const yHi = sel.bottom? roomH-wallGap-bd-aisle : roomH-wallGap;

        const rowXlo = sel.left ? wallGap+bd : wallGap;
        const rowXhi = sel.right? roomW-wallGap-bd : roomW-wallGap;

        function placeRow(runIsX, runLo, runHi, stripLo, stripHi, fixed, makeRect){
            const blk=stripBlockers(runIsX, stripLo, stripHi, p);
            for(const [f0,f1] of subtractIntervals([runLo,runHi], blk))
                for(const pc of packInterval(f0,f1,types)) shelves.push(makeRect(pc, fixed));
        }
        if(sel.top)
            placeRow(true, rowXlo, rowXhi, wallGap, wallGap+bd, wallGap,
                (pc,y)=>({x:pc.a0,y,w:pc.w,h:bd,len:pc.w,type:pc.type}));
        if(sel.bottom)
            placeRow(true, rowXlo, rowXhi, roomH-wallGap-bd, roomH-wallGap, roomH-wallGap-bd,
                (pc,y)=>({x:pc.a0,y,w:pc.w,h:bd,len:pc.w,type:pc.type}));
        if(sel.left)
            placeRow(false, wallGap, roomH-wallGap, wallGap, wallGap+bd, wallGap,
                (pc,x)=>({x,y:pc.a0,w:bd,h:pc.w,len:pc.w,type:pc.type}));
        if(sel.right)
            placeRow(false, wallGap, roomH-wallGap, roomW-wallGap-bd, roomW-wallGap, roomW-wallGap-bd,
                (pc,x)=>({x,y:pc.a0,w:bd,h:pc.w,len:pc.w,type:pc.type}));

        const region = orient==="h"
            ? {aStart:xLo, aEnd:xHi, cStart:yLo, cEnd:yHi}
            : {aStart:yLo, aEnd:yHi, cStart:xLo, cEnd:xHi};
        const inner=generateBands({orient, lead, spines:[], bd, region}, p);

        return {shelves:shelves.concat(inner.shelves), cross:inner.cross,
            bandsCount:inner.bandsCount, walls};
    }

    /* ---------------------------------------------------------------------
       WRAPPER: erzeugt Layout + filtert Türschwenk (Regal-Sperre)
    --------------------------------------------------------------------- */
    function generateLayout(opts, p){
        const base = (opts.walls && opts.walls.length)
            ? generatePerimeter(opts, p) : generateBands(opts, p);
        const swings=p.doors.map(d=>doorSwing(d,p));
        const shelves=base.shelves.filter(s=>!swings.some(sw=>rectHitsSwing(s,sw)));
        return {shelves, cross:base.cross, bandsCount:base.bandsCount, walls:opts.walls||null};
    }

    /* ---------------------------------------------------------------------
       BEGEHBARKEIT + GEHWEG-DISTANZ  (Türschwenk blockiert NICHT den Weg)
    --------------------------------------------------------------------- */
    function evaluate(shelves, p){
        const {roomW,roomH,aisle,colClear,columns,doors} = p;
        const maxDim=Math.max(roomW,roomH);
        let res=Math.max(40, Math.round(maxDim/200/10)*10);
        const cols=Math.ceil(roomW/res), rows=Math.ceil(roomH/res), N=cols*rows;
        const blocked=new Uint8Array(N);
        const fill=(rect)=>{ const c0=clamp(Math.floor(rect.x/res),0,cols-1),c1=clamp(Math.ceil((rect.x+rect.w)/res)-1,0,cols-1);
            const r0=clamp(Math.floor(rect.y/res),0,rows-1),r1=clamp(Math.ceil((rect.y+rect.h)/res)-1,0,rows-1);
            for(let r=r0;r<=r1;r++) for(let c=c0;c<=c1;c++) blocked[r*cols+c]=1; };
        for(const s of shelves) fill(s);
        for(const col of columns) fill({x:col.x-colClear,y:col.y-colClear,w:col.w+2*colClear,h:col.h+2*colClear});

        const BIG=1e9,D=1,Q=1.41421356;
        const dist=new Float32Array(N);
        for(let i=0;i<N;i++) dist[i]=blocked[i]?0:BIG;
        for(let r=0;r<rows;r++) for(let c=0;c<cols;c++){ const i=r*cols+c; if(blocked[i])continue; let d=dist[i];
            if(r>0)d=Math.min(d,dist[i-cols]+D);else d=Math.min(d,D);
            if(c>0)d=Math.min(d,dist[i-1]+D);else d=Math.min(d,D);
            if(r>0&&c>0)d=Math.min(d,dist[i-cols-1]+Q);
            if(r>0&&c<cols-1)d=Math.min(d,dist[i-cols+1]+Q); dist[i]=d; }
        for(let r=rows-1;r>=0;r--) for(let c=cols-1;c>=0;c--){ const i=r*cols+c; if(blocked[i])continue; let d=dist[i];
            if(r<rows-1)d=Math.min(d,dist[i+cols]+D);else d=Math.min(d,D);
            if(c<cols-1)d=Math.min(d,dist[i+1]+D);else d=Math.min(d,D);
            if(r<rows-1&&c<cols-1)d=Math.min(d,dist[i+cols+1]+Q);
            if(r<rows-1&&c>0)d=Math.min(d,dist[i+cols-1]+Q); dist[i]=d; }

        const coreThresh=(aisle/2 - res*0.5)/res;
        const core=new Uint8Array(N);
        for(let i=0;i<N;i++) if(!blocked[i] && dist[i]>=coreThresh) core[i]=1;

        const reach=new Uint8Array(N); const stack=[]; const doorSeeds=[]; let doorsWithoutAccess=0;
        for(const d of doors){ const z=doorZone(d,roomW,roomH,aisle); let seeded=false;
            const c0=clamp(Math.floor(z.x/res),0,cols-1),c1=clamp(Math.ceil((z.x+z.w)/res)-1,0,cols-1);
            const r0=clamp(Math.floor(z.y/res),0,rows-1),r1=clamp(Math.ceil((z.y+z.h)/res)-1,0,rows-1);
            for(let r=r0;r<=r1;r++) for(let c=c0;c<=c1;c++){ const i=r*cols+c; if(core[i]&&!reach[i]){reach[i]=1;stack.push(i);doorSeeds.push(i);seeded=true;} }
            if(!seeded) doorsWithoutAccess++; }
        while(stack.length){ const i=stack.pop(); const r=(i/cols)|0,c=i%cols;
            for(let dr=-1;dr<=1;dr++) for(let dc=-1;dc<=1;dc++){ if(!dr&&!dc)continue;
                const nr=r+dr,nc=c+dc; if(nr<0||nc<0||nr>=rows||nc>=cols)continue; const j=nr*cols+nc;
                if(core[j]&&!reach[j]){reach[j]=1;stack.push(j);} } }

        const acc=new Uint8Array(N); const adist=new Float32Array(N).fill(BIG); const q=[];
        for(let i=0;i<N;i++) if(reach[i]){acc[i]=1;adist[i]=0;q.push(i);}
        const limit=aisle/2+res; let head=0;
        while(head<q.length){ const i=q[head++]; const r=(i/cols)|0,c=i%cols; const base=adist[i];
            for(let dr=-1;dr<=1;dr++) for(let dc=-1;dc<=1;dc++){ if(!dr&&!dc)continue;
                const nr=r+dr,nc=c+dc; if(nr<0||nc<0||nr>=rows||nc>=cols)continue; const j=nr*cols+nc; if(blocked[j])continue;
                const nd=base+((dr&&dc)?Q:D)*res; if(nd<adist[j]&&nd<=limit){adist[j]=nd;acc[j]=1;q.push(j);} } }

        const gdist=new Float32Array(N).fill(BIG); const wq=[]; let wh=0;
        for(const i of doorSeeds){ if(gdist[i]===BIG){ gdist[i]=0; wq.push(i); } }
        while(wh<wq.length){ const i=wq[wh++]; const r=(i/cols)|0,c=i%cols;
            const nb=[i-cols,i+cols,i-1,i+1];
            if(r>0 && acc[nb[0]] && gdist[nb[0]]===BIG){gdist[nb[0]]=gdist[i]+1;wq.push(nb[0]);}
            if(r<rows-1 && acc[nb[1]] && gdist[nb[1]]===BIG){gdist[nb[1]]=gdist[i]+1;wq.push(nb[1]);}
            if(c>0 && acc[nb[2]] && gdist[nb[2]]===BIG){gdist[nb[2]]=gdist[i]+1;wq.push(nb[2]);}
            if(c<cols-1 && acc[nb[3]] && gdist[nb[3]]===BIG){gdist[nb[3]]=gdist[i]+1;wq.push(nb[3]);} }

        const served=new Array(shelves.length).fill(false);
        const walk=new Float32Array(shelves.length).fill(BIG);
        let sum=0,cnt=0,mx=0;
        shelves.forEach((s,k)=>{
            const c0=clamp(Math.floor(s.x/res),0,cols-1),c1=clamp(Math.ceil((s.x+s.w)/res)-1,0,cols-1);
            const r0=clamp(Math.floor(s.y/res),0,rows-1),r1=clamp(Math.ceil((s.y+s.h)/res)-1,0,rows-1);
            let best=BIG;
            const wide = s.w >= s.h;   // Zugang nur über die LÄNGSSEITE (Vorderseite), nicht die Stirnseite
            if(wide){
                for(let c=c0;c<=c1;c++){ if(r0-1>=0&&acc[(r0-1)*cols+c])best=Math.min(best,gdist[(r0-1)*cols+c]);
                    if(r1+1<rows&&acc[(r1+1)*cols+c])best=Math.min(best,gdist[(r1+1)*cols+c]); }
            } else {
                for(let r=r0;r<=r1;r++){ if(c0-1>=0&&acc[r*cols+(c0-1)])best=Math.min(best,gdist[r*cols+(c0-1)]);
                    if(c1+1<cols&&acc[r*cols+(c1+1)])best=Math.min(best,gdist[r*cols+(c1+1)]); }
            }
            if(best<BIG){ served[k]=true; const mm=best*res; walk[k]=mm; sum+=mm; cnt++; if(mm>mx)mx=mm; }
        });
        return {served, walk, count:cnt, doorsWithoutAccess, avgWalk:cnt?sum/cnt:1e9, maxWalk:mx};
    }

    function doorSpineCoord(d, orient, p){
        if(orient==="h") return (d.wall==="top"||d.wall==="bottom") ? d.pos : (d.wall==="left"? p.aisle/2 : p.roomW-p.aisle/2);
        return (d.wall==="left"||d.wall==="right") ? d.pos : (d.wall==="top"? p.aisle/2 : p.roomH-p.aisle/2);
    }

    /* ---------------------------------------------------------------------
       OPTIMIZER: 1) max erreichbare Regalfläche  2) min Weg
    --------------------------------------------------------------------- */
    function scoreCandidate(gen, p){
        const ev=evaluate(gen.shelves, p);
        let area=0, lin=0;
        gen.shelves.forEach((s,i)=>{ if(ev.served[i]){ area+=s.w*s.h; lin+=s.len; } });
        return {shelves:gen.shelves, cross:gen.cross, bandsCount:gen.bandsCount, walls:gen.walls,
            served:ev.served, count:ev.count, total:gen.shelves.length,
            avgWalk:ev.avgWalk, maxWalk:ev.maxWalk, doorsWithoutAccess:ev.doorsWithoutAccess, area, lin};
    }

    /* --- NACHVERDICHTUNG: Restpockets an Wänden / Gangenden füllen ---
       Fügt Standardregale in freie Wand-/Ecktaschen ein und behält sie nur,
       wenn sie über die gleiche Erreichbarkeitsprüfung als nutzbar gelten und
       kein vorher nutzbares Regal dadurch unzugänglich wird. */
    function freeSpot(r, shelves, p){
        if(r.x < p.wallGap-0.5 || r.y < p.wallGap-0.5 ||
            r.x+r.w > p.roomW-p.wallGap+0.5 || r.y+r.h > p.roomH-p.wallGap+0.5) return false;
        for(const c of p.columns){
            const cc={x:c.x-p.colClear, y:c.y-p.colClear, w:c.w+2*p.colClear, h:c.h+2*p.colClear};
            if(rectsOverlap(r, cc)) return false;
        }
        for(const d of p.doors) if(rectsOverlap(r, doorZone(d,p.roomW,p.roomH,p.aisle))) return false;
        for(const sw of p.doors.map(d=>doorSwing(d,p))) if(rectHitsSwing(r, sw)) return false;
        for(const s of shelves) if(rectsOverlap(r, s)) return false;
        return true;
    }

    function proposeWallPockets(p){
        const types=enabledTypes(p); const out=[]; const seen=new Set();
        const minW=Math.min(...types.map(t=>t.w));
        const step=Math.max(SNAP*2, Math.round(minW/SNAP)*SNAP);
        const pushU=(x,y,w,h,len,type)=>{ x=snap(x); y=snap(y);
            const k=x+","+y+","+w+","+h; if(seen.has(k))return; seen.add(k);
            out.push({x,y,w,h,len,type}); };
        for(const t of types){
            const tw=t.w, td=t.d;
            // an oberer/unterer Wand: längs (tw×td) und quer/tief (td×tw)
            for(let x=p.wallGap; x+tw<=p.roomW-p.wallGap+1; x+=step){
                pushU(x, p.wallGap,            tw, td, tw, t.type);
                pushU(x, p.roomH-p.wallGap-td, tw, td, tw, t.type);
            }
            for(let x=p.wallGap; x+td<=p.roomW-p.wallGap+1; x+=step){
                pushU(x, p.wallGap,            td, tw, tw, t.type);
                pushU(x, p.roomH-p.wallGap-tw, td, tw, tw, t.type);
            }
            // an linker/rechter Wand
            for(let y=p.wallGap; y+tw<=p.roomH-p.wallGap+1; y+=step){
                pushU(p.wallGap,            y, td, tw, tw, t.type);
                pushU(p.roomW-p.wallGap-td, y, td, tw, tw, t.type);
            }
            for(let y=p.wallGap; y+td<=p.roomH-p.wallGap+1; y+=step){
                pushU(p.wallGap,            y, tw, td, tw, t.type);
                pushU(p.roomW-p.wallGap-tw, y, tw, td, tw, t.type);
            }
        }
        return out;
    }

    function densify(cand, p){
        let shelves=cand.shelves.slice();
        let ev=evaluate(shelves, p);
        let served=ev.count;
        const proposals=proposeWallPockets(p);
        const DETOUR_ABS=1500, DETOUR_REL=1.4;   // Eckregal darf Wege zu vorhandenen Regalen nicht stark verlängern
        let added=0, guard=0, changed=true;
        while(changed && guard++<2){
            changed=false; let trials=0;
            for(const r of proposals){
                if(trials>=24) break;
                if(!freeSpot(r, shelves, p)) continue;      // billiger Vorfilter: passt überhaupt?
                trials++;
                const trial=shelves.concat([r]);
                const tev=evaluate(trial, p);
                if(tev.count>served && tev.served[shelves.length]){
                    let ok=true;
                    for(let i=0;i<shelves.length;i++){
                        if(!ev.served[i]) continue;
                        if(!tev.served[i]){ ok=false; break; }                          // vorher nutzbar → jetzt unzugänglich
                        if(tev.walk[i] > ev.walk[i]*DETOUR_REL + DETOUR_ABS){ ok=false; break; } // starker Umweg
                    }
                    if(ok){ shelves=trial; ev=tev; served=tev.count; added++; changed=true; }
                }
            }
        }
        if(!added) return cand;
        const re=scoreCandidate({shelves, cross:cand.cross, bandsCount:cand.bandsCount, walls:cand.walls}, p);
        re.endcaps=added;
        return re;
    }

    function wallSetsFor(p){
        const longIsX = p.roomW >= p.roomH;
        const longPair  = longIsX ? ['top','bottom'] : ['left','right'];
        const shortPair = longIsX ? ['left','right'] : ['top','bottom'];
        const sets = [ ['top','bottom','left','right'], longPair, shortPair ];
        const manual = Object.keys(p.walls).filter(w=>p.walls[w]);
        if(manual.length){
            const key=manual.slice().sort().join('');
            if(!sets.some(s=>s.slice().sort().join('')===key)) sets.push(manual);
        }
        return sets;
    }

    function optimize(p){
        const pool=[];
        const depths=[...new Set(enabledTypes(p).map(t=>t.d))];
        const forced=Object.keys(p.walls).filter(w=>p.walls[w]);   // angekreuzte Wände = harte Vorgabe

        for(const bd of depths){
            const period=p.aisle+2*bd;
            const leads=[0, Math.round(period/2)];

            if(forced.length){
                // NUR Layouts, die genau diese Wände möblieren (beide Ausrichtungen, beide Phasen)
                for(const orient of ["h","v"]) for(const lead of leads){
                    const gen=generateLayout({orient,lead,spines:[],bd,walls:forced}, p);
                    if(!gen.shelves.length) continue;
                    pool.push(scoreCandidate(gen, p));
                }
                continue;   // freie/andere Wandvarianten überspringen
            }

            for(const orient of ["h","v"]){
                const A = orient==="h" ? p.roomW : p.roomH;
                const doorCoords=[...new Set(p.doors.map(d=>Math.round(clamp(doorSpineCoord(d,orient,p),0,A))))];
                const spineSets=[[]];
                for(const c of doorCoords) spineSets.push([c]);
                if(doorCoords.length>1) spineSets.push(doorCoords.slice());
                spineSets.push([Math.round(A/2)]);
                for(const lead of leads) for(const spines of spineSets){
                    const gen=generateLayout({orient,lead,spines,bd,walls:null}, p);
                    if(!gen.shelves.length) continue;
                    pool.push(scoreCandidate(gen, p));
                }
            }

            for(const walls of wallSetsFor(p)){
                for(const orient of ["h","v"]){
                    const gen=generateLayout({orient,lead:0,spines:[],bd,walls}, p);
                    if(!gen.shelves.length) continue;
                    pool.push(scoreCandidate(gen, p));
                }
            }
        }

        if(!pool.length) return [{shelves:[],cross:[],bandsCount:0,walls:null,served:[],count:0,total:0,avgWalk:0,doorsWithoutAccess:0,area:0,lin:0}];

        const BUCKET=0.25e6; // 0,25 m² Toleranz → "flächengleich"
        const cmp=(a,b)=>{
            const ba=Math.round(b.area/BUCKET), aa=Math.round(a.area/BUCKET);
            return ba-aa || a.avgWalk-b.avgWalk || b.area-a.area;
        };
        pool.sort(cmp);

        // Die besten Kandidaten mit Eckregalen nachverdichten (K=4: guter Kompromiss
        // Ausbeute/Tempo), dann neu sortieren …
        const K=Math.min(pool.length, 4);
        for(let i=0;i<K;i++) pool[i]=densify(pool[i], p);
        pool.sort(cmp);

        // … und 3 verschiedene Grundstrukturen als Top-3 zeigen
        const chosen=[]; const seen=new Set();
        for(const c of pool){
            const sig=(c.walls?c.walls.slice().sort().join(""):"-")+"|"+c.bandsCount+"|"+c.total;
            if(seen.has(sig)) continue; seen.add(sig); chosen.push(c);
            if(chosen.length===3) break;
        }
        for(const c of pool){ if(chosen.length>=3)break; if(!chosen.includes(c)) chosen.push(c); }
        return chosen;
    }

    // Auto-Ergebnis cachen: nur neu rechnen, wenn sich relevante Eingaben ändern
    let _autoCache={sig:"", top:null};
    function autoSig(p){
        return JSON.stringify([p.roomW,p.roomH,p.aisle,p.type1W,p.type1D,p.type2W,p.type2D,p.useType2,p.wallGap,p.colClear,p.walls,p.columns,p.doors]);
    }
    function computeAuto(p){
        const sig=autoSig(p);
        if(sig!==_autoCache.sig) _autoCache={sig, top:optimize(p)};
        return _autoCache.top;
    }

    // Kennzahlen für eine beliebige Regalliste (fürs manuelle Layout)
    function statsFor(shelves, p){
        const ev=evaluate(shelves, p);
        let area=0, lin=0;
        shelves.forEach((s,i)=>{ if(ev.served[i]){ area+=s.w*s.h; lin+=(s.len||Math.max(s.w,s.h)); } });
        return {shelves, served:ev.served, count:ev.count, total:shelves.length,
            avgWalk:ev.avgWalk, maxWalk:ev.maxWalk, area, lin, doorsWithoutAccess:ev.doorsWithoutAccess};
    }

    const WMAP={top:"O",bottom:"U",left:"L",right:"R"};
    function variantLabel(c){
        if(c.walls && c.walls.length){
            if(c.walls.length===4) return "Perimeter (alle Wände)";
            return "Wandreihe " + c.walls.slice().sort().map(w=>WMAP[w]).join("");
        }
        return "Frei · " + Math.max(0, c.bandsCount-1) + " Gänge";
    }

    /* ---------------------------------------------------------------------
       MANUELLE BEARBEITUNG
    --------------------------------------------------------------------- */
    function clampAxis(pos, size, max){
        let lo=state.wallGap, hi=max-state.wallGap-size;
        if(hi<lo){ lo=0; hi=Math.max(0, max-size); }
        return clamp(pos, lo, hi);
    }
    function fits(rect, ignoreIdx){
        const p=state;
        if(rect.x<-0.5 || rect.y<-0.5 || rect.x+rect.w>p.roomW+0.5 || rect.y+rect.h>p.roomH+0.5) return false;
        for(const c of p.columns){
            const cc={x:c.x-p.colClear, y:c.y-p.colClear, w:c.w+2*p.colClear, h:c.h+2*p.colClear};
            if(rectsOverlap(rect, cc)) return false;               // Säule gewinnt
        }
        for(const d of p.doors) if(rectsOverlap(rect, doorZone(d,p.roomW,p.roomH,p.aisle))) return false;
        for(let i=0;i<p.manual.length;i++){ if(i===ignoreIdx) continue; if(rectsOverlap(rect, p.manual[i])) return false; }
        return true;
    }
    function findManualShelfAt(pt){
        for(let i=state.manual.length-1;i>=0;i--) if(pointInRect(pt.x, pt.y, state.manual[i])) return i;
        return -1;
    }
    function placeShelf(pt){
        const p=state; const dims=typeDims(p, p.placeType);
        const w = p.placeOrient==="h" ? dims.w : dims.d;
        const h = p.placeOrient==="h" ? dims.d : dims.w;
        let x=clampAxis(snap(pt.x - w/2), w, p.roomW);
        let y=clampAxis(snap(pt.y - h/2), h, p.roomH);
        const r={x, y, w, h, len:dims.w, type:p.placeType};
        if(fits(r,-1)){ p.manual.push(r); render(); }
        else flashHint("Platz belegt oder blockiert (Säule / Tür / anderes Regal).");
    }
    function computeRowRects(a, b){
        const p=state; const dims=typeDims(p, p.placeType);
        const tw=dims.w, td=dims.d;
        const horiz=Math.abs(b.x-a.x) >= Math.abs(b.y-a.y);
        const out=[];
        if(horiz){
            const yy=clampAxis(snap(a.y - td/2), td, p.roomH);
            let x0=Math.min(a.x,b.x), x1=Math.max(a.x,b.x), x=snap(x0);
            while(x+tw <= x1+1){ out.push({x, y:yy, w:tw, h:td, len:tw, type:p.placeType}); x+=tw; }
        } else {
            const xx=clampAxis(snap(a.x - td/2), td, p.roomW);
            let y0=Math.min(a.y,b.y), y1=Math.max(a.y,b.y), y=snap(y0);
            while(y+tw <= y1+1){ out.push({x:xx, y, w:td, h:tw, len:tw, type:p.placeType}); y+=tw; }
        }
        return out;
    }
    function placeRow(a, b){
        const rects=computeRowRects(a,b); let added=0;
        for(const r of rects){ if(fits(r,-1)){ state.manual.push(r); added++; } }
        if(!added) flashHint("Reihe passt nicht (zu kurz oder blockiert).");
        render();
    }
    function commitMove(d){
        const o=d.orig; const dx=d.cur.x-d.start.x, dy=d.cur.y-d.start.y;
        let nx=clampAxis(snap(o.x+dx), o.w, state.roomW);
        let ny=clampAxis(snap(o.y+dy), o.h, state.roomH);
        const cand={...o, x:nx, y:ny};
        if(fits(cand, d.idx)) state.manual[d.idx]=cand;
        else flashHint("Zielposition belegt — Regal bleibt.");
        render();
    }
    function deleteAt(pt){
        const mi=findManualShelfAt(pt);
        if(mi>=0){ state.manual.splice(mi,1); render(); return; }
        const hc=state.columns.find(c=>pointInRect(pt.x,pt.y,c));
        if(hc){ state.columns=state.columns.filter(c=>c.id!==hc.id); render(); return; }
        const hd=state.doors.find(d=>pointInRect(pt.x,pt.y,doorZone(d,state.roomW,state.roomH,state.aisle)));
        if(hd){ state.doors=state.doors.filter(d=>d.id!==hd.id); render(); }
    }

    /* ---------------------------------------------------------------------
       RENDER
    --------------------------------------------------------------------- */
    let currentScale=1;
    let _hintT=null;
    function flashHint(msg){
        $("hint").textContent=msg;
        clearTimeout(_hintT);
        _hintT=setTimeout(()=>{ $("hint").textContent=DEFAULT_HINT; }, 2000);
    }

    function renderVariants(top){
        const p=state; const box=$("variants"); const roomArea=(p.roomW*p.roomH)/1e6;
        box.innerHTML=top.map((c,idx)=>{
            const area=c.area/1e6;
            const util=roomArea>0?(area/roomArea*100):0;
            const walk=c.count?(c.avgWalk/1000).toFixed(1):"–";
            const tag=(c.walls&&c.walls.length)?'<span class="vtag">Wand</span>':'';
            const capTag=c.endcaps?`<span class="vtag" style="background:#e6efe0;color:#3f6212">+${c.endcaps} Eck</span>`:'';
            return `<button class="vcard${idx===p.selected?' active':''}" data-idx="${idx}">
      <div class="vrow"><span class="vrank">${idx+1}</span><span class="vlabel">${variantLabel(c)}</span>${tag}${capTag}</div>
      <div class="vstats"><b>${c.count}</b> Regale · <b>${util.toFixed(0)}</b> % · Ø Weg <b>${walk}</b> m</div>
    </button>`;
        }).join("");
    }

    function swingPath(sw, S){
        const c={x:S(sw.cx), y:S(sw.cy)};
        const p1={x:S(sw.cx+sw.dirX*sw.r), y:S(sw.cy)};
        const p2={x:S(sw.cx), y:S(sw.cy+sw.dirY*sw.r)};
        const rr=S(sw.r);
        const sweep=(sw.wall==="top"||sw.wall==="left")?1:0;
        return `M ${c.x} ${c.y} L ${p1.x} ${p1.y} A ${rr} ${rr} 0 0 ${sweep} ${p2.x} ${p2.y} Z`;
    }

    // Ghost-Overlay (für Reihe-ziehen / Verschieben) — ohne Neuberechnung
    function clearGhost(){ const g=$("ghostLayer"); if(g) g.remove(); }
    function setGhost(rectsScreen){
        const svg=$("plan"); clearGhost();
        const ns="http://www.w3.org/2000/svg";
        const g=document.createElementNS(ns,"g"); g.setAttribute("id","ghostLayer");
        for(const r of rectsScreen){
            const el=document.createElementNS(ns,"rect");
            el.setAttribute("x",r.x); el.setAttribute("y",r.y);
            el.setAttribute("width",Math.max(0,r.w)); el.setAttribute("height",Math.max(0,r.h));
            el.setAttribute("fill","var(--shelf2)"); el.setAttribute("opacity","0.35");
            el.setAttribute("stroke","var(--shelf2-edge)"); el.setAttribute("stroke-dasharray","4 3");
            g.appendChild(el);
        }
        svg.appendChild(g);
    }
    const w2s=(r)=>({x:r.x*currentScale, y:r.y*currentScale, w:r.w*currentScale, h:r.h*currentScale});
    function showRowGhost(a,b){ setGhost(computeRowRects(a,b).map(w2s)); }
    function showMoveGhost(d){
        const o=d.orig; const dx=d.cur.x-d.start.x, dy=d.cur.y-d.start.y;
        const nx=clampAxis(snap(o.x+dx), o.w, state.roomW);
        const ny=clampAxis(snap(o.y+dy), o.h, state.roomH);
        setGhost([w2s({x:nx,y:ny,w:o.w,h:o.h})]);
    }

    function fmtDelta(v, digits, goodDown){
        const good = goodDown ? v<0 : v>0;
        const cls = Math.abs(v)<1e-9 ? "zero" : (good ? "up" : "down");
        const sign = v>0 ? "+" : "";
        return `<span class="delta ${cls}">${sign}${v.toFixed(digits)}</span>`;
    }
    function renderCompare(a, m, p){
        const roomArea=(p.roomW*p.roomH)/1e6;
        const uA=roomArea>0?(a.area/1e6/roomArea*100):0;
        const uM=roomArea>0?(m.area/1e6/roomArea*100):0;
        const wA=a.count?a.avgWalk/1000:null, wM=m.count?m.avgWalk/1000:null;
        let html=`<tr><th>Kennzahl</th><th>Auto</th><th>Manuell</th><th>Δ</th></tr>`;
        const rows=[
            ["Regale", a.count, m.count, m.count-a.count, 0, false],
            ["Fläche %", uA, uM, uM-uA, 0, false],
            ["Regalmeter", a.lin/1000, m.lin/1000, (m.lin-a.lin)/1000, 1, false],
        ];
        for(const [label,av,mv,dv,dig,gd] of rows)
            html+=`<tr><td>${label}</td><td class="num">${av.toFixed(dig)}</td><td class="num">${mv.toFixed(dig)}</td><td>${fmtDelta(dv,dig,gd)}</td></tr>`;
        const avw=wA==null?"–":wA.toFixed(1), mvw=wM==null?"–":wM.toFixed(1);
        const dw=(wA!=null&&wM!=null)?fmtDelta(wM-wA,1,true):'<span class="delta zero">–</span>';
        html+=`<tr><td>Ø Weg m</td><td class="num">${avw}</td><td class="num">${mvw}</td><td>${dw}</td></tr>`;
        const xA=a.count?a.maxWalk/1000:null, xM=m.count?m.maxWalk/1000:null;
        const axw=xA==null?"–":xA.toFixed(1), mxw=xM==null?"–":xM.toFixed(1);
        const dxw=(xA!=null&&xM!=null)?fmtDelta(xM-xA,1,true):'<span class="delta zero">–</span>';
        html+=`<tr><td>Max Weg m</td><td class="num">${axw}</td><td class="num">${mxw}</td><td>${dxw}</td></tr>`;
        $("compare").innerHTML=html;
    }

    function render(){
        const p=state;
        const top=computeAuto(p);
        if(p.selected>=top.length) p.selected=0;
        const auto=top[p.selected]||top[0]||{shelves:[],cross:[],served:[],count:0,area:0,lin:0,avgWalk:0,total:0};
        renderVariants(top);

        const manualStats=statsFor(p.manual, p);
        const autoStats={shelves:auto.shelves, served:auto.served, count:auto.count,
            area:auto.area, lin:auto.lin, avgWalk:auto.avgWalk, total:auto.total};

        const showManual = p.shown==="manual";
        const shShelves = showManual ? p.manual : (auto.shelves||[]);
        const shServed  = showManual ? manualStats.served : (auto.served||[]);
        const shCross   = showManual ? [] : (auto.cross||[]);
        const shStats   = showManual ? manualStats : autoStats;

        const MAXW=660, MAXH=460;
        const scale=Math.min(MAXW/p.roomW, MAXH/p.roomH);
        currentScale=scale;
        const svgW=p.roomW*scale, svgH=p.roomH*scale;
        const S=(v)=>v*scale;
        const parts=[];

        parts.push(`<rect x="0" y="0" width="${svgW}" height="${svgH}" fill="var(--paper)" stroke="var(--ink)" stroke-width="2"/>`);
        for(const cr of shCross)
            parts.push(`<rect x="${S(cr.x)}" y="${S(cr.y)}" width="${S(cr.w)}" height="${S(cr.h)}" fill="var(--aisle)" opacity="0.6"/>`);
        for(let i=1;i*1000<p.roomW;i++) parts.push(`<line x1="${S(i*1000)}" y1="0" x2="${S(i*1000)}" y2="${svgH}" stroke="var(--grid)" stroke-width="1"/>`);
        for(let i=1;i*1000<p.roomH;i++) parts.push(`<line x1="0" y1="${S(i*1000)}" x2="${svgW}" y2="${S(i*1000)}" stroke="var(--grid)" stroke-width="1"/>`);

        for(const d of p.doors)
            parts.push(`<path d="${swingPath(doorSwing(d,p), S)}" fill="var(--swing)" opacity="0.08" stroke="var(--swing)" stroke-opacity="0.35" stroke-width="1" stroke-dasharray="4 3"/>`);

        shShelves.forEach((s,i)=>{ if(!shServed[i])
            parts.push(`<rect x="${S(s.x)}" y="${S(s.y)}" width="${S(s.w)}" height="${S(s.h)}" fill="none" stroke="#bb1188" stroke-dasharray="3 3" stroke-width="0.8" opacity="0.55"/>`); });
        shShelves.forEach((s,i)=>{ if(shServed[i]){
            const fill=s.type===1?"var(--shelf2)":"var(--shelf)";
            const edge=s.type===1?"var(--shelf2-edge)":"var(--shelf-edge)";
            parts.push(`<rect x="${S(s.x)}" y="${S(s.y)}" width="${S(s.w)}" height="${S(s.h)}" fill="${fill}" stroke="${edge}" stroke-width="0.75" opacity="0.92"/>`);
        }});

        for(const c of p.columns){
            if(p.colClear>0)
                parts.push(`<rect x="${S(c.x-p.colClear)}" y="${S(c.y-p.colClear)}" width="${S(c.w+2*p.colClear)}" height="${S(c.h+2*p.colClear)}" fill="none" stroke="var(--column)" stroke-dasharray="3 3" stroke-width="1" opacity="0.5"/>`);
            parts.push(`<rect x="${S(c.x)}" y="${S(c.y)}" width="${S(c.w)}" height="${S(c.h)}" fill="var(--column)"/>`);
        }

        const T=7;
        for(const d of p.doors){
            let x,y,w,h;
            if(d.wall==="top"){ x=S(d.pos-d.width/2); y=-T/2; w=S(d.width); h=T; }
            else if(d.wall==="bottom"){ x=S(d.pos-d.width/2); y=svgH-T/2; w=S(d.width); h=T; }
            else if(d.wall==="left"){ x=-T/2; y=S(d.pos-d.width/2); w=T; h=S(d.width); }
            else { x=svgW-T/2; y=S(d.pos-d.width/2); w=T; h=S(d.width); }
            parts.push(`<rect x="${x}" y="${y}" width="${w}" height="${h}" fill="var(--door)" rx="1"/>`);
        }

        const svg=$("plan");
        svg.setAttribute("width",svgW); svg.setAttribute("height",svgH);
        svg.style.cursor = p.mode==="delete" ? "not-allowed" : (p.mode==="move" ? "move" : "crosshair");
        svg.innerHTML=parts.join("");

        const roomArea=(p.roomW*p.roomH)/1e6;
        const shelfArea=shStats.area/1e6;
        $("stCount").textContent=shStats.count;
        $("stUtil").textContent=roomArea>0?(shelfArea/roomArea*100).toFixed(0):"0";
        $("stLin").textContent=(shStats.lin/1000).toFixed(1);
        $("stWalk").textContent=shStats.count?(shStats.avgWalk/1000).toFixed(1):"0";
        if($("hint").textContent==="" ) $("hint").textContent=DEFAULT_HINT;

        renderCompare(autoStats, manualStats, p);

        const removed=shStats.total-shStats.count;
        const narrowDoor=p.doors.filter(d=>d.width<p.aisle).length;
        const noteEl=$("note"); const msgs=[]; const which= showManual? "Manuell":"Auto";
        if(!p.doors.length) msgs.push("Keine Tür gesetzt — ohne Zugang gilt kein Regal als erreichbar.");
        if(narrowDoor>0) msgs.push(narrowDoor+" Tür(en) schmaler als die Gangbreite ("+p.aisle+" mm) — mögliche Engstelle.");
        if(removed>0) msgs.push(removed+" Regal(e) ohne durchgehend breiten Zugang in Ansicht "+which+" (ausgegraut).");
        if(msgs.length){ noteEl.className="note"; noteEl.style.display="block"; noteEl.textContent=msgs.join("  "); }
        else { noteEl.className="note ok"; noteEl.style.display="block"; noteEl.textContent="Alle Regale erreichbar; Weg von der Tür minimiert."; }
    }

    /* ---------------------------------------------------------------------
       UI-WIRING
    --------------------------------------------------------------------- */
    let _renderTimer=null;
    function renderSoon(){ clearTimeout(_renderTimer); _renderTimer=setTimeout(render, 90); }

    ["roomW","roomH","aisle","type1W","type1D","type2W","type2D","wallGap","colClear"].forEach(key=>{
        const el=$(key); el.value=state[key];
        el.addEventListener("input",()=>{ state[key]=Math.max(Number(el.min)||0, Number(el.value)||0); renderSoon(); });
    });

    function syncTypeB(){
        const b=$("ptypeB");
        if(!state.useType2){
            b.classList.add("disabled");
            if(state.placeType===1){ state.placeType=0;
                document.querySelectorAll("[data-ptype]").forEach(x=>x.classList.toggle("active", x.dataset.ptype==="0")); }
        } else b.classList.remove("disabled");
    }

    const useT2=$("useType2");
    useT2.checked=state.useType2;
    $("typeBBox").classList.toggle("on", state.useType2);
    useT2.addEventListener("change",()=>{ state.useType2=useT2.checked; $("typeBBox").classList.toggle("on",state.useType2); syncTypeB(); render(); });

    const sp=$("shelfPreset");
    SHELF_PRESETS.forEach((preset,i)=>{const o=document.createElement("option");o.value=i;o.textContent=preset.label;sp.appendChild(o);});
    sp.addEventListener("change",()=>{const q=SHELF_PRESETS[sp.value];if(q){state.type1W=q.w;state.type1D=q.d;$("type1W").value=q.w;$("type1D").value=q.d;render();}});

    const ap=$("aislePreset");
    AISLE_PRESETS.forEach(a=>{const o=document.createElement("option");o.value=a.v;o.textContent=a.label;ap.appendChild(o);});
    ap.addEventListener("change",()=>{state.aisle=Number(ap.value);$("aisle").value=state.aisle;render();});

    $("wallBox").addEventListener("change",e=>{
        const lab=e.target.closest(".wallchk"); if(!lab)return;
        const w=lab.dataset.wall; state.walls[w]=e.target.checked;
        lab.classList.toggle("on", e.target.checked);
        render();
    });

    function wireSeg(id,attr,key){ const box=$(id);
        box.addEventListener("click",e=>{const b=e.target.closest("button");if(!b)return; state[key]=b.dataset[attr];
            [...box.querySelectorAll("button")].forEach(x=>x.classList.toggle("active",x===b)); }); }
    wireSeg("modeSeg","mode","mode");
    $("modeSeg").querySelector('[data-mode="column"]').classList.add("active");

    // Typ / Achse / Ansicht
    document.querySelectorAll("[data-ptype]").forEach(b=>b.addEventListener("click",()=>{
        const v=Number(b.dataset.ptype);
        if(v===1 && !state.useType2) return;
        state.placeType=v;
        document.querySelectorAll("[data-ptype]").forEach(x=>x.classList.toggle("active", x===b));
    }));
    document.querySelectorAll("[data-orient]").forEach(b=>b.addEventListener("click",()=>{
        state.placeOrient=b.dataset.orient;
        document.querySelectorAll("[data-orient]").forEach(x=>x.classList.toggle("active", x===b));
    }));
    function setShownButtons(v){ document.querySelectorAll("[data-shown]").forEach(x=>x.classList.toggle("active", x.dataset.shown===v)); }
    document.querySelectorAll("[data-shown]").forEach(b=>b.addEventListener("click",()=>{
        state.shown=b.dataset.shown; setShownButtons(state.shown); render();
    }));

    $("btnAdopt").addEventListener("click",()=>{
        const top=computeAuto(state); const a=top[state.selected]||top[0];
        state.manual=((a&&a.shelves)||[]).map(s=>({x:s.x,y:s.y,w:s.w,h:s.h,len:s.len,type:s.type}));
        state.shown="manual"; setShownButtons("manual"); render();
        flashHint("Auto-Variante übernommen — jetzt von Hand feilen.");
    });
    $("btnClearManual").addEventListener("click",()=>{ state.manual=[]; render(); });

    /* --- Export / Import: kompletter Grundriss als JSON --- */
    function shownSnapshot(){
        const p=state; const top=computeAuto(p); const a=top[p.selected]||top[0]||{shelves:[],served:[]};
        const showManual=p.shown==="manual";
        const shelves=showManual? p.manual : (a.shelves||[]);
        const served =showManual? statsFor(p.manual,p).served : (a.served||[]);
        return shelves.map((s,i)=>({x:Math.round(s.x),y:Math.round(s.y),w:Math.round(s.w),h:Math.round(s.h),type:s.type||0,served:!!served[i]}));
    }
    function serializeState(){
        const p=state;
        return {
            v:2,
            room:{w:p.roomW,h:p.roomH}, aisle:p.aisle, wallGap:p.wallGap, colClear:p.colClear,
            typeA:{w:p.type1W,d:p.type1D}, typeB:{w:p.type2W,d:p.type2D,on:p.useType2},
            walls:{top:!!p.walls.top,bottom:!!p.walls.bottom,left:!!p.walls.left,right:!!p.walls.right},
            columns:p.columns.map(c=>({x:Math.round(c.x),y:Math.round(c.y),w:c.w,h:c.h})),
            doors:p.doors.map(d=>({wall:d.wall,pos:Math.round(d.pos),width:d.width})),
            view:p.shown, selected:p.selected,
            manual:p.manual.map(s=>({x:Math.round(s.x),y:Math.round(s.y),w:Math.round(s.w),h:Math.round(s.h),type:s.type||0})),
            shown:shownSnapshot()
        };
    }
    function syncInputsFromState(){
        const p=state;
        ["roomW","roomH","aisle","type1W","type1D","type2W","type2D","wallGap","colClear"].forEach(k=>{ if($(k)) $(k).value=p[k]; });
        useT2.checked=p.useType2; $("typeBBox").classList.toggle("on",p.useType2); syncTypeB();
        document.querySelectorAll("#wallBox .wallchk").forEach(lab=>{ const w=lab.dataset.wall;
            const cb=lab.querySelector("input"); cb.checked=!!p.walls[w]; lab.classList.toggle("on",!!p.walls[w]); });
        document.querySelectorAll("[data-ptype]").forEach(x=>x.classList.toggle("active", Number(x.dataset.ptype)===p.placeType));
        document.querySelectorAll("[data-orient]").forEach(x=>x.classList.toggle("active", x.dataset.orient===p.placeOrient));
        setShownButtons(p.shown);
    }
    function applyState(obj){
        if(!obj || typeof obj!=="object") throw new Error("kein Objekt");
        const num=(v,def)=>Number.isFinite(+v)?+v:def; const p=state;
        if(obj.room){ p.roomW=num(obj.room.w,p.roomW); p.roomH=num(obj.room.h,p.roomH); }
        p.aisle=num(obj.aisle,p.aisle); p.wallGap=num(obj.wallGap,p.wallGap); p.colClear=num(obj.colClear,p.colClear);
        if(obj.typeA){ p.type1W=num(obj.typeA.w,p.type1W); p.type1D=num(obj.typeA.d,p.type1D); }
        if(obj.typeB){ p.type2W=num(obj.typeB.w,p.type2W); p.type2D=num(obj.typeB.d,p.type2D); p.useType2=!!obj.typeB.on; }
        if(obj.walls){ p.walls={top:!!obj.walls.top,bottom:!!obj.walls.bottom,left:!!obj.walls.left,right:!!obj.walls.right}; }
        if(Array.isArray(obj.columns)) p.columns=obj.columns.map(c=>({id:nextId++,x:num(c.x,0),y:num(c.y,0),w:num(c.w,400),h:num(c.h,400)}));
        if(Array.isArray(obj.doors)) p.doors=obj.doors.map(d=>({id:nextId++,wall:["top","bottom","left","right"].includes(d.wall)?d.wall:"left",pos:num(d.pos,1000),width:num(d.width,1000)}));
        if(Array.isArray(obj.manual)) p.manual=obj.manual.map(s=>({x:num(s.x,0),y:num(s.y,0),w:num(s.w,0),h:num(s.h,0),len:num(s.len,Math.max(num(s.w,0),num(s.h,0))),type:s.type===1?1:0}));
        if(obj.view==="auto"||obj.view==="manual") p.shown=obj.view;
        if(Number.isInteger(obj.selected)) p.selected=Math.max(0,obj.selected);
        _autoCache.sig="";       // Cache invalidieren, damit Auto neu gerechnet wird
        syncInputsFromState(); render();
    }
    $("btnExport").addEventListener("click",()=>{
        $("dataBox").value=JSON.stringify(serializeState(),null,1);
        $("dataMsg").textContent="Exportiert. Mit Kopieren-Knopf in die Zwischenablage.";
    });
    $("btnCopy").addEventListener("click",async()=>{
        const t=$("dataBox").value||JSON.stringify(serializeState(),null,1);
        $("dataBox").value=t;
        try{ await navigator.clipboard.writeText(t); $("dataMsg").textContent="In Zwischenablage kopiert."; }
        catch(_){ $("dataBox").select(); $("dataMsg").textContent="Bitte manuell kopieren (Strg+C)."; }
    });
    $("btnImport").addEventListener("click",()=>{
        try{ const obj=JSON.parse($("dataBox").value); applyState(obj); $("dataMsg").textContent="Import übernommen."; }
        catch(e){ $("dataMsg").textContent="Import fehlgeschlagen: "+e.message; }
    });

    $("variants").addEventListener("click",e=>{ const b=e.target.closest("[data-idx]"); if(!b)return;
        state.selected=Number(b.dataset.idx); state.shown="auto"; setShownButtons("auto"); render(); });

    /* --- Zeiger-Interaktion: Klick setzt, Ziehen macht Reihe / verschiebt --- */
    function planPt(e){
        const rect=$("plan").getBoundingClientRect();
        return { x:(e.clientX-rect.left)/currentScale, y:(e.clientY-rect.top)/currentScale };
    }
    function handleClick(pt){
        const p=state, px=pt.x, py=pt.y;
        if(p.mode==="delete"){ deleteAt(pt); return; }
        if(p.mode==="column"){
            if(px<0||py<0||px>p.roomW||py>p.roomH) return;
            const size=400;
            p.columns.push({id:nextId++, x:clamp(px-size/2,0,p.roomW-size), y:clamp(py-size/2,0,p.roomH-size), w:size, h:size});
            render(); return;
        }
        if(p.mode==="door"){
            if(px<0||py<0||px>p.roomW||py>p.roomH) return;
            const dTop=py,dBottom=p.roomH-py,dLeft=px,dRight=p.roomW-px;
            const m=Math.min(dTop,dBottom,dLeft,dRight), width=Math.max(1000,p.aisle); let wall,pos;
            if(m===dTop){wall="top";pos=px;} else if(m===dBottom){wall="bottom";pos=px;}
            else if(m===dLeft){wall="left";pos=py;} else {wall="right";pos=py;}
            const span=(wall==="top"||wall==="bottom")?p.roomW:p.roomH;
            p.doors.push({id:nextId++,wall,pos:clamp(pos,width/2,span-width/2),width});
            render(); return;
        }
        if(p.mode==="shelf"||p.mode==="row"){ placeShelf(pt); return; }
        // move: einfacher Klick tut nichts
    }

    const svgEl=$("plan");
    let drag=null;
    svgEl.addEventListener("pointerdown",e=>{
        const pt=planPt(e);
        drag={ start:pt, cur:pt, moved:false, mode:state.mode, idx:-1, orig:null };
        if(state.mode==="move"){
            const i=findManualShelfAt(pt);
            if(i<0){ drag=null; return; }
            drag.idx=i; drag.orig={...state.manual[i]};
        }
        try{ svgEl.setPointerCapture(e.pointerId); }catch(_){}
    });
    svgEl.addEventListener("pointermove",e=>{
        if(!drag) return;
        drag.cur=planPt(e);
        if(Math.hypot(drag.cur.x-drag.start.x, drag.cur.y-drag.start.y) > SNAP) drag.moved=true;
        if(drag.mode==="row" && drag.moved) showRowGhost(drag.start, drag.cur);
        else if(drag.mode==="move") showMoveGhost(drag);
    });
    svgEl.addEventListener("pointerup",e=>{
        if(!drag) return;
        const d=drag; drag=null; clearGhost();
        if(!d.moved){ handleClick(d.start); return; }
        if(d.mode==="row") placeRow(d.start, d.cur);
        else if(d.mode==="shelf") placeShelf(d.start);
        else if(d.mode==="move") commitMove(d);
    });
    svgEl.addEventListener("pointercancel",()=>{ drag=null; clearGhost(); });

    $("btnPrint").addEventListener("click",()=>window.print());
    $("btnPng").addEventListener("click",()=>{
        clearGhost();
        const svg=$("plan"); const cs=getComputedStyle(document.documentElement);
        let str=new XMLSerializer().serializeToString(svg);
        str=str.replace(/var\(([^)]+)\)/g,(_,n)=>cs.getPropertyValue(n.trim()).trim()||"#000");
        const w=svg.getAttribute("width"),h=svg.getAttribute("height");
        const img=new Image();
        img.onload=()=>{ const sc=2, cv=document.createElement("canvas"); cv.width=w*sc; cv.height=h*sc;
            const ctx=cv.getContext("2d"); ctx.fillStyle="#F7F9FB"; ctx.fillRect(0,0,cv.width,cv.height);
            ctx.scale(sc,sc); ctx.drawImage(img,0,0);
            const a=document.createElement("a"); a.download="regalplan.png"; a.href=cv.toDataURL("image/png"); a.click(); };
        img.src="data:image/svg+xml;base64,"+btoa(unescape(encodeURIComponent(str)));
    });

    syncTypeB();
    render();
</script>
</body>
</html>