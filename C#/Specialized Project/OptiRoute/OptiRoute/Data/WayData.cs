using System.Collections.Generic;

public class WayData
{
    public long osmId;
    public string osmType = "way";
    public string name = string.Empty;
    public double longitude;
    public double latitude;

    public List<long> Nodes { get; set; } = new List<long>();

    public WayData(){}

    public WayData (long osmId, string name, double longitude, double latitude, List<long> Nodes)
    {
        this.osmId = osmId;
        this.name = name;
        this.longitude = longitude;
        this.latitude = latitude;
        this.Nodes = Nodes;
    }
}