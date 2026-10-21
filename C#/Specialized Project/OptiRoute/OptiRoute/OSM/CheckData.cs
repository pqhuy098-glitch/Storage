using System;
using System.Collections.Generic;
using System.Linq;

public class CheckData
{
    public void checkTheOfflineData(IEnumerable<PlaceData> placeList, TemporaryData? target)
    {
        if (placeList == null || target == null) 
        { 
            Console.WriteLine("Data is null"); 
            return; 
        }

        var byId = placeList.FirstOrDefault(p => p.osmId == target.osmId && p.osmType == target.osmType);
        if (byId != null) 
        { 
            Console.WriteLine($"[ID] {byId.name}"); 
            Console.WriteLine("This id is on osm file");
            Console.WriteLine($"Coordinate of this id is: {byId.longitude} {byId.latitude}");
            return; 
        }

        // var inBox = placeList.Where(p =>
        //     p.latitude >= target.south && p.latitude <= target.north &&
        //     p.longitude >= target.west && p.longitude <= target.east).ToList();

        // var key = Normalize("Tân Sơn Nhất");
        // var byName = inBox.FirstOrDefault(p => Normalize(p.name).Contains(key));
        // if (byName != null) { Console.WriteLine($"[BBOX+NAME] {byName.name}"); return; }

        var nearest = placeList
            .Select(p => (p, d: HaversineMeters(p.latitude, p.longitude, target.latitude, target.longitude)))
            .OrderBy(x => x.d).First();
        if (nearest.d < 3000)
        {
            Console.WriteLine($"NEAREST {nearest.p.name} far away {nearest.d:F0} m");
            Console.WriteLine($"Coordinate: {nearest.p.longitude} {nearest.p.latitude}");
        }
        else
        {
            Console.WriteLine($"Coordinate: {nearest.p.longitude} {nearest.p.latitude}");
            Console.WriteLine($"Coordinate: {nearest.p.longitude} {nearest.p.latitude}");
        }
    }

    static double HaversineMeters(double lat1, double lon1, double lat2, double lon2)
    {
        const double R = 6371000;
        double dLat = (lat2 - lat1) * Math.PI / 180, dLon = (lon2 - lon1) * Math.PI / 180;
        double a = Math.Sin(dLat / 2) * Math.Sin(dLat / 2) +
                   Math.Cos(lat1 * Math.PI / 180) * Math.Cos(lat2 * Math.PI / 180) *
                   Math.Sin(dLon / 2) * Math.Sin(dLon / 2);
        return R * 2 * Math.Atan2(Math.Sqrt(a), Math.Sqrt(1 - a));
    }

    // static string Normalize(string s)
    // {
    //     var f = s.Normalize(System.Text.NormalizationForm.FormD);
    //     var sb = new System.Text.StringBuilder();
    //     foreach (var c in f)
    //         if (System.Globalization.CharUnicodeInfo.GetUnicodeCategory(c) != System.Globalization.UnicodeCategory.NonSpacingMark)
    //             sb.Append(c);
    //     return sb.ToString().Replace('đ', 'd').Replace('Đ', 'D').ToLowerInvariant().Trim();
    // }
}