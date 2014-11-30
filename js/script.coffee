$ ->
    $('input[type=button]').click ->
        $.cookie("test", $(@).attr('case'))
        $('form').submit()
