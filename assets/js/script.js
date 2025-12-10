// Funções utilitárias
class MRPProjectManager {
    constructor() {
        this.init();
    }

    init() {
        this.initDatePickers();
        this.initTooltips();
        this.initFilters();
        this.calculateProjectMetrics();
    }

    initDatePickers() {
        // Inicializar datepickers se existirem
        if (document.querySelector('.datepicker')) {
            flatpickr(".datepicker", {
                dateFormat: "Y-m-d",
                locale: "pt"
            });
        }
    }

    initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    initFilters() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const filterType = e.target.dataset.filter;
                this.filterProjects(filterType);
            });
        });
    }

    filterProjects(filterType) {
        const projects = document.querySelectorAll('.project-card');
        projects.forEach(project => {
            if (filterType === 'all') {
                project.style.display = 'block';
            } else {
                const projectType = project.dataset.type;
                if (projectType === filterType) {
                    project.style.display = 'block';
                } else {
                    project.style.display = 'none';
                }
            }
        });
    }

    calculateProjectMetrics() {
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const progress = bar.dataset.progress;
            bar.style.width = `${progress}%`;

            // Mudar cor baseada no progresso
            if (progress < 30) {
                bar.classList.add('bg-danger');
            } else if (progress < 70) {
                bar.classList.add('bg-warning');
            } else {
                bar.classList.add('bg-success');
            }
        });
    }

    // Calcular diferença entre datas
    calculateDateDifference(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays;
    }

    // Formatar moeda
    formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    }

    // Validar formulário
    validateProjectForm(formData) {
        const errors = [];

        if (!formData.project_name || formData.project_name.trim().length < 3) {
            errors.push('Nome do projeto deve ter pelo menos 3 caracteres');
        }

        if (!formData.start_date) {
            errors.push('Data de início é obrigatória');
        }

        if (!formData.end_date) {
            errors.push('Data de término é obrigatória');
        }

        if (formData.start_date && formData.end_date) {
            const start = new Date(formData.start_date);
            const end = new Date(formData.end_date);

            if (end < start) {
                errors.push('Data de término não pode ser anterior à data de início');
            }
        }

        if (!formData.total_budget || formData.total_budget <= 0) {
            errors.push('Orçamento deve ser maior que zero');
        }

        return errors;
    }
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    const mrpManager = new MRPProjectManager();

    // Configurar filtros
    window.filterByStatus = function (status) {
        document.querySelectorAll('.project-card').forEach(card => {
            if (status === 'all' || card.dataset.status === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    };

    // Ordenar projetos
    window.sortProjects = function (criteria) {
        const container = document.querySelector('.projects-container');
        const projects = Array.from(container.querySelectorAll('.project-card'));

        projects.sort((a, b) => {
            switch (criteria) {
                case 'date':
                    return new Date(b.dataset.startDate) - new Date(a.dataset.startDate);
                case 'budget':
                    return parseFloat(b.dataset.budget) - parseFloat(a.dataset.budget);
                case 'name':
                    return a.dataset.name.localeCompare(b.dataset.name);
                default:
                    return 0;
            }
        });

        // Reordenar no container
        projects.forEach(project => {
            container.appendChild(project);
        });
    };

    // Busca em tempo real
    const searchInput = document.querySelector('#projectSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.project-card').forEach(card => {
                const name = card.dataset.name.toLowerCase();
                const description = card.dataset.description.toLowerCase();

                if (name.includes(searchTerm) || description.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});