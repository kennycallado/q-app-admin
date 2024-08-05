// import '/public/assets/bootstrap/dist/js/bootstrap.bundle.min.js'
// import '/public/assets/@siemens/ix/dist/index.js'


import "./ix_init.js";
import "./theme_mode.js";

onloadAdd(() => {
  htmx.config.globalViewTransitions = true;
  me("body")
    .on("htmx:beforeSwap", (evt) => {
      if (
        evt.detail.xhr.status === 422 ||
        evt.detail.xhr.status === 400 ||
        evt.detail.xhr.status === 401
      ) {
        evt.detail.shouldSwap = true;
        evt.detail.isError = false;
      }
    })
    .on("htmx:afterRequest", (evt) => {
      // console.log(evt.detail)
    });
})
