import {
  applyPolyfills,
  defineCustomElements,
} from "/public/assets/@siemens/ix/loader/index.js";
import { defineCustomElements as ixIconsDefineCustomElements } from "/public/assets/@siemens/ix-icons/loader/index.js";

(async () => {
  await applyPolyfills();
  await ixIconsDefineCustomElements();
  defineCustomElements();
})();
