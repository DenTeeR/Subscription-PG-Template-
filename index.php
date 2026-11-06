<?php

    // Requests without a User-Agent are sent back to the home page
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        header('Location: /');
        exit();
    }

    // str_contains needs PHP 8.0+
    if (!function_exists('str_contains')) {
        die('Please upgrade your PHP version to 8.0 or above');
    }

    $isHtmlRequest = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'text/html');

    // ------------------------------------------------------------------
    // Browser request: serve the page immediately.
    // The page loads its own data from JS (/info and /usage), so calling the
    // panel here was wasted work that blocked every refresh until the panel
    // answered (up to 17s) and showed a raw "cURL error" if it was down.
    // ------------------------------------------------------------------
    if ($isHtmlRequest) {
        $etag = 'xpn-' . dechex((int) @filemtime(__FILE__)) . '-' . dechex((int) @filesize(__FILE__));
        header('Vary: Accept');
        header('Cache-Control: no-cache');
        header('ETag: "' . $etag . '"');
        if (str_contains($_SERVER['HTTP_IF_NONE_MATCH'] ?? '', $etag)) {
            http_response_code(304);
            exit();
        }
        header('Content-Type: text/html; charset=UTF-8');
        if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
            ob_start('ob_gzhandler');
        }
        ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#000000">
<script>try{var d=document.documentElement,l=localStorage;d.dataset.theme=l.getItem("th")||"dark";d.dataset.accent=l.getItem("ac")||"mono"}catch(e){}document.documentElement.classList.add("ld");try{var n=navigator;if((n.deviceMemory||8)<=4||(n.hardwareConcurrency||8)<=4||(n.connection&&n.connection.saveData))document.documentElement.classList.add("gl-lite")}catch(e){}</script>
<script>function mi(l){l.media="all";var c=document.documentElement.classList,f=document.fonts;f&&f.load?f.load("24px Material Icons").then(function(){c.add("mi")},function(){}):c.add("mi")}</script>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" media="print" onload="mi(this)">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300..800&family=Inter:wght@400..800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" async onload="window.__qr&&__qr()"></script>
<title>XpnManage</title>
<style>
:root{--bg:#03024E;--bg2:#041b9c;--card:rgba(9,22,118,.55);--bd:rgba(96,130,255,.28);--tx:#eef2ff;--mut:#9aaae0;--acc:#0135FC;--acc2:#4a7bff;--grn:#22e6b0;--hi:#5aa2ff;--ar:1,53,252;--on:#fff;--at:#7ea3ff;--d:rtl;--pop:#08126b;--hh:60px;--ch:190px}
html[dir=ltr]{--d:ltr}
[data-theme=light]{--bg:#eef2ff;--bg2:#cdd9ff;--card:rgba(255,255,255,.88);--bd:rgba(1,53,252,.18);--tx:#03024E;--mut:#4f5b94;--acc:#0135FC;--acc2:#3b6bff;--grn:#09a47c;--hi:#2f7bff;--at:#0135FC;--pop:#fff}
[data-accent=green]{--bg:#2d531a;--bg2:#477023;--card:rgba(7,30,7,.46);--bd:rgba(176,205,135,.28);--tx:#f1f7ea;--mut:#c2d2ab;--acc:#6e8649;--acc2:#9bb56f;--hi:#b5d17f;--ar:110,134,73;--pop:#0d330e;--on:#071e07;--at:#c8de9c}
[data-theme=light][data-accent=green]{--bg:#f1f6ec;--bg2:#d3e1c3;--card:rgba(255,255,255,.88);--bd:rgba(45,83,26,.22);--tx:#0d330e;--mut:#506b3f;--acc:#2d531a;--acc2:#477023;--hi:#6e8649;--ar:45,83,26;--pop:#fff;--on:#fff;--at:#2d531a}
[data-accent=yellow]{--bg:#120a02;--bg2:#5c3400;--card:rgba(48,29,6,.62);--bd:rgba(255,176,32,.24);--tx:#fff4e0;--mut:#cdb08a;--acc:#f59e0b;--acc2:#ffc247;--hi:#ff7a21;--ar:245,158,11;--pop:#2e1c06;--on:#2a1c00;--at:#ffc653}
[data-theme=light][data-accent=yellow]{--bg:#fff8eb;--bg2:#ffe0a3;--card:rgba(255,255,255,.9);--bd:rgba(194,120,3,.26);--tx:#2b1a00;--mut:#80622e;--acc:#f59e0b;--acc2:#ffb73a;--hi:#f97316;--ar:245,158,11;--pop:#fff;--on:#2a1c00;--at:#a65a00}
[data-accent=purple]{--bg:#320850;--bg2:#5B1690;--card:rgba(91,22,144,.42);--bd:rgba(176,120,216,.3);--tx:#f6ecff;--mut:#c4a6de;--acc:#8230C0;--acc2:#9954CC;--hi:#B078D8;--ar:130,48,192;--pop:#411069;--on:#fff;--at:#d2aef0}
[data-theme=light][data-accent=purple]{--bg:#f6effc;--bg2:#E0C8F0;--card:rgba(255,255,255,.88);--bd:rgba(91,22,144,.2);--tx:#320850;--mut:#6b4a88;--acc:#5B1690;--acc2:#8230C0;--hi:#B078D8;--ar:91,22,144;--pop:#fff;--on:#fff;--at:#5B1690}
/* Christmas: pine night + cranberry + gold (dark) / snow + pine + cranberry (light) */
:root[data-accent=xmas]{--bg:#051f16;--bg2:#0d4a30;--card:rgba(6,36,25,.62);--bd:rgba(255,222,160,.2);--tx:#f6fff9;--mut:#a5cbb8;--acc:#cf1f3b;--acc2:#e8425a;--hi:#ffc857;--ar:207,31,59;--pop:#0a2c1e;--on:#fff;--at:#ffd98a;--gl-hl:rgba(255,240,205,.34);--gl-bd:rgba(255,228,170,.2);--sn:rgba(255,255,255,.9)}
:root[data-theme=light][data-accent=xmas]{--bg:#f4faf5;--bg2:#cfe8d9;--card:rgba(255,255,255,.9);--bd:rgba(14,90,58,.2);--tx:#0c3a28;--mut:#476f5b;--acc:#c8102e;--acc2:#df3a52;--hi:#d99a1c;--ar:200,16,46;--pop:#fff;--on:#fff;--at:#b50f2b;--gl-hl:#fff;--gl-bd:rgba(255,255,255,.85);--gl-sh:0 26px 46px -28px rgba(12,84,54,.42),0 2px 8px -4px rgba(12,84,54,.16);--sn:rgba(84,150,205,.62)}
[data-accent=xmas] body{background:radial-gradient(900px 520px at 85% -10%,var(--bg2),transparent),radial-gradient(700px 420px at 0 105%,rgba(var(--ar),.1),transparent),var(--bg)}
[data-accent=green] body,[data-accent=yellow] body,[data-accent=purple] body{background:radial-gradient(900px 520px at 85% -10%,var(--bg2),transparent),radial-gradient(700px 420px at 0 105%,color-mix(in srgb,var(--hi) 16%,transparent),transparent),var(--bg)}
/* mono theme (default): dark = black bg / white fg, light = white bg / black fg */
:root[data-accent=mono]{--bg:#000;--bg2:#1f1f1f;--card:rgba(26,26,26,.62);--bd:rgba(255,255,255,.2);--tx:#fff;--mut:#a3a3a3;--acc:#fff;--acc2:#d4d4d4;--hi:#e5e5e5;--ar:255,255,255;--pop:#111;--on:#000;--at:#fff;--gl-f1:rgba(255,255,255,.1);--gl-f2:rgba(255,255,255,.03);--gl-hl:rgba(255,255,255,.3);--gl-bd:rgba(255,255,255,.18);--gl-bo:.3}
:root[data-theme=light][data-accent=mono]{--bg:#fff;--bg2:#ebebeb;--card:rgba(244,244,245,.92);--bd:rgba(0,0,0,.16);--tx:#000;--mut:#555;--acc:#000;--acc2:#2e2e2e;--hi:#404040;--ar:0,0,0;--pop:#fff;--on:#fff;--at:#000;--gl-f1:rgba(255,255,255,.55);--gl-f2:rgba(255,255,255,.2);--gl-hl:#fff;--gl-bd:rgba(0,0,0,.14);--gl-sh:0 22px 40px -28px rgba(0,0,0,.4),0 2px 8px -4px rgba(0,0,0,.16);--gl-bo:.14}
h3{font-size:15px}
html:not(.mi) .material-icons{font-size:0!important}html:not(.mi) summary::after{font-size:0}
.ths{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-top:16px}
.tc{position:relative;display:grid;gap:12px;padding:10px 10px 14px;border:1px solid var(--bd);border-radius:24px;background:rgba(var(--ar),.05);cursor:pointer;text-align:start;font-size:14px;font-weight:600;transition:transform .2s,box-shadow .3s,border-color .3s,background .3s}
.tc:active{transform:scale(.97)}
.tc i{position:relative;display:block;height:88px;border-radius:16px;overflow:hidden;background:linear-gradient(135deg,var(--p1),var(--p2) 55%,var(--p3));box-shadow:inset 0 1px 0 rgba(255,255,255,.45),0 10px 20px -10px var(--p1)}
.tc i::before{content:"";position:absolute;inset-inline-start:10px;bottom:10px;width:40%;height:22px;border-radius:999px;background:rgba(255,255,255,.92);box-shadow:0 4px 10px rgba(0,0,0,.18)}
.tc i::after{content:"";position:absolute;inset-inline-end:12px;bottom:12px;width:50px;height:30px;opacity:.9;filter:drop-shadow(0 1px 2px rgba(0,0,0,.25));background:linear-gradient(#fff,#fff) 0 100%/8px 40% no-repeat,linear-gradient(#fff,#fff) 14px 100%/8px 70% no-repeat,linear-gradient(#fff,#fff) 28px 100%/8px 100% no-repeat,linear-gradient(#fff,#fff) 42px 100%/8px 55% no-repeat}
.tc>span:not(.ck){padding:0 4px}
.tc .ck{position:absolute;top:18px;inset-inline-end:18px;display:grid;place-items:center;width:26px;height:26px;border-radius:50%;font-size:18px;line-height:1;color:color-mix(in srgb,var(--p1),#000 35%);background:#fff;box-shadow:0 4px 12px rgba(0,0,0,.28);opacity:0;transform:scale(.4);transition:opacity .2s,transform .35s cubic-bezier(.3,1.7,.5,1)}
.tc[aria-pressed=true]{border-color:var(--p2);background:rgba(var(--ar),.1);box-shadow:0 0 0 3px rgba(var(--ar),.2),0 18px 36px -18px var(--p1)}
.tc[aria-pressed=true] .ck{opacity:1;transform:none}
@media(prefers-reduced-motion:reduce){.tc,.tc .ck{transition:none}.tc:active{transform:none}}
#st h3{display:flex;align-items:center;gap:10px;font-size:16px}
#st h3::before{content:'palette';font-family:'Material Icons';display:grid;place-items:center;flex:none;width:34px;height:34px;border-radius:12px;font-size:20px;line-height:1;color:var(--on);background:linear-gradient(135deg,var(--acc),var(--acc2));box-shadow:0 8px 18px -8px rgba(var(--ar),.8)}
html:not(.mi) #st h3::before{font-size:0}
#st h3+.mut{margin-top:6px}
*{box-sizing:border-box;margin:0;-webkit-tap-highlight-color:transparent}
html{-webkit-text-size-adjust:100%;overflow-x:hidden}
body{font-family:'Inter',Vazirmatn,Tahoma,system-ui,sans-serif;font-weight:450;color:var(--tx);background:radial-gradient(900px 520px at 85% -10%,var(--bg2),transparent),var(--bg);min-height:100vh;line-height:1.6}
button,select,a{font:inherit;color:inherit}
.top{position:sticky;top:0;z-index:30;display:flex;justify-content:space-between;align-items:center;gap:12px;min-height:var(--hh);padding:10px 16px;direction:ltr;background:var(--card);backdrop-filter:blur(14px);border-bottom:1px solid var(--bd)}
.brand{display:flex;gap:10px;align-items:center;min-width:0}
.logo{width:40px;height:40px;flex:none;display:grid;place-items:center;border-radius:13px;color:var(--on);background:linear-gradient(135deg,var(--acc),var(--acc2));box-shadow:inset 0 1px 0 rgba(255,255,255,.35),0 8px 18px -8px rgba(var(--ar),.55)}.logo svg{width:26px;height:26px;display:block}
.bt{min-width:0}
.brand b{font-size:17px;display:block;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.brand small{display:block;color:var(--mut);font-size:11px;line-height:1.4;white-space:normal;overflow:hidden}
.ctl{display:flex;gap:8px;align-items:center;flex:none}
.ib,.lgb{background:var(--card);border:1px solid var(--bd);border-radius:12px;height:40px;padding:0 10px;cursor:pointer}
.ib{width:40px;padding:0;display:grid;place-items:center;flex:none}
.lg{position:relative}
.lgb{display:flex;align-items:center;gap:7px;padding:0 9px 0 11px;font-size:14px;white-space:nowrap}
.lgb:hover,.lgb.open{border-color:var(--acc2)}
.lgb svg{width:20px;height:20px;flex:none}.lgb .gl{color:var(--at)}
.lgb .cv{width:16px;height:16px;color:var(--mut);transition:transform .2s}.lgb.open .cv{transform:rotate(180deg)}
.lgm{position:absolute;top:calc(100% + 8px);right:0;width:max-content;min-width:max(100%,140px);margin:0;padding:6px;list-style:none;background:var(--pop);border:1px solid var(--bd);border-radius:14px;box-shadow:0 18px 40px rgba(0,0,0,.35);animation:pop .15s ease-out;z-index:40}
.lgm[hidden]{display:none}
.lgm li{display:flex;align-items:center;justify-content:space-between;gap:18px;min-height:42px;padding:0 12px;border-radius:10px;cursor:pointer;font-size:14px;outline:0}
.lgm li:hover,.lgm li:focus-visible{background:rgba(var(--ar),.22)}
.lgm li[aria-selected=true]{background:linear-gradient(135deg,var(--acc),var(--acc2));color:var(--on)}
.lgm li svg{width:16px;height:16px;flex:none;opacity:0}.lgm li[aria-selected=true] svg{opacity:1}
@keyframes pop{from{opacity:0;transform:translateY(-6px)}}
@media(prefers-reduced-motion:reduce){.lgm{animation:none}.lgb .cv{transition:none}}
.av{position:relative;width:40px;height:40px;flex:none;border:2px solid transparent;border-radius:50%;display:grid;place-items:center;color:var(--on);background:linear-gradient(135deg,var(--acc),var(--acc2)) padding-box,conic-gradient(from 210deg,var(--hi),var(--acc2),var(--acc),var(--hi)) border-box;background-origin:border-box;box-shadow:0 6px 18px rgba(var(--ar),.45)}
.av>svg{width:21px;height:21px}
.avb{position:absolute;right:-5px;bottom:-5px;width:17px;height:17px;border-radius:50%;display:grid;place-items:center;background:var(--grn);box-shadow:0 0 0 2px var(--bg)}.avb svg{width:11px;height:11px}
html[dir=rtl] .top{direction:rtl}
html[dir=rtl] .lg{margin-right:0;margin-left:auto}
html[dir=rtl] .lgm{right:0;left:auto}
.hero-wave svg{display:block;width:15px;height:15px}
.mdr{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:16px;font-size:14px;font-weight:600}
.seg{flex:1 1 220px;max-width:340px;display:flex;gap:4px;padding:4px;border:1px solid var(--bd);border-radius:14px;background:rgba(var(--ar),.05)}
.sg{flex:1;display:flex;align-items:center;justify-content:center;gap:7px;height:38px;padding:0 14px;border:0;border-radius:10px;background:transparent;cursor:pointer;font-size:14px;font-weight:600;transition:background .25s,color .25s,box-shadow .25s}
.sg .material-icons{font-size:19px}
.sg[aria-pressed=true]{background:linear-gradient(135deg,var(--acc),var(--acc2));color:var(--on);box-shadow:0 8px 18px -8px rgba(var(--ar),.6)}
.sg:focus-visible{outline:2px solid var(--acc2);outline-offset:2px}
@media(prefers-reduced-motion:reduce){.sg{transition:none}}
button:focus-visible{outline:2px solid var(--acc2);outline-offset:2px}
.app{display:grid;grid-template-columns:210px 1fr;direction:ltr;max-width:1200px;margin:auto}
.side,.main{direction:var(--d)}
.side{position:sticky;top:var(--hh);align-self:start;height:calc(100vh - var(--hh));padding:16px 12px;border-right:1px solid var(--bd)}
.side a{display:flex;gap:10px;align-items:center;padding:11px 14px;border-radius:12px;color:var(--mut);text-decoration:none;margin-bottom:6px}
.side a:hover,.side a.on{background:linear-gradient(135deg,var(--acc),var(--acc2));color:var(--on)}
.main{padding:20px 14px 100px;min-width:0}
h1{font-size:22px}.mut,small{color:var(--mut);font-size:13px}
.card{background:var(--card);border:1px solid var(--bd);border-radius:18px;padding:16px;margin-top:14px;backdrop-filter:blur(10px)}
.c3{display:grid;grid-template-columns:repeat(auto-fit,minmax(215px,1fr));gap:12px;margin-top:16px}
.c3 .card{margin:0;display:flex;gap:12px;align-items:center}
.ico{width:46px;height:46px;border-radius:50%;display:grid;place-items:center;background:rgba(var(--ar),.18);color:var(--at);flex:none}
.c3 b{font-size:22px;display:block;color:var(--at);line-height:1.3}
.c3 .ok{border-color:rgba(34,230,176,.45);box-shadow:0 0 28px rgba(34,230,176,.14)}.ok b,.ok .ico{color:var(--grn)}
.bad b,.bad .ico{color:#ff7b8a}
.hd{display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:12px}.hd h2{font-size:17px}
.pills{display:flex;gap:6px;flex-wrap:wrap}
.pills button{border:1px solid var(--bd);background:transparent;color:var(--mut);padding:4px 12px;border-radius:999px;cursor:pointer;font-size:13px}
.pills .on{background:linear-gradient(135deg,var(--acc),var(--acc2));color:var(--on);border-color:transparent}
.plot{display:flex;direction:ltr;gap:8px}
.yl{display:flex;flex-direction:column;justify-content:space-between;height:var(--ch);font-size:11px;color:var(--mut);min-width:54px;text-align:right}
.cw{flex:1;min-width:0}
.bars{height:var(--ch);overflow:hidden;display:flex;align-items:flex-end;gap:3px;background:repeating-linear-gradient(to top,var(--bd) 0 1px,transparent 1px 25%)}
.bars i{flex:1;min-width:2px;border-radius:6px 6px 2px 2px;background:linear-gradient(to top,var(--acc),var(--hi))}
.xl{position:relative;height:20px;margin-top:6px;font-size:11px;color:var(--mut)}
.xl span{position:absolute;transform:translateX(-50%);white-space:nowrap}
.sts{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:12px;padding:12px;border:1px solid var(--bd);border-radius:14px}
.sts b{font-size:18px;display:block}
details{border:1px solid var(--bd);border-radius:14px;margin-top:8px;background:rgba(var(--ar),.07)}
summary{list-style:none;cursor:pointer;display:flex;gap:10px;align-items:center;padding:12px 14px;font-weight:500}
summary::-webkit-details-marker{display:none}
summary::after{content:'expand_more';font-family:'Material Icons';margin-inline-start:auto;color:var(--mut)}
.in{padding:0 12px 12px}
.a1{padding:10px 0;border-top:1px solid var(--bd)}
.acts{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px}
.btn{padding:6px 14px;border-radius:999px;background:linear-gradient(135deg,var(--acc),var(--acc2));color:var(--on);text-decoration:none;font-size:13px}
.lk{display:flex;gap:10px;align-items:center;padding:10px 10px 10px 12px;border:1px solid var(--bd);border-radius:14px;background:rgba(var(--ar),.08)}
.lk code{flex:1;min-width:0;direction:ltr;text-align:left;word-break:break-all;user-select:all;font:13px/1.7 ui-monospace,SFMono-Regular,Menlo,Consolas,monospace}
.qc{display:flex;flex-direction:column;align-items:center;gap:14px;text-align:center;background:linear-gradient(160deg,rgba(var(--ar),.22),var(--card) 60%)}
.qc .hd{margin:0}
.qf{position:relative;padding:22px;border-radius:28px;background:linear-gradient(135deg,var(--acc),var(--acc2) 50%,var(--hi));box-shadow:0 18px 44px rgba(var(--ar),.38)}
.qf #qp{background:#fff;padding:14px;border-radius:18px;line-height:0;min-height:60px}
.qf i{position:absolute;width:26px;height:26px;border:0 solid var(--on)}
.qf i:nth-child(1){top:8px;left:8px;border-width:3px 0 0 3px;border-top-left-radius:12px}
.qf i:nth-child(2){top:8px;right:8px;border-width:3px 3px 0 0;border-top-right-radius:12px}
.qf i:nth-child(3){bottom:8px;left:8px;border-width:0 0 3px 3px;border-bottom-left-radius:12px}
.qf i:nth-child(4){bottom:8px;right:8px;border-width:0 3px 3px 0;border-bottom-right-radius:12px}
.qc .btn{border:0;cursor:pointer;display:inline-flex;align-items:center;gap:6px;padding:9px 20px;font-size:14px}.qc .btn .material-icons{font-size:18px}

#qrb{background:#fff;padding:8px;border-radius:10px;line-height:0;min-height:60px}
#qrb{word-break:break-all;line-height:1.4;color:#111;font-size:11px;max-width:290px}
.qm{position:fixed;inset:0;background:rgba(0,0,0,.65);display:grid;place-items:center;z-index:50}.qm[hidden]{display:none}
.bot{position:fixed;inset:auto 0 0;display:flex;justify-content:space-around;padding:8px 8px calc(8px + env(safe-area-inset-bottom));background:var(--card);backdrop-filter:blur(14px);border-top:1px solid var(--bd);z-index:20}
.bot a{display:grid;justify-items:center;gap:3px;min-width:84px;font-size:11px;color:var(--mut);text-decoration:none}.bot a.on{color:var(--tx)}.bot a.on .material-icons{padding:3px 20px;border-radius:999px;background:linear-gradient(135deg,var(--acc),var(--acc2));color:var(--on)}
#toast{position:fixed;bottom:88px;left:50%;transform:translateX(-50%);background:var(--acc);color:var(--on);padding:8px 18px;border-radius:999px;opacity:0;transition:.25s;pointer-events:none;z-index:60}
#toast.s{opacity:1}
.spb{display:inline-flex;align-items:center;gap:9px;padding:7px 16px;border-radius:999px;font-size:15px;font-weight:600;color:var(--grn);background:rgba(34,230,176,.12);border:1px solid rgba(34,230,176,.4)}
.spb i{position:relative;width:10px;height:10px;flex:none;border-radius:50%;background:currentColor}
.spb i::after{content:"";position:absolute;inset:0;border-radius:50%;background:currentColor;opacity:0;pointer-events:none}
.spb.off{color:#ff7b8a;background:rgba(255,123,138,.12);border-color:rgba(255,123,138,.4)}
@keyframes pl{0%{transform:scale(1);opacity:.6}70%,100%{transform:scale(2.8);opacity:0}}
.spm{max-width:460px;font-size:15px}
.qc .btn.big{padding:12px 30px;font-size:15px;font-weight:600}
html[dir=rtl] .fl{transform:scaleX(-1)}
@media(max-width:640px){.c3.t2 .card:first-child{grid-column:auto;flex-direction:column;align-items:flex-start;gap:8px}.c3.t2 b{font-size:17px}}
@media(prefers-reduced-motion:reduce){.spb i{animation:none}}
@media(min-width:900px){.bot{display:none}.main{padding-bottom:40px}}
@media(max-width:899px){.app{grid-template-columns:1fr}.side{display:none}}
@media(max-width:640px){
:root{--ch:160px}
.top{position:relative;flex-wrap:wrap;gap:10px;padding:max(10px,env(safe-area-inset-top)) max(14px,env(safe-area-inset-right)) 10px max(14px,env(safe-area-inset-left))}
.brand{flex:1 1 100%}.brand b{font-size:15px}
.ctl{flex:1 1 100%}.lg{margin-right:auto}.lgm{right:auto;left:0}
.main{padding:16px max(12px,env(safe-area-inset-right)) calc(84px + env(safe-area-inset-bottom)) max(12px,env(safe-area-inset-left))}
h1{font-size:19px;overflow-wrap:anywhere}
.card{padding:14px;border-radius:16px}
.c3{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
.c3 .card:first-child{grid-column:1/-1}
.c3 .card:not(:first-child){flex-direction:column;align-items:flex-start;gap:8px}
.ico{width:40px;height:40px}
.c3 b{font-size:19px;overflow-wrap:anywhere}
.pills{width:100%}.pills button{flex:1 1 auto;padding:6px 8px;font-size:12.5px}
.plot{gap:6px}.yl{min-width:46px;font-size:10px}.bars{gap:2px}
.sts{gap:6px;padding:10px}.sts>div{min-width:0}.sts b{font-size:15px;overflow-wrap:anywhere}.sts small{display:block;font-size:11.5px;line-height:1.35}

#toast{bottom:calc(76px + env(safe-area-inset-bottom))}
}
@media(max-width:359px){.c3{grid-template-columns:1fr}}
.lgb .fl{display:none}
html[lang=fa] .lgb .f-fa,html[lang=en] .lgb .f-en{display:block}
.lgm li .ln{display:flex;align-items:center;gap:10px}
.lgm li svg.fl{width:20px;height:20px;opacity:1}
#ldg{position:fixed;inset:0;z-index:100;display:grid;place-items:center;padding:24px;direction:ltr;background:radial-gradient(560px 420px at 50% 42%,rgba(var(--ar),.2),transparent 70%),var(--bg);transition:opacity .5s ease,visibility .5s}
#ldg.out{opacity:0;visibility:hidden;pointer-events:none}
.ldg-box{display:grid;justify-items:center;gap:30px;max-width:100%;text-align:center}
.ldg-ico{position:relative;width:104px;height:104px;display:grid;place-items:center}
.ldg-glow,.ldg-track,.ldg-ring{position:absolute;border-radius:50%}
.ldg-glow{inset:-26px;background:radial-gradient(circle,rgba(var(--ar),.38),transparent 66%);animation:ldgPulse 2.4s ease-in-out infinite}
.ldg-track,.ldg-ring{inset:0;-webkit-mask:radial-gradient(farthest-side,transparent calc(100% - 5px),#000 calc(100% - 4px));mask:radial-gradient(farthest-side,transparent calc(100% - 5px),#000 calc(100% - 4px))}
.ldg-track{background:rgba(var(--ar),.18)}
.ldg-ring{background:conic-gradient(from 0deg,transparent 0 28%,var(--acc) 82%,var(--hi));animation:ldgSpin 1.15s linear infinite}
.ldg-ref{position:relative;width:44px;height:44px;animation:ldgRef 1.9s cubic-bezier(.7,0,.25,1) infinite}
.ldg-line{font-family:'Inter',system-ui,sans-serif;font-weight:800;font-size:clamp(13px,4.2vw,20px);line-height:1.45;letter-spacing:.06em;text-wrap:balance;animation:ldgIn .6s .15s ease-out both}
.ldg-tx{color:var(--tx);background:linear-gradient(100deg,var(--mut) 35%,var(--acc2) 45%,var(--tx) 50%,var(--acc2) 55%,var(--mut) 65%) 100% 0/200% 100%;-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;animation:ldgShine 2.4s linear infinite}
.ldg-dots{color:var(--acc2)}
.ldg-dots span{animation:ldgDot 1.2s ease-in-out infinite both}
.ldg-dots span:nth-child(2){animation-delay:.2s}.ldg-dots span:nth-child(3){animation-delay:.4s}
@keyframes ldgSpin{to{transform:rotate(360deg)}}
@keyframes ldgRef{0%{transform:rotate(0)}65%,100%{transform:rotate(360deg)}}
@keyframes ldgPulse{50%{transform:scale(1.12);opacity:.6}}
@keyframes ldgShine{to{background-position:-100% 0}}
@keyframes ldgIn{from{opacity:0;transform:translateY(8px)}}
@keyframes ldgDot{0%,70%,100%{opacity:.25}35%{opacity:1}}
@media(prefers-reduced-motion:reduce){.ldg-glow,.ldg-ring,.ldg-ref,.ldg-tx,.ldg-dots span{animation:none}}

/* ===== modern layer ===== */
@supports(height:100dvh){body{min-height:100dvh}}
.side{height:calc(100vh - var(--hh) - 1px)}
@media(pointer:coarse),(hover:none){html{scrollbar-width:none}html::-webkit-scrollbar,body::-webkit-scrollbar{display:none;width:0;height:0}}
@media(pointer:coarse){html.ld{overflow:hidden}}
::selection{background:rgba(var(--ar),.4)}
a:focus-visible,.tc:focus-visible{outline:2px solid var(--acc2);outline-offset:2px}
summary:focus-visible{outline:2px solid var(--acc2);outline-offset:-3px}
.card{border-radius:22px;box-shadow:inset 0 1px 0 rgba(255,255,255,.07),0 26px 50px -34px rgba(var(--ar),.6);transition:border-color .3s,box-shadow .3s}
[data-theme=light] .card:not(.ok){box-shadow:inset 0 1px 0 #fff,0 26px 46px -32px rgba(var(--ar),.55)}
@media(max-width:640px){.card{border-radius:20px}}
.ico{border-radius:15px;background:linear-gradient(150deg,rgba(var(--ar),.32),rgba(var(--ar),.08));box-shadow:inset 0 0 0 1px rgba(var(--ar),.32)}
.ok .ico{background:linear-gradient(150deg,rgba(34,230,176,.32),rgba(34,230,176,.07));box-shadow:inset 0 0 0 1px rgba(34,230,176,.42)}
.bad .ico{background:linear-gradient(150deg,rgba(255,123,138,.32),rgba(255,123,138,.07));box-shadow:inset 0 0 0 1px rgba(255,123,138,.42)}
.top::after{content:"";position:absolute;inset:auto 0 -1px;height:1px;pointer-events:none;background:linear-gradient(90deg,transparent,rgba(var(--ar),.85),transparent)}
.bot{border-radius:22px 22px 0 0;box-shadow:0 -16px 34px -18px rgba(0,0,0,.55)}
.side a,.bot a{font-weight:600}
.side a.on{box-shadow:0 14px 26px -14px rgba(var(--ar),1)}
.ib,.lgb,.pills button,.side a,.bot a .material-icons{transition:border-color .25s,background .25s,color .25s,box-shadow .25s,transform .25s cubic-bezier(.3,1.5,.5,1),padding .35s cubic-bezier(.3,1.5,.5,1)}
.ib:active,.lgb:active,.pills button:active{transform:scale(.94)}
.pills .on{box-shadow:0 8px 18px -10px rgba(var(--ar),1)}
.lk{border-radius:16px;transition:border-color .25s,box-shadow .25s}
.lk:focus-within{border-color:rgba(var(--ar),.6);box-shadow:0 0 0 3px rgba(var(--ar),.15)}
.bars i{box-shadow:0 0 14px -4px rgba(var(--ar),.6);transform-origin:bottom;transition:filter .2s}
.bars i:hover{filter:brightness(1.3) saturate(1.15)}
/* OS picker */
details{border-radius:18px;margin-top:10px;overflow:hidden;background:linear-gradient(135deg,rgba(var(--ar),.1),rgba(var(--ar),.035));transition:border-color .3s,box-shadow .3s}
details[open]{border-color:rgba(var(--ar),.55);box-shadow:0 20px 38px -26px rgba(var(--ar),.85)}
summary{gap:13px;padding:11px 14px;font-weight:600;transition:background .2s}
summary::after{font-size:24px;transition:transform .35s cubic-bezier(.3,1.4,.5,1),color .2s}
details[open] summary::after{transform:rotate(180deg);color:var(--at)}
.osi{display:grid;place-items:center;flex:none;width:46px;height:46px;border-radius:15px;color:#fff;background:linear-gradient(145deg,var(--acc),var(--acc2));box-shadow:inset 0 1px 0 rgba(255,255,255,.3),0 10px 20px -10px rgba(0,0,0,.6);transition:transform .4s cubic-bezier(.3,1.6,.5,1)}
.osi svg{width:26px;height:26px;display:block}
details[open] .osi{transform:scale(1.07) rotate(-5deg)}
.osi[data-os=apple]{background:linear-gradient(145deg,#41414a,#0b0b0e)}
.osi[data-os=windows]{background:linear-gradient(145deg,#3fb8ff,#0a62e0)}
.osi[data-os=android]{background:linear-gradient(145deg,#6cf5ac,#10b562)}
.osi[data-os=linux]{background:linear-gradient(145deg,#fff,#e3e7f4);box-shadow:inset 0 0 0 1px rgba(20,25,60,.1),0 10px 20px -10px rgba(0,0,0,.6)}
.osi[data-os=device]{color:var(--on)}
.osi[data-os=linux] svg{width:33px;height:33px}
.a1:first-child{margin-top:0}
.in{padding:2px 12px 12px}
.a1{margin-top:8px;padding:12px 14px;border:1px solid var(--bd);border-radius:14px;background:rgba(var(--ar),.06)}
.a1>b{font-size:15px}.a1 p{margin-top:2px}
.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 16px;font-weight:600;box-shadow:inset 0 1px 0 rgba(255,255,255,.28),0 8px 16px -10px rgba(var(--ar),.95);transition:transform .25s cubic-bezier(.3,1.5,.5,1),box-shadow .25s}
.btn:active{transform:scale(.95)}
.btn.dl::before,.btn.cfg::before,.btn.tut::before{font-family:'Material Icons';font-size:17px;line-height:1;font-weight:400}
.btn.dl::before{content:'file_download'}.btn.cfg::before{content:'link'}.btn.tut::before{content:'play_circle_filled'}
html:not(.mi) .btn::before{display:none}
.qm{background:rgba(0,0,0,.72);backdrop-filter:blur(8px)}
#qrb{padding:15px;border-radius:20px;box-shadow:0 30px 80px -20px rgba(0,0,0,.7)}
#toast{font-weight:600;box-shadow:0 14px 30px -12px rgba(var(--ar),1);transform:translate(-50%,14px) scale(.95);transition:opacity .25s,transform .4s cubic-bezier(.3,1.5,.5,1)}
#toast.s{transform:translate(-50%,0)}
@media(hover:hover){
.ib:hover{border-color:var(--acc2);box-shadow:0 10px 20px -12px rgba(var(--ar),1);transform:translateY(-2px)}
.pills button:not(.on):hover{border-color:var(--acc2);color:var(--tx)}
.side a:not(.on):hover{background:rgba(var(--ar),.15);color:var(--tx)}
details:not([open]):hover{border-color:rgba(var(--ar),.45)}
summary:hover{background:rgba(var(--ar),.08)}
.btn:hover{transform:translateY(-2px);box-shadow:inset 0 1px 0 rgba(255,255,255,.28),0 14px 22px -10px rgba(var(--ar),1)}
}
/* one load sequence + motion that answers an action */
@media(prefers-reduced-motion:no-preference){
@keyframes rise{from{opacity:0;transform:translateY(14px)}}
@keyframes fade{from{opacity:0}}
@keyframes grow{from{transform:scaleY(0)}}
@keyframes dIn{from{opacity:0;transform:translateY(-6px)}}
@keyframes zin{from{opacity:0;transform:scale(.88)}}
html.ent #ui>*{animation:rise .5s cubic-bezier(.2,.8,.2,1) both}
html.ent #ui>:nth-child(n+2){animation-delay:.05s}html.ent #ui>:nth-child(n+3){animation-delay:.1s}html.ent #ui>:nth-child(n+4){animation-delay:.16s}html.ent #ui>:nth-child(n+5){animation-delay:.22s}html.ent #ui>:nth-child(n+6){animation-delay:.28s}html.ent #ui>:nth-child(n+7){animation-delay:.34s}
#ui,#sp,#st{animation:fade .2s both}
.bars.g i{animation:grow .6s cubic-bezier(.2,.8,.2,1) both;animation-delay:calc(min(var(--i,0),40)*10ms)}
details[open]>.in{animation:dIn .35s cubic-bezier(.2,.8,.2,1)}
.qm{animation:fade .2s}.qm #qrb{animation:zin .35s cubic-bezier(.3,1.5,.5,1)}
}
/* ===== bento grid (dashboard) ===== */
.bento{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:16px}
.bento>.c3{display:contents}
.bento .card{margin:0;min-width:0;border-radius:20px}
.bento #stats,.bento #os,.bento #sub,.bento #qrs{grid-column:1/-1}
.bento .qf{width:100%;max-width:236px;padding:16px;border-radius:26px}
.bento .qf #qp{padding:10px;border-radius:14px}
.bento .qf #qp img,.bento .qf #qp canvas{width:100%!important;height:auto!important}
#ol{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,250px),1fr));gap:10px;align-items:start}
#ol details{margin:0}
@media(max-width:640px){
.bento #qrs{display:grid;grid-template-columns:auto 1fr;grid-template-areas:"f h" "f s" "f b";align-items:center;justify-items:start;gap:4px 16px;text-align:start}
.bento #qrs .hd{grid-area:h;margin:0}
.bento #qrs .qf{grid-area:f;width:118px;padding:10px;border-radius:20px}
.bento #qrs .qf #qp{padding:6px;border-radius:8px}
.bento #qrs .qf i{display:none}
.bento #qrs>small{grid-area:s}
.bento #qrs>.btn{grid-area:b;margin-top:6px}
}
@media(max-width:359px){.bento{grid-template-columns:minmax(0,1fr)}}
@media(min-width:641px){
.bento{grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
.bento .card{border-radius:24px;padding:18px}
.bento .c3>.card{grid-row:1;flex-direction:column;align-items:flex-start;justify-content:space-between;gap:14px}
.bento .c3 b{font-size:26px}
.bento #stats{grid-column:1/3;grid-row:2}
.bento #sub{grid-column:1/3;grid-row:3}
.bento #qrs{grid-column:3;grid-row:2/4;justify-content:center}
.bento #os{grid-column:1/-1;grid-row:4}
}
/* ===== typography + badges ===== */
h1{font-size:clamp(21px,5.2vw,30px);font-weight:800;line-height:1.3;text-wrap:balance}
html[lang=en] h1{letter-spacing:-.02em}
h3,.hd h2{font-weight:700}
.c3 b,.sts b,#used{font-weight:800;font-variant-numeric:tabular-nums}
.c3 .card div>small:first-child{font-weight:600}
.yl,.xl{font-variant-numeric:tabular-nums}
.pills button{font-weight:600}
#s2,#s3,.c3 #v1{display:flex;align-items:center;gap:7px;width:fit-content;border:1px solid var(--bd);border-radius:999px;background:rgba(var(--ar),.1);line-height:1.7}
#s2,#s3{margin-top:5px;padding:1px 10px;font-size:12px;font-weight:700}
#s2:empty,#s3:empty{display:none}
.c3 #v1{margin:3px 0;padding:2px 13px 2px 11px;font-size:15px;font-weight:800}
.c3 #v1::before{content:"";width:8px;height:8px;flex:none;border-radius:50%;background:currentColor}
.ok #v1{background:rgba(34,230,176,.12);border-color:rgba(34,230,176,.4)}
.bad #v1{background:rgba(255,123,138,.12);border-color:rgba(255,123,138,.4)}
#c1[data-s=on_hold] #v1{color:#ffc857;background:rgba(255,200,87,.12);border-color:rgba(255,200,87,.4)}
:is(#s2,#s3)[data-tone=ok]{color:var(--grn);background:rgba(34,230,176,.12);border-color:rgba(34,230,176,.38)}
:is(#s2,#s3)[data-tone=warn]{color:#ffc857;background:rgba(255,200,87,.12);border-color:rgba(255,200,87,.4)}
:is(#s2,#s3)[data-tone=bad]{color:#ff7b8a;background:rgba(255,123,138,.12);border-color:rgba(255,123,138,.4)}
[data-theme=light] .ok #v1,[data-theme=light] :is(#s2,#s3)[data-tone=ok]{color:#067a5c}
[data-theme=light] .bad #v1,[data-theme=light] :is(#s2,#s3)[data-tone=bad]{color:#d3354d}
[data-theme=light] #c1[data-s=on_hold] #v1,[data-theme=light] :is(#s2,#s3)[data-tone=warn]{color:#a86a00}
.avb::after{content:"";position:absolute;inset:0;border-radius:50%;pointer-events:none}
.c3 #v1{position:relative}
.ok #v1::after{content:"";position:absolute;top:50%;width:8px;height:8px;margin-top:-4px;inset-inline-start:11px;border-radius:50%;background:currentColor;opacity:0;pointer-events:none}
html[dir=rtl] .ok #v1::after{inset-inline-start:13px}
.avb svg{position:relative;z-index:1}
@media(prefers-reduced-motion:no-preference){
.spb:not(.off) i::after,.ok #v1::after{animation:pl 1.8s ease-out infinite}
html[data-svc=ok] .avb::after{background:var(--grn);animation:pl 2.2s ease-out infinite}
}
@media(prefers-reduced-motion:reduce){.spb i{animation:none}}
/* ===== page + theme transitions (View Transitions API; older browsers keep the existing fades) ===== */
@media(prefers-reduced-motion:no-preference){
html.vt-theme::view-transition-old(root),html.vt-theme::view-transition-new(root){animation:none;mix-blend-mode:normal}
}
/* ===== glassmorphism layer: animated mesh + frosted surfaces ===== */
:root{--gl-f1:rgba(255,255,255,.14);--gl-f2:rgba(255,255,255,.035);--gl-hl:rgba(255,255,255,.36);--gl-bd:rgba(255,255,255,.13);--gl-b:12px;--gl-s:1.4;--gl-br:.95;--gl-sh:0 26px 50px -30px rgba(0,0,0,.7),0 2px 10px -4px rgba(0,0,0,.35);--gl-bo:1;--gl-n:.06}
[data-theme=light]{--gl-f1:rgba(255,255,255,.72);--gl-f2:rgba(255,255,255,.38);--gl-hl:#fff;--gl-bd:rgba(255,255,255,.8);--gl-s:1.6;--gl-br:1.03;--gl-sh:0 26px 46px -28px rgba(var(--ar),.55),0 2px 8px -4px rgba(var(--ar),.2);--gl-bo:.6;--gl-n:.04}
@media(max-width:640px){:root{--gl-b:10px}}
html.gl-lite{--gl-b:10px}
html::before,body::before,body::after{content:"";position:fixed;z-index:-1;pointer-events:none;border-radius:50%;opacity:var(--gl-bo)}
html::before{width:70vmax;height:70vmax;top:-26vmax;right:-20vmax;background:radial-gradient(closest-side,rgba(var(--ar),.5),transparent 72%)}
body::before{width:58vmax;height:58vmax;bottom:-24vmax;left:-18vmax;background:radial-gradient(closest-side,rgba(var(--ar),.34),transparent 72%)}
body::after{width:44vmax;height:44vmax;top:34%;left:38%;background:radial-gradient(closest-side,color-mix(in srgb,var(--hi) 30%,transparent),transparent 72%)}
html::after{content:"";position:fixed;inset:0;z-index:-1;pointer-events:none;opacity:var(--gl-n);background:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='2' stitchTiles='stitch'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E")}
html.gl-lite::after{display:none}
@media(pointer:coarse){html::after{display:none}}
@media(prefers-reduced-motion:no-preference) and (hover:hover) and (pointer:fine){
html:not(.gl-lite)::before,html:not(.gl-lite) body::before,html:not(.gl-lite) body::after{will-change:transform}
@keyframes glA{to{transform:translate3d(-16vmax,12vmax,0) scale(1.18)}}
@keyframes glB{to{transform:translate3d(18vmax,-10vmax,0) scale(.88)}}
@keyframes glC{to{transform:translate3d(-12vmax,-14vmax,0) scale(1.25)}}
html::before{animation:glA 44s ease-in-out infinite alternate}
body::before{animation:glB 56s ease-in-out infinite alternate}
body::after{animation:glC 38s ease-in-out infinite alternate}
html.gl-lite::before,html.gl-lite body::before,html.gl-lite body::after{animation:none}
}
.card,.top,.bot,.side{background:linear-gradient(145deg,var(--gl-f1),var(--gl-f2)),var(--card);background:linear-gradient(145deg,var(--gl-f1),var(--gl-f2)),color-mix(in srgb,var(--card) 56%,transparent);-webkit-backdrop-filter:none;backdrop-filter:none;border-color:var(--gl-bd)}
.top,.bot{-webkit-backdrop-filter:blur(var(--gl-b)) saturate(var(--gl-s));backdrop-filter:blur(var(--gl-b)) saturate(var(--gl-s))}
html.gl-lite .top,html.gl-lite .bot{-webkit-backdrop-filter:none;backdrop-filter:none;background:var(--card)}
.card{position:relative;box-shadow:inset 0 1px 0 var(--gl-hl),inset 0 -1px 0 rgba(0,0,0,.1),var(--gl-sh)}
[data-theme=light] .card:not(.ok){box-shadow:inset 0 1px 0 var(--gl-hl),var(--gl-sh)}
.card.qc{background:linear-gradient(160deg,rgba(var(--ar),.24),transparent 72%),linear-gradient(145deg,var(--gl-f1),var(--gl-f2)),var(--card);background:linear-gradient(160deg,rgba(var(--ar),.24),transparent 72%),linear-gradient(145deg,var(--gl-f1),var(--gl-f2)),color-mix(in srgb,var(--card) 56%,transparent)}
.card::before{content:"";position:absolute;inset:-1px;border-radius:inherit;padding:1px;pointer-events:none;background:linear-gradient(140deg,var(--gl-hl),transparent 38%,transparent 62%,var(--gl-bd));-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);mask-composite:exclude}
.card::after{content:"";position:absolute;inset:0;border-radius:inherit;pointer-events:none;opacity:0;transition:opacity .4s;background:radial-gradient(300px circle at var(--mx,50%) var(--my,0%),rgba(255,255,255,.14),transparent 62%)}
[data-theme=light] .card::after{background:radial-gradient(300px circle at var(--mx,50%) var(--my,0%),rgba(var(--ar),.1),transparent 62%)}
@media(hover:hover){.card:hover::after{opacity:1}}
.top{box-shadow:0 14px 32px -22px rgba(0,0,0,.6)}
[data-theme=light] .top{box-shadow:0 14px 32px -22px rgba(var(--ar),.5)}
.bot{--gl-b:14px;inset:auto max(12px,env(safe-area-inset-right)) calc(10px + env(safe-area-inset-bottom)) max(12px,env(safe-area-inset-left));padding:8px;border:1px solid var(--gl-bd);border-radius:26px;box-shadow:inset 0 1px 0 var(--gl-hl),var(--gl-sh)}
@media(max-width:899px){#toast{bottom:calc(90px + env(safe-area-inset-bottom))}}
.ib,.lgb{background:linear-gradient(145deg,var(--gl-f1),var(--gl-f2));border-color:var(--gl-bd);box-shadow:inset 0 1px 0 var(--gl-hl)}
.lgm{background:color-mix(in srgb,var(--pop) 94%,transparent);border-color:var(--gl-bd);box-shadow:inset 0 1px 0 var(--gl-hl),0 22px 48px -14px rgba(0,0,0,.5)}
@supports not ((backdrop-filter:blur(1px)) or (-webkit-backdrop-filter:blur(1px))){.card,.top,.bot,.side{background:var(--card)}.card.qc{background:linear-gradient(160deg,rgba(var(--ar),.22),var(--card) 60%)}}
@media(prefers-reduced-transparency:reduce){.card,.top,.bot,.side{background:var(--card);-webkit-backdrop-filter:none;backdrop-filter:none}.card.qc{background:linear-gradient(160deg,rgba(var(--ar),.22),var(--card) 60%)}html::after{display:none}}
@media(prefers-contrast:more){:root{--gl-bd:rgba(255,255,255,.4)}[data-theme=light]{--gl-bd:rgba(1,53,252,.5)}:root[data-accent=mono]{--gl-bd:rgba(255,255,255,.5)}:root[data-theme=light][data-accent=mono]{--gl-bd:rgba(0,0,0,.6)}}
/* ===== hero greeting card ===== */
@property --ha{syntax:"<angle>";inherits:false;initial-value:0deg}
@keyframes heroSpin{to{--ha:360deg}}
@keyframes heroPing{0%{box-shadow:0 0 0 0 rgba(34,230,176,.55)}70%,100%{box-shadow:0 0 0 10px rgba(34,230,176,0)}}
@keyframes heroWave{0%,60%,100%{transform:rotate(0)}10%,30%,50%{transform:rotate(16deg)}20%,40%{transform:rotate(-8deg)}}
.card.hero{--hw:.22;display:flex;align-items:center;gap:20px;padding:22px 26px;margin-top:0;border-radius:28px;isolation:isolate;background:radial-gradient(110% 160% at 100% 0%,rgba(var(--ar),var(--hw)),transparent 62%),linear-gradient(145deg,var(--gl-f1),var(--gl-f2)),color-mix(in srgb,var(--card) 56%,transparent)}
[data-theme=light] .card.hero{--hw:.09}
.hero-bg{position:absolute;inset:0;z-index:-1;border-radius:inherit;overflow:hidden;pointer-events:none}
.hero-bg i{position:absolute;inset-inline-end:-56px;top:-70px;width:220px;height:220px;border-radius:50%;background:conic-gradient(from 140deg,rgba(var(--ar),.55),transparent 55%,rgba(var(--ar),.3));filter:blur(26px);opacity:.4}
[data-theme=light] .hero-bg i{opacity:.14}
.hero-av{position:relative;flex:none;display:grid;place-items:center;width:64px;height:64px;border-radius:22px;font-size:28px;font-weight:800;line-height:1;color:var(--on);background:linear-gradient(135deg,var(--acc),var(--acc2));box-shadow:inset 0 1px 0 rgba(255,255,255,.4),0 14px 26px -12px rgba(var(--ar),.7)}
.hero-av::before{content:"";position:absolute;inset:-5px;padding:2px;border-radius:27px;pointer-events:none;background:conic-gradient(from var(--ha),var(--hi),transparent 35%,var(--acc2) 55%,transparent 85%,var(--hi));-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);mask-composite:exclude;animation:heroSpin 7s linear infinite}
.hero-av::after{content:"";position:absolute;inset-inline-end:-4px;bottom:-4px;width:17px;height:17px;border-radius:50%;background:var(--grn);border:3px solid var(--bg);animation:heroPing 2.4s ease-out infinite}
html[data-svc=bad] .hero-av::after{background:#ff5d73;animation:none}
.hero-tx{min-width:0;display:grid;gap:7px;justify-items:start}
.hero-chip{display:inline-flex;align-items:center;gap:7px;padding:4px 13px 4px 11px;border-radius:999px;font-size:12px;font-weight:700;color:var(--at);background:rgba(var(--ar),.12);border:1px solid rgba(var(--ar),.24)}
.hero-wave{display:inline-block;font-style:normal;line-height:1;transform-origin:70% 75%;animation:heroWave 2.4s ease-in-out .7s 2}
.hero h1{font-size:clamp(22px,5.6vw,32px);line-height:1.3;font-weight:800;letter-spacing:-.01em;overflow-wrap:anywhere}
.hero-nm{background:linear-gradient(100deg,var(--tx),var(--acc2) 75%);-webkit-background-clip:text;background-clip:text;color:transparent}
.hero p{font-size:14px;line-height:1.8}
@media(max-width:520px){.card.hero{gap:15px;padding:18px;border-radius:24px}.hero-av{width:52px;height:52px;border-radius:18px;font-size:23px}.hero-av::before{border-radius:23px}.hero p{font-size:13px}}
@media(prefers-reduced-motion:reduce){.hero-av::before,.hero-av::after,.hero-wave{animation:none}}
/* ===== expiry / data-limit notice (modal): colors come from the theme variables, text from T[L] ===== */
.nx{--nt:#ff7b8a;--nr:255,123,138;position:fixed;inset:var(--hh) 0 0;z-index:25;display:flex;padding:16px max(16px,env(safe-area-inset-right)) max(16px,env(safe-area-inset-bottom)) max(16px,env(safe-area-inset-left));overflow-y:auto;overscroll-behavior:contain;background:rgba(0,0,0,.68);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px)}
.nx[hidden]{display:none}
.nx[data-k=vol]{--nt:#ffc857;--nr:255,200,87}
[data-theme=light] .nx{--nt:#d3354d;--nr:211,53,77;background:rgba(14,20,60,.4)}
[data-theme=light] .nx[data-k=vol]{--nt:#a86a00;--nr:240,150,10}
.nx .nxc{width:min(100%,400px);margin:auto;padding:30px 24px 18px;display:grid;justify-items:center;gap:10px;text-align:center;border-radius:28px;border-color:rgba(var(--nr),.4);background:radial-gradient(120% 80% at 50% 0%,rgba(var(--nr),.2),transparent 65%),linear-gradient(145deg,var(--gl-f1),var(--gl-f2)),var(--pop)}
.nxi{position:relative;display:grid;place-items:center;width:96px;height:96px;margin-bottom:6px;border-radius:32px;color:var(--nt);background:linear-gradient(150deg,rgba(var(--nr),.36),rgba(var(--nr),.08));box-shadow:inset 0 0 0 1px rgba(var(--nr),.45),0 18px 34px -14px rgba(var(--nr),.75)}
.nxi svg{position:relative;z-index:1;width:52px;height:52px}
.nxi::before,.nxi::after{content:"";position:absolute;inset:-6px;border-radius:38px;border:2px solid rgba(var(--nr),.55);opacity:0;pointer-events:none}
.nx[data-k=exp] .i-v,.nx[data-k=vol] .i-e{display:none}
.nxc h2{margin:0;font-size:clamp(20px,5.4vw,24px);line-height:1.45;font-weight:800;color:var(--tx);overflow-wrap:anywhere}
.nxd{margin:0;font-size:14px;line-height:1.9}
.nxm{margin:0;display:inline-flex;align-items:center;padding:5px 14px;border-radius:999px;font-size:12.5px;font-weight:700;color:var(--nt);background:rgba(var(--nr),.12);border:1px solid rgba(var(--nr),.4)}
.nxm:empty{display:none}
.nxb{display:flex;align-items:center;justify-content:center;gap:12px;width:100%;margin-top:8px;padding:12px 22px;border-radius:999px;color:var(--on);text-decoration:none;font-weight:700;font-size:15px;background:linear-gradient(135deg,var(--acc),var(--acc2));box-shadow:inset 0 1px 0 rgba(255,255,255,.28),0 14px 26px -12px rgba(var(--ar),.95);transition:transform .25s cubic-bezier(.3,1.5,.5,1),box-shadow .25s}
.nxb svg{flex:none;width:22px;height:22px}
.nxl{display:grid;gap:1px;text-align:center;line-height:1.4}
.nxbi{font-size:12px;font-weight:600;opacity:.8;letter-spacing:.2px;unicode-bidi:isolate}
.nxb:hover{transform:translateY(-2px)}.nxb:active{transform:scale(.97)}
.nxk{margin-top:2px;padding:8px 18px;border:0;border-radius:999px;background:none;color:var(--mut);font:inherit;font-size:13px;cursor:pointer;transition:color .2s,background .2s}
.nxk:hover{color:var(--tx);background:rgba(var(--ar),.1)}
.nxb:focus-visible,.nxk:focus-visible{outline:2px solid var(--acc2);outline-offset:3px}
@media(max-width:520px){.nx .nxc{padding:26px 18px 16px;border-radius:24px}.nxi{width:84px;height:84px;border-radius:28px}.nxi svg{width:46px;height:46px}}
@media(prefers-reduced-motion:no-preference){
@keyframes nxFade{from{opacity:0}}
@keyframes nxIn{from{opacity:0;transform:translateY(18px) scale(.94)}}
@keyframes nxPop{from{opacity:0;transform:scale(.5) rotate(-12deg)}}
@keyframes nxRing{0%{transform:scale(.9);opacity:.8}100%{transform:scale(1.42);opacity:0}}
.nx:not([hidden]){animation:nxFade .25s both}
.nx:not([hidden]) .nxc{animation:nxIn .45s cubic-bezier(.3,1.4,.5,1) both}
.nxi svg{animation:nxPop .6s .15s cubic-bezier(.3,1.5,.5,1) both}
.nxi::before{animation:nxRing 2.4s ease-out .5s infinite}.nxi::after{animation:nxRing 2.4s ease-out 1.7s infinite}
}
@media(prefers-reduced-motion:reduce){.nxi::before{opacity:.45}}
/* ===== Christmas theme: festive mesh, ribbon line, string lights, snowfall, Santa hat, theme-card preview ===== */
:root[data-accent=xmas] body::before{background:radial-gradient(closest-side,rgba(22,170,96,.42),transparent 72%)}
:root[data-accent=xmas] body::after{background:radial-gradient(closest-side,rgba(255,196,87,.24),transparent 72%)}
:root[data-accent=xmas] .top::after{height:2px;background:linear-gradient(90deg,transparent,#d62b45 18%,#ffc857 50%,#22a45f 82%,transparent)}
:root[data-theme=light][data-accent=xmas] .top{box-shadow:0 14px 32px -22px rgba(12,84,54,.4)}
:root[data-theme=light][data-accent=xmas]::before{background:radial-gradient(closest-side,rgba(var(--ar),.34),transparent 72%)}
:root[data-accent=xmas] .ico{background:linear-gradient(150deg,rgba(226,52,78,.6),rgba(226,52,78,.2));box-shadow:inset 0 0 0 1px rgba(255,208,120,.4)}
:root[data-theme=light][data-accent=xmas] .ico{background:linear-gradient(150deg,rgba(var(--ar),.2),rgba(var(--ar),.06));box-shadow:inset 0 0 0 1px rgba(var(--ar),.3)}
:root[data-accent=xmas] .ok .ico{background:linear-gradient(150deg,rgba(34,230,176,.32),rgba(34,230,176,.07));box-shadow:inset 0 0 0 1px rgba(34,230,176,.42)}
:root[data-accent=xmas] .bad .ico{background:linear-gradient(150deg,rgba(255,123,138,.32),rgba(255,123,138,.07));box-shadow:inset 0 0 0 1px rgba(255,123,138,.42)}
:root[data-accent=xmas] .card.qc{background:linear-gradient(160deg,rgba(226,52,78,.15),transparent 70%),linear-gradient(145deg,var(--gl-f1),var(--gl-f2)),color-mix(in srgb,var(--card) 56%,transparent)}
:root[data-theme=light][data-accent=xmas] .card.qc{background:linear-gradient(160deg,rgba(var(--ar),.09),transparent 70%),linear-gradient(145deg,var(--gl-f1),var(--gl-f2)),color-mix(in srgb,var(--card) 56%,transparent)}
:root[data-accent=xmas] .logo{position:relative;background:linear-gradient(135deg,#22b56c,#0d6a43);box-shadow:inset 0 1px 0 rgba(255,255,255,.35),0 8px 18px -8px rgba(34,181,108,.65)}
:root[data-accent=xmas] .logo::after{content:"";position:absolute;top:-8px;left:-4px;width:29px;height:23px;transform:rotate(12deg);pointer-events:none;filter:drop-shadow(0 2px 2px rgba(0,0,0,.3));background:url("data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2740%27 height=%2732%27 viewBox=%270 0 40 32%27%3E%3Cdefs%3E%3ClinearGradient id=%27h%27 x1=%270%27 y1=%270%27 x2=%271%27 y2=%271%27%3E%3Cstop offset=%270%27 stop-color=%27%23ff5468%27/%3E%3Cstop offset=%271%27 stop-color=%27%23bf0f2c%27/%3E%3C/linearGradient%3E%3ClinearGradient id=%27f%27 x1=%270%27 y1=%270%27 x2=%270%27 y2=%271%27%3E%3Cstop offset=%270%27 stop-color=%27%23fff%27/%3E%3Cstop offset=%271%27 stop-color=%27%23dce8e2%27/%3E%3C/linearGradient%3E%3C/defs%3E%3Cpath d=%27M5.5 24C6 12 14 4.5 24 4.5c6 0 10.5 3.2 11.8 8.6.9 3.7 1.2 7.4.7 11.4z%27 fill=%27url(%23h)%27/%3E%3Cpath d=%27M10.2 19.5C11.2 13 16 8.4 22.6 7.9%27 fill=%27none%27 stroke=%27%23ff9aa7%27 stroke-width=%271.5%27 stroke-linecap=%27round%27 opacity=%27.55%27/%3E%3Crect x=%272.5%27 y=%2721%27 width=%2735%27 height=%279.5%27 rx=%274.75%27 fill=%27url(%23f)%27/%3E%3Ccircle cx=%2734.5%27 cy=%276.5%27 r=%274.6%27 fill=%27url(%23f)%27/%3E%3C/svg%3E") center/contain no-repeat}
/* string lights hanging under the header */
.gar{display:none}
:root[data-accent=xmas] .gar{display:block;position:absolute;inset:100% 0 auto;height:24px;pointer-events:none;z-index:2;background:url("data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2796%27 height=%2724%27 viewBox=%270 0 96 24%27%3E%3Cpath d=%27M0 1Q48 16 96 1%27 fill=%27none%27 stroke=%27%232c7a58%27 stroke-width=%271.3%27 opacity=%27.9%27/%3E%3C/svg%3E") repeat-x 0 0/96px 24px}
.gar i{position:absolute;inset:0;background-repeat:repeat-x;background-size:96px 24px}
.gar i:nth-child(1){background-image:url("data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2796%27 height=%2724%27 viewBox=%270 0 96 24%27%3E%3Cdefs%3E%3CradialGradient id=%27g1%27%3E%3Cstop offset=%270%27 stop-color=%27%23ff3b55%27 stop-opacity=%27.7%27/%3E%3Cstop offset=%271%27 stop-color=%27%23ff3b55%27 stop-opacity=%270%27/%3E%3C/radialGradient%3E%3CradialGradient id=%27b1%27 cx=%27.38%27 cy=%27.3%27 r=%27.8%27%3E%3Cstop offset=%270%27 stop-color=%27%23fff%27 stop-opacity=%27.95%27/%3E%3Cstop offset=%27.4%27 stop-color=%27%23ff3b55%27/%3E%3Cstop offset=%271%27 stop-color=%27%23a80f28%27/%3E%3C/radialGradient%3E%3C/defs%3E%3Ccircle cx=%2712%27 cy=%2710.18%27 r=%279%27 fill=%27url(%23g1)%27/%3E%3Crect x=%2710.5%27 y=%273.68%27 width=%273%27 height=%273.2%27 rx=%27.9%27 fill=%27%231c3a2d%27/%3E%3Cellipse cx=%2712%27 cy=%2710.18%27 rx=%272.9%27 ry=%274.1%27 fill=%27url(%23b1)%27/%3E%3Cdefs%3E%3CradialGradient id=%27g2%27%3E%3Cstop offset=%270%27 stop-color=%27%2333e68c%27 stop-opacity=%27.7%27/%3E%3Cstop offset=%271%27 stop-color=%27%2333e68c%27 stop-opacity=%270%27/%3E%3C/radialGradient%3E%3CradialGradient id=%27b2%27 cx=%27.38%27 cy=%27.3%27 r=%27.8%27%3E%3Cstop offset=%270%27 stop-color=%27%23fff%27 stop-opacity=%27.95%27/%3E%3Cstop offset=%27.4%27 stop-color=%27%2333e68c%27/%3E%3Cstop offset=%271%27 stop-color=%27%230b7f45%27/%3E%3C/radialGradient%3E%3C/defs%3E%3Ccircle cx=%2760%27 cy=%2713.93%27 r=%279%27 fill=%27url(%23g2)%27/%3E%3Crect x=%2758.5%27 y=%277.43%27 width=%273%27 height=%273.2%27 rx=%27.9%27 fill=%27%231c3a2d%27/%3E%3Cellipse cx=%2760%27 cy=%2713.93%27 rx=%272.9%27 ry=%274.1%27 fill=%27url(%23b2)%27/%3E%3C/svg%3E")}
.gar i:nth-child(2){background-image:url("data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2796%27 height=%2724%27 viewBox=%270 0 96 24%27%3E%3Cdefs%3E%3CradialGradient id=%27g3%27%3E%3Cstop offset=%270%27 stop-color=%27%23ffd23f%27 stop-opacity=%27.7%27/%3E%3Cstop offset=%271%27 stop-color=%27%23ffd23f%27 stop-opacity=%270%27/%3E%3C/radialGradient%3E%3CradialGradient id=%27b3%27 cx=%27.38%27 cy=%27.3%27 r=%27.8%27%3E%3Cstop offset=%270%27 stop-color=%27%23fff%27 stop-opacity=%27.95%27/%3E%3Cstop offset=%27.4%27 stop-color=%27%23ffd23f%27/%3E%3Cstop offset=%271%27 stop-color=%27%23b87a00%27/%3E%3C/radialGradient%3E%3C/defs%3E%3Ccircle cx=%2736%27 cy=%2713.93%27 r=%279%27 fill=%27url(%23g3)%27/%3E%3Crect x=%2734.5%27 y=%277.43%27 width=%273%27 height=%273.2%27 rx=%27.9%27 fill=%27%231c3a2d%27/%3E%3Cellipse cx=%2736%27 cy=%2713.93%27 rx=%272.9%27 ry=%274.1%27 fill=%27url(%23b3)%27/%3E%3Cdefs%3E%3CradialGradient id=%27g4%27%3E%3Cstop offset=%270%27 stop-color=%27%2354b6ff%27 stop-opacity=%27.7%27/%3E%3Cstop offset=%271%27 stop-color=%27%2354b6ff%27 stop-opacity=%270%27/%3E%3C/radialGradient%3E%3CradialGradient id=%27b4%27 cx=%27.38%27 cy=%27.3%27 r=%27.8%27%3E%3Cstop offset=%270%27 stop-color=%27%23fff%27 stop-opacity=%27.95%27/%3E%3Cstop offset=%27.4%27 stop-color=%27%2354b6ff%27/%3E%3Cstop offset=%271%27 stop-color=%27%231360b8%27/%3E%3C/radialGradient%3E%3C/defs%3E%3Ccircle cx=%2784%27 cy=%2710.18%27 r=%279%27 fill=%27url(%23g4)%27/%3E%3Crect x=%2782.5%27 y=%273.68%27 width=%273%27 height=%273.2%27 rx=%27.9%27 fill=%27%231c3a2d%27/%3E%3Cellipse cx=%2784%27 cy=%2710.18%27 rx=%272.9%27 ry=%274.1%27 fill=%27url(%23b4)%27/%3E%3C/svg%3E")}
/* snowfall: two tiled layers (far = small + slow, near = big + faster), pure CSS; low-power devices get one light layer, reduced-motion gets none */
#snow{display:none}
@media(prefers-reduced-motion:no-preference){
@keyframes xmA{0%,42%{opacity:1}58%,100%{opacity:.32}}
@keyframes xmB{0%,42%{opacity:.32}58%,100%{opacity:1}}
@keyframes snA{to{transform:translate3d(0,300px,0)}}
@keyframes snB{to{transform:translate3d(0,440px,0)}}
@keyframes snS{from{translate:-16px 0}to{translate:16px 0}}
:root[data-accent=xmas]:not(.gl-lite) .gar i:nth-child(1){animation:xmA 3.6s ease-in-out infinite}
:root[data-accent=xmas]:not(.gl-lite) .gar i:nth-child(2){animation:xmB 3.6s ease-in-out infinite}
:root[data-accent=xmas] #snow{display:block;position:fixed;inset:0;z-index:15;pointer-events:none;overflow:hidden}
#snow::before,#snow::after{content:"";position:absolute;left:-20px;right:-20px;bottom:0;will-change:transform}
#snow::before{top:-300px;opacity:.6;background:radial-gradient(circle at 22px 38px,var(--sn) 0,var(--sn) 0.7px,transparent 2.1px),radial-gradient(circle at 96px 14px,var(--sn) 0,var(--sn) 0.6px,transparent 1.9px),radial-gradient(circle at 160px 80px,var(--sn) 0,var(--sn) 0.8px,transparent 2.4px),radial-gradient(circle at 236px 30px,var(--sn) 0,var(--sn) 0.6px,transparent 2.0px),radial-gradient(circle at 60px 140px,var(--sn) 0,var(--sn) 0.6px,transparent 1.9px),radial-gradient(circle at 130px 176px,var(--sn) 0,var(--sn) 0.8px,transparent 2.3px),radial-gradient(circle at 250px 150px,var(--sn) 0,var(--sn) 0.7px,transparent 2.1px),radial-gradient(circle at 24px 238px,var(--sn) 0,var(--sn) 0.8px,transparent 2.4px),radial-gradient(circle at 100px 270px,var(--sn) 0,var(--sn) 0.6px,transparent 1.9px),radial-gradient(circle at 196px 248px,var(--sn) 0,var(--sn) 0.7px,transparent 2.2px);background-size:300px 300px;animation:snA 17s linear infinite,snS 7s ease-in-out infinite alternate}
#snow::after{top:-440px;opacity:.9;background:radial-gradient(circle at 60px 70px,var(--sn) 0,var(--sn) 1.3px,transparent 3.9px),radial-gradient(circle at 190px 22px,var(--sn) 0,var(--sn) 1.0px,transparent 3.4px),radial-gradient(circle at 310px 120px,var(--sn) 0,var(--sn) 1.4px,transparent 4.1px),radial-gradient(circle at 120px 214px,var(--sn) 0,var(--sn) 1.2px,transparent 3.7px),radial-gradient(circle at 262px 266px,var(--sn) 0,var(--sn) 1.5px,transparent 4.3px),radial-gradient(circle at 380px 206px,var(--sn) 0,var(--sn) 1.2px,transparent 3.6px),radial-gradient(circle at 34px 350px,var(--sn) 0,var(--sn) 1.4px,transparent 4.0px),radial-gradient(circle at 196px 388px,var(--sn) 0,var(--sn) 1.1px,transparent 3.5px),radial-gradient(circle at 340px 372px,var(--sn) 0,var(--sn) 1.3px,transparent 3.9px);background-size:440px 440px;animation:snB 10s linear infinite,snS 9s ease-in-out -3s infinite alternate}
html.gl-lite #snow::after{display:none}
html.gl-lite #snow::before{animation:snA 17s linear infinite}
}
/* theme-card thumbnail in Settings */
.tc[data-a=xmas] i{background:radial-gradient(circle at 15% 24%,#fff 0 1.1px,transparent 1.8px),radial-gradient(circle at 34% 12%,#fff 0 1px,transparent 1.6px),radial-gradient(circle at 56% 30%,#fff 0 1.3px,transparent 2px),radial-gradient(circle at 78% 15%,#fff 0 1px,transparent 1.6px),radial-gradient(circle at 93% 36%,#fff 0 1.2px,transparent 1.9px),radial-gradient(circle at 26% 48%,#fff 0 1px,transparent 1.6px),radial-gradient(110% 90% at 100% 0%,rgba(222,45,72,.8),transparent 62%),radial-gradient(80% 70% at 50% 125%,rgba(255,200,87,.55),transparent 65%),linear-gradient(160deg,#157550,#06281b)}
.tc[data-a=xmas] i::after{width:34px;height:48px;bottom:7px;inset-inline-end:12px;opacity:1;filter:drop-shadow(0 3px 5px rgba(0,0,0,.35));background:url("data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2730%27 height=%2742%27 viewBox=%270 0 30 42%27%3E%3Cdefs%3E%3ClinearGradient id=%27t%27 x1=%270%27 y1=%270%27 x2=%270%27 y2=%271%27%3E%3Cstop offset=%270%27 stop-color=%27%235af0a0%27/%3E%3Cstop offset=%271%27 stop-color=%27%2314a058%27/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect x=%2712.2%27 y=%2736%27 width=%275.6%27 height=%275.2%27 rx=%271.2%27 fill=%27%239a6430%27/%3E%3Cpolygon points=%2715,6.5 22,17.5 8,17.5%27 fill=%27url(%23t)%27/%3E%3Cpolygon points=%2715,12.5 24.2,26.5 5.8,26.5%27 fill=%27url(%23t)%27/%3E%3Cpolygon points=%2715,20.5 27.5,37.5 2.5,37.5%27 fill=%27url(%23t)%27/%3E%3Cpath d=%27M15 .6l1.7 3.4 3.7.5-2.7 2.6.7 3.7L15 9l-3.4 1.8.7-3.7L9.6 4.5l3.7-.5z%27 fill=%27%23ffd24a%27/%3E%3Ccircle cx=%2713%27 cy=%2723.5%27 r=%271.6%27 fill=%27%23ff4d5e%27/%3E%3Ccircle cx=%2718.3%27 cy=%2731%27 r=%271.7%27 fill=%27%23ffd24a%27/%3E%3Ccircle cx=%279.5%27 cy=%2733%27 r=%271.5%27 fill=%27%23fff%27/%3E%3Ccircle cx=%2716.5%27 cy=%2715.5%27 r=%271.3%27 fill=%27%23ff4d5e%27/%3E%3Ccircle cx=%2721%27 cy=%2725%27 r=%271.2%27 fill=%27%23fff%27/%3E%3C/svg%3E") center/contain no-repeat}
@media(prefers-contrast:more){:root[data-accent=xmas]{--gl-bd:rgba(255,228,170,.55)}:root[data-theme=light][data-accent=xmas]{--gl-bd:rgba(12,84,54,.55)}}
</style>
<noscript><style>#ldg{display:none}</style></noscript>
</head>
<body>
<div id="ldg" role="status" aria-live="polite" aria-label="Load XpnTeam information"><div class="ldg-box"><div class="ldg-ico"><span class="ldg-glow"></span><span class="ldg-track"></span><span class="ldg-ring"></span><svg class="ldg-ref" viewBox="0 0 24 24" fill="none" stroke="url(#ldgG)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><defs><linearGradient id="ldgG" gradientUnits="userSpaceOnUse" x1="2" y1="3" x2="22" y2="21"><stop offset="0" style="stop-color:var(--acc2)"/><stop offset="1" style="stop-color:var(--hi)"/></linearGradient></defs><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg></div><p class="ldg-line"><span class="ldg-tx">Load XpnTeam information</span><span class="ldg-dots" aria-hidden="true"><span>.</span><span>.</span><span>.</span></span></p></div></div>
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs><symbol id="f-fa" viewBox="0 0 24 24"><clipPath id="fc-fa"><circle cx="12" cy="12" r="12"/></clipPath><g clip-path="url(#fc-fa)"><rect width="24" height="8" fill="#239f40"/><rect y="8" width="24" height="8" fill="#fff"/><rect y="16" width="24" height="8" fill="#da0000"/><g transform="translate(3.15 4.1) scale(0.16)"><path d="M74.95,33.67L91,35L74.95,36.33ZM74.72,38.1L84.08,41.99L74.02,40.67ZM73.34,42.32L86.58,51.5L72.01,44.63ZM70.93,46.04L77.09,54.09L69.04,47.93ZM67.63,49.01L74.5,63.58L65.32,50.34ZM63.67,51.02L64.99,61.08L61.1,51.72ZM59.33,51.95L58,68L56.67,51.95ZM54.9,51.72L51.01,61.08L52.33,51.02ZM50.68,50.34L41.5,63.58L48.37,49.01ZM46.96,47.93L38.91,54.09L45.07,46.04ZM43.99,44.63L29.42,51.5L42.66,42.32ZM41.98,40.67L31.92,41.99L41.28,38.1ZM41.05,36.33L25,35L41.05,33.67ZM41.28,31.9L31.92,28.01L41.98,29.33ZM42.66,27.68L29.42,18.5L43.99,25.37ZM45.07,23.96L38.91,15.91L46.96,22.07ZM48.37,20.99L41.5,6.42L50.68,19.66ZM52.33,18.98L51.01,8.92L54.9,18.28ZM56.67,18.05L58,2L59.33,18.05ZM61.1,18.28L64.99,8.92L63.67,18.98ZM65.32,19.66L74.5,6.42L67.63,20.99ZM69.04,22.07L77.09,15.91L70.93,23.96ZM72.01,25.37L86.58,18.5L73.34,27.68ZM74.02,29.33L84.08,28.01L74.72,31.9Z" fill="#ffd84a" stroke="#b87a00" stroke-width=".9" stroke-linejoin="round"/><circle cx="58" cy="35" r="16.8" fill="#ffd84a" stroke="#b87a00" stroke-width=".9"/><circle cx="58" cy="35" r="12.4" fill="none" stroke="#b87a00" stroke-width=".7" opacity=".7"/><g fill="#4d2b00" stroke="#4d2b00" stroke-width="1.8" stroke-linejoin="round"><ellipse cx="42" cy="56" rx="14" ry="14.5"/><ellipse cx="65" cy="54" rx="21" ry="10.5"/><ellipse cx="78" cy="57" rx="10.5" ry="13"/><path d="M74.5,64L85.5,64L85.25,87L78.75,87ZM85.25,83Q85.75,89.6 83.25,89.6L75.55,89.6Q74.95,86.2 79.25,83Z"/><path d="M61.5,64L70.5,64L69,87L63,87ZM69,83Q69.5,89.6 67,89.6L59.8,89.6Q59.2,86.2 63.5,83Z"/><path d="M39,62L49,62L46.4,87L39.6,87ZM46.4,83Q46.9,89.6 44.4,89.6L36.4,89.6Q35.8,86.2 40.1,83Z"/></g><path d="M84,55C100,57 100,38 90,32" fill="none" stroke="#4d2b00" stroke-width="6.2" stroke-linecap="round"/><path d="M39,60C32,60 25,58 18,54" fill="none" stroke="#4d2b00" stroke-width="9" stroke-linecap="round"/><circle cx="17.5" cy="54" r="5" fill="#4d2b00" stroke="#4d2b00" stroke-width="1.8"/><ellipse cx="89" cy="30" rx="4.2" ry="6.4" transform="rotate(-28 89 30)" fill="#4d2b00" stroke="#4d2b00" stroke-width="1.8"/><g fill="#f3a915"><ellipse cx="42" cy="56" rx="14" ry="14.5"/><ellipse cx="65" cy="54" rx="21" ry="10.5"/><ellipse cx="78" cy="57" rx="10.5" ry="13"/><path d="M74.5,64L85.5,64L85.25,87L78.75,87ZM85.25,83Q85.75,89.6 83.25,89.6L75.55,89.6Q74.95,86.2 79.25,83Z"/><path d="M61.5,64L70.5,64L69,87L63,87ZM69,83Q69.5,89.6 67,89.6L59.8,89.6Q59.2,86.2 63.5,83Z"/><path d="M39,62L49,62L46.4,87L39.6,87ZM46.4,83Q46.9,89.6 44.4,89.6L36.4,89.6Q35.8,86.2 40.1,83Z"/></g><path d="M84,55C100,57 100,38 90,32" fill="none" stroke="#f3a915" stroke-width="4.4" stroke-linecap="round"/><path d="M39,60C32,60 25,58 18,54" fill="none" stroke="#f3a915" stroke-width="7.2" stroke-linecap="round"/><circle cx="17.5" cy="54" r="5" fill="#f3a915"/><ellipse cx="89" cy="30" rx="4.2" ry="6.4" transform="rotate(-28 89 30)" fill="#c47608"/><path d="M51.5,50L53.2,51.26L53.27,52.54L51.57,53.49L49.47,54.15L48.62,54.96L49.43,56.39L50.74,58.26L50.97,59.8L49.52,60.37L47.25,60.16L45.61,60.18L45.32,61.32L45.81,63.47L45.83,65.42L44.69,66L42.75,65.16L40.97,64.13L39.95,64.38L39.49,66.18L38.91,68.34L37.82,69.18L36.39,68.15L35.07,66.3L34,65.4L32.93,66.3L31.61,68.15L30.18,69.18L29.09,68.34L28.51,66.18L28.05,64.38L27.03,64.13L25.25,65.16L23.31,66L22.17,65.42L22.19,63.47L22.68,61.32L22.39,60.18L20.75,60.16L18.48,60.37L17.03,59.8L17.26,58.26L18.57,56.39L19.38,54.96L18.53,54.15L16.43,53.49L14.73,52.54L14.8,51.26L16.5,50L18.28,48.97L18.57,47.97L17.24,46.67L15.66,45.09L15.48,43.71L17.09,43L19.35,42.78L20.66,42.3L20.42,40.93L19.48,38.86L19.29,37.1L20.58,36.58L22.73,37.15L24.53,37.66L25.25,36.9L25.25,34.84L25.49,32.74L26.56,32.04L28.24,33.04L29.85,34.53L30.99,34.86L31.82,33.45L32.78,31.37L34,30.4L35.22,31.37L36.18,33.45L37.01,34.86L38.15,34.53L39.76,33.04L41.44,32.04L42.51,32.74L42.75,34.84L42.75,36.9L43.47,37.66L45.27,37.15L47.42,36.58L48.71,37.1L48.52,38.86L47.58,40.93L47.34,42.3L48.65,42.78L50.91,43L52.52,43.71L52.34,45.09L50.76,46.67L49.43,47.97L49.72,48.97Z" fill="#c47608" stroke="#4d2b00" stroke-width="1.8" stroke-linejoin="round"/><circle cx="29" cy="47" r="9.4" fill="#f3a915" stroke="#4d2b00" stroke-width="1"/><rect x="13.5" y="45.2" width="12" height="9.4" rx="3.8" fill="#f3a915" stroke="#4d2b00" stroke-width="1"/><circle cx="32.5" cy="38.6" r="2.9" fill="#c47608" stroke="#4d2b00" stroke-width=".9"/><path d="M22.2,41.2L28.4,42.6" stroke="#4d2b00" stroke-width="1.3" stroke-linecap="round"/><circle cx="26.6" cy="44.6" r="1.35" fill="#4d2b00"/><ellipse cx="14.6" cy="47.2" rx="1.7" ry="1.4" fill="#4d2b00"/><path d="M15.5,53.2Q20,55.2 25,52.6" fill="none" stroke="#4d2b00" stroke-width=".9" stroke-linecap="round"/><path d="M17.5,52C9,37 13,19 33,8" fill="none" stroke="#4d2b00" stroke-width="4.4" stroke-linecap="round"/><path d="M17.5,52C9,37 13,19 33,8" fill="none" stroke="#fff2bd" stroke-width="2.4" stroke-linecap="round"/><path d="M11.6,50.2L23.6,52.8" stroke="#4d2b00" stroke-width="3.4" stroke-linecap="round"/><path d="M11.6,50.2L23.6,52.8" stroke="#f3a915" stroke-width="1.6" stroke-linecap="round"/></g></g><circle cx="12" cy="12" r="11.8" fill="none" stroke="currentColor" stroke-opacity=".22" stroke-width=".4"/></symbol><symbol id="f-en" viewBox="0 0 24 24"><clipPath id="fc-en"><circle cx="12" cy="12" r="12"/></clipPath><clipPath id="fk-en"><path d="M12,12h12v12zv12h-12zh-12v-12zv-12h12z"/></clipPath><g clip-path="url(#fc-en)"><rect width="24" height="24" fill="#012169"/><path d="M0,0L24,24M24,0L0,24" stroke="#fff" stroke-width="4.8"/><path d="M0,0L24,24M24,0L0,24" clip-path="url(#fk-en)" stroke="#c8102e" stroke-width="3.2"/><path d="M12,0V24M0,12H24" stroke="#fff" stroke-width="8"/><path d="M12,0V24M0,12H24" stroke="#c8102e" stroke-width="4.8"/></g><circle cx="12" cy="12" r="11.8" fill="none" stroke="currentColor" stroke-opacity=".22" stroke-width=".4"/></symbol></defs></svg>
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs>
<symbol id="os-apple" viewBox="0 0 24 24"><path fill="currentColor" d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701z"/></symbol>
<symbol id="os-windows" viewBox="0 0 24 24"><path fill="currentColor" d="M3 3h8.4v8.4H3zM12.6 3H21v8.4h-8.4zM3 12.6h8.4V21H3zM12.6 12.6H21V21h-8.4z"/></symbol>
<symbol id="os-android" viewBox="0 0 24 24"><g fill="currentColor"><path fill-rule="evenodd" d="M5.8 9.6a6.2 6.2 0 0 1 12.4 0zM8.35 6.9a.85.85 0 1 0 1.7 0 .85.85 0 1 0-1.7 0zM13.95 6.9a.85.85 0 1 0 1.7 0 .85.85 0 1 0-1.7 0z"/><path d="M5.8 10.6h12.4v6.4a1.6 1.6 0 0 1-1.6 1.6H7.4a1.6 1.6 0 0 1-1.6-1.6z"/><rect x="8.2" y="16" width="2.6" height="5.8" rx="1.3"/><rect x="13.2" y="16" width="2.6" height="5.8" rx="1.3"/><rect x="2.2" y="10.6" width="2.7" height="6.8" rx="1.35"/><rect x="19.1" y="10.6" width="2.7" height="6.8" rx="1.35"/></g><path d="M8.3 4.7 7 2.3M15.7 4.7 17 2.3" stroke="currentColor" stroke-width="1.1" stroke-linecap="round" fill="none"/></symbol>
<symbol id="os-linux" viewBox="0 0 48 48"><g fill="#1d1f2b"><ellipse cx="24" cy="14.6" rx="8.4" ry="11.4"/><ellipse cx="24" cy="30.2" rx="14.4" ry="14.6"/><ellipse cx="9.4" cy="30.5" rx="3.3" ry="9.6" transform="rotate(13 9.4 30.5)"/><ellipse cx="38.6" cy="30.5" rx="3.3" ry="9.6" transform="rotate(-13 38.6 30.5)"/></g><ellipse cx="24" cy="32.6" rx="9.9" ry="11.6" fill="#fff"/><ellipse cx="24" cy="19.4" rx="6.5" ry="7.3" fill="#fff"/><g fill="#f5a524"><path d="M4.8 44.6c-1.2-2.2.4-4.6 3.6-5.4 3.2-.8 7 .2 8.6 2.2 1.1 1.5-.4 3.2-3.3 3.6-3 .4-7.4.3-8.9-.4z"/><path transform="matrix(-1 0 0 1 48 0)" d="M4.8 44.6c-1.2-2.2.4-4.6 3.6-5.4 3.2-.8 7 .2 8.6 2.2 1.1 1.5-.4 3.2-3.3 3.6-3 .4-7.4.3-8.9-.4z"/><path d="M19.4 21.1c1.6-1.9 7.6-1.9 9.2 0 .9 1.1-1.5 3.6-4.6 3.6s-5.5-2.5-4.6-3.6z"/></g><path d="M20.6 22.8c2 1.2 4.8 1.2 6.8 0" fill="none" stroke="#d98a0f" stroke-width=".7" stroke-linecap="round"/><g fill="#1d1f2b"><ellipse cx="21.2" cy="17.4" rx="1.25" ry="1.8"/><ellipse cx="26.8" cy="17.4" rx="1.25" ry="1.8"/></g></symbol>
<symbol id="os-device" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="4" width="14" height="10" rx="1.8"/><path d="M6.5 18h6M9.5 14v4"/><rect x="15.5" y="8.5" width="6" height="11" rx="1.6"/></g></symbol>
</defs></svg>
<header class="top">
<div class="brand"><span class="logo" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="3.5" width="19" height="17" rx="4.5"/><path d="M7.25 8.4h.01M7.25 11v2.9M11.25 8.4h6.5M11.25 12.2h4"/><path d="M6.5 16.9h11" opacity=".32"/><path d="M6.5 16.9h6.6"/></svg></span><div class="bt"><b data-t="bt1">XpnTeam اطلاعات سرویس</b><small data-t="bt2">تجربه‌ای متفاوت در اتصال و مدیریت.</small></div></div>
<div class="ctl">
<div class="lg" id="lg"><button type="button" class="lgb" id="lgb" aria-haspopup="listbox" aria-expanded="false" aria-controls="lgm" aria-label="Language"><svg class="fl f-fa" viewBox="0 0 24 24" aria-hidden="true"><use href="#f-fa"/></svg><svg class="fl f-en" viewBox="0 0 24 24" aria-hidden="true"><use href="#f-en"/></svg><span id="lgt">فارسی</span><svg class="cv" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg></button>
<ul class="lgm" id="lgm" role="listbox" aria-label="Language" hidden>
<li role="option" tabindex="-1" data-l="fa" aria-selected="true"><span class="ln"><svg class="fl" viewBox="0 0 24 24" aria-hidden="true"><use href="#f-fa"/></svg>فارسی</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7"/></svg></li>
<li role="option" tabindex="-1" data-l="en" aria-selected="false"><span class="ln"><svg class="fl" viewBox="0 0 24 24" aria-hidden="true"><use href="#f-en"/></svg>English</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7"/></svg></li>
</ul></div>
<span class="av" id="av" role="img" aria-label="User information" title="User information"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20.6c0-3.8 3.6-6.2 8-6.2s8 2.4 8 6.2c0 .3-.2.4-.5.4h-15c-.3 0-.5-.1-.5-.4z"/></svg><span class="avb"><svg viewBox="0 0 16 16" fill="#fff" aria-hidden="true"><circle cx="8" cy="4.2" r="1.5"/><rect x="6.8" y="7" width="2.4" height="6" rx="1.2"/></svg></span></span>
</div>
<div class="gar" aria-hidden="true"><i></i><i></i></div>
</header>
<div class="app">
<nav class="side"><a class="on" href="#home"><span class="material-icons">home</span><span data-t="n1"></span></a><a href="#support"><span class="material-icons">headset_mic</span><span data-t="n3"></span></a><a href="#settings"><span class="material-icons">settings</span><span data-t="n2"></span></a></nav>
<main class="main" id="home">
<p class="mut" id="ld">...</p>
<div id="ui" hidden>
<section class="card hero"><span class="hero-bg" aria-hidden="true"><i></i></span>
<div class="hero-av" aria-hidden="true"><b id="hini"></b></div>
<div class="hero-tx"><span class="hero-chip"><i class="hero-wave" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 11V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2"/><path d="M14 10V4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v2"/><path d="M10 10.5V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/></svg></i><span data-t="hk"></span></span>
<h1 id="hello"></h1><p class="mut" data-t="welcome"></p></div></section>
<div class="bento">
<section class="c3">
<div class="card" id="c1"><span class="ico material-icons">power_settings_new</span><div><small data-t="svc"></small><b id="v1"></b><small id="s1"></small></div></div>
<div class="card"><span class="ico material-icons">all_inclusive</span><div><small data-t="vol"></small><b id="v2"></b><small id="s2"></small></div></div>
<div class="card"><span class="ico material-icons">event</span><div><small data-t="time"></small><b id="v3"></b><small id="s3"></small></div></div>
</section>
<section class="card" id="stats">
<div class="hd"><h2 data-t="chart"></h2><div class="pills"><button data-r="24H" class="on"></button><button data-r="7D"></button><button data-r="30D"></button><button data-r="12M"></button><button data-r="All"></button></div></div>
<div class="plot"><div class="yl" id="yl"></div><div class="cw"><div class="bars" id="bars"></div><div class="xl" id="xl"></div></div></div>
<div class="sts"><div><small data-t="total"></small><b id="t1"></b></div><div><small id="l2"></small><b id="t2"></b></div><div><small id="l3"></small><b id="t3"></b></div></div>
</section>
<section class="card" id="os"><div class="hd"><h2 data-t="os"></h2></div><div id="ol"></div></section>
<section class="card" id="sub"><div class="hd"><h2 data-t="conn"></h2></div>
<div class="lk"><code id="sl"></code><button class="ib" id="cs" aria-label="Copy"><span class="material-icons">content_copy</span></button></div>
<p class="mut" style="margin-top:10px"><span data-t="used"></span>: <b id="used"></b></p>
</section>
<section class="card qc" id="qrs"><div class="hd"><h2 data-t="qrt"></h2></div>
<div class="qf"><i></i><i></i><i></i><i></i><div id="qp"></div></div>
<small data-t="scan"></small>
<button type="button" class="btn" id="qs"><span class="material-icons">zoom_out_map</span><span data-t="big"></span></button>
</section>
</div>
</div>
<div id="sp" hidden>
<h1 data-t="sp_t"></h1><p class="mut" data-t="sp_d"></p>
<section class="card qc" id="spc">
<div class="spb" id="spb" role="status"><i></i><span id="spl"></span></div>
<p class="spm" id="spm"></p>
<small id="spr"></small>
<a class="btn big" id="spa" target="_blank" rel="noopener noreferrer"><span data-t="sp_go"></span><span class="material-icons fl">send</span></a>
</section>
<section class="c3 t2">
<div class="card"><span class="ico material-icons">schedule</span><div><small data-t="sp_h"></small><b id="sph"></b><small data-t="sp_tz"></small></div></div>
<div class="card"><span class="ico material-icons">public</span><div><small data-t="sp_n"></small><b id="spn"></b><small data-t="sp_c"></small></div></div>
</section>
</div>
<div id="st" hidden>
<section class="card"><div class="hd"><h2 data-t="stt"></h2></div>
<h3 data-t="thm"></h3><p class="mut" data-t="thd"></p>
<div class="mdr"><span class="mdt" data-t="md"></span><div class="seg" role="group" aria-label="Appearance"><button type="button" class="sg" data-m="dark" aria-pressed="false"><span class="material-icons">dark_mode</span><span data-t="mdd"></span></button><button type="button" class="sg" data-m="light" aria-pressed="false"><span class="material-icons">light_mode</span><span data-t="mdl"></span></button></div></div>
<div class="ths">
<button type="button" class="tc" data-a="mono" style="--p1:#000;--p2:#6b6b6b;--p3:#bdbdbd"><i></i><span data-t="tm"></span><span class="material-icons ck">check_circle</span></button>
<button type="button" class="tc" data-a="violet" style="--p1:#03024E;--p2:#0135FC;--p3:#5aa2ff"><i></i><span data-t="tv"></span><span class="material-icons ck">check_circle</span></button>
<button type="button" class="tc" data-a="green" style="--p1:#2d531a;--p2:#477023;--p3:#6e8649"><i></i><span data-t="tg"></span><span class="material-icons ck">check_circle</span></button>
<button type="button" class="tc" data-a="yellow" style="--p1:#f59e0b;--p2:#ffc247;--p3:#ff7a21"><i></i><span data-t="ty"></span><span class="material-icons ck">check_circle</span></button>
<button type="button" class="tc" data-a="purple" style="--p1:#320850;--p2:#8230C0;--p3:#B078D8"><i></i><span data-t="tp"></span><span class="material-icons ck">check_circle</span></button>
<button type="button" class="tc" data-a="xmas" style="--p1:#157550;--p2:#d62b45;--p3:#ffc857"><i></i><span data-t="tx"></span><span class="material-icons ck">check_circle</span></button>
</div></section>
</div>
</main>
</div>
<nav class="bot"><a class="on" href="#home"><span class="material-icons">home</span><span data-t="n1"></span></a><a href="#support"><span class="material-icons">headset_mic</span><span data-t="n3"></span></a><a href="#settings"><span class="material-icons">settings</span><span data-t="n2"></span></a></nav>
<div class="qm" id="qm" hidden><div id="qrb"></div></div>
<div class="nx" id="nx" data-k="exp" hidden>
<section class="card nxc" role="alertdialog" aria-modal="true" aria-labelledby="nxt" aria-describedby="nxd">
<span class="nxi" aria-hidden="true">
<svg class="i-e" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"/><path d="M16 2v4M8 2v4M3 10h18M9.5 13.5l5 5M14.5 13.5l-5 5"/></svg>
<svg class="i-v" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5.5" rx="8.5" ry="3"/><path d="M3.5 5.5v13c0 1.66 3.8 3 8.5 3s8.5-1.34 8.5-3v-13"/><path d="M12 10.5v4.2M12 17.6h.01"/></svg>
</span>
<h2 id="nxt"></h2>
<p class="nxd mut" id="nxd"></p>
<p class="nxm" id="nxm"></p>
<a class="nxb" id="nxa" target="_blank" rel="noopener noreferrer"><span class="nxl"><span id="nxb"></span><span class="nxbi" id="nxid" dir="ltr"></span></span><svg class="fl" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg></a>
<button type="button" class="nxk" id="nxk" data-t="nx_c"></button>
</section></div>
<div id="snow" aria-hidden="true"></div>
<div id="toast"></div>
<script>
const BT0=Date.now();
const $=s=>document.querySelector(s),$$=s=>[...document.querySelectorAll(s)];
let BD=0;const bootEnd=()=>{if(BD)return;BD=1;const h=document.documentElement,b=$('#ldg'),r=()=>{h.classList.remove('ld');h.classList.add('ent');setTimeout(()=>h.classList.remove('ent'),1600)};if(!b)return r();setTimeout(()=>{r();b.classList.add('out');setTimeout(()=>b.remove(),600)},Math.max(0,300-(Date.now()-BT0)))};setTimeout(bootEnd,8e3);
const ls=(k,v)=>{try{if(v===undefined)return localStorage.getItem(k);localStorage.setItem(k,v)}catch(e){}};
const jget=(u,ms,c)=>{c=c||new AbortController();const tm=setTimeout(()=>c.abort(),ms);return fetch(u,{signal:c.signal,cache:'no-store'}).then(r=>{if(!r.ok)throw r.status;return r.json()}).finally(()=>clearTimeout(tm))};
const T={fa:{nx_t:'تاریخ سرویس شما تمام شده است',nx_d:'مدت زمان سرویس شما به پایان رسیده است. برای خرید سرویس جدید یا تمدید سرویس، در تلگرام به پشتیبانی پیام بدهید.',nx_b:'خرید یا تمدید سرویس',nx_m:'تاریخ پایان:',nl_t:'حجم سرویس شما تمام شده است',nl_d:'حجم مصرفی سرویس شما به پایان رسیده است. برای خرید حجم یا تمدید سرویس، در تلگرام به پشتیبانی پیام بدهید.',nl_b:'خرید حجم و تمدید سرویس',nl_m:'مصرف:',nx_c:'بستن',n3:'پشتیبانی',sp_t:'پشتیبانی',sp_d:'برای هر سؤال یا مشکل در مورد سرویس، مستقیم در تلگرام به پشتیبانی پیام بدهید.',sp_open:'پشتیبانی باز است',sp_closed:'پشتیبانی بسته است',sp_mo:'الان در ساعت پشتیبانی هستیم؛ می‌توانید همین حالا پیام بدهید.',sp_mc:'الان خارج از ساعت پشتیبانی هستیم. لطفاً بعد از ساعت {o} (به وقت ایران) پیام بدهید.',sp_co:'{d} تا پایان ساعت پشتیبانی',sp_cc:'{d} تا شروع ساعت پشتیبانی',sp_go:'ارسال پیام در تلگرام',sp_h:'ساعات پشتیبانی',sp_ot:'۹ صبح',sp_ct:'۱۲ شب',sp_to:' تا ',sp_tz:'به وقت ایران',sp_n:'ساعت فعلی ایران',sp_c:'تهران',sp_hr:'ساعت',sp_mn:'دقیقه',sp_and:' و ',hello:'سلام، ',welcome:'به پنل کاربری سرویس خود خوش آمدید.',hk:'پنل کاربری',bt1:'XpnTeam اطلاعات سرویس',bt2:'تجربه‌ای متفاوت در اتصال و مدیریت.',svc:'وضعیت سرویس',vol:'حجم باقی‌مانده',time:'زمان باقی‌مانده',unl:'نامحدود',nlv:'بدون محدودیت حجم',nlt:'بدون محدودیت زمانی',of:'از',days:'روز',active:'فعال',disabled:'غیرفعال',limited:'حجم تمام شده',expired:'منقضی شده',on_hold:'در انتظار اتصال',ok:'سرویس شما در حال حاضر فعال است.',no:'سرویس شما فعال نیست.',chart:'نمودار مصرف',total:'کل مصرف',avg:'میانگین',peak:'پیک',hour:'ساعتی',day:'روزانه',month:'ماهانه',os:'انتخاب سیستم‌عامل',conn:'کد اتصال / کانفیگ',qrt:'کد QR اشتراک',big:'نمایش بزرگ',scan:'اسکن برای اتصال',copied:'کپی شد',used:'ترافیک مصرفی',dl:'دانلود',cfg:'افزودن کانفیگ',tut:'آموزش',err:'خطا در دریافت اطلاعات اشتراک.',retry:'تلاش دوباره',n2:'تنظیمات',stt:'تنظیمات صفحه',thm:'تم',md:'حالت نمایش',mdd:'تیره',mdl:'روشن',thd:'رنگ‌بندی صفحه را انتخاب کنید. همه تم‌ها با حالت تیره و روشن سازگارند.',tm:'سیاه و سفید (پیش‌فرض)',tv:'رویای کیهانی',tg:'سبز',ty:'زرد',tp:'بنفش',tx:'کریسمس',n1:'داشبورد',ui:'اطلاعات کاربر',lg:'زبان',r:['۲۴ ساعت','۷ روز','۳۰ روز','۱۲ ماه','همه'],u:['بایت','کیلوبایت','مگ','گیگ','ترابایت']},
en:{nx_t:'Your service has expired',nx_d:'Your service period has ended. To buy a new service or renew your current one, message our support on Telegram.',nx_b:'Buy or renew service',nx_m:'Ended on',nl_t:'Your data is used up',nl_d:'You have used all the data in your plan. To buy more data or renew your service, message our support on Telegram.',nl_b:'Buy data & renew service',nl_m:'Used',nx_c:'Close',n3:'Support',sp_t:'Support',sp_d:'Have a question or a problem with your service? Message our Telegram support directly.',sp_open:'Support is open',sp_closed:'Support is closed',sp_mo:'We are within support hours — you can message us right now.',sp_mc:'We are outside support hours right now. Please message us after {o} (Iran time).',sp_co:'Closes in {d}',sp_cc:'Opens in {d}',sp_go:'Message on Telegram',sp_h:'Support hours',sp_ot:'9:00 AM',sp_ct:'midnight',sp_to:' – ',sp_tz:'Iran time',sp_n:'Current time in Iran',sp_c:'Tehran',sp_hr:'h',sp_mn:'min',sp_and:' ',hello:'Hello, ',welcome:'Welcome to your service user panel.',hk:'User panel',bt1:'XpnTeam Service information',bt2:'A Different Experience in Connectivity and Management.',svc:'Service status',vol:'Remaining data',time:'Time left',unl:'Unlimited',nlv:'No data limit',nlt:'No time limit',of:'of',days:'days',active:'Active',disabled:'Disabled',limited:'Data used up',expired:'Expired',on_hold:'On hold',ok:'Your service is active.',no:'Your service is not active.',chart:'Usage chart',total:'Total',avg:'Average',peak:'Peak',hour:'hourly',day:'daily',month:'monthly',os:'Choose your OS',conn:'Connection / config',scan:'Scan to connect',qrt:'Subscription QR code',big:'Enlarge',copied:'Copied',used:'Used traffic',dl:'Download',cfg:'Add config',tut:'Tutorial',err:'Could not load subscription info.',retry:'Retry',n1:'Dashboard',n2:'Settings',stt:'Page settings',thm:'Theme',md:'Appearance',mdd:'Dark',mdl:'Light',thd:'Pick a color scheme. Every theme works in dark and light mode.',tm:'Black & White (default)',tv:'Cosmic Dream',tg:'Green',ty:'Yellow',tp:'Purple',tx:'Christmas',ui:'User information',lg:'Language',r:['24 hours','7 days','30 days','12 months','All'],u:['B','KB','MB','GB','TB']}};
// support: Telegram ID + hours (24h clock, Iran time); keep texts sp_ot/sp_ct in sync with from/to
const SUP={id:'XpnAdmin',from:9,to:24,tz:'Asia/Tehran'};
const LN={fa:'فارسی',en:'English'};
const P=location.pathname.split('#')[0].replace(/\/info\/?$/,'').replace(/\/$/,''),SUB=location.origin+P;
let L=ls('lang')||'fa',R='24H',PD='hour',D=null,OS=[],U=[];
const t=k=>T[L][k]||k,LC=()=>L=='fa'?'fa-IR':'en-US',nf=(n,m=1)=>(+n).toLocaleString(LC(),{maximumFractionDigits:m});
const fmt=b=>{b=+b||0;let i=0;while(b>=1024&&i<4){b/=1024;i++}return nf(b,i>2?2:i==2?0:1)+' '+T[L].u[i]};
const esc=s=>String(s).replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
const exp=e=>!e?0:typeof e=='number'?(e<1e12?e*1e3:e):Date.parse(e);
const toast=m=>{const x=$('#toast');x.textContent=m;x.classList.add('s');setTimeout(()=>x.classList.remove('s'),1500)};
function cp(x){const f=()=>{const a=document.createElement('textarea');a.value=x;document.body.appendChild(a);a.select();try{document.execCommand('copy')}catch(e){}a.remove()};navigator.clipboard?navigator.clipboard.writeText(x).catch(f):f();toast(t('copied'))}
function qrInto(el,x,s){el.innerHTML='';if(window.QRCode){try{new QRCode(el,{text:x,width:s,height:s,correctLevel:QRCode.CorrectLevel.L});return}catch(e){}}el.textContent=x}
function qr(x){$('#qm').hidden=false;qrInto($('#qrb'),x,260)}
const p2=n=>(+n).toLocaleString(LC(),{minimumIntegerDigits:2,useGrouping:false});
const dur=m=>{const h=Math.floor(m/60),n=m%60,a=[];if(h)a.push(nf(h,0)+' '+t('sp_hr'));if(n||!h)a.push(nf(n,0)+' '+t('sp_mn'));return a.join(t('sp_and'))};
let IF;function irn(){const n=new Date();try{const q=(IF=IF||new Intl.DateTimeFormat('en-US',{timeZone:SUP.tz,hour12:false,hour:'numeric',minute:'numeric'})).formatToParts(n),g=k=>+q.find(x=>x.type==k).value;return g('hour')%24*60+g('minute')}catch(e){const z=new Date(n.getTime()+126e5);return z.getUTCHours()*60+z.getUTCMinutes()}}
function sup(){const m=irn(),a=SUP.from*60,b=SUP.to*60,op=m>=a&&m<b,w=op?b-m:m<a?a-m:1440-m+a,set=(s,v)=>{const e=$(s);if(e.textContent!==v)e.textContent=v};
$('#spb').classList.toggle('off',!op);
set('#spl',t(op?'sp_open':'sp_closed'));
set('#spm',t(op?'sp_mo':'sp_mc').replace('{o}',t('sp_ot')));
set('#spr',t(op?'sp_co':'sp_cc').replace('{d}',dur(w)));
set('#sph',t('sp_ot')+t('sp_to')+t('sp_ct'));
set('#spn',p2(Math.floor(m/60))+':'+p2(m%60))}
function tr(){document.documentElement.lang=L;document.documentElement.dir=L=='fa'?'rtl':'ltr';
$$('[data-t]').forEach(e=>e.textContent=t(e.dataset.t));$$('.pills button').forEach((b,i)=>b.textContent=T[L].r[i]);lgUI();sup();if(D)view()}
function view(){const d=D,s=d.status||'active',ok=s=='active',lim=d.data_limit>0,ex=exp(d.expire),dl=ex?Math.ceil((ex-Date.now())/864e5):0;
document.title=d.username+' Sub Info';{const h=$('#hello'),n=document.createElement('span');h.textContent=t('hello');n.className='hero-nm';n.textContent=d.username;h.appendChild(n);$('#hini').textContent=([...String(d.username||'?')][0]||'?').toUpperCase()}
$('#c1').className='card '+(ok?'ok':'bad');$('#v1').textContent=t(s);$('#s1').textContent=t(ok?'ok':'no');
$('#v2').textContent=lim?fmt(Math.max(d.data_limit-(d.used_traffic||0),0)):t('unl');$('#s2').textContent=lim?t('of')+' '+fmt(d.data_limit):t('nlv');
$('#v3').textContent=ex?(dl>0?nf(dl,0)+' '+t('days'):t('expired')):t('unl');$('#s3').textContent=ex?new Date(ex).toLocaleDateString(LC()):t('nlt');
$('#c1').dataset.s=s;document.documentElement.dataset.svc=ok?'ok':'bad';
{const rm=lim?Math.max(d.data_limit-(d.used_traffic||0),0)/d.data_limit:1;$('#s2').dataset.tone=lim?(rm>.3?'ok':rm>.1?'warn':'bad'):'mute';$('#s3').dataset.tone=ex?(dl>7?'ok':dl>3?'warn':'bad'):'mute'}
$('#used').textContent=fmt(d.lifetime_used_traffic||d.used_traffic);$('#sl').textContent=SUB;
oses();draw();notice()}
// ----- expiry / data-limit notice: text follows the page language (view() re-runs on every language switch); colors follow the theme CSS variables -----
let NX=0;
const nxClose=()=>{$('#nx').hidden=true};
function notice(){const d=D,s=d.status||'active',ex=exp(d.expire),u=d.used_traffic||0,m=$('#nx'),
k=s=='on_hold'?'':(s=='expired'||(ex>0&&ex<=Date.now()))?'exp':(s=='limited'||(d.data_limit>0&&u>=d.data_limit))?'vol':'';
if(!k){m.hidden=true;return}
const e=k=='exp';m.dataset.k=k;
$('#nxt').textContent=t(e?'nx_t':'nl_t');$('#nxd').textContent=t(e?'nx_d':'nl_d');$('#nxb').textContent=t(e?'nx_b':'nl_b');
$('#nxm').textContent=e?(ex>0?t('nx_m')+' '+new Date(ex).toLocaleDateString(LC()):''):t('nl_m')+' '+fmt(u)+' '+t('of')+' '+fmt(d.data_limit);
$('#nxid').textContent='@'+SUP.id;$('#nxa').href='https://t.me/'+SUP.id;
if(NX)return;NX=1;
const go=()=>{m.hidden=false;try{$('#nxa').focus({preventScroll:true})}catch(x){}};
document.documentElement.classList.contains('ld')?setTimeout(go,550):go()}
$('#nxk').onclick=nxClose;
$('#nx').addEventListener('click',e=>{if(e.target.id=='nx')nxClose()});
document.addEventListener('keydown',e=>{if($('#nx').hidden)return;if(e.key=='Escape'){nxClose();return}if(e.key=='Tab'){const f=[$('#nxa'),$('#nxk')],i=f.indexOf(document.activeElement);e.preventDefault();f[(i+(e.shiftKey?-1:1)+f.length)%f.length].focus()}});
const su=u=>/^\s*(javascript|data|vbscript):/i.test(u)?'#':u;
const oi=n=>/ios|iphone|ipad|apple|mac/i.test(n)?'apple':/android/i.test(n)?'android':/win/i.test(n)?'windows':/linux/i.test(n)?'linux':'device';
function oses(){$('#ol').innerHTML=OS.map(o=>`<details><summary><span class="osi" data-os="${oi(o.engName||'')}"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#os-${oi(o.engName||'')}"/></svg></span>${esc(L=='fa'?o.name:o.engName)}</summary><div class="in">${(o.apps||[]).map(a=>`<div class="a1"><b>${esc(a.name)}</b><p class="mut">${esc((L=='fa'?a.faDescription:a.description)||'')}</p><div class="acts">${a.downloadLink&&!a.isAd?`<a class="btn dl" target="_blank" rel="noopener" href="${esc(su(a.downloadLink))}">${t('dl')}</a>`:''}${a.configLink?`<a class="btn cfg" href="${esc(su(a.configLink.replace('{url}',SUB)))}">${t('cfg')}</a>`:''}${a.videoLink?`<a class="btn tut" target="_blank" rel="noopener" href="${esc(su(a.videoLink))}">${t('tut')}</a>`:''}</div></div>`).join('')}</div></details>`).join('')}
const lbl=d=>d.toLocaleString(LC(),PD=='hour'?{hour:'2-digit',minute:'2-digit'}:PD=='day'?{month:'short',day:'numeric'}:{year:'2-digit',month:'short'});
let BK='';
function draw(an){const n=U.length,mx=Math.max(...U.map(x=>x.v),0),tot=U.reduce((a,x)=>a+x.v,0),m=mx||1,bk=PD+L+'|'+n+'|'+tot+'|'+mx,B=$('#bars');
if(bk!==BK||an){BK=bk;B.classList.toggle('g',!!an);B.innerHTML=U.map((x,i)=>`<i title="${lbl(x.d)} — ${fmt(x.v)}" style="height:${Math.max(x.v/m*100,1.5)}%;--i:${i}"></i>`).join('')}
const w=$('#bars').clientWidth||240,k=Math.max(2,Math.min(6,Math.floor(w/70))),st=Math.max(1,Math.ceil(n/k));
$('#xl').innerHTML=U.map((x,i)=>{if(i%st)return'';const p=(i+.5)/n;return`<span style="left:${p*100}%;transform:translateX(${p<.1?0:p>.9?-100:-50}%)">${lbl(x.d)}</span>`}).join('')
$('#yl').innerHTML=[1,.75,.5,.25,0].map(k=>`<span>${fmt(m*k)}</span>`).join('');
$('#t1').textContent=fmt(tot);$('#t2').textContent=fmt(n?tot/n:0);$('#t3').textContent=fmt(mx);
$('#l2').textContent=t('avg')+' '+t(PD);$('#l3').textContent=t('peak')+' '+t(PD)}
let UQ=0,UC;
async function usage(){const q=++UQ,n=new Date(),s=new Date(n);let pd='day';UC&&UC.abort();
if(R=='24H'){s.setTime(n-864e5);pd='hour'}else if(R=='7D'){s.setDate(n.getDate()-6);s.setHours(0,0,0,0)}else if(R=='30D'){s.setDate(n.getDate()-29);s.setHours(0,0,0,0)}else if(R=='12M'){s.setMonth(n.getMonth()-11);s.setDate(1);s.setHours(0,0,0,0);pd='month'}else{s.setTime(0);pd='month'}
let u=[],ok=true;
if(D.lifetime_used_traffic||D.used_traffic){for(let i=0;i<2;i++){try{const c=UC=new AbortController(),j=await jget(P+'/usage?'+new URLSearchParams({period:pd,start:s.toISOString(),end:n.toISOString()}),1e4,c),st=j.stats||{};u=(st[-1]||Object.values(st)[0]||[]).map(x=>({d:new Date(x.period_start),v:x.total_traffic||0}));ok=true;break}catch(e){if(q!=UQ)return;ok=false;if(!i){await new Promise(f=>setTimeout(f,700));if(q!=UQ)return}}}}
if(q!=UQ)return;
U=u;PD=pd;const sc=$('#stats');
if(ok)sc.dataset.ok=1;else if(sc.dataset.ok)toast(t('err'));else sc.hidden=true;
draw(ok&&u.length)}
document.addEventListener('click',e=>{const b=e.target.closest('button');
if(e.target.id=='qm')$('#qm').hidden=true;if(!b)return;
if(b.dataset.r){R=b.dataset.r;$$('.pills button').forEach(x=>x.classList.toggle('on',x==b));usage()}
else if(b.dataset.a){const a=b.dataset.a;if(document.documentElement.dataset.accent!=a)reveal(()=>{ls('ac',a);ac()},b)}else if(b.id=='cs')cp(SUB);else if(b.id=='qs')qr(SUB)});
function lgUI(){$('#lgt').textContent=LN[L];$$('#lgm [data-l]').forEach(o=>o.setAttribute('aria-selected',o.dataset.l==L));$('#lgb').setAttribute('aria-label',t('lg'));const a=$('#av'),x=t('ui')+(D?': '+D.username:'');a.title=x;a.setAttribute('aria-label',x)}
const lgOpen=o=>{const m=$('#lgm'),b=$('#lgb');m.hidden=!o;b.classList.toggle('open',o);b.setAttribute('aria-expanded',o);if(o){const x=m.querySelector('[aria-selected=true]');if(x)x.focus()}};
$('#lgb').onclick=()=>lgOpen($('#lgm').hidden);
$('#lgm').onclick=e=>{const o=e.target.closest('[data-l]');if(!o)return;L=o.dataset.l;ls('lang',L);tr();lgOpen(false);$('#lgb').focus()};
document.addEventListener('click',e=>{if(!e.target.closest('#lg'))lgOpen(false)});
$('#lg').addEventListener('keydown',e=>{const m=$('#lgm'),it=[...m.querySelectorAll('[data-l]')],i=it.indexOf(document.activeElement);
if(e.key=='Escape'){lgOpen(false);$('#lgb').focus()}
else if(e.key=='ArrowDown'||e.key=='ArrowUp'){e.preventDefault();if(m.hidden)lgOpen(true);else it[(i+(e.key=='ArrowDown'?1:-1)+it.length)%it.length].focus()}
else if((e.key=='Enter'||e.key==' ')&&i>=0){e.preventDefault();it[i].click()}
else if(e.key=='Tab')lgOpen(false)});
let rz,lw=innerWidth;addEventListener('resize',()=>{if(innerWidth==lw)return;lw=innerWidth;clearTimeout(rz);rz=setTimeout(()=>D&&draw(),120)});
const AC=['mono','violet','green','yellow','purple','xmas'];
const metaTheme=()=>{const m=document.querySelector('meta[name=theme-color]');if(m)m.content=getComputedStyle(document.documentElement).getPropertyValue('--bg').trim()};
const ac=()=>{const a=AC.includes(ls('ac'))?ls('ac'):'mono';document.documentElement.dataset.accent=a;$$('.tc').forEach(b=>b.setAttribute('aria-pressed',b.dataset.a==a));metaTheme()};
const nav=()=>{const h=location.hash,s=h=='#settings',p=h=='#support',o=s||p;$('#st').hidden=!s;$('#sp').hidden=!p;$('#ui').hidden=o||!D;$('#ld').hidden=o||!!D;$$('.side a,.bot a').forEach(a=>a.classList.toggle('on',a.hash==(o?h:'#home')));if(o)scrollTo(0,0);else if(D)draw();if(p)sup()};
const rm=()=>matchMedia('(prefers-reduced-motion:reduce)').matches;let VQ=0;
const FX=()=>{const c=document.documentElement.classList;return !!document.startViewTransition&&!rm()&&matchMedia('(hover:hover) and (pointer:fine)').matches&&!c.contains('gl-lite')&&!c.contains('ld')};
const vt=(fn,cls)=>{const h=document.documentElement;if(!FX()){fn();return}const q=++VQ;h.classList.remove('vt-theme');h.classList.add(cls);const x=document.startViewTransition(fn);x.ready.catch(()=>{});x.finished.catch(()=>{}).then(()=>{if(q==VQ)h.classList.remove(cls)});return x};
const reveal=(fn,el)=>{const r=el.getBoundingClientRect(),x=r.left+r.width/2,y=r.top+r.height/2,R=Math.hypot(Math.max(x,innerWidth-x),Math.max(y,innerHeight-y)),v=vt(fn,'vt-theme'),c='circle('+'0px at '+x+'px '+y+'px)';
v&&v.ready.then(()=>document.documentElement.animate({clipPath:[c,'circle('+R+'px at '+x+'px '+y+'px)']},{duration:600,easing:'cubic-bezier(.2,.8,.2,1)',pseudoElement:'::view-transition-new(root)'})).catch(()=>{})};
addEventListener('hashchange',nav);
const th=()=>{const m=ls('th')||'dark';document.documentElement.dataset.theme=m;$$('.sg').forEach(b=>b.setAttribute('aria-pressed',b.dataset.m==m));metaTheme()};
$$('.sg').forEach(b=>b.onclick=()=>{if((ls('th')||'dark')==b.dataset.m)return;reveal(()=>{ls('th',b.dataset.m);th();const i=b.querySelector('.material-icons');if(i&&!rm())i.animate({transform:['rotate(-100deg) scale(.3)','none'],opacity:[0,1]},{duration:520,easing:'cubic-bezier(.3,1.5,.5,1)'})},b)});th();ac();tr();nav();$('#spa').href='https://t.me/'+SUP.id;setInterval(()=>{if(!document.hidden)sup()},1e4);document.addEventListener('visibilitychange',()=>{if(!document.hidden)sup()});
let QD=0;window.__qr=()=>{if(QD||!window.QRCode)return;QD=1;qrInto($('#qp'),SUB,184)};if(window.QRCode)__qr();addEventListener('load',()=>{if(!QD){QD=1;qrInto($('#qp'),SUB,184)}});
if(matchMedia('(hover:hover) and (pointer:fine)').matches){let f=0,a;document.addEventListener('pointermove',e=>{const c=e.target.closest&&e.target.closest('.card');if(!c)return;a=[c,e.clientX,e.clientY];f||(f=requestAnimationFrame(()=>{f=0;const r=a[0].getBoundingClientRect();a[0].style.setProperty('--mx',a[1]-r.left+'px');a[0].style.setProperty('--my',a[2]-r.top+'px')}))},{passive:true})}
(()=>{const K='os_v1',c=(()=>{try{return JSON.parse(ls(K)||'null')}catch(e){return null}})();
if(c&&c.j){OS=c.j;oses()}if(c&&Date.now()-c.t<432e5)return;
const ac=new AbortController(),tm=setTimeout(()=>ac.abort(),6e3);
fetch('https://raw.githubusercontent.com/MatinDehghanian/public-assets/refs/heads/main/json/os.json',{signal:ac.signal}).then(r=>{if(!r.ok)throw 0;return r.json()}).then(j=>{const a=j.operatingSystems||[];if(!a.length)return;OS=a;oses();ls(K,JSON.stringify({t:Date.now(),j:a}))}).catch(()=>{}).finally(()=>clearTimeout(tm))})();
const showErr=()=>{const l=$('#ld'),b=document.createElement('button');l.textContent=t('err')+' ';b.type='button';b.className='btn';b.style.cssText='border:0;cursor:pointer;margin-inline-start:8px';b.textContent=t('retry');b.onclick=()=>{l.textContent='...';boot()};l.append(b)};
async function boot(){for(let i=0;i<2&&!D;i++){try{const j=await jget(P+'/info',1e4);if(!j||typeof j!='object')throw 0;D=j}catch(e){if(!i)await new Promise(f=>setTimeout(f,600))}}
if(!D){showErr();bootEnd();return}
try{nav();tr()}finally{bootEnd()}usage()}
boot();
document.addEventListener('keydown',e=>{if(e.key=='Escape')$('#qm').hidden=true});
</script>
</body>
</html>
<?php
        return;
    }

    // ------------------------------------------------------------------
    // Everything else (VPN clients, /info, /usage ...) is proxied to the panel
    // ------------------------------------------------------------------
    define('BASE_URL', 'https://login.masteryxlog.space:443'); // Set the appropriate URL
    $requestUrl = BASE_URL . ($_SERVER['REQUEST_URI'] ?? '');

    function upstreamFail($code, $msg)
    {
        http_response_code($code);
        header('Content-Type: text/plain; charset=UTF-8');
        echo $msg;
        exit();
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $requestUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_CONNECTTIMEOUT => 5,            // fail fast when the panel is unreachable
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_ENCODING => '',                 // accept gzip from the panel
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // avoid stalls on broken IPv6 routes
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        upstreamFail(502, 'Upstream error');
    }

    $status       = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $headerSize   = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headerText   = substr($response, 0, $headerSize);
    $responseBody = substr($response, $headerSize);

    // Forward the necessary headers from the cURL response
    $isValidHeader = false;
    foreach (explode("\r\n", $headerText) as $i => $line) {
        if ($i === 0) continue;
        if (strpos($line, ': ') !== false) {
            list($key, $value) = explode(': ', $line, 2);
            if (in_array(strtolower($key), ['content-disposition', 'content-type', 'subscription-userinfo', 'profile-update-interval'])) {
                header("$key: $value");
                $isValidHeader = true;
            }
        }
    }

    if (!$isValidHeader) {
        upstreamFail(502, 'Error! No valid headers found.');
    }

    // Pass the panel's status through (404 for a bad token, etc.) so the page can detect errors
    if ($status >= 100 && $status < 600) {
        http_response_code($status);
    }

    echo $responseBody;
