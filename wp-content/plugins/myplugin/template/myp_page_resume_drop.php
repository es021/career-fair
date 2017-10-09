<?php ?>
<input id="general_resume" type="file"/>

<div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6 text-center">
        <h2>Drop Your Resume Here</h2>

        <div id="btn_select_file" title="Select File" class="btn_icon">
            <i class="fa fa-file-text  fa-3x"></i>
            <br><small>Select File</small>
        </div> 

        <div hidden="hidden" id="load">
            <i class="fa fa-spinner fa-pulse fa-3x"></i><br>Uploading..
        </div> 

        <div hidden="hidden"  id="container_upload">
            <div id="btn_upload_file" title="Upload File" class="btn_icon">
                <i class="fa fa-upload fa-3x"></i>
                <br><small>Upload</small>
            </div>
            <div class="file_selected"></div>
            <br>
            <form id="form_upload">
                <div class="wzs21_label_form">Please provide your <strong>email</strong> before uploading</div>
                <input name="email" 
                       id="email" 
                       placeholder="john.doe@gmail.com"
                       class="wzs21_input_form" type="text">
            </form>
        </div> 


    </div>
    <div class="col-sm-3"></div>
</div>
<script>
    jQuery(document).ready(function () {
        var form_upload = jQuery("#form_upload");
        var general_resume = jQuery("#general_resume");
        var btn_select_file = jQuery("#btn_select_file");
        var load = jQuery("#load");
        var container_upload = jQuery("#container_upload");
        var btn_upload_file = container_upload.find("#btn_upload_file");
        var file_selected = container_upload.find(".file_selected");

        var current_file = null;
        initSelect();
        btn_select_file.click(function () {
            general_resume.val("");
            general_resume.trigger("click");
        });

        function initSelect() {
            btn_select_file.removeAttr("hidden");
            container_upload.attr("hidden", "hidden");
            load.attr("hidden", "hidden");
            current_file = null;
        }

        var rules = {email: {
                required: true,
                email: true,
                minlength: 3
            }};

        initFormValidationCustom(form_upload, rules, submitUpload);

        general_resume.on('change', function (event) {

            var files = event.target.files;
            if (files.length <= 0) {
                return;
            }

            if (files.length > 1) {
                alert("Please select only 1 file");
                return;
            }

            var file = files[0];

            if (file.type.indexOf("pdf") <= -1) {
                alert("Only pdf format is accepted");
                return;
            }

            current_file = file;
            //console.log(file);
            enableUpload();
        });



        function enableUpload() {
            toogleShowHidden(btn_select_file, container_upload);
            file_selected.html(current_file.name);
        }

        btn_upload_file.click(function () {
            form_upload.submit();
        });

        function submitUpload() {
            load.removeAttr("hidden");
            container_upload.attr("hidden", "hidden");
            btn_select_file.attr("hidden", "hidden");

            if (current_file === null) {
                return;
            }

            var post_data = new FormData();
            var form_data = formDataToObject(form_upload);
            
            for (var key in form_data) {
                post_data.append(key, form_data[key]);
            }

            post_data.append("action", "wzs21_upload_file");
            post_data.append(<?= SiteInfo::FILE_INDEX_RESUME_DROP ?>, current_file);

            jQuery.ajax({
                url: ajaxurl,
                data: post_data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function (res) {
                    console.log(res);
                    res = JSON.parse(res);
                    if (res.status === "<?= SiteInfo::STATUS_SUCCESS ?>") {
                        popup.openPopup("Upload Success", "<strong>Thank you!</strong><br>Your resume has been successfully uploaded");
                        initSelect();
                    } else {
                        popup.openPopup("Upload Failed", res.data, true);
                    }
                },
                error: function (err) {

                }
            });
        }



    });
</script>
<style>
    .btn_icon{
        border: none;
        position: relative;
        cursor: pointer;
        text-align: center;
        font-size: 20px;    
    }

    .btn_icon:hover{
        color: #004092;
    }

    .btn_icon:active{
        color: #00547d;
    }

    input#general_resume{
        opacity: 0;
        left:0;
        height: 100%;
        position: absolute;

    }

</style>
