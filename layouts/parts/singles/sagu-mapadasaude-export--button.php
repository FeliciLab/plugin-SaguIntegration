<button type="button" class="btn export-sagu-data-btn" export-students-btn data-opportunity-id="<?php echo $opportunity_id ?>">
    <?php \MapasCulturais\i::_e("Exportar dados para o Sagu");?>
</button>

<?php $this->part('modals/exported-students'); ?>
