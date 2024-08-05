import { LitElement, html } from '/public/assets/js/lit-core.js'

customElements.define(
  'my-component',
  class extends LitElement {
    static get properties() {
      return {
        name: { type: String },
      }
    }

    clicked() {
      const foo = Math.random().toString(36).substring(2, 10);
      this.dispatchEvent(new CustomEvent('foo', { detail: { lastName: this.name, newName: foo}}))

      this.name = foo
    }

    render() {
      return html`
<h1>Component ${this.name}</h1>
<ix-button @click="${this.clicked}">foo</ix-button>
`
    }
  },
)
