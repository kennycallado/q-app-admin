import { showModal, closeModal, dismissModal } from "/public/assets/@siemens/ix/dist/index.js";

export function create(modalName, modalTemplate) {
  window.customElements.define(
    modalName,
    class extends HTMLElement {
      isInitalRender = false;

      // constructor() { super(); }

      connectedCallback() {
        if (this.isInitalRender) {
          return;
        }

        this.isInitalRender = true;
        this.firstRender();
      }

      firstRender() {
        const template = modalTemplate.content.cloneNode(true);

        const cancelButton = template.querySelector("[data-cancel]");
        const okayButton = template.querySelector("[data-accept]");

        if (cancelButton) {
          cancelButton.addEventListener("click", () => {
            dismissModal(this);
          });
        }

        if (okayButton) {
          okayButton.addEventListener("click", () => {
            closeModal(this);
          });
        }

        this.append(template);
      }
    },
  );

  return modalName;
}

export async function show(name) {
  const customModal = document.createElement(name);
  await showModal({ content: customModal });
}
