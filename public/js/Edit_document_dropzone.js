var image = [];
var removedimg = [];
Dropzone.options.dropzone = {

    url: $('#addfrm').attr('action'),
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    autoProcessQueue: false,
    parallelUploads: 15,
    uploadMultiple: true,
    maxFilesize: 20,
    dictFileTooBig: "File too Big, please select a file less than 20mb",
    addRemoveLinks: true,
    removedfile: function (file) {
        removedimg.push(file.name);
        $('#removed_images').val(removedimg);
        var _ref; // removed images hide from dropzone
        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) :
            void 0;
    },
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
            formData.append("customer_id", jQuery("input[name='customer_id']").val());
            formData.append("name", jQuery("input[name='name']").val());
            formData.append("address", jQuery("input[name='address']").val());
            formData.append("mobile_number", jQuery("input[name='mobile_number']").val());
            formData.append("document", jQuery("#document_type").val());
            formData.append("remark", jQuery("input[name='remark']").val());
            formData.append("old_img", jQuery("#old_img").val());
            formData.append("removed_images", jQuery("input[name='removed_images']").val());
        });
    },
    success: function () {
        window.location.href =$(".cancle").attr("href");
    }
}

$(function () {
    var old_images = $('#old_img').val();
    var imageurl = $('#imageurl').val();
    if (old_images != '') {
        const Img = old_images.split(",",);
        Img.forEach(function (value) {
            // callback and crossOrigin are optional
            image.push(value);
            let mockFile = {
                name: value,
                size: 12345
            };
            // alert(value)
            myDropzone.displayExistingFile(mockFile,
                imageurl + '/' + value);
        });
    }
})
