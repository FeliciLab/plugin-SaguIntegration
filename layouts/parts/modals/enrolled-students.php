<div data-remodal-id="modal-enrolled-students" class="modal-enrolled-students">
    <button data-remodal-action="close" class="remodal-close"></button>

    <h5>Alunos matriculados</h5>

    <div enrolled-students-wrapper>
        <img src="<?php $this->asset('img/spinner_192.gif') ?>" width="48" class="d-none">
    </div>

    <div class="export-infos-wrapper d-none">
        <div class="export-infos">
            Matriculado com sucesso <span quantity-success></span> &#9679;
            Já matriculado na turma <span quantity-info></span> &#9679;
            Erro ao matricular <span quantity-error></span> &#9679;
            Total <span quantity-total></span>
        </div>
    </div>
</div>
