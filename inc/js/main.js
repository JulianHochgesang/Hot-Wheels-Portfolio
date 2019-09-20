(function($) {

  $('#add_car').click(function(e) {
    e.preventDefault();
    $('#modal_add').fadeIn(300);
  });
  $('#add_abort').click(function(e) {
    e.preventDefault();
    $('#modal_add').fadeOut(300);
  });

  $('.fahrzeug').each(function() {
    $(this).find('#edit_car').click(function(e) {
      e.preventDefault();
      $edit_id = $(this).closest('.fahrzeug-image').find('.fahrzeug-id').text();
      $('#edit_id_span').text($edit_id);
      $('#edit_id').val($edit_id);
      $('#modal_edit').fadeIn(300);
    });
  });

  $('#edit_abort').click(function(e) {
    e.preventDefault();
    $('#modal_edit').fadeOut(300);
  });

})(jQuery);