// Open confirmation box
function openConfirmationBox(options)
{
    var title = options.title ?? 'Confirmation';
    var content = options.content ?? 'Are you sure want to submit?';
    var primaryBtn = options.primaryBtn ?? 'Confirm';
    var secondaryBtn = options.secondaryBtn ?? 'Cancel';
    var primaryAction = options.primaryAction ?? null;
    var secondaryAction = options.secondaryAction ?? null;
    var primaryActionParams = options.primaryActionParams ?? null;
    var secondaryActionParams = options.secondaryActionActionParams ?? null;

    $.confirm({
        title: title,
        content: content,
        draggable: false,
        buttons: {
            cancel: {
                text: secondaryBtn ?? 'Cancel',
                btnClass: 'secondary-button',
                action: function () {
                    if(secondaryAction){
                        if(secondaryActionParams){
                            secondaryAction(...secondaryActionParams);
                        }else{
                            secondaryAction();
                        }
                    }
                }
            },
            confirm: {
                text: primaryBtn ?? 'Confirm',
                btnClass: 'primary-button',
                action: function () {
                    if(primaryAction){
                        if(primaryActionParams){
                            primaryAction(...primaryActionParams);
                        }else{
                            primaryAction();
                        }
                    }
                }
            },
        },
        onAction: function () {
            $('.jconfirm-buttons button').attr('disabled', true);
        }   

    });
}
