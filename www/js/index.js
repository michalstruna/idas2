function toDbString(parameter) {
    if (parameter == null) {
        return '';
    } else {
        return parameter.toString();
    }
}

function removeRow() {
    let td = event.target.parentNode;
    let tr = td.parentNode;
    tr.parentNode.removeChild(tr);
}

function importAll() {
    const form = document.getElementsByTagName('form')[0];
    form.submit();
}