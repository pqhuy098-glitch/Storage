namespace OptiRoute.Models
{
    public class ResolvedWaypoint
    {
        public string QueryName { get; set; }
    
        public double TargetLongitude { get; set; }
        public double TargetLatitude { get; set; }
        
        public long ResolvedOsmId { get; set; }
        public string ResolvedOsmType { get; set; }
        public string ResolvedName { get; set; }
        public double ResolvedLongitude { get; set; }
        public double ResolvedLatitude { get; set; }
        
        public double DistanceErrorMeters { get; set; }
        public bool IsExactIdMatch { get; set; }
    }
}