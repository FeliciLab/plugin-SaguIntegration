const enrollStudent = {
    openEnrollmentModal: false,
    remodalInstance: null,
    restartSelectClass() {
        $('#select-class').html('<option selected disabled>Selecione</option>')
    },
    renderStudentEnrollmentOpts(options) {
        return options.map(option => `<option value="${option.id}">${option.descricao}</option>`).join()
    }
}

$(() => {
    $('[export-students-btn]').on('click', () => {
        const remodalInstance = $('[data-remodal-id=modal-exported-students]').remodal()
        const options = { icon: 'info', text: 'Aguarde! Os dados estão sendo exportados' }

        remodalInstance.open()
        $('[selected-students-table]').remove()
        $('[selected-students-table-wrapper] img').removeClass()

        showSweetAlert(options)

        $.get(`/sagu-integration/selectedStudentData/${MapasCulturais.entity.id}`, students => {
            const options = { icon: 'success', text: 'Dados exportados com sucesso' }

            $('[selected-students-table-wrapper] img').addClass('d-none')
            $('[selected-students-table-wrapper]').append(renderSelectedStudentsTable(students))

            showSweetAlert(options)
        })
    })

    $('[enroll-students-btn]').on('click', () => {
        enrollStudent.openEnrollmentModal = true
        enrollStudent.remodalInstance = $('[data-remodal-id=modal-enroll-students]').remodal()

        enrollStudent.remodalInstance.open()
        $('#select-course #select-course-opt').nextAll().remove()

        $.get(`/student-enrollment/coursesOffered/${MapasCulturais.entity.id}`, courses => {
            $('#select-course').append(enrollStudent.renderStudentEnrollmentOpts(courses))
        })

        $('#select-course').select2({
            placeholder: "Selecione"
        })
    })

    $('#select-course').on('change', event => {
        const courseId = event.val
        const data = {
            courseId,
            opportunityId: MapasCulturais.entity.id
        }

        enrollStudent.restartSelectClass()

        $.post('/student-enrollment/activeClassesByCourses', data, classes => {
            $('#select-class').append(enrollStudent.renderStudentEnrollmentOpts(classes))
        })
    })

    $(window).on('click', () => {
        const select2DropdownOpen = $('.modal-enroll-students .select-course').hasClass('select2-dropdown-open')

        if (enrollStudent.openEnrollmentModal && select2DropdownOpen) $('#select-course').select2('close')
    })

    $(document).on('closed', '.modal-enroll-students', () => {
        enrollStudent.openEnrollmentModal = false
        enrollStudent.remodalInstance = null

        enrollStudent.restartSelectClass()
    })

    $('#btn-enroll-students-sagu').on('click', () => {
        const isEnrollment = true
        const remodalInstance = $('[data-remodal-id=modal-enrolled-students]').remodal()
        const options = { icon: 'info', text: 'Aguarde! Os dados estão sendo enviados' }
        const data = {
            classId: $('#select-class').val(),
            opportunityId: MapasCulturais.entity.id
        }

        showSweetAlert(options)

        enrollStudent.remodalInstance.close()
        enrollStudent.restartSelectClass()
        $('[selected-students-table]').remove()
        $('.modal-enrolled-students .export-infos-wrapper').addClass('d-none')
        $('[enrolled-students-wrapper] img').removeClass()
        remodalInstance.open()

        $.post('/student-enrollment/enrolledStudents', data, enrolledStudents => {
            const options = { icon: 'success', text: 'Matrículas enviadas com sucesso' }

            showSweetAlert(options)

            $('[enrolled-students-wrapper] img').addClass('d-none')
            $('[enrolled-students-wrapper]').append(renderSelectedStudentsTable(enrolledStudents, isEnrollment))
            $('.modal-enrolled-students .export-infos-wrapper').removeClass('d-none')

            setStatusAmounts(enrolledStudents)
        })
    })
})

const renderSelectedStudentsTable = (students, isEnrollment = false) => {
    return `
        <table class="table table-bordered" selected-students-table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                ${students.map(student => {
                    const status = isEnrollment ? student.registration_status : student.export_status

                    return `
                        <tr>
                            <td>${student.data.nome}</td>
                            <td>${student.data.cpf}</td>
                            <td>${handleExportedStudentStatus(status, isEnrollment)}</td>
                        </tr>
                    `
                }).join('')}
            </tbody>
        </table>
    `
}

const handleExportedStudentStatus = (status, isEnrollment) => {
    switch (status) {
        case 400:
            return `
                <span class="badge-pill badge-info">
                    ${isEnrollment ? 'Aluno já matriculado na turma' : 'Já possui cadastro no Sagu'}
                </span>
            `
        case 500:
            return `
                <span class="badge-pill badge-danger">
                    ${isEnrollment ? 'Não foi possível matricular o aluno' : 'Não foi possível exportar pessoa'}
                </span>
            `
        default:
            return `
                <span class="badge-pill badge-success">
                    ${isEnrollment ? 'Matriculado com sucesso' : 'Exportado com sucesso'}
                </span>
            `
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

const setStatusAmounts = students => {
    const quantitySuccess = students.filter(student => student.registration_status === 200)
    const quantityInfo = students.filter(student => student.registration_status === 400)
    const quantityError = students.filter(student => student.registration_status === 500)

    $('[quantity-success]').text(`(${quantitySuccess.length})`)
    $('[quantity-info]').text(`(${quantityInfo.length})`)
    $('[quantity-error]').text(`(${quantityError.length})`)
    $('[quantity-total]').text(`(${students.length})`)
}
