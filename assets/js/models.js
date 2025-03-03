$(function () {
    $(".file-manager-container .file-item .file-item-actions").each(function () {
        // change selected school year for the model
        $(this).find(".befs-dropend .dropdown-menu button.dropdown-item").on("click", function(e) {
            e.preventDefault();
            const sy_id = $(this).attr("data-befs-sy-id");
            const model_id = $(this).attr("data-befs-model-id");
            if (!sy_id || !model_id) {
                console.log("Warning: No school year id or no model id");
                return;
            }
            $.post(window.location.href, {sy_id, model_id, action: "choose"})
                .done(function (result) {
                    alert(result);
                    window.location.reload()
                })
                .fail((error, statusText) => { alert(statusText); console.log(statusText); });
        });
        // rename model
        $(this).find(".dropdown-menu.dropdown-menu-right .dropdown-item[data-befs-dropdown-action=rename]").on("click", function (e) {
            const modelname = $(this).attr("data-befs-model-name");
            const model_id = $(this).attr("data-befs-model-id");
            if (!model_id) {
                alert("Warning: No model id");
            }
            Swal.fire({
                title: `Rename model '${modelname}'`,
                input: "text",
                inputLabel: "Enter new model name:",
                inputValue: modelname,
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                preConfirm: async (model_name) => {
                    if (!model_name) {
                        Swal.showValidationMessage(`Fill in required field.`);
                        return;
                    }
                    return new Promise(async (resolve) => {
                        $.post(window.location.href, {model_id, model_name, action: "rename"})
                            .done(function (result) {
                                resolve([true, result]);
                            })
                            .fail((error, statusText) => resolve([false, statusText]));
                    }).then(([success, message]) => {
                        if (success && !!message) {
                            Swal.fire({
                                icon: "success",
                                title: message,
                                showConfirmButton: false,
                                position: "center",
                                timer: 1000 
                            }).then(() => {
                                window.location.reload()
                            })
                        } else {
                            Swal.showValidationMessage("Failed to rename model.");
                        }
                    })
                }
            });
         });
        // delete model
         $(this).find(".dropdown-menu.dropdown-menu-right .dropdown-item[data-befs-dropdown-action=delete]").on("click", function (e) {
            const modelname = $(this).attr("data-befs-model-name");
            const model_id = $(this).attr("data-befs-model-id");
            if (!model_id) {
                alert("Warning: No model id");
            }
            Swal.fire({
                title: `Delete model '${modelname}'?`,
                text: "You won't be able to revert this!",
                icon: "warning",
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                preConfirm: async () => {
                    return new Promise(async (resolve) => {
                        $.post(window.location.href, {model_id, action: "delete"})
                            .done(function (result) {
                                resolve([true, result]);
                            })
                            .fail((error, statusText) => resolve([false, statusText]));
                    }).then(([success, message]) => {
                        if (success && !!message) {
                            Swal.fire({
                                icon: "success",
                                title: message,
                                showConfirmButton: false,
                                position: "center",
                                timer: 1000 
                            }).then(() => {
                                window.location.reload()
                            })
                        } else {
                            Swal.showValidationMessage("Failed to delete model.");
                        }
                    })
                }
            });
         });
    });

    $(".befs-dropend .befs-dropdown-dropend").hover(
        function() { // Mouse enter
            const $dm = $(this).parent().find(".dropdown-menu");
            !$dm.hasClass("show") && $dm.addClass("show");
        },
        function() { // Mouse leave
            const $dm = $(this).parent().find(".dropdown-menu");
            !!$dm.hasClass("show") && $dm.removeClass("show");
        }
    );

    $(".befs-dropend .dropdown-menu").hover(
        function() { // Mouse enter
            !$(this).hasClass("show") && $(this).addClass("show");
        },
        function() { // Mouse leave
            !!$(this).hasClass("show") && $(this).removeClass("show");
        }
    );
});