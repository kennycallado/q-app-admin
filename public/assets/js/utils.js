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
  const tabs = any("ix-tabs", el);
  const items = any("ix-tab-item", el);
  const contents = any("[data-tab-content]", el);

  items.forEach((tab, i) => {
    // preselect the tab based on the user's language
    if (tab.dataset.tabId === navigator.language.split('-')[0]) {
      me(tab).classAdd("tab-active");
      me(tabs).setAttribute('selected', i)
    }

    // preselect the content based on the user's language
    contents.run((el) => {
      me(el).classToggle('show', el.dataset.tabContent === navigator.language.split('-')[0])
    })

    me(tab).on("click", () => {
      contents.run((content) =>
        me(content).classToggle("show", content.dataset.tabContent === tab.dataset.tabId),
      );

      items.run((t) => me(t).classToggle("tab-active", t.dataset.tabId === tab.dataset.tabId));
    });
  });
};
