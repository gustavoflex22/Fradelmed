(function () {
  const isMobile = () => window.matchMedia("(max-width: 768px)").matches;
  const isHome = () => document.body.classList.contains("home");

  function forcePlayInline(video) {
    // attempt immediate play
    const tryPlay = () => {
      const p = video.play();
      if (p && typeof p.catch === "function") p.catch(() => {});
    };

    tryPlay();

    // if Safari still shows overlay/paused, replace node to reset state then play
    setTimeout(() => {
      if (!video.paused) return;
      const clone = video.cloneNode(true);
      clone.removeAttribute("controls");
      clone.controls = false;
      clone.autoplay = true;
      clone.muted = true;
      clone.defaultMuted = true;
      clone.loop = true;
      clone.playsInline = true;
      clone.webkitPlaysInline = true;
      clone.setAttribute("autoplay", "");
      clone.setAttribute("muted", "");
      clone.setAttribute("loop", "");
      clone.setAttribute("playsinline", "");
      clone.setAttribute("webkit-playsinline", "");
      clone.setAttribute("preload", "auto");
      clone.setAttribute("disablepictureinpicture", "");
      clone.setAttribute("controlslist", "nodownload noplaybackrate nofullscreen");
      clone.style.pointerEvents = "none";
      clone.poster = "";

      video.parentNode && video.parentNode.replaceChild(clone, video);
      const p2 = clone.play();
      if (p2 && typeof p2.catch === "function") p2.catch(() => {});
    }, 250);
  }

  function fixHeroVideoIOS() {
    if (!isMobile() || !isHome()) return;

    const videos = document.querySelectorAll(".hero video, .hero--video video, .site-main video");
    videos.forEach((v) => {
      try {
        v.removeAttribute("controls");
        v.controls = false;

        v.setAttribute("autoplay", "");
        v.autoplay = true;
        v.setAttribute("muted", "");
        v.muted = true;
        v.defaultMuted = true;
        v.setAttribute("loop", "");
        v.loop = true;
        v.setAttribute("playsinline", "");
        v.playsInline = true;
        v.setAttribute("webkit-playsinline", "");
        v.webkitPlaysInline = true;
        v.setAttribute("preload", "auto");
        v.setAttribute("disablepictureinpicture", "");
        v.disablePictureInPicture = true;
        v.setAttribute("controlslist", "nodownload noplaybackrate nofullscreen");
        v.style.pointerEvents = "none";
        v.poster = "";

        v.onpause = () => {
          if (isMobile() && isHome()) {
            const p = v.play();
            if (p && typeof p.catch === "function") p.catch(() => {});
          }
        };

        v.load();
        v.addEventListener("loadeddata", () => {
          const p2 = v.play();
          if (p2 && typeof p2.catch === "function") p2.catch(() => {});
        }, { once: true });
        forcePlayInline(v);
      } catch (e) {}
    });

    const selectors = [
      ".plyr__control--overlaid",
      ".vjs-big-play-button",
      ".mejs__overlay",
      ".mejs__overlay-button",
      ".wp-video-play-icon",
      ".elementor-custom-embed-play",
      ".ytp-large-play-button",
      "[class*='play-button']",
      "[class*='video-play']",
      "[id*='play']"
    ];

    selectors.forEach((sel) => {
      document.querySelectorAll(sel).forEach((el) => el.remove());
    });
  }

  document.addEventListener("DOMContentLoaded", fixHeroVideoIOS);
  window.addEventListener("load", fixHeroVideoIOS);
  document.addEventListener("visibilitychange", () => {
    if (!document.hidden) {
      fixHeroVideoIOS();
    }
  });

  let tries = 0;
  const interval = setInterval(() => {
    fixHeroVideoIOS();
    tries += 1;
    if (tries > 120) clearInterval(interval);
  }, 500);

  const mo = new MutationObserver(() => fixHeroVideoIOS());
  mo.observe(document.documentElement, { childList: true, subtree: true });
})();
