<div data-remodal-id="modal-enroll-students" class="modal-enroll-students">
    <button data-remodal-action="close" class="remodal-close"></button>

    <h5 class="modal-title-enroll-students">Matricular no Sagu</h5>

    <p class="modal-description-enroll-students">Selecione um curso e turma para matricular os <b>alunos selecionados</b> nesta oportunidade</p>

    <form>
        <div class="form-group">
            <label for="select-course">Defina uma oferta de <b>curso</b>:</label>
            <select id="select-course" class="select-course">
                <option id="select-course-opt"></option>
            </select>
        </div>
        <div class="form-group">
            <label for="select-class">Defina uma <b>turma</b>:</label>
            <select id="select-class" class="select-class">
                <option selected disabled>Selecione</option>
            </select>
        </div>
    </form>

    <p>
        <small>Serão matriculados automaticamente no SAGU os alunos com status de selecionado nesta oportunidade.</small>
    </p>

    <div class="modal-btns-enroll-students">
        <button type="button" data-remodal-action="close" class="btn btn-default">
            <?php \MapasCulturais\i::_e("Cancelar"); ?>
        </button>
        <button type="button" class="btn btn-default" id="btn-enroll-students-sagu" disabled>
            <?php \MapasCulturais\i::_e("Matricular alunos"); ?>
        </button>
    </div>
</div>
