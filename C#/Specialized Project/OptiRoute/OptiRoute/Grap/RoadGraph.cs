using System;
using System.Collections.Generic;
public class RoadGraph
{
    public Dictionary<long, List<Edge>> AdjacencyList = new();
    public Dictionary<long, NodeData> NodeLookup = new();

    public void BuildGraph(NodeData[] nodes, WayData[] ways)
    {
        AdjacencyList.Clear();
        NodeLookup.Clear();

        foreach (var node in nodes)
        {
            if (!NodeLookup.ContainsKey(node.osmId))
            {
                NodeLookup[node.osmId] = node;
                AdjacencyList[node.osmId] = new List<Edge>();
            }
        }

        foreach (var way in ways)
        {
            if (way.Nodes == null || way.Nodes.Count < 2) continue;

            for (int i = 0; i < way.Nodes.Count - 1; i++)
            {
                long uId = way.Nodes[i];
                long vId = way.Nodes[i + 1];

                if (NodeLookup.TryGetValue(uId, out var nodeU) 
                    && NodeLookup.TryGetValue(vId, out var nodeV))
                {
                    double distance = GeoUtils.HaversineMeters(
                        nodeU.latitude, nodeU.longitude, 
                        nodeV.latitude, nodeV.longitude
                    );

                    // Add edge from U -> V
                    AdjacencyList[uId].Add(new Edge
                    {
                        TargetNodeId = vId,
                        Weight = distance,
                        WayId = way.osmId,
                        WayName = way.name
                    });

                    // Add edge from V -> U
                    AdjacencyList[vId].Add(new Edge
                    {
                        TargetNodeId = uId,
                        Weight = distance,
                        WayId = way.osmId,
                        WayName = way.name
                    });
                }
            }
        }

        Console.WriteLine($"[RoadGraph] Graph built successfully! Total nodes in graph: {AdjacencyList.Count}");
    }
}