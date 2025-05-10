import { Controller } from '@hotwired/stimulus'
import { DataTable } from 'simple-datatables'

export default class extends Controller {
  static targets = ['table']

  connect () {
    this.initializeDataTable()
  }

  initializeDataTable () {
    const table = new DataTable(this.tableTarget, {
      perPage: 10,
      perPageSelect: [10, 20, 30, 50],
      sortable: true,
      searchable: true,
      fixedHeight: true,
      // Ajout de l'option pour personnaliser la pagination
      labels: {
        placeholder: 'Chercher...',
        perPage: 'Lignes par page',
        noRows: 'Pas de résultats trouvés',
        info: '{start} à {end} sur {rows}',
        noResults: 'Aucun résultat trouvé'
      }
    })
  }
}
