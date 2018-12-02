let departments = [];

const getDepartments = async () => {
    const request = await fetch('/idas2/www/department/json');
    departments = await request.json();
};
getDepartments();

let form = document.getElementsByTagName('form')[0];
form.addEventListener("submit", function (e) {
    e.preventDefault();
    const file = document.getElementsByName('file')[0].files[0];
    if (file === undefined) {
        return;
    }

    if (file.type !== 'application/json') {
        alert('Vybraný soubor musí být JSON!');
        return ''
    }

    const reader = new FileReader();
    reader.onload = (function () {
        return function (e) {
            const data = JSON.parse(e.target.result);
            let json = data[0].ucitel;
            if (json === undefined) {
                if (Array.isArray(data)) {
                    json = data;
                } else {
                    alert('Nepovedlo se přečíst zdrojový soubor. Jste si jistí, že má správný formát?');
                    return;
                }
            }

            const name = document.getElementsByName('name')[0].value;
            const surname = document.getElementsByName('surname')[0].value;
            const titlePrefix = document.getElementsByName('titlePrefix')[0].value;
            const titlePostfix = document.getElementsByName('titlePostfix')[0].value;
            const telephone = document.getElementsByName('telephone')[0].value;
            const mobile = document.getElementsByName('mobile')[0].value;
            const email = document.getElementsByName('email')[0].value;

            const content = document.getElementsByClassName('content')[0];

            let html = `<header class="item__list__header">
        <h1 class="item__list__title">
            Import učitelů
        </h1>
        <span class="button button--flex button--large button--inner clickable" onclick="importAll()">Importovat vše</span>
    </header>`;
            html += '<form method="post"><table class="table">';
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
                const teacher = json[i];
                html += '<tr class="table__row">';

                html += '<td class="table__cell"> <input type="text" name="name[]" value="' + toDbString(teacher[name]) + '"></td>';
                html += '<td class="table__cell"> <input type="text" name="surname[]" value="' + toDbString(teacher[surname]) + '"></td>';
                html += '<td class="table__cell"> <input type="text" name="titlePrefix[]" value="' + toDbString(teacher[titlePrefix]) + '"></td>';
                html += '<td class="table__cell"> <input type="text" name="titlePostfix[]" value="' + toDbString(teacher[titlePostfix]) + '"></td>';
                html += '<td class="table__cell"> <input type="text" name="telephone[]" value="' + toDbString(teacher[telephone]) + '"></td>';
                html += '<td class="table__cell"> <input type="text" name="mobile[]" value="' + toDbString(teacher[mobile]) + '"></td>';
                html += '<td class="table__cell"> <input type="text" name="email[]" value="' + toDbString(teacher[email]) + '"></td>';

                html += '<td class="table__cell"> <select name="department[]"> ';
                for (let j = 0; j < departments.length; j++) {
                    const department = departments[j];
                    html += '<option value="' + department.id + '">' + department.nazev + '</option>';
                }
                html += '</select></td>';

                html += '<td class="table__cell"> ' + '<button onclick="removeRow()">Zahodit záznam</button>' + '</td>';

                html += '</tr>';
            }
            html += '</table></form>';
            content.innerHTML = html;
        };
    })();
    reader.readAsText(file);

}, true);