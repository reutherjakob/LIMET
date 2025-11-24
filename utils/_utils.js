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

window.move_item = window.move_item || function (item2move_id, where2move_id) {
    let item = document.getElementById(item2move_id);
    if (item) {
        item.parentNode.removeChild(item);
        document.getElementById(where2move_id).appendChild(item);
    }
};

window.getExcelFilename = window.getExcelFilename || async function (documentName, options = {}) {
    const formData = new URLSearchParams({ documentName, ...options });
    const response = await fetch('/utils/get_excel_filename.php', {
        method: 'POST',
        body: formData
    });
    if (!response.ok) throw new Error('Network response was not ok');
    const data = await response.json();
    return data.filename;
};

// Tooltips only once
if (!window.tooltipList) {
    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    window.tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            delay: { "show": 10, "hide": 0 }
        });
    });
}
