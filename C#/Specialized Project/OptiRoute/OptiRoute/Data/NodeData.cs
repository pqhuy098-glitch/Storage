public class NodeData
{
    public long osmId;
    public string osmType = "node";
    public string name = string.Empty;
    public double longitude;
    public double latitude;

    public NodeData() {}

    public NodeData (long osmId, string name, double longitude, double latitude)
    {
        this.osmId = osmId;
        this.name = name;
        this.longitude = longitude;
        this.latitude = latitude;
    }
}