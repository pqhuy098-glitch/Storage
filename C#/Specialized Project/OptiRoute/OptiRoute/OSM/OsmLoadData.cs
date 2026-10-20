using System;
using System.IO;
using System.Collections.Generic;
using System.Net.Http;
using System.Text.Json;
using System.Threading.Tasks;
using OsmSharp;
using OsmSharp.Streams;

public class OsmLoadData
{
    public static readonly HttpClient httpClient = new HttpClient();

    public OsmLoadData()
    {
        if (!httpClient.DefaultRequestHeaders.Contains("User-Agent"))
        {
            httpClient.DefaultRequestHeaders.Add("User-Agent", "OptiRouteApp/1.0");
        }
    }

    public async Task<PlaceData[]> LoadData(string filepath)
    {
        return await Task.Run(async () =>
        {
            if (!File.Exists(filepath))
            {
                Console.WriteLine("File does not exist");
                return Array.Empty<PlaceData>();
            }

            var list = new List<PlaceData>();
            var fileStream = File.OpenRead(filepath);
            var source = new PBFOsmStreamSource(fileStream);

            int count = 0;

            foreach (var element in source)
            {
                if (element is not Node node || node.Tags == null) continue;
                if (!node.Latitude.HasValue || !node.Longitude.HasValue) continue;
                    
                string name = element.Tags.GetValue("name") ?? "";

                if (!string.IsNullOrEmpty(name))
                {
                    double lat = node.Latitude.Value;
                    double lon = node.Longitude.Value;

                    string fullAddress = await GetAddressFromNominatimAsync(lat, lon);
                    list.Add(new PlaceData
                    {
                        name = name,
                        address = fullAddress,
                        longitude = lon,
                        latitude = lat
                    });

                    count++;
                    
                    if (count >= 5) break; 
                }
            }

            return list.ToArray();
        });
    }

    public async Task<string> GetAddressFromNominatimAsync(double lat, double lon)
    {
        string url = $"https://nominatim.openstreetmap.org/reverse?format=json&lat={lat}&lon={lon}&zoom=18&addressdetails=1";
        await Task.Delay(1000);

        var response = await httpClient.GetAsync(url);

        if (!response.IsSuccessStatusCode) return "Cannot connect to call the address";

        string jsonResponse = await response.Content.ReadAsStringAsync();
        using var doc = JsonDocument.Parse(jsonResponse);
        var root = doc.RootElement;

        if (root.TryGetProperty("display_name", out var displayNameProp))
        {
            return displayNameProp.GetString();
        }
        
        return $"Coordinate ({lat:F4}, {lon:F4})";
    }
}