using System;
using System.IO;
using System.Collections.Generic;
using System.Net.Http;
using System.Text.Json;
using System.Threading.Tasks;
using OsmSharp;
using OsmSharp.Streams;

public class OsmGetCoordinate
{
    public static readonly HttpClient httpClient = new HttpClient();

    public OsmGetCoordinate()
    {
        if (!httpClient.DefaultRequestHeaders.Contains("User-Agent"))
        {
            httpClient.DefaultRequestHeaders.Add("User-Agent", "OptiRouteApp/1.0");
        }
    }

    public async Task<PlaceData?> GetCoordinatesFromAddressAsync(string address)
    {
        if (string.IsNullOrWhiteSpace(address)) return null;

        string searchQuery = $"{address}, Ho Chi Minh City";
        string url = $"https://nominatim.openstreetmap.org/search?format=json&q={Uri.EscapeDataString(searchQuery)}&addressdetails=1&limit=1";


        await Task.Delay(1000);

        var response = await httpClient.GetAsync(url);
        if (!response.IsSuccessStatusCode) return null;

        string jsonResponse = await response.Content.ReadAsStringAsync();
        using var doc = JsonDocument.Parse(jsonResponse);
        var root = doc.RootElement;

        if (root.ValueKind == JsonValueKind.Array && root.GetArrayLength() > 0)
        {
            var firstResult = root[0];

            string latStr = firstResult.GetProperty("lat").GetString() ?? "0";
            string lonStr = firstResult.GetProperty("lon").GetString() ?? "0";

            if (double.TryParse(latStr, System.Globalization.CultureInfo.InvariantCulture, out double lat) &&
                double.TryParse(lonStr, System.Globalization.CultureInfo.InvariantCulture, out double lon))
            {
                string displayName = firstResult.TryGetProperty("display_name", out var d) 
                    ? d.GetString() ?? address 
                    : address;

                string name = firstResult.TryGetProperty("name", out var n) && !string.IsNullOrEmpty(n.GetString())
                    ? n.GetString()!
                    : address;

                return new PlaceData
                {
                    name = name,
                    address = displayName,
                    latitude = lat,
                    longitude = lon
                };
            }
        }
        return null;
    }
}