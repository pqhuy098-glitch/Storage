public class PlaceData
{
    public long osmId;
    public string osmType;
    public string name = string.Empty;
    public string address = string.Empty;
    public double longitude;
    public double latitude;

    public PlaceData (){
        this.osmId = osmId;
        this.osmType = osmType;
        this.name = name;
        this.address = address;
        this.longitude = longitude;
        this.latitude = latitude;
    }
}