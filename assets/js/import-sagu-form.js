$(() => {
    const formImportFields = $('#registration-attachments').find('form[name="impotFields"]')
    // Pega o id da oportunidade presente na url
    const opportunityId = formImportFields.context.referrer.replace(/\D/g, '')

    formImportFields.append(renderSaguFormImportBtn())

    $('#import-btn').on('click', () => {
        $.post('/sagu-integration/importForm', { opportunity_id: opportunityId }, () => {
            Swal.fire({
                position: 'top-end',
                toast: true,
                icon: 'success',
                text: 'Formulário importado com sucesso',
                showConfirmButton: false,
                timer: 2000,
                customClass: {
                    container: 'imported-form-alert'
                }
            })

            setTimeout(() => {
                document.location.reload(true)
            }, 2000)
        })
    })
})

const renderSaguFormImportBtn = () => {
    return `
        <button type="button" class="btn btn-import-form-sagu" id="import-btn">
            Importar formulário do Sagu
        </button>
    `
}
