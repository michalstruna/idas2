let teachingForms = [];

const getTeachingForms = async () => {
    const request = await fetch('/idas2/www/teaching-form/json');
    teachingForms = await request.json();
};
getTeachingForms();

let completionTypes = [];

const getCompletionTypes = async () => {
    const request = await fetch('/idas2/www/completion-type/json');
    completionTypes = await request.json();
};
getCompletionTypes();

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
            let json = data[0].predmetKatedryFullInfo;
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
            Import předmětů
        </h1>
        <span class="button button--flex button--large button--inner clickable" onclick="importAll()">Importovat vše</span>
    </header>`;
            html += '<form method="post"><table class="table">';
            html += `<tr class="table__row table__row-header">
<th class="table__cell table__cell--header">Zkratka</th>
<th class="table__cell table__cell--header">Název</th>
<th class="table__cell table__cell--header">Forma výuky</th>
<th class="table__cell table__cell--header">Způsob zakončení</th>
<td class="table__cell table__cell--header">Odstranit</td>
</tr>`;
            for (let i = 0; i < json.length; i++) {
                const subject = json[i];
                html += '<tr class="table__row">';

                html += '<td class="table__cell"> <input type="text" name="shortName[]" value="' + toDbString(subject[shortName]) + '"></td>';
                html += '<td class="table__cell"> <input type="text" name="name[]" value="' + toDbString(subject[name]) + '"></td>';

                html += '<td class="table__cell"> <select name="teachingForm[]"> ';
                for (let j = 0; j < teachingForms.length; j++) {
                    const teachingForm = teachingForms[j];
                    html += '<option value="' + teachingForm.id + '">' + teachingForm.nazev + '</option>';
                }
                html += '</select></td>';

                html += '<td class="table__cell"> <select name="completionType[]"> ';
                for (let j = 0; j < completionTypes.length; j++) {
                    const completionType = completionTypes[j];
                    html += '<option value="' + completionType.id + '">' + completionType.nazev + '</option>';
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