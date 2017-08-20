// Este ejemplo muestra un formulario de dirección, utilizando la característica de autocompletar
// de la API de Google Places para ayudar a los usuarios a rellenar la información.

var placeSearch, autocomplete;
var componentForm = {
  street_number: 'short_name',
  route: 'long_name',
  locality: 'long_name',
  administrative_area_level_1: 'short_name',
  country: 'long_name',
  postal_code: 'short_name'
};

function initAutocomplete() {
  // Cree el objeto de autocompletado, restringiendo la búsqueda a
  // location types.
  autocomplete = new google.maps.places.Autocomplete(
      (document.getElementById('autocomplete')),
      {types: ['geocode']});

  // Cuando el usuario selecciona una dirección de la lista desplegable, llene la dirección
  // Campos en el formulario.
  autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
  // Obtenga los detalles del lugar del objeto de autocompletar.
  var place = autocomplete.getPlace();

  for (var component in componentForm) {
    document.getElementById(component).value = '';
    document.getElementById(component).disabled = false;
  }

  // Obtener cada componente de la dirección desde el lugar
  // y rellenar el campo correspondiente en el formulario.
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {
      var val = place.address_components[i][componentForm[addressType]];
      document.getElementById(addressType).value = val;
    }
  }
}


// Bias el objeto de autocompletar a la ubicación geográfica del usuario,
// como se suministra con el objeto 'navigator.geolocation' del navegador.
function geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      var circle = new google.maps.Circle({
        center: geolocation,
        radius: position.coords.accuracy
      });
      autocomplete.setBounds(circle.getBounds());
    });
  }
}