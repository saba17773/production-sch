// @ts-nocheck

/**
 *
 * => object
 * selector: stirng
 * data: array
 * id: string
 * value: string
 * defaultValue: string
 */
function generateDropdown(object) {
  try {
    var el = $(object.selector).html("");

    $.each(object.data, function(i, v) {
      el.append(
        "<option value='" + v[object.id] + "'>" + v[object.value] + "</option>"
      );
    });

    if (
      typeof object.defaultValue !== "undefined" &&
      object.defaultValue !== null
    ) {
      el.value(object.defaultValue);
    }
  } catch (err) {
    console.log(err.message);
  }
}

/**
 *
 * => object
 * type: string
 * url: string
 * data: object
 */
function ajax(object) {
  if (typeof object.data === "undefined") {
    object.data = {};
  }

  return $.ajax({
    type: object.type,
    url: object.url,
    data: object.data,
    dataType: "json",
    cache: false,
  });
}
