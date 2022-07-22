<div data-remodal-id="modal-enroll-students" class="modal-enroll-students">
    <button data-remodal-action="close" class="remodal-close"></button>

    <h5 class="modal-title-enroll-students">Matricular no Sagu</h5>

    <p class="modal-description-enroll-students">Selecione um curso e turma para matricular no Sagu os alunos com status de <b>selecionado</b> nesta oportunidade.</p>

    <form>
        <div class="form-group">
            <label for="">Defina uma oferta de <b>curso</b>:</label>
            <select name="" id="select-course" class="select-course">
                <option></option>
            </select>
        </div>
        <div class="form-group">
            <label for="">Defina uma <b>turma</b>:</label>
            <select name="" id="select-class" class="select-class">
                <option></option>
            </select>
        </div>
    </form>

    <p>
        <small>Após aplicação da matrícula, os alunos selecionados estarão integrados as suas respectivas turmas dentro do Sagu.</small>
    </p>

    <div>
        <button type="button" class="btn">
            <?php \MapasCulturais\i::_e("Cancelar"); ?>
        </button>
        <button type="button" class="btn">
            <?php \MapasCulturais\i::_e("Matricular alunos"); ?>
        </button>
    </div>
</div>
