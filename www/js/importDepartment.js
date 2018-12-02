let faculties = [];

const getFaculties = async () => {
    const request = await fetch('/idas2/www/faculty/json');
    faculties = await request.json();
};
getFaculties();

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
            let json = data[0].pracoviste;
            if (json === undefined) {
                if (Array.isArray(data)) {
                    json = data;
                } else {
                    alert('Nepovedlo se přečíst zdrojový soubor. Jste si jistí, že má správný formát?');
                    return;
                }
            }

            const shortName = document.getElementsByName('shortName')[0].value;
            const name = document.getElementsByName('name')[0].value;

            const content = document.getElementsByClassName('content')[0];

            let html = `<header class="item__list__header">
        <h1 class="item__list__title">
            Import kateder
        </h1>
        <span class="button button--flex button--large button--inner clickable" onclick="importAll()">Importovat vše</span>
    </header>`;
            html += '<form method="post"><table class="table">';
            html += `<tr class="table__row table__row-header">
<th class="table__cell table__cell--header">Zkratka</th>
<th class="table__cell table__cell--header">Název</th>
<th class="table__cell table__cell--header">Fakulta</th>
<td class="table__cell table__cell--header">Odstranit</td>
</tr>`;
            for (let i = 0; i < json.length; i++) {
                const department = json[i];
                html += '<tr class="table__row">';

                html += '<td class="table__cell"> <input type="text" name="shortName[]" value="' + toDbString(department[shortName]) + '"></td>';
                html += '<td class="table__cell"> <input type="text" name="name[]" value="' + toDbString(department[name]) + '"></td>';

                html += '<td class="table__cell"> <select name="faculty[]"> ';
                for (let j = 0; j < faculties.length; j++) {
                    const faculty = faculties[j];
                    html += '<option value="' + faculty.id + '">' + faculty.nazev + '</option>';
                }
                html += '</select></td>';

                html += '<td class="table__cell"> ' + '<span class="button underlined clickable" onclick="removeRow()">Zahodit záznam</span>' + '</td>';

                html += '</tr>';
            }
            html += '</table></form>';
            content.innerHTML = html;
        };
    })();
    reader.readAsText(file);

}, true);