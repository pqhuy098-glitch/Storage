using System.Linq;
using Mapsui;
using Mapsui.Layers;
using Mapsui.Styles;
using Mapsui.UI.Avalonia;

public class MapUI
{
    private readonly MapControl _mapControl;

    public MapUI(MapControl mapControl)
    {
        _mapControl = mapControl;
    }

    public void AddPinToMap(double longitude, double latitude, string label)
    {
        var lonLatMercator = Mapsui.Projections.SphericalMercator.FromLonLat(longitude, latitude);

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

        var existingLayer = _mapControl.Map.Layers.FirstOrDefault(l => l.Name == "PinsLayer");
        if (existingLayer != null)
        {
            _mapControl.Map.Layers.Remove(existingLayer);
        }

        _mapControl.Map.Layers.Add(pinLayer);
        _mapControl.Map.Refresh();
    }
}