$ ->
    $('input[type=button]').click ->
        $.cookie("test", $(@).attr('case'))
        $('input[name=case]').val($(@).attr('case'))
        $('form').submit()
