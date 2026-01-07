function getNumeroColonne() {
    return parseInt(sessionStorage.getItem("NumCol")) || 5;
}

function calcolaOffsetJS() {
    const colonne = getNumeroColonne();
    return paginaCorrente * colonne;
}

function applicaOffset(tabellaId) {
    const table = document.getElementById(tabellaId);
    const cells = table.rows[0].cells;
    const offset = calcolaOffsetJS();
    const visibili = getNumeroColonne();

    for (let i = 0; i < cells.length; i++) {
        cells[i].style.display =
            (i >= offset && i < offset + visibili) ? "table-cell" : "none";
    }
}
window.addEventListener("resize", () => applicaOffset("GameTable0"));
window.addEventListener("load", () => applicaOffset("GameTable0"));
