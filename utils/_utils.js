window.makeToaster = window.makeToaster || function (headerText, success) {
    const existingToasts = Array.from(document.querySelectorAll('.toast'));
    const visibleToasts = existingToasts.filter(toast => toast.classList.contains('show'));
    const toast = document.createElement('div');
    toast.classList.add('toast', 'fade', 'show');
    toast.setAttribute('role', 'alert');
    toast.style.position = 'fixed';
    toast.style.right = '10px';
    headerText = headerText.replace(/\n/g, '<br>');
    toast.innerHTML = `
        <div class="toast-header ${success ? "grün" : "rot"}">
            <strong class="mr-auto">${headerText}</strong>
        </div>`;
    document.body.appendChild(toast);

    const topPosition = 20 + visibleToasts.reduce((acc, t) => acc + t.offsetHeight + 10, 0);
    toast.style.top = `${topPosition}px`;

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
            updateToastPositions();
        }, 50);
    }, 10000);
};

window.updateToastPositions = window.updateToastPositions || function () {
    const visibleToasts = Array.from(document.querySelectorAll('.toast.show'));
    let topPosition = 10;
    visibleToasts.forEach(toast => {
        toast.style.top = `${topPosition}px`;
        topPosition += toast.offsetHeight + 10;
    });
};



window.relocateControls = window.relocateControls || function (api, targetSel) {
    $(targetSel).empty();
    api.buttons().container().appendTo(targetSel);
    const $c = $(api.table().container());
    $c.find('.dt-search label').remove();
    $c.find('.dt-search input')
        .removeClass('form-control-sm').addClass('form-control me-1 ms-1')
        .attr('placeholder', 'Suche…');
    $c.find('.dt-search').appendTo(targetSel);

}



if (!window.tooltipList) {
    let tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        .filter(el => el.nodeType === Node.ELEMENT_NODE && el.nodeName && typeof el.nodeName === 'string');
    window.tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            delay: {"show": 10, "hide": 0}
        });
    });
}

window.normalizeCosts = window.normalizeCosts || function (input) {
    let ep = input?.textContent || input?.val?.() || String(input || '');
    if (!ep) return '0';
    ep = ep.toString().toLowerCase();
    if (ep.endsWith('k')) {
        ep = ep.slice(0, -1) + '000';
    }
    return parseFloat(ep.replace(/,/g, '.').replace(/[^0-9.]/g, '')) || 0;
};

