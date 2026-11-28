document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-table-search]').forEach((input) => {
        const table = document.querySelector(input.dataset.tableSearch || '');
        if (!table) return;
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        input.addEventListener('input', () => {
            const term = input.value.trim().toLocaleLowerCase('fr');
            rows.forEach((row) => {
                row.hidden = !row.textContent.toLocaleLowerCase('fr').includes(term);
            });
        });
    });
});
