public class AddressData
{
    public long osmId;
    public string osmType = string.Empty;
    public string name = string.Empty;
    public string address = string.Empty;
    public double longitude;
    public double latitude;

    public AddressData(){}

    public AddressData (long osmId, string osmType, string name, string address, double longitude, double latitude)
    {
        this.osmId = osmId;
        this.osmType = osmType;
        this.name = name;
        this.address = address;
        this.longitude = longitude;
        this.latitude = latitude;
    }
}