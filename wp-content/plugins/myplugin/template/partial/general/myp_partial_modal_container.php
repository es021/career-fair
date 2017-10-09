<?php ?>

<div class="modal_container">
</div>

<script>
    var modal_container = jQuery(".modal_container");
    function openModal(modal_file_path, param) {
        modal_container.append(jQuery("<div>").
                load(modal_file_path, param));
    }
</script>

