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
    private NodeData[] nodeData = Array.Empty<NodeData>(); 
    private WayData[] wayData = Array.Empty<WayData>();
    private List<string> searchInputs = new List<string>
    {
        "233 Bình Quới",
        "Chợ Bến Thành",
        "Dinh Độc Lập"
    };

    private readonly RoadGraph _roadGraph = new();

    public MainWindow()
    {
        InitializeComponent();
        LoadMap();
        _ = InitAsync();
    }

    private async Task InitAsync()
    {
        try
        {
            await LoadPlaceData();
            await SearchMultipleAddressesExample();
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Error in InitAsync: {ex.Message}");
            Console.WriteLine(ex.StackTrace);
        }
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
        string path = "/home/huy/Storage/C#/Specialized Project/OptiRoute/OptiRoute/Data/hcmc.osm.pbf";
        var osmLoadData = new OsmServices();
        var osmLoadResult = await osmLoadData.LoadDataFromFile(path);

        nodeData = osmLoadResult.Node;
        wayData = osmLoadResult.Way;

        Console.WriteLine($"Loaded {nodeData.Length} nodes and {wayData.Length} ways");

        _roadGraph.BuildGraph(nodeData, wayData);

        foreach (var query in searchInputs)
        {
            Console.WriteLine($"List {query} address:");

            foreach (var n in nodeData.Where(n => n.name.Contains(query, StringComparison.OrdinalIgnoreCase)))
            {
                Console.WriteLine($"-> {n.osmType} {n.osmId} {n.name} ({n.latitude}, {n.longitude})");
            }
                
            foreach (var w in wayData.Where(w => w.name.Contains(query, StringComparison.OrdinalIgnoreCase)))
            {
                Console.WriteLine($"-> {w.osmType} {w.osmId} {w.name} ({w.latitude}, {w.longitude})");
            }
        }
    } 

    private async Task SearchMultipleAddressesExample()
    {
        Console.WriteLine($"-----Finding {searchInputs.Count} addresses...-----");

        var osmService = new OsmServices();
        var osmRouteManager = new OsmRouteManager();
        var mapUI = new MapUI(Map);
        var allResults = new List<AddressData>();

        osmRouteManager.Clear();

        foreach (var query in searchInputs)
        {
            var results = await osmService.GetCoordinatesFromAddress(query);

            if (results != null && results.Count > 0)
            {
                allResults.AddRange(results);
            }
        }

        foreach (var result in allResults)
        {
            Console.ForegroundColor = ConsoleColor.Green;
            Console.WriteLine($"Found: {result.name} | OSM ID: {result.osmId}");
            Console.WriteLine($"Coordinates: ({result.latitude}, {result.longitude})");
            Console.ResetColor();

            mapUI.AddPinToMap(result.longitude, result.latitude, result.name);

            osmService.ResolveAndCollect(nodeData, result, osmRouteManager);
        }

        Console.WriteLine($"Total stored waypoints for routing: {osmRouteManager.Waypoints.Count}");

        if (allResults.Count > 0)
        {
            CenterMapToLocation(allResults[0].longitude, allResults[0].latitude);
        }
        else
        {
            Console.WriteLine("Cannot find any of the requested addresses.");
        }
    }

    private void CenterMapToLocation(double longitude, double latitude)
    {
        var lonLatMercator = SphericalMercator.FromLonLat(longitude, latitude);
        var centerPoint = new Mapsui.MPoint(lonLatMercator.x, lonLatMercator.y);
        var zoomResolution = Map.Map.Navigator.Resolutions[16];

        Map.Map.Navigator.CenterOnAndZoomTo(centerPoint, zoomResolution);
    }

}