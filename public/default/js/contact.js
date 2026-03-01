const Contact = {
    selector: {
        form: "#formContact",
        modalID: 'msgModal',
        modal: '#msgModal',
    },
    validate: {
        row: ".form-input",
        error: 'has-error',
        message: 'message-error'
    },
    reset: function () {
        $(this.validate.row).removeClass(this.validate.error);
        $(`${this.validate.row} .${this.validate.message}`).remove();
    },
    message(selector, message) {
        selector = $(".form-control[name='" + selector + "']").parents(this.validate.row).addClass(this.validate.error);
        selector.append(`<p class="` + this.validate.message + ` mb-0">${message}</p>`);
    },
    modal: function (html) {
        let xhtml = `
        <div class="modal" id="${this.selector.modalID}" tabindex="-1">
             <div class="modal-dialog modal-dialog-centered">
                 <div class="modal-content bg-white">
                    <div class="modal-header border-0 p-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                     <div class="modal-body pt-4">
                    ${html}
                    </div>
                 </div>
            </div>
        </div>
        `;
        $(this.selector.modal).remove();
        $('body').append(xhtml);
        $(this.selector.modal).modal("show");
    },
    init: function () {
        $(this.selector.form).on('submit', (function (e) {
            e.preventDefault();
            var link = $(this).attr("action");
            $.ajax({
                url: link,
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    Loading.show();
                },
                success: function (res) {
                    Loading.hide();
                    Contact.reset();
                    if (res.errors) {
                        let errors = res.errors;
                        if (errors.name) {
                            Contact.message('name', errors.name);
                        }
                        if (errors.phone) {
                            Contact.message('phone', errors.phone);
                        }
                        if (errors.email) {
                            Contact.message('email', errors.email);
                        }
                        if (errors.title) {
                            Contact.message('title', errors.title);
                        }
                        if (errors.message) {
                            Contact.message('message', errors.message);
                        }
                    }
                    if (res.status == true) {
                        $(Contact.selector.form)[0].reset();
                        Contact.modal(`
                                <svg class="checkmark success" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark_circle_success" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark_check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" stroke-linecap="round"></path></svg>
                                <h4 class="title my-4 text-center text-success">
                                BẠN ĐÃ GỬI YÊU CẦU THÀNH CÔNG
                                </h4>
                            `);
                    }
                    if (res.status == false && res.code == 200) {
                        Contact.modal(`
                        <svg class="checkmark error" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark_circle_error" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark_check" stroke-linecap="round" fill="none" d="M16 16 36 36 M36 16 16 36
                        "></path></svg>
                        <h4 class="title my-4 text-center text-danger">
                           BẠN GỬI YÊU CẦU THẤT BẠI
                        </h4>
                            `);
                    }
                }
            });
        }));
    }
};
$(document).ready(function () {
    Contact.init();
});