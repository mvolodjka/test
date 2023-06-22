jQuery(document).ready(function ($) {
  var ajaxUrl = '/wp-admin/admin-ajax.php';

  // Функція для виконання Ajax-запиту
  function doAjaxRequest(data, successCallback) {
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: ajaxUrl,
      data: data,
      success: successCallback,
      error: function (xhr, textStatus, errorThrown) {
        console.log(xhr.responseText);
        console.log(textStatus);
        console.log(errorThrown);
      },
    });
  }

  // Обробник події для кнопки "Фільтрувати"
  $('#real-estate-filter button').click(function (e) {
    e.preventDefault();

    var filterData = {
      action: 'real_estate_search',
      filter: {
        house_name: $('#house-name-filter').val(),
        coordinates: $('#coordinates-filter').val(),
        floor_count: $('#floor-count-filter').val(),
        building_type: $('#building-type-filter').val(),
      },
      page: 1,
    };

    doAjaxRequest(filterData, function (response) {
      $('#real-estate-results').html(response.html);
      $('#real-estate-pagination').html(response.pagination);
    });
  });

  // Обробник події для пагінації
  $('#real-estate-pagination').on('click', 'a', function (e) {
    e.preventDefault();

    var page = $(this).attr('href');
    page = page.replace(/\D/g, '');

    var filterData = {
      action: 'real_estate_search',
      filter: {
        house_name: $('#house-name-filter').val(),
        coordinates: $('#coordinates-filter').val(),
        floor_count: $('#floor-count-filter').val(),
        building_type: $('#building-type-filter').val(),
      },
      page: page,
    };

    doAjaxRequest(filterData, function (response) {
      $('#real-estate-results').html(response.html);
      $('#real-estate-pagination').html(response.pagination);
    });
  });
});
