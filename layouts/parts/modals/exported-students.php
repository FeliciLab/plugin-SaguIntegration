<div data-remodal-id="modal-exported-students" class="modal-exported-students">
    <button data-remodal-action="close" class="remodal-close"></button>

    <h5>Alunos exportados</h5>

    <div selected-students-table-wrapper>
        <img src="<?php $this->asset('img/spinner_192.gif') ?>" width="48" class="d-none">
    </div>

    <div class="export-infos-wrapper d-none">
        <div class="export-infos">
            Exportado com sucesso <span quantity-success></span> &#9679;
            Já cadastrado <span quantity-info></span> &#9679;
            Erro ao cadastrar <span quantity-error></span> &#9679;
            Total <span quantity-total></span>
        </div>
    </div>
</div>
