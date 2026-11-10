using System;
using System.IO;
using System.Collections.Generic;
using System.Net.Http;
using System.Text.Json;
using System.Threading.Tasks;
using OsmSharp;
using OsmSharp.Streams;
using System.Linq;
using OptiRoute.Models;

public class OsmServices
{
    public static readonly HttpClient httpClient = new HttpClient();

    public OsmServices()
    {
        if (!httpClient.DefaultRequestHeaders.Contains("User-Agent"))
        {
            httpClient.DefaultRequestHeaders.Add("User-Agent", "OptiRouteApp/1.0");
        }
    }
    
    public async Task<OsmLoadResult> LoadDataFromFile(string filepath)
    {
        return await Task.Run(() =>
        {
            if (!File.Exists(filepath))
            {
                Console.WriteLine("File does not exist");
                return new OsmLoadResult();
            }

            var nodeList = new List<NodeData>();
            var wayList = new List<WayData>();
            var namedWays = new List<(Way Way, List<long> ValidNodes)>();
            var neededNodeIds = new HashSet<long>();

            using (var fs = File.OpenRead(filepath))
            {
                foreach (var el in new PBFOsmStreamSource(fs))
                {
                    if (el is Way way 
                        && way.Id.HasValue 
                        && way.Nodes != null
                        && way.Tags != null 
                        && way.Tags.ContainsKey("highway"))
                    {
                        var validWayNodes = new List<long>(way.Nodes);

                        namedWays.Add((way, validWayNodes));

                        foreach (var n in validWayNodes) 
                        {
                            neededNodeIds.Add(n);
                        }
                    }
                }
            }

            var coords = new Dictionary<long, (double lat, double lon)>();

            using (var fs = File.OpenRead(filepath))
            {
                foreach (var el in new PBFOsmStreamSource(fs))
                {
                    if (el is not Node node 
                        || !node.Id.HasValue
                        || !node.Latitude.HasValue 
                        || !node.Longitude.HasValue) 
                        continue;

                    long id = node.Id.Value;
                    double lat = node.Latitude.Value;
                    double lon = node.Longitude.Value;

                    if (neededNodeIds.Contains(id))
                        coords[id] = (lat, lon);

                    if (node.Tags != null && node.Tags.ContainsKey("name"))
                    {
                        nodeList.Add(new NodeData
                        {
                            osmId = id,
                            name = node.Tags.GetValue("name") ?? "",
                            latitude = lat,
                            longitude = lon
                        });
                    }
                        
                }
            }

            foreach (var item in namedWays)
            {
                double sumLat = 0;
                double sumLon = 0; 
                int count = 0;
                var wayNodeIds = new List<long>();
                
                foreach (var nid in item.ValidNodes)
                {
                    if (coords.TryGetValue(nid, out var c)) 
                    { 
                        sumLat += c.lat; 
                        sumLon += c.lon; 
                        count++; 
                        wayNodeIds.Add(nid);
                    }
                }

                if (count == 0) continue;

                wayList.Add(new WayData
                {
                    osmId = item.Way.Id!.Value,
                    osmType = "way",
                    name = item.Way.Tags.GetValue("name") ?? "",
                    latitude = sumLat / count,
                    longitude = sumLon / count,
                    Nodes = wayNodeIds
                });
            }

            return new OsmLoadResult
            {
                Node = nodeList.ToArray(),
                Way = wayList.ToArray()
            };
        });
    }

    public async Task<List<AddressData>> GetCoordinatesFromAddress(string address)
    {
        if (string.IsNullOrWhiteSpace(address)) 
            return null;

        string searchQuery = $"{address}, Ho Chi Minh City";
        string url = $"https://nominatim.openstreetmap.org/search?format=jsonv2&q={Uri.EscapeDataString(searchQuery)}&addressdetails=1&limit=1";

        await Task.Delay(1000);

        var response = await httpClient.GetAsync(url);

        if (!response.IsSuccessStatusCode) 
            return null;

        string jsonResponse = await response.Content.ReadAsStringAsync();
        using var doc = JsonDocument.Parse(jsonResponse);
        var root = doc.RootElement;
        var addressList = new List<AddressData>();

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

                long osmId = firstResult.TryGetProperty("osm_id", out var idProp) ? idProp.GetInt64() : 0;

                string osmType = firstResult.TryGetProperty("osm_type", out var typeProp)
                    ? typeProp.GetString() ?? "" 
                    : "";

                addressList.Add(new AddressData
                {
                    osmId = osmId,
                    name = name,
                    address = displayName,
                    longitude = lon,
                    latitude = lat,
                    osmType = osmType
                });
            }
        }
        return addressList;
    }

    public void ResolveAndCollect(IEnumerable<NodeData> nodeList, AddressData? target, OsmRouteManager routeManager)
    {
        if (nodeList == null || target == null) 
        { 
            Console.WriteLine("Data or target is null"); 
            return; 
        }

        var byId = nodeList.FirstOrDefault(p => p.osmId == target.osmId && p.osmType == target.osmType);
        if (byId != null) 
        { 
            Console.WriteLine($"[ID Match] Found offline match by OSM ID: {byId.name}"); 
                
            var waypoint = new ResolvedWaypoint
            {
                QueryName = target.name,
                TargetLongitude = target.longitude,
                TargetLatitude = target.latitude,
                ResolvedOsmId = byId.osmId,
                ResolvedOsmType = byId.osmType,
                ResolvedName = byId.name,
                ResolvedLongitude = byId.longitude,
                ResolvedLatitude = byId.latitude,
                DistanceErrorMeters = 0,
                IsExactIdMatch = true
            };

            routeManager?.AddWaypoint(waypoint);
            return;
        }

        var nearest = nodeList
            .Select(p => (p, d: GeoUtils.HaversineMeters(p.latitude, p.longitude, target.latitude, target.longitude)))
            .OrderBy(x => x.d).FirstOrDefault();
                
        if (nearest.p != null)
        {
            var matchedNode = nearest.p;
            Console.WriteLine($"[Fallback Nearest] No exact ID match. Nearest: '{matchedNode.name}' (ID: {matchedNode.osmId}), Distance: {nearest.d:F0}m");

            var waypoint = new ResolvedWaypoint
            {
                QueryName = target.name,
                TargetLongitude = target.longitude,
                TargetLatitude = target.latitude,
                ResolvedOsmId = matchedNode.osmId,
                ResolvedOsmType = matchedNode.osmType,
                ResolvedName = matchedNode.name,
                ResolvedLongitude = matchedNode.longitude,
                ResolvedLatitude = matchedNode.latitude,
                DistanceErrorMeters = nearest.d,
                IsExactIdMatch = false
            };

            routeManager?.AddWaypoint(waypoint);
        }
        else
        {
            Console.WriteLine("No nearest location found.");
        }
    }
}
