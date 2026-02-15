import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        statsUrl: String
    }

    static targets = [ "stats" ]

    connect() {
        this.loadStats();
    }

    async loadStats() {
        const response = await fetch(this.statsUrlValue, {
          headers: { 'Accept': 'application/json' }
        });

        if (response.status === 200) {
          const data = await response.json();
          this.statsTarget.textContent = JSON.stringify(data);
        } else if (response.status === 304) {
          console.log('Cache Varnish / navigateur valide');
        }
      }
}
