<button
    type="button"
    class="btn export-sagu-data-btn hltip"
    export-students-btn
    title="<?php \MapasCulturais\i::esc_attr_e("Alunos selecionados serão cadastrados no Sagu como pessoa física");?>"
>
    <?php \MapasCulturais\i::_e("Exportar selecionados para o Sagu");?>
</button>

<button type="button" class="btn enroll-students-btn" enroll-students-btn>
    <?php \MapasCulturais\i::_e("Matricular no Sagu");?>
</button>

<?php $this->part('modals/exported-students'); ?>
<?php $this->part('modals/students-enrollment'); ?>
<?php $this->part('modals/enrolled-students'); ?>
