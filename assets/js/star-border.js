/**
 * React Bits "StarBorder" Animation Initializer
 * Automatically applies the .star-border class to all buttons across the website.
 */
(function() {
  'use strict';
  
  function applyStarBorder() {
    // Target all common button classes and form submit elements
    const buttons = document.querySelectorAll(`
      button, 
      .btn, 
      .btn-primary, 
      .btn-secondary, 
      .btn-login, 
      .btn-outline, 
      .btn-arrow,
      input[type="submit"],
      input[type="button"]
    `);

    buttons.forEach(btn => {
      // Exclude specific elements like navbar text links if they matched
      // Also exclude demo-accounts buttons in the login modal
      if (!btn.classList.contains('star-border') && !btn.closest('.demo-accounts')) {
        btn.classList.add('star-border');
      }
    });
  }

  // 1. Run immediately on load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applyStarBorder);
  } else {
    applyStarBorder();
  }

  // 2. Setup a MutationObserver to catch dynamically added buttons
  // (e.g., when a modal opens, or dashboard tables load new content)
  const observer = new MutationObserver(mutations => {
    let shouldApply = false;
    for (let m of mutations) {
      if (m.addedNodes.length > 0) {
        shouldApply = true;
        break;
      }
    }
    if (shouldApply) {
      // Small timeout to allow DOM to settle
      setTimeout(applyStarBorder, 10);
    }
  });
  
  observer.observe(document.body, { 
    childList: true, 
    subtree: true 
  });
  
})();
