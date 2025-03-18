import {Tooltip} from 'bootstrap';

document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(tooltipTriggerEl =>
    new Tooltip(tooltipTriggerEl)
)
