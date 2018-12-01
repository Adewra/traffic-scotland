<?php

namespace \Adewra\TrafficScotland\Helpers;

if (!function_exists('scottish_local_authorities'))
{
    function scottish_local_authorities()
    {
        $client = new Client();
        /* Courtesy of  the Office For National Statistics */
        $response = $client->get(
            'https://opendata.arcgis.com/datasets/593018bf59ab4699b66355bd33cd186d_4.geojson'
        )->json();

        return \GeoJson\GeoJson::jsonUnserialize($response);
    }
}

if (!function_exists('urban_audit_core_cities')) {
    function urban_audit_core_cities()
    {
        $client = new Client();
        /* Courtesy of  the Office For National Statistics Open Geography Portal (Audit VII) */
        $response = $client->get(
            'https://opendata.arcgis.com/datasets/54a7d804f2354fd8aaeae9807a012ce4_2.geojson'
        )->json();

        return \GeoJson\GeoJson::jsonUnserialize($response);
    }
}
if (!function_exists('urban_audit_functional_urban_areas')) {
    function urban_audit_functional_urban_areas()
    {
        $client = new Client();
        /* Courtesy of  the Office For National Statistics Open Geography Portal (Audit VII) */
        $response = $client->get(
            'https://opendata.arcgis.com/datasets/567ceae3cb184680ae8e6952e78a1938_0.geojson'
        )->json();

        return \GeoJson\GeoJson::jsonUnserialize($response);
    }
}