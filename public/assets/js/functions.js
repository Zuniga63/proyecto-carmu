const functions = function () {
  return {
    generalValidation: (id, rules, messages) => {
      const form = $(`#${id}`);
      form.validate({
        rules,
        messages,
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.insertAfter(element);
          error.addClass('invalid-feedback');
          // element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        },
        submitHandler: function (form) {
          return true;
        }
      })
    },
    notifications: function (message, title, type) {
      toastr.options = {
        closeButton: true,
        newestOnTop: true,
        positionClass: 'toast-top-right',
        // preventDuplicates: true,
        timeOut: '3000'
      };
      switch (type) {
        case 'error':
          toastr.error(message, title);
          break;
        case 'success':
          toastr.success(message, title);
          break;
        case 'info':
          toastr.info(message, title);
          break;
        case 'warning':
          toastr.warning(message, title);
          break;
      }
    },//End of notifications
  }
}();