$(() => {
    handleSaguFormImportBtn()

    $('#import-btn').on('click', () => {
        $.post('/sagu-integration/importForm', { opportunity_id: MapasCulturais.entity.id }, () => {
            Swal.fire({
                position: 'top-end',
                toast: true,
                icon: 'success',
                text: 'Formulário importado com sucesso',
                showConfirmButton: false,
                timer: 1500,
                customClass: {
                    container: 'imported-form-alert'
                }
            })

            setTimeout(() => {
                document.location.reload(true)
            }, 1500)
        })
    })
})

const handleSaguFormImportBtn = () => {
    const formImportFields = $('#registration-attachments').find('form[name="impotFields"]')

    formImportFields.append(renderSaguFormImportBtn())
}

const renderSaguFormImportBtn = () => {
    return `
        <button type="button" class="btn btn-import-form-sagu" id="import-btn">
            Importar formulário do Sagu
        </button>
    `
}
