<?php

function degreesToRadians($degrees)
{
  return $degrees * M_PI / 180;
}

function latLonDistance($lat1, $lon1, $lat2, $lon2)
{
  $earthRadiusKm = 6371;
  $dLat = degreesToRadians($lat2 - $lat1);
  $dLon = degreesToRadians($lon2 - $lon1);

  $lat1 = degreesToRadians($lat1);
  $lat2 = degreesToRadians($lat2);

  $a =  (sin($dLat / 2) * sin($dLat / 2)) +
    (sin($dLon / 2) * sin($dLon / 2) * cos($lat1) * cos($lat2));
  $c =  2 * atan2(sqrt($a), sqrt(1 - $a));
  return $earthRadiusKm * $c;
}


//@TODO PARAMETRIZAR EL NOMINATIM
function reverseGeoCode($lat, $lon)
{
  $nominatim_data = json_decode(file_get_contents('https://nominatim.sistemas-teleurban.com/nominatim/reverse?format=json&lat=' . $lat . '&lon=' . $lon));

  if ($nominatim_data == '')         return false;
  if (isset($nominatim_data->error)) return false;

  return $nominatim_data;
}


function geoCode($street, $ext_number, $cp)
{

  $url = 'https://nominatim.sistemas-teleurban.com/nominatim/search?format=json';
  $url .= 'country=mexico';
  $url .= '&postalcode=' . $cp;
  $url .= '&street=' . $street . '%20' . $ext_number;

  $nominatim_data = json_decode(file_get_contents($url));

  if ($nominatim_data == '')         return false;
  if (isset($nominatim_data->error)) return false;

  return $nominatim_data;
}
