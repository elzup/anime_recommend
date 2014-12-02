$ ->
    $('input[type=button]').click ->
        $('input[name=case]').val($(@).attr('case'))
        $('form').submit()

    $('button.reset-button').click ->
        $.cookie("logs", "")
        $(@).attr('disabled', "")
