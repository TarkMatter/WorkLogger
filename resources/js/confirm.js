// Global confirm handler for forms.
// Usage: <form data-confirm="..."> ... </form>
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;

    const msg = form.dataset.confirm;
    if (!msg) return;

    if (!window.confirm(msg)) {
        e.preventDefault();
        e.stopPropagation();
    }
});
