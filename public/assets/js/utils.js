/**
 * @param {string} url - The URL to navigate to.
 * @param {boolean} [push=true] - Whether to push the URL to the browser history.
 * @param {string} [swap="transition:true"] - The swap attribute for the htmx request.
 * @returns {void}
 */
const back_arrow = (url, push = true, swap = "transition:true") => {
  if (!url) return;
  if (typeof url !== "string") return;
  if (swap && typeof swap !== "string") return;

  htmx
    .ajax("GET", url, { target: "#content", swap })
    .then(() => push && history.pushState({}, "", url));
};

/**
 * @param {HTMLElement} el - The parent element containing the tabs and their content.
 * @returns {void}
 */
const tabs_content = (el) => {
  const tabs = any("ix-tab-item", el);
  const contents = any("[data-tab-content]", el);

  tabs.run((tab) => {
    me(tab).on("click", () => {
      contents.run((content) =>
        me(content).classToggle("show", content.dataset.tabContent === tab.dataset.tabId),
      );

      tabs.run((t) => me(t).classToggle("tab-active", t.dataset.tabId === tab.dataset.tabId));
    });
  });
};
