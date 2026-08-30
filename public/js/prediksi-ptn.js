document.addEventListener('DOMContentLoaded', function () {
    const ptnSelect = document.getElementById('ptn_id');
    const prodiSelect = document.getElementById('prodi_id');

    if (!ptnSelect || !prodiSelect) {
        return;
    }

    const data = window.ptnProdiData || {};
    const selectedValue = prodiSelect.dataset.selected || '';

    function renderProdiOptions() {
        const ptnId = ptnSelect.value;
        const options = data[ptnId] || [];

        prodiSelect.innerHTML = '<option value="">Pilih program studi</option>';

        options.forEach(function (item) {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.prodi;
            if (String(selectedValue) === String(item.id)) {
                option.selected = true;
            }
            prodiSelect.appendChild(option);
        });
    }

    ptnSelect.addEventListener('change', renderProdiOptions);
    renderProdiOptions();
});
