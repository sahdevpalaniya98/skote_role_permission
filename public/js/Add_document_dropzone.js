Dropzone.options.dropzone = {

    url:$('#addfrm').attr('action'),
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    autoProcessQueue: false,
    parallelUploads: 15,
    uploadMultiple: true,
    maxFilesize: 20,
    dictFileTooBig: "File too Big, please select a file less than 20mb",
    addRemoveLinks: true,
    init: function () {
        myDropzone = this; // Makes sure that 'this' is understood inside the functions below.

        // for Dropzone to process the queue (instead of default form behavior):

        document.getElementById("submit_data").addEventListener("click", function (e) {
            if (myDropzone.getQueuedFiles().length > 0) {
                e.preventDefault();
                e.stopPropagation();
                myDropzone.processQueue();
            }
            else {
                myDropzone.processQueue();
                form.submit();
            }
        });

        //send all the form data along with the files:
        this.on("sendingmultiple", function (data, xhr, formData) {
            formData.append("name", jQuery("input[name='name']").val());
            formData.append("address", jQuery("input[name='address']").val());
            formData.append("mobile_number", jQuery("input[name='mobile_number']").val());
            formData.append("document", jQuery("#document_type").val());
            formData.append("remark", jQuery($('#remark').val()).val());
        });
    },
    success: function () {
        window.location.href = $(".cancle").attr("href");
    }
}