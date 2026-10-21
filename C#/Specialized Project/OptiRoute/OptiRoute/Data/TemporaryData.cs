public class TemporaryData
{
    public long osmId;
    public string osmType;
    public string name = string.Empty;
    public string address = string.Empty;
    public double longitude;
    public double latitude;

    public double south;
    public double north;
    public double east;
    public double west;

    public TemporaryData (){
        this.osmId = osmId;
        this.osmType = osmType;
        this.name = name;
        this.address = address;
        this.longitude = longitude;
        this.latitude = latitude;
        this.south = south;
        this.north = north;
        this.east = east;
        this.west = west;
    }
}