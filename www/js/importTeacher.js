let form = document.getElementsByTagName('form')[0];
form.addEventListener("submit", function (e) {
    e.preventDefault();
    let file = document.getElementsByName('file')[0].files[0];
    if (file === undefined) {
        return;
    }

    if (file.type !== 'application/json') {
        alert('Vybraný soubor musí být JSON!');
        return ''
    }

    let reader = new FileReader();
    reader.onload = (function () {
        return function (e) {
            let json = JSON.parse(e.target.result)[0].ucitel;
            if (json === undefined) {
                alert('Nepovedlo se přečíst zdrojový soubor. Jste si jistí, že má správný formát?');
                return;
            }

            let name = document.getElementsByName('name')[0].value;
            let surname = document.getElementsByName('surname')[0].value;
            let titlePrefix = document.getElementsByName('titlePrefix')[0].value;
            let titlePostfix = document.getElementsByName('titlePostfix')[0].value;
            let telephone = document.getElementsByName('telephone')[0].value;
            let mobile = document.getElementsByName('mobile')[0].value;
            let email = document.getElementsByName('email')[0].value;

            let content = document.getElementsByClassName('content')[0];

            let html = `<header class="item__list__header">
        <h1 class="item__list__title">
            Import učitelů
        </h1>
    </header>`;
            html += '<table class="table">';
            html += `<tr class="table__row table__row-header">
<th class="table__cell table__cell--header">Jméno</th>
<th class="table__cell table__cell--header">Příjmení</th>
<th class="table__cell table__cell--header">Titul před</th>
<th class="table__cell table__cell--header">Titul za</th>
<th class="table__cell table__cell--header">Telefon</th>
<th class="table__cell table__cell--header">Mobil</th>
<th class="table__cell table__cell--header">Kontaktní e-mail</th>
<th class="table__cell table__cell--header">Katedra</th>
<td class="table__cell table__cell--header">Odstranit</td>
</tr>`;
            for (let i = 0; i < json.length; i++) {
                let teacher = json[i];
                html += '<tr class="table__row">';

                html += '<td class="table__cell"> ' + toDbString(teacher[name]) + '</td>';
                html += '<td class="table__cell"> ' + toDbString(teacher[surname]) + '</td>';
                html += '<td class="table__cell"> ' + toDbString(teacher[titlePrefix]) + '</td>';
                html += '<td class="table__cell"> ' + toDbString(teacher[titlePostfix]) + '</td>';
                html += '<td class="table__cell"> ' + toDbString(teacher[telephone]) + '</td>';
                html += '<td class="table__cell"> ' + toDbString(teacher[mobile]) + '</td>';
                html += '<td class="table__cell"> ' + toDbString(teacher[email]) + '</td>';
                html += '<td class="table__cell"> ' + 'cxy' + '</td>';
                html += '<td class="table__cell"> ' + '<button onclick="removeRow()">X</button>' + '</td>';

                html += '</tr>';
            }
            html += '</table>';
            content.innerHTML = html;
        };
    })();
    reader.readAsText(file);

}, true);