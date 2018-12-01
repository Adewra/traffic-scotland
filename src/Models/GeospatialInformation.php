<?php

namespace Adewra\TrafficScotland;


class GeospatialInformation
{
    public static function scottishLocalAuthorities()
    {
        $client = new Client();
        $response = $client->get(
            'https://opendata.arcgis.com/datasets/593018bf59ab4699b66355bd33cd186d_4.geojson'
        )->json();

        return \GeoJson\GeoJson::jsonUnserialize($response);
    }

    public static function urbanAuditGreaterCities()
    {
        $client = new Client();
        $response = $client->get(
            'https://opendata.arcgis.com/datasets/9ffc540bab97494ead80214a1a7d0dc2_0.geojson'
        )->json();

        return \GeoJson\GeoJson::jsonUnserialize($response);
    }

    public function urbanAuditFunctionalUrbanAreas()
    {
        $client = new Client();
        $response = $client->get(
            'https://opendata.arcgis.com/datasets/567ceae3cb184680ae8e6952e78a1938_0.geojson'
        )->json();

        return \GeoJson\GeoJson::jsonUnserialize($response);
    }
}