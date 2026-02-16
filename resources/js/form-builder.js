class FormBuilder {
    constructor() {
        this.fields = [];
        this.selectedIndex = null;
        this.canvas = document.getElementById('form-canvas');
        this.toolbox = document.getElementById('field-toolbox');
        this.properties = document.getElementById('field-properties');
        this.structureInput = document.getElementById('structure-input');
    }

    init() {
        // Set up drag & drop from toolbox items
        document.querySelectorAll('.field-type').forEach(item => {
            item.setAttribute('draggable', 'true');
            item.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', item.dataset.type);
                e.dataTransfer.effectAllowed = 'copy';
            });
        });

        // Canvas drop zone
        this.canvas.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
            this.canvas.classList.add('drag-over');
        });

        this.canvas.addEventListener('dragleave', () => {
            this.canvas.classList.remove('drag-over');
        });

        this.canvas.addEventListener('drop', (e) => {
            e.preventDefault();
            this.canvas.classList.remove('drag-over');
            const type = e.dataTransfer.getData('text/plain');
            if (type) this.addField(type);
        });

        // Load existing structure if editing
        const existing = document.getElementById('existing-structure');
        if (existing && existing.value) {
            try {
                const data = JSON.parse(existing.value);
                if (data.fields) {
                    this.fields = data.fields;
                    this.renderCanvas();
                }
            } catch(e) { console.error('Failed to load structure:', e); }
        }
    }

    addField(type) {
        const labels = {
            text: 'Texte court', textarea: 'Texte long', email: 'Email',
            number: 'Numéro', date: 'Date', radio: 'Choix unique',
            checkbox: 'Choix multiple', select: 'Liste déroulante',
            file: 'Upload fichier', rating: 'Échelle de notation'
        };

        const field = {
            type: type,
            label: labels[type] || type,
            placeholder: '',
            required: false,
            options: ['radio', 'checkbox', 'select'].includes(type) ? ['Option 1', 'Option 2'] : [],
            order: this.fields.length
        };

        this.fields.push(field);
        this.selectedIndex = this.fields.length - 1;
        this.renderCanvas();
        this.renderProperties();
        this.updateStructure();
    }

    removeField(index) {
        this.fields.splice(index, 1);
        this.fields.forEach((f, i) => f.order = i);
        if (this.selectedIndex === index) {
            this.selectedIndex = null;
            this.properties.innerHTML = '<p class="text-muted text-center">Sélectionnez un champ</p>';
        } else if (this.selectedIndex > index) {
            this.selectedIndex--;
        }
        this.renderCanvas();
        this.updateStructure();
    }

    selectField(index) {
        this.selectedIndex = index;
        this.renderCanvas();
        this.renderProperties();
    }

    moveField(from, to) {
        if (to < 0 || to >= this.fields.length) return;
        const [field] = this.fields.splice(from, 1);
        this.fields.splice(to, 0, field);
        this.fields.forEach((f, i) => f.order = i);
        this.selectedIndex = to;
        this.renderCanvas();
        this.updateStructure();
    }

    renderCanvas() {
        if (this.fields.length === 0) {
            this.canvas.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-plus-circle fs-1 d-block mb-3"></i><p>Glissez les champs ici pour construire votre formulaire</p></div>';
            return;
        }

        const typeIcons = {
            text: 'bi-input-cursor', textarea: 'bi-textarea-t', email: 'bi-envelope',
            number: 'bi-123', date: 'bi-calendar', radio: 'bi-record-circle',
            checkbox: 'bi-check-square', select: 'bi-list', file: 'bi-upload', rating: 'bi-star'
        };

        this.canvas.innerHTML = this.fields.map((field, index) => `
            <div class="card mb-2 field-card ${this.selectedIndex === index ? 'border-primary shadow-sm' : ''}"
                 style="cursor:pointer" onclick="formBuilder.selectField(${index})" draggable="true"
                 ondragstart="event.dataTransfer.setData('field-index', ${index})"
                 ondragover="event.preventDefault()"
                 ondrop="event.preventDefault(); formBuilder.moveField(parseInt(event.dataTransfer.getData('field-index')), ${index})">
                <div class="card-body py-2 px-3 d-flex align-items-center">
                    <i class="bi bi-grip-vertical text-muted me-2"></i>
                    <i class="bi ${typeIcons[field.type] || 'bi-question'} me-2 text-primary"></i>
                    <span class="flex-grow-1">${this.escapeHtml(field.label)}</span>
                    ${field.required ? '<span class="text-danger me-2">*</span>' : ''}
                    <span class="badge bg-secondary me-2">${field.type}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); formBuilder.removeField(${index})">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    renderProperties() {
        if (this.selectedIndex === null || !this.fields[this.selectedIndex]) {
            this.properties.innerHTML = '<p class="text-muted text-center">Sélectionnez un champ</p>';
            return;
        }

        const field = this.fields[this.selectedIndex];
        const idx = this.selectedIndex;
        const hasOptions = ['radio', 'checkbox', 'select'].includes(field.type);

        let html = `
            <h6 class="mb-3">Propriétés du champ</h6>
            <div class="mb-3">
                <label class="form-label">Libellé</label>
                <input type="text" class="form-control form-control-sm" value="${this.escapeHtml(field.label)}"
                       onchange="formBuilder.updateField(${idx}, 'label', this.value)">
            </div>
            <div class="mb-3">
                <label class="form-label">Texte indicatif</label>
                <input type="text" class="form-control form-control-sm" value="${this.escapeHtml(field.placeholder || '')}"
                       onchange="formBuilder.updateField(${idx}, 'placeholder', this.value)">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="req-${idx}" ${field.required ? 'checked' : ''}
                       onchange="formBuilder.updateField(${idx}, 'required', this.checked)">
                <label class="form-check-label" for="req-${idx}">Obligatoire</label>
            </div>`;

        if (hasOptions) {
            html += `<div class="mb-3"><label class="form-label">Options</label><div id="options-list">`;
            (field.options || []).forEach((opt, oi) => {
                html += `<div class="input-group input-group-sm mb-1">
                    <input type="text" class="form-control" value="${this.escapeHtml(opt)}"
                           onchange="formBuilder.updateOption(${idx}, ${oi}, this.value)">
                    <button type="button" class="btn btn-outline-danger" onclick="formBuilder.removeOption(${idx}, ${oi})">
                        <i class="bi bi-x"></i>
                    </button>
                </div>`;
            });
            html += `</div><button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="formBuilder.addOption(${idx})">
                <i class="bi bi-plus"></i> Ajouter une option</button></div>`;
        }

        html += `<div class="mt-3 d-flex gap-1">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formBuilder.moveField(${idx}, ${idx - 1})" ${idx === 0 ? 'disabled' : ''}>
                <i class="bi bi-arrow-up"></i></button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formBuilder.moveField(${idx}, ${idx + 1})" ${idx === this.fields.length - 1 ? 'disabled' : ''}>
                <i class="bi bi-arrow-down"></i></button>
        </div>`;

        this.properties.innerHTML = html;
    }

    updateField(index, property, value) {
        this.fields[index][property] = value;
        this.renderCanvas();
        this.updateStructure();
    }

    addOption(fieldIndex) {
        if (!this.fields[fieldIndex].options) this.fields[fieldIndex].options = [];
        this.fields[fieldIndex].options.push('Nouvelle option');
        this.renderProperties();
        this.updateStructure();
    }

    removeOption(fieldIndex, optionIndex) {
        this.fields[fieldIndex].options.splice(optionIndex, 1);
        this.renderProperties();
        this.updateStructure();
    }

    updateOption(fieldIndex, optionIndex, value) {
        this.fields[fieldIndex].options[optionIndex] = value;
        this.updateStructure();
    }

    updateStructure() {
        const structure = JSON.stringify({ fields: this.fields });
        if (this.structureInput) this.structureInput.value = structure;
    }

    getPreviewHtml() {
        return this.fields.map(field => {
            let input = '';
            const req = field.required ? ' required' : '';
            const reqStar = field.required ? '<span class="text-danger">*</span>' : '';
            switch (field.type) {
                case 'text': input = `<input type="text" class="form-control" placeholder="${this.escapeHtml(field.placeholder || '')}"${req}>`; break;
                case 'textarea': input = `<textarea class="form-control" rows="3" placeholder="${this.escapeHtml(field.placeholder || '')}"${req}></textarea>`; break;
                case 'email': input = `<input type="email" class="form-control" placeholder="${this.escapeHtml(field.placeholder || '')}"${req}>`; break;
                case 'number': input = `<input type="number" class="form-control"${req}>`; break;
                case 'date': input = `<input type="date" class="form-control"${req}>`; break;
                case 'file': input = `<input type="file" class="form-control"${req}>`; break;
                case 'radio': input = (field.options || []).map((o, i) => `<div class="form-check"><input class="form-check-input" type="radio" name="preview_${field.order}" id="pr_${field.order}_${i}"><label class="form-check-label" for="pr_${field.order}_${i}">${this.escapeHtml(o)}</label></div>`).join(''); break;
                case 'checkbox': input = (field.options || []).map((o, i) => `<div class="form-check"><input class="form-check-input" type="checkbox" id="pc_${field.order}_${i}"><label class="form-check-label" for="pc_${field.order}_${i}">${this.escapeHtml(o)}</label></div>`).join(''); break;
                case 'select': input = `<select class="form-select"${req}><option value="">-- Choisir --</option>${(field.options || []).map(o => `<option>${this.escapeHtml(o)}</option>`).join('')}</select>`; break;
                case 'rating': input = '<div class="rating-preview">' + [1,2,3,4,5].map(i => `<i class="bi bi-star fs-4 text-warning" style="cursor:pointer"></i>`).join(' ') + '</div>'; break;
            }
            return `<div class="mb-3"><label class="form-label fw-semibold">${this.escapeHtml(field.label)} ${reqStar}</label>${input}</div>`;
        }).join('');
    }

    showPreview() {
        let modal = document.getElementById('previewModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'previewModal'; modal.className = 'modal fade'; modal.tabIndex = -1;
            modal.innerHTML = `<div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Aperçu du formulaire</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="previewBody"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button></div></div></div>`;
            document.body.appendChild(modal);
        }
        document.getElementById('previewBody').innerHTML = this.getPreviewHtml() || '<p class="text-muted">Aucun champ ajouté.</p>';
        new bootstrap.Modal(modal).show();
    }

    escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
}

let formBuilder;
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('form-canvas')) {
        formBuilder = new FormBuilder();
        formBuilder.init();
    }
});
