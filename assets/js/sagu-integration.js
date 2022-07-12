$(() => {
    $('[export-students-btn]').on('click', function() {
        const remodalInstance = $('[data-remodal-id=modal-exported-students]').remodal()
        const options = { icon: 'info', text: 'Aguarde! Os dados estão sendo exportados.'}

        remodalInstance.open()
        $('[selected-students-table]').remove()
        $('[selected-students-table-wrapper] img').removeClass()

        showSweetAlert(options)

        $.get(`/sagu-integration/selectedStudentData/${this.dataset.opportunityId}`, students => {
            const options = { icon: 'success', text: 'Dados exportados com sucesso'}

            $('[selected-students-table-wrapper] img').addClass('d-none')
            $('[selected-students-table-wrapper]').append(renderSelectedStudentsTable(students))

            showSweetAlert(options)
        })
    })
})

const renderSelectedStudentsTable = students => {
    return `
        <table class="table table-bordered" selected-students-table>
            <thead>
                <tr>
                    <th>Inscrição</th>
                    <th>Nome</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                ${students.map(student => {
                    return `
                        <tr>
                            <td>${student.registration_number}</td>
                            <td>${student.data.nome}</td>
                            <td>${handleExportedStudentStatus(student.status)}</td>
                        </tr>
                    `
                }).join('')}
            </tbody>
        </table>
    `
}

const handleExportedStudentStatus = status => {
    switch(status) {
        case 400:
            return '<span class="badge-pill badge-info">Já possui cadastro no Sagu</span>'
        case 500:
            return '<span class="badge-pill badge-danger">Não foi possível exportar pessoa</span>'
        default:
            return '<span class="badge-pill badge-success">Exportado com sucesso</span>'
    }
}

const showSweetAlert = options => {
    Swal.fire({
        position: 'top-end',
        toast: true,
        icon: options.icon,
        text: options.text,
        showConfirmButton: false,
        timer: 4000,
        customClass: {
            container: 'student-export-alert'
        }
    })
}
