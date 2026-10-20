using System;
using System.Linq;
using System.Collections.Generic;
using Avalonia.Controls;
using Mapsui;
using Mapsui.Layers;
using Mapsui.Projections;
using Mapsui.Styles;
using Mapsui.Tiling;
using Mapsui.Nts;
using NetTopologySuite.Geometries;

namespace OptiRoute;

public partial class MainWindow : Window
{
    private PlaceData[] placeData = Array.Empty<PlaceData>(); 
    public MainWindow()
    {
        InitializeComponent();
        LoadMap();
        
        SearchAddressExample();
    }

    private void LoadMap()
    {
        var longitude = 106.7009;
        var latitude = 10.7769;

        var map = new Map();

        map.Layers.Add(OpenStreetMap.CreateTileLayer());

        var lonLatMercator = SphericalMercator.FromLonLat(longitude, latitude);
        var centerPoint = new MPoint(lonLatMercator.x, lonLatMercator.y);
        var zoomResolution = map.Navigator.Resolutions[15];

        map.Navigator.CenterOnAndZoomTo(centerPoint, zoomResolution);
        
        Map.Map = map;
    }

    private async void LoadPlaceData()
    {
        string path = "/home/huy/Workspace/OptiRoute/OptiRoute/Data/hcmc.osm.pbf";
        var osmLoadData = new OsmLoadData();

        placeData = await osmLoadData.LoadData(path);

        Console.WriteLine($"Loaded {placeData.Length} places");
        Console.WriteLine($"The first place is {placeData[0].name}");
        Console.WriteLine($"The first place is {placeData[0].address}");
    } 

    private async void SearchAddressExample()
    {
        string searchInput = "Sân bay Tân Sơn Nhất";
        Console.WriteLine($"Finding: {searchInput}...");

        var getCoordinate = new OsmGetCoordinate();
        PlaceData? result = await getCoordinate.GetCoordinatesFromAddressAsync(searchInput);

        if (result != null)
        {
            Console.WriteLine($"Find: {result.name}");
            Console.WriteLine($"Address: {result.address}");
            Console.WriteLine($"Latitude: {result.latitude}");
            Console.WriteLine($"Longitude: {result.longitude}");
            
            CenterMapToLocation(result.longitude, result.latitude);
            AddPinToMap(result.longitude, result.latitude, result.name);
        }
        else
        {
            Console.WriteLine("Cannot find this address");
        }
    }

    private void CenterMapToLocation(double longitude, double latitude)
    {
        var lonLatMercator = SphericalMercator.FromLonLat(longitude, latitude);
        var centerPoint = new Mapsui.MPoint(lonLatMercator.x, lonLatMercator.y);
        var zoomResolution = Map.Map.Navigator.Resolutions[16];

        Map.Map.Navigator.CenterOnAndZoomTo(centerPoint, zoomResolution);
    }

    private void AddPinToMap(double longitude, double latitude, string label)
    {
        var lonLatMercator = SphericalMercator.FromLonLat(longitude, latitude);

        var mPoint = new MPoint(lonLatMercator.x, lonLatMercator.y);
        var feature = new PointFeature(mPoint);
        feature["Label"] = label;

        var pinStyle = new SymbolStyle
        {
            Fill = new Brush(Color.Red),
            Outline = new Pen(Color.White, 2),
            SymbolScale = 1.2,
            SymbolType = SymbolType.Ellipse
        };

        feature.Styles.Add(pinStyle);

        var pinLayer = new MemoryLayer
        {
            Features = new[] { feature },
            Name = "PinsLayer"
        };

        var existingLayer = Map.Map.Layers.FirstOrDefault(l => l.Name == "PinsLayer");
        if (existingLayer != null)
        {
            Map.Map.Layers.Remove(existingLayer);
        }

        Map.Map.Layers.Add(pinLayer);
        Map.Map.Refresh();
    }
}