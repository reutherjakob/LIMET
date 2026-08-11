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
        .btnrow{display:flex;gap:6px;flex-wrap:wrap}
        .btn{border:1px solid var(--border);border-radius:6px;background:#fff;color:#475569;padding:7px 10px;font-size:12px;cursor:pointer;transition:.15s}
        .btn:hover{border-color:#94a3b8}
        .btn.active{border-color:#334155;background:#1E2A33;color:#fff}
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
        .stage{display:flex;justify-content:center;overflow:auto;border-radius:6px;background:var(--paper)}
        svg{display:block}
        .legend{display:flex;flex-wrap:wrap;gap:6px 16px;margin-top:8px;font-size:11px;color:var(--muted)}
        .legend .sw{display:inline-block;width:12px;height:12px;border-radius:3px;vertical-align:-2px;margin-right:5px}
        .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:16px}
        @media(max-width:560px){.stats{grid-template-columns:1fr 1fr}}
        .stat{border:1px solid #e2e8f0;background:#fff;border-radius:6px;padding:8px 12px}
        .stat .k{font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted)}
        .stat .v{font:18px/1.2 ui-monospace,monospace;color:#334155}
        .stat .v .u{font-size:12px;color:#94a3b8;margin-left:4px;font-family:inherit}
        .tools{display:flex;gap:6px}
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
    <p class="sub">Raum maximal mit Regalen füllen — erreichbar, Gangbreite eingehalten, um Säulen herum geplant. Zwei Regaltypen mischbar. Unter flächengleichen Lösungen wird der Weg von der Tür minimiert.</p>

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
                <div style="font-size:11px;color:var(--muted);margin-top:8px">Perimeter-Varianten (alle Wände / Längs / Quer) werden ohnehin automatisch geprüft und erscheinen oben, wenn sie flächeneffizienter sind. Ankreuzen erzwingt zusätzlich genau diese Wandkombination.</div>
            </section>

            <section class="panel soft">
                <p class="cap">Varianten · Top 3</p>
                <div id="variants"></div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px">Erst maximale Regalfläche, dann kürzester Weg. Antippen zum Anzeigen.</div>
            </section>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px">
            <div class="panel">
                <div class="planhead">
                    <div class="btnrow" id="modeSeg">
                        <button class="btn" data-mode="column">＋ Säule</button>
                        <button class="btn" data-mode="door">＋ Tür</button>
                        <button class="btn" data-mode="delete">✕ Löschen</button>
                    </div>
                    <div class="tools">
                        <button class="btn" id="btnPng">PNG speichern</button>
                        <button class="btn" id="btnPrint">Drucken</button>
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

    const state = {
        roomW: 12000, roomH: 8000, aisle: 1200,
        type1W: 1200, type1D: 600,
        type2W: 800,  type2D: 600, useType2: false,
        wallGap: 0, colClear: 0,
        walls: { top:false, bottom:false, left:false, right:false },
        columns: [],
        doors: [{ id: 1, wall: "left", pos: 4000, width: 1200 }],
        mode: "column", selected: 0,
    };
    let nextId = 2;

    /* ---------------------------------------------------------------------
       SMALL HELPERS
    --------------------------------------------------------------------- */
    const $ = (id) => document.getElementById(id);
    const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
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
       region (optional): {aStart,aEnd,cStart,cEnd}
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

        // Quergänge (spines) → A-Positionen; verbrauchen je eine Gangbreite
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

        // Innenbereich-Grenzen (perimeter + Gang abrücken, wenn Wand belegt)
        const xLo = sel.left  ? wallGap+bd+aisle : wallGap;
        const xHi = sel.right ? roomW-wallGap-bd-aisle : roomW-wallGap;
        const yLo = sel.top   ? wallGap+bd+aisle : wallGap;
        const yHi = sel.bottom? roomH-wallGap-bd-aisle : roomH-wallGap;

        // Wandreihen: Ecken den vertikalen Wänden zuschlagen, damit sie sich nicht überlappen
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

        // Innenfüllung mit Kern-Generator im gewählten orient
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
        let sum=0,cnt=0,mx=0;
        shelves.forEach((s,k)=>{
            const c0=clamp(Math.floor(s.x/res),0,cols-1),c1=clamp(Math.ceil((s.x+s.w)/res)-1,0,cols-1);
            const r0=clamp(Math.floor(s.y/res),0,rows-1),r1=clamp(Math.ceil((s.y+s.h)/res)-1,0,rows-1);
            let best=BIG;
            for(let c=c0;c<=c1;c++){ if(r0-1>=0&&acc[(r0-1)*cols+c])best=Math.min(best,gdist[(r0-1)*cols+c]);
                if(r1+1<rows&&acc[(r1+1)*cols+c])best=Math.min(best,gdist[(r1+1)*cols+c]); }
            for(let r=r0;r<=r1;r++){ if(c0-1>=0&&acc[r*cols+(c0-1)])best=Math.min(best,gdist[r*cols+(c0-1)]);
                if(c1+1<cols&&acc[r*cols+(c1+1)])best=Math.min(best,gdist[r*cols+(c1+1)]); }
            if(best<BIG){ served[k]=true; const mm=best*res; sum+=mm; cnt++; if(mm>mx)mx=mm; }
        });
        return {served, count:cnt, doorsWithoutAccess, avgWalk:cnt?sum/cnt:1e9, maxWalk:mx};
    }

    function doorSpineCoord(d, orient, p){
        if(orient==="h") return (d.wall==="top"||d.wall==="bottom") ? d.pos : (d.wall==="left"? p.aisle/2 : p.roomW-p.aisle/2);
        return (d.wall==="left"||d.wall==="right") ? d.pos : (d.wall==="top"? p.aisle/2 : p.roomH-p.aisle/2);
    }

    /* ---------------------------------------------------------------------
       OPTIMIZER: 1) max erreichbare Regale  2) min Weg
    --------------------------------------------------------------------- */
    function scoreCandidate(gen, p){
        const ev=evaluate(gen.shelves, p);
        let area=0, lin=0;
        gen.shelves.forEach((s,i)=>{ if(ev.served[i]){ area+=s.w*s.h; lin+=s.len; } });
        return {shelves:gen.shelves, cross:gen.cross, bandsCount:gen.bandsCount, walls:gen.walls,
            served:ev.served, count:ev.count, total:gen.shelves.length,
            avgWalk:ev.avgWalk, doorsWithoutAccess:ev.doorsWithoutAccess, area, lin};
    }

    // Welche Wandkombinationen automatisch als Perimeter-Varianten geprüft werden
    function wallSetsFor(p){
        const longIsX = p.roomW >= p.roomH;
        const longPair  = longIsX ? ['top','bottom'] : ['left','right'];
        const shortPair = longIsX ? ['left','right'] : ['top','bottom'];
        const sets = [ ['top','bottom','left','right'], longPair, shortPair ];
        // vom Nutzer erzwungene Wandwahl zusätzlich aufnehmen (falls nicht schon dabei)
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

        for(const bd of depths){
            const period=p.aisle+2*bd;
            const leads=[0, Math.round(period/2)];

            // (1) Freie Anordnungen ohne Wandreihen — inkl. Quergang-Varianten
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

            // (2) Perimeter-Anordnungen — IMMER automatisch geprüft (alle 4 / Längs / Quer)
            for(const walls of wallSetsFor(p)){
                for(const orient of ["h","v"]){
                    const gen=generateLayout({orient,lead:0,spines:[],bd,walls}, p);
                    if(!gen.shelves.length) continue;
                    pool.push(scoreCandidate(gen, p));
                }
            }
        }

        if(!pool.length) return [{shelves:[],cross:[],bandsCount:0,walls:null,served:[],count:0,total:0,avgWalk:0,doorsWithoutAccess:0,area:0,lin:0}];

        // ZIEL: maximale nutzbare Regalfläche; bei ~gleicher Fläche kürzester Weg.
        const BUCKET=0.25e6; // 0,25 m² Toleranz → "flächengleich"
        pool.sort((a,b)=>{
            const ba=Math.round(b.area/BUCKET), aa=Math.round(a.area/BUCKET);
            return ba-aa || a.avgWalk-b.avgWalk || b.area-a.area;
        });

        const chosen=[]; const seen=new Set();
        for(const c of pool){
            const sig=(c.walls?c.walls.slice().sort().join(""):"-")+"|"+c.bandsCount+"|"+c.total;
            if(seen.has(sig)) continue; seen.add(sig); chosen.push(c);
            if(chosen.length===3) break;
        }
        for(const c of pool){ if(chosen.length>=3)break; if(!chosen.includes(c)) chosen.push(c); }
        return chosen;
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
       RENDER
    --------------------------------------------------------------------- */
    let currentScale=1;

    function renderVariants(top){
        const p=state; const box=$("variants"); const roomArea=(p.roomW*p.roomH)/1e6;
        box.innerHTML=top.map((c,idx)=>{
            const area=c.area/1e6;
            const util=roomArea>0?(area/roomArea*100):0;
            const walk=c.count?(c.avgWalk/1000).toFixed(1):"–";
            const tag=(c.walls&&c.walls.length)?'<span class="vtag">Wand</span>':'';
            return `<button class="vcard${idx===p.selected?' active':''}" data-idx="${idx}">
      <div class="vrow"><span class="vrank">${idx+1}</span><span class="vlabel">${variantLabel(c)}</span>${tag}</div>
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

    function render(){
        const p=state;
        const top=optimize(p);
        if(p.selected>=top.length) p.selected=0;
        const best=top[p.selected]||top[0];
        renderVariants(top);

        const MAXW=660, MAXH=460;
        const scale=Math.min(MAXW/p.roomW, MAXH/p.roomH);
        currentScale=scale;
        const svgW=p.roomW*scale, svgH=p.roomH*scale;
        const S=(v)=>v*scale;
        const parts=[];

        parts.push(`<rect x="0" y="0" width="${svgW}" height="${svgH}" fill="var(--paper)" stroke="var(--ink)" stroke-width="2"/>`);
        for(const cr of best.cross)
            parts.push(`<rect x="${S(cr.x)}" y="${S(cr.y)}" width="${S(cr.w)}" height="${S(cr.h)}" fill="var(--aisle)" opacity="0.6"/>`);
        for(let i=1;i*1000<p.roomW;i++) parts.push(`<line x1="${S(i*1000)}" y1="0" x2="${S(i*1000)}" y2="${svgH}" stroke="var(--grid)" stroke-width="1"/>`);
        for(let i=1;i*1000<p.roomH;i++) parts.push(`<line x1="0" y1="${S(i*1000)}" x2="${svgW}" y2="${S(i*1000)}" stroke="var(--grid)" stroke-width="1"/>`);

        // Türschwenk-Flächen (unter den Regalen, halbtransparent)
        for(const d of p.doors)
            parts.push(`<path d="${swingPath(doorSwing(d,p), S)}" fill="var(--swing)" opacity="0.08" stroke="var(--swing)" stroke-opacity="0.35" stroke-width="1" stroke-dasharray="4 3"/>`);

        // nicht erreichbare Stellplätze
        best.shelves.forEach((s,i)=>{ if(!best.served[i])
            parts.push(`<rect x="${S(s.x)}" y="${S(s.y)}" width="${S(s.w)}" height="${S(s.h)}" fill="none" stroke="#bb1188" stroke-dasharray="3 3" stroke-width="0.8" opacity="0.55"/>`); });
        // erreichbare Regale (Typ A / B eingefärbt)
        best.shelves.forEach((s,i)=>{ if(best.served[i]){
            const fill=s.type===1?"var(--shelf2)":"var(--shelf)";
            const edge=s.type===1?"var(--shelf2-edge)":"var(--shelf-edge)";
            parts.push(`<rect x="${S(s.x)}" y="${S(s.y)}" width="${S(s.w)}" height="${S(s.h)}" fill="${fill}" stroke="${edge}" stroke-width="0.75" opacity="0.92"/>`);
        }});

        for(const c of p.columns){
            if(p.colClear>0)
                parts.push(`<rect x="${S(c.x-p.colClear)}" y="${S(c.y-p.colClear)}" width="${S(c.w+2*p.colClear)}" height="${S(c.h+2*p.colClear)}" fill="none" stroke="var(--column)" stroke-dasharray="3 3" stroke-width="1" opacity="0.5"/>`);
            parts.push(`<rect x="${S(c.x)}" y="${S(c.y)}" width="${S(c.w)}" height="${S(c.h)}" fill="var(--column)"/>`);
        }

        // Türen — feste Bildschirmdicke (Bugfix A1)
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
        svg.style.cursor = p.mode==="delete" ? "not-allowed" : "crosshair";
        svg.innerHTML=parts.join("");

        const roomArea=(p.roomW*p.roomH)/1e6;
        const shelfArea=best.area/1e6;
        $("stCount").textContent=best.count;
        $("stUtil").textContent=roomArea>0?(shelfArea/roomArea*100).toFixed(0):"0";
        $("stLin").textContent=(best.lin/1000).toFixed(1);
        $("stWalk").textContent=best.count?(best.avgWalk/1000).toFixed(1):"0";
        $("hint").textContent="In den Plan klicken zum Platzieren. Variante links wählen.";

        const removed=best.total-best.count;
        const narrowDoor=p.doors.filter(d=>d.width<p.aisle).length;
        const noteEl=$("note"); const msgs=[];
        if(!p.doors.length) msgs.push("Keine Tür gesetzt — ohne Zugang gilt kein Regal als erreichbar.");
        if(narrowDoor>0) msgs.push(narrowDoor+" Tür(en) schmaler als die Gangbreite ("+p.aisle+" mm) — mögliche Engstelle.");
        if(removed>0) msgs.push(removed+" Stellplätze dieser Variante ohne durchgehend breiten Zugang (ausgegraut).");
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

    const useT2=$("useType2");
    useT2.checked=state.useType2;
    $("typeBBox").classList.toggle("on", state.useType2);
    useT2.addEventListener("change",()=>{ state.useType2=useT2.checked; $("typeBBox").classList.toggle("on",state.useType2); render(); });

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

    $("variants").addEventListener("click",e=>{ const b=e.target.closest("[data-idx]"); if(!b)return;
        state.selected=Number(b.dataset.idx); render(); });

    $("plan").addEventListener("click",e=>{
        const rect=e.currentTarget.getBoundingClientRect();
        const px=(e.clientX-rect.left)/currentScale, py=(e.clientY-rect.top)/currentScale;
        if(px<0||py<0||px>state.roomW||py>state.roomH) return;
        if(state.mode==="delete"){
            const hc=state.columns.find(c=>pointInRect(px,py,c));
            if(hc){state.columns=state.columns.filter(c=>c.id!==hc.id);render();return;}
            const hd=state.doors.find(d=>pointInRect(px,py,doorZone(d,state.roomW,state.roomH,state.aisle)));
            if(hd){state.doors=state.doors.filter(d=>d.id!==hd.id);render();}
            return;
        }
        if(state.mode==="column"){ const size=400;
            state.columns.push({id:nextId++, x:clamp(px-size/2,0,state.roomW-size), y:clamp(py-size/2,0,state.roomH-size), w:size,h:size});
            render();return; }
        if(state.mode==="door"){
            const dTop=py,dBottom=state.roomH-py,dLeft=px,dRight=state.roomW-px;
            const m=Math.min(dTop,dBottom,dLeft,dRight), width=Math.max(1000,state.aisle); let wall,pos;
            if(m===dTop){wall="top";pos=px;} else if(m===dBottom){wall="bottom";pos=px;}
            else if(m===dLeft){wall="left";pos=py;} else {wall="right";pos=py;}
            const span=(wall==="top"||wall==="bottom")?state.roomW:state.roomH;
            state.doors.push({id:nextId++,wall,pos:clamp(pos,width/2,span-width/2),width});
            render();
        }
    });

    $("btnPrint").addEventListener("click",()=>window.print());
    $("btnPng").addEventListener("click",()=>{
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

    render();
</script>
</body>
</html>