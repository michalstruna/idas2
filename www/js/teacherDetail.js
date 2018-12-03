document.getElementsByName('image')[0].onchange = function() {
    const file = document.getElementsByName('image')[0].files[0];
    if (file === undefined) {
        return;
    }
    if (file.type !== 'image/png' && file.type !== 'image/jpeg' && file.type !== 'image/gif') {
        alert('Vybraný soubor musí být obrázek!');
        return;
    }

    const reader = new FileReader();
    reader.onload = (function(aImg) { return function(e) {
        aImg.src = e.target.result;
    }; })(document.getElementById('teacher--image'));
    reader.readAsDataURL(file);
};