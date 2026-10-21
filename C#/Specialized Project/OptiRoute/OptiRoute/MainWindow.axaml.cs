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
using System.Threading.Tasks;

namespace OptiRoute;

public partial class MainWindow : Window
{
    private PlaceData[] placeData = Array.Empty<PlaceData>(); 
    private string searchInput = "233 Bình Quới";
    public MainWindow()
    {
        InitializeComponent();
        LoadMap();
        _ = InitAsync();
    }


    private async Task InitAsync()
    {
        await LoadPlaceData();
        await SearchAddressExample();
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

    private async Task LoadPlaceData()
    {
        string path = "/home/huy/Workspace/OptiRoute/OptiRoute/Data/hcmc.osm.pbf";
        var osmLoadData = new OsmLoadData();

        placeData = await osmLoadData.LoadDataFromFile(path);

        Console.WriteLine($"Loaded {placeData.Length} places");
        // Console.WriteLine($"The first place is {placeData[0].name}");
        // Console.WriteLine($"The first place is {placeData[0].address}");
        Console.WriteLine($"List {searchInput} address:");
        foreach (var p in placeData.Where(p => p.name.Contains(searchInput)))
            Console.WriteLine($"{p.osmType} {p.osmId} {p.name} ({p.latitude}, {p.longitude})");
    } 

    private async Task SearchAddressExample()
    {
        // string searchInput = "Ga Sài Gòn, Phường Nhiêu Lộc, Hồ Chí Minh";
        Console.WriteLine($"Finding: {searchInput}...");

        var getCoordinate = new OsmGetCoordinate();
        TemporaryData? result = await getCoordinate.GetCoordinatesFromAddressAsync(searchInput);

        if (result != null)
        {
            Console.WriteLine($"Found: {result.name}");
            Console.WriteLine($"Address: {result.address}");
            Console.WriteLine($"Latitude: {result.latitude}");
            Console.WriteLine($"Longitude: {result.longitude}");
            Console.WriteLine($"OSM: {result.osmType} {result.osmId}");
            
            CenterMapToLocation(result.longitude, result.latitude);
            AddPinToMap(result.longitude, result.latitude, result.name);

            Console.WriteLine("Checking the offline data...");
            var checkData = new CheckData();
            checkData.checkTheOfflineData(placeData, result);
        }
        else
        {
            Console.WriteLine("Cannot found this address");
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