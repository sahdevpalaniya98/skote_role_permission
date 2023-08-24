$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


function ajax_request(url, data = {}) {
    return $.ajax({
        type: "POST",
        headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
        url: url,
        dataType: 'json',
        data: data,
    });
}

function swal_success(title = 'Success', msg = 'Saved') {
    Swal.fire(title, msg, 'success');
}

function swal_error(error = 'Error Occured') {
    Swal.fire('Error!', error, 'error');
}

function reload_table(table) {
    $(table).DataTable().clear();
    $(table).DataTable().ajax.reload();
}

function server_validation_error(container,data){
    var error_html = '<div class="alert alert-danger"><ul style="margin:0px !important;">';
    if(data.length > 0){
        $.each(data,function(index,value){
            error_html += '<li>'+value+'</li>';
        });
    }
    error_html += '</ul></div>';
    $(container).html(error_html);
}

function ajax_fail(jqXHR, status, exception) {
    if (jqXHR.status === 0) {
        error = 'Not connected.\nPlease verify your network connection.';
    } else if (jqXHR.status == 404) {
        error = 'The requested page not found. [404]';
    } else if (jqXHR.status == 500) {
        error = 'Internal Server Error [500].';
    } else if (exception === 'parsererror') {
        error = 'Requested JSON parse failed.';
    } else if (exception === 'timeout') {
        error = 'Time out error.';
    } else if (exception === 'abort') {
        error = 'Ajax request aborted.';
    } else {
        error = 'Uncaught Error.\n' + jqXHR.responseText;
    }
    Swal.fire('Error!', error, 'error');
}

function reset_validation(form){
    $(form)[0].reset();
    $(form).validate().resetForm();
    $(form).find('.error').removeClass('error');
    $(form).find('.error_container').html('');
    if($(form).find('select').length){
        $.each($(form).find('select'),function(index,value){
            $(value).val(null).trigger('change');
        });
    }
    if($(form).find('.dropify-errors-container').length > 0){
        $(form).find('.dropify-errors-container').remove();
    }
}

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

function resetFilter() {
    $('.select2').val('').trigger('change');
    $('.date').val('');

    createDataTable();
}

function createDataTable(url, columns, filter = []) {
    if (!($.fn.DataTable.isDataTable('#dataTable'))) {
        table = $('#dataTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                    d.filter = getFilterData(filter);
                },
                url: url,
                type: 'POST',
            },
            columns: columns,
            order: [0, 'desc']
        });
        return table;
    } else {
        table.ajax.reload();
    }
}

function getFilterData(array = []){
    filterValArr = {};
    if (array.length > 0) {
        array.map(function (itm, inx) {
            filterValArr[itm] = $(`#${itm}`).val();
        });
    }
    return filterValArr;
}

$(document).on('click', '.btnDelete', function () {
    var id = $(this).data('id');
    var url = $(this).data('url');
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    id: id,
                    "_token": $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
            }).done(function (data) {
                if (data.success == true) {
                    Toast.fire({ icon: 'success', title: data.message })
                } else {
                    Toast.fire({ icon: 'error', title: data.message })
                }
                table.ajax.reload();
            }).fail(function (jqXHR, status, exception) {
                if (jqXHR.status === 0) {
                    error = 'Not connected.\nPlease verify your network connection.';
                } else if (jqXHR.status == 404) {
                    error = 'The requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    error = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    error = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    error = 'Time out error.';
                } else if (exception === 'abort') {
                    error = 'Ajax request aborted.';
                } else {
                    error = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                Toast.fire({ icon: 'error', title: error })
            });
        }
    })
});

$(document).on('click', '.btn-reset_btn', function () {
    $('form').trigger('reset');
    $('input[type=hidden]').val('');
    $('.form_title').html('Add');
    $('#show_img').html('');
    $('#show_img').hide();

})

$('.numbers_only').keyup(function () {
    this.value = this.value.replace(/[^0-9\.]/g, '');
});

function customDatePicker(selector) {
    $(selector).datepicker({
        dateFormat: 'dd/mm/yy',
    });
}

function customDateRangePicker(selector) {
    $(selector).daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        }
    }).val('');

    $(selector).on('apply.daterangepicker', function (ev, picker) {
        // console.log(picker.startDate.format('DD/MM/YYYY'));
        // console.log(picker.endDate.format('DD/MM/YYYY'));
    });
}

$(document).on('change', '.js-switch', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    var status = $(this).prop('checked') == true ? "1" : "0";
    var url = $(this).data('url');

    const Toast = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
    });
    Toast.fire({
        title: 'Are You Sure you want to change status?',
        text: "You can be able to revert this again!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: url,
                data: { id: id, status: status },
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            }).done(function (data) {
                if (data.success == true) {
                    Toast.fire("Status Changed!", data.message, "success");
                } else {
                    Toast.fire("Cancelled!", data.message, "error");
                }
                table.ajax.reload();
            }).fail(function (jqXHR, status, exception) {
                if (jqXHR.status === 0) {
                    error = 'Not connected.\nPlease verify your network connection.';
                } else if (jqXHR.status == 404) {
                    error = 'The requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    error = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    error = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    error = 'Time out error.';
                } else if (exception === 'abort') {
                    error = 'Ajax request aborted.';
                } else {
                    error = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                Swal.fire('Error!', error, 'error');
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            let switch_val = $(this).is(':checked');
            let state = (switch_val == true) ? false : true;

            $(this).prop('checked', state).trigger('change');

            Toast.fire('Cancelled', 'Status changing cancelled :)', 'error');
        }
    });
});

function bulk_status_change(url, ids) {
    const Toast = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
    });

    Toast.fire({
        title: 'Are You Sure you want to change status?',
        text: "You can be able to revert this again!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: url,
                data: { ids: ids },
                dataType: 'json',
            }).done(function (data) {
                if (data.success == true) {
                    $('#div_multiple_status').hide();
                    $('#div_multiple_delete').hide();
                    $("#master_status_checkbox").prop('checked', false);
                    table.ajax.reload();
                    Toast.fire({ icon: 'success', title: data.message });
                } else {
                    Toast.fire({ icon: 'error', title: data.message });
                }
            }).fail(function (jqXHR, status, exception) {
                if (jqXHR.status === 0) {
                    error = 'Not connected.\nPlease verify your network connection.';
                } else if (jqXHR.status == 404) {
                    error = 'The requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    error = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    error = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    error = 'Time out error.';
                } else if (exception === 'abort') {
                    error = 'Ajax request aborted.';
                } else {
                    error = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                Swal.fire('Error!', error, 'error');
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Toast.fire('Cancelled', 'Status changing cancelled :)', 'error')
        }
    });
}

function bulk_destroy(url, ids) {
    const Toast = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
    });

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    ids: ids,
                },
                dataType: 'json',
            }).done(function (data) {
                if (data.success == true) {
                    $('#div_multiple_status').hide();
                    $('#div_multiple_delete').hide();
                    $("#master_delete_checkbox").prop('checked', false);
                    Toast.fire({ icon: 'success', title: data.message });
                    table.ajax.reload();
                } else {
                    Toast.fire({ icon: 'error', title: data.message });
                }
            }).fail(function (jqXHR, status, exception) {
                if (jqXHR.status === 0) {
                    error = 'Not connected.\nPlease verify your network connection.';
                } else if (jqXHR.status == 404) {
                    error = 'The requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    error = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    error = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    error = 'Time out error.';
                } else if (exception === 'abort') {
                    error = 'Ajax request aborted.';
                } else {
                    error = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                Toast.fire({ icon: 'error', title: error })
            });
        }
    });
}
