function makeToaster(headerText, success) {
    const existingToasts = Array.from(document.querySelectorAll('.toast'));
    const visibleToasts = existingToasts.filter(toast => toast.classList.contains('show'));
    const toast = document.createElement('div');
    toast.classList.add('toast', 'fade', 'show');
    toast.setAttribute('role', 'alert');
    toast.style.position = 'fixed';
    toast.style.right = '10px';
    headerText = headerText.replace(/\n/g, '<br>'); // Replace \n with <br>
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
}

function updateToastPositions() {
    const visibleToasts = Array.from(document.querySelectorAll('.toast.show'));
    let topPosition = 10;
    visibleToasts.forEach(toast => {
        toast.style.top = `${topPosition}px`;
        topPosition += toast.offsetHeight + 10;
    });
}

function move_item(item2move_id, where2move_id) {
    let item = document.getElementById(item2move_id);

    if (item) {
        item.parentNode.removeChild(item);
        document.getElementById(where2move_id).appendChild(item);
    }
}

function move_item_by_class(sourceClass, targetId) {
    let sourceElements = document.getElementsByClassName(sourceClass);
    let targetElement = document.getElementById(targetId);
    Array.from(sourceElements).forEach(function (element) {
        targetElement.appendChild(element);
    });
}

function show_modal(modal_id){
    $('#'+ modal_id).modal('show');
}

function format_data_input(newData, dataIdentifier) {
    switch (getCase(dataIdentifier)) {
        case "num":
            newData = formatNum(newData);
    }
    return newData;
}

function formatNum(newData) {
    newData = newData.replace(/[^0-9,.-]/g, ''); // Remove non-numeric characters (except for '.' and '-')
    newData = newData.replace(/,/g, '.'); // Replace ',' with '.'
    return newData;
}


// function letterToNumber(letter) {
//     if (typeof letter !== 'string' || letter.length !== 1 || !/[a-zA-Z]/.test(letter)) {
//         return null; // or throw an error if you prefer
//     }
//     let upper = letter.toUpperCase();
//     return upper.charCodeAt(0) - 'A'.charCodeAt(0) + 1;
// }
