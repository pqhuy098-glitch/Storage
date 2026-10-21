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


    public async Task<PlaceData[]> LoadDataFromFile(string filepath)
    {
        return await Task.Run(() =>
        {
            if (!File.Exists(filepath))
            {
                Console.WriteLine("File does not exist");
                return Array.Empty<PlaceData>();
            }

            //Find the way that has name

            var list = new List<PlaceData>();
            var namedWays = new List<Way>();
            var neededNodeIds = new HashSet<long>();

            using (var fs = File.OpenRead(filepath))
            {
                foreach (var el in new PBFOsmStreamSource(fs))
                {
                    if (el is Way way && way.Id.HasValue && way.Nodes != null
                        && way.Tags != null && way.Tags.ContainsKey("name"))
                    {
                        namedWays.Add(way);
                        foreach (var n in way.Nodes) neededNodeIds.Add(n);
                    }
                }
            }

            // Find the node that has name and the coordinate of nodes that the way need
            var coords = new Dictionary<long, (double lat, double lon)>();
            using (var fs = File.OpenRead(filepath))
            {
                foreach (var el in new PBFOsmStreamSource(fs))
                {
                    if (el is not Node node || !node.Id.HasValue
                        || !node.Latitude.HasValue || !node.Longitude.HasValue) continue;

                    long id = node.Id.Value;
                    if (neededNodeIds.Contains(id))
                        coords[id] = (node.Latitude.Value, node.Longitude.Value);

                    if (node.Tags != null && node.Tags.ContainsKey("name"))
                        list.Add(BuildPlace(node.Tags, id, "node",
                            node.Latitude.Value, node.Longitude.Value));
                }
            }

            // Compute the center for each ways
            foreach (var way in namedWays)
            {
                double sumLat = 0, sumLon = 0; int count = 0;
                foreach (var nid in way.Nodes)
                    if (coords.TryGetValue(nid, out var c)) { sumLat += c.lat; sumLon += c.lon; count++; }
                if (count == 0) continue;

                list.Add(BuildPlace(way.Tags, way.Id!.Value, "way", sumLat / count, sumLon / count));
            }

            return list.ToArray();
        });
    }

    private static PlaceData BuildPlace(OsmSharp.Tags.TagsCollectionBase tags,
        long id, string type, double lat, double lon)
    {
        var parts = new List<string>();
        foreach (var key in new[] { "addr:housenumber", "addr:street", "addr:suburb", "addr:district", "addr:city" })
        {
            var v = tags.GetValue(key);
            if (!string.IsNullOrEmpty(v)) parts.Add(v);
        }

        return new PlaceData
        {
            osmId = id,
            osmType = type,
            name = tags.GetValue("name") ?? "",
            address = string.Join(", ", parts),
            latitude = lat,
            longitude = lon
        };
    }


    // public async Task<string> GetAddressFromNominatimAsync(double lat, double lon)
    // {
    //     string url = $"https://nominatim.openstreetmap.org/reverse?format=json&lat={lat}&lon={lon}&zoom=18&addressdetails=1";
    //     await Task.Delay(1000);

    //     var response = await httpClient.GetAsync(url);

    //     if (!response.IsSuccessStatusCode) return "Cannot connect to call the address";

    //     string jsonResponse = await response.Content.ReadAsStringAsync();
    //     using var doc = JsonDocument.Parse(jsonResponse);
    //     var root = doc.RootElement;

    //     if (root.TryGetProperty("display_name", out var displayNameProp))
    //     {
    //         return displayNameProp.GetString();
    //     }
        
    //     return $"Coordinate ({lat:F4}, {lon:F4})";
    // }
}