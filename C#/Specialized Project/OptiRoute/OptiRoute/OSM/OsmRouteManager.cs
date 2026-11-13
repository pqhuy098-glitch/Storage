using System.Collections.Generic;
using System.Linq;
using OptiRoute.Models;

public class OsmRouteManager
{
    public List<ResolvedWaypoint> Waypoints { get; } = new List<ResolvedWaypoint>();

    public void AddWaypoint(ResolvedWaypoint waypoint)
    {
        if (waypoint == null) return;

        bool exists = Waypoints.Any(w => w.ResolvedOsmId == waypoint.ResolvedOsmId && w.ResolvedOsmType == waypoint.ResolvedOsmType);
        if (!exists)
        {
            Waypoints.Add(waypoint);
            System.Console.WriteLine($"[RouteManager] Stored waypoint: {waypoint.ResolvedName} | Match Type: {(waypoint.IsExactIdMatch ? "Exact ID" : "Fallback Nearest")}");
        }
    }

    public void Clear()
    {
        Waypoints.Clear();
  
    }
}