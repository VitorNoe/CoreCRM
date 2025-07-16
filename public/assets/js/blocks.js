// JS para blocos reutilizáveis (exemplo inicial)
$(function(){
    // Modal open/close
    $(document).on('click', '[data-modal-open]', function(){
        var target = $(this).data('modal-open');
        $('#' + target).removeClass('hidden');
    });
    $(document).on('click', '[data-modal-close]', function(){
        $(this).closest('.block-modal').addClass('hidden');
    });
    // Toast auto-hide
    $('.block-toast').each(function(){
        var $el = $(this);
        setTimeout(function(){ $el.fadeOut(); }, 3000);
    });
});
