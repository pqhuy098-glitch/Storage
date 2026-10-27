using System.Globalization;
using System.Net;
using System.Net.Sockets;
using System.Text;

public class Server
{
    private const int PORT = 8899;
    private const string ServerServiceName = "FileServer_Service";
    private static readonly byte[] ServiceKey = Encoding.UTF8.GetBytes("FileServerKey123");

    public static void Main(string[] args)
    {
        TcpListener listener = new TcpListener(IPAddress.Any, PORT);
        listener.Start();
        Console.WriteLine($"[Server] is running and listening at {PORT}");

        while (true)
        {
            TcpClient client = listener.AcceptTcpClient();
            Task.Run(() => HandleClient(client));
        }
    }

    public static void HandleClient(TcpClient client)
    {
        using var stream = client.GetStream();
        using var reader = new StreamReader(stream);
        using var writer = new StreamWriter(stream)
        {
            AutoFlush = true
        };

        string? request = reader.ReadLine();
        if (string.IsNullOrEmpty(request))
        {
            return;
        }

        string[] parts = request.Split('|');
        if (parts[0] == "AP_REQ" && parts.Length >= 3)
        {
            HandleApReq(parts[1], parts[2], writer);
        }

        client.Close();
    }

    private static void HandleApReq(string encryptedServiceTicket, string encryptedAuthenticator, StreamWriter writer)
    {
        string decryptedTicket;
        try
        {
            decryptedTicket = DecryptText(encryptedServiceTicket, ServiceKey);
        }
        catch
        {
            writer.WriteLine("ACCESS_DENIED|Invalid_Service_Ticket");
            return;
        }

        string[] ticketParts = decryptedTicket.Split('|');
        string clientId = ticketParts[0];
        string serviceName = ticketParts[1];
        DateTime expiryTime = DateTime.Parse(ticketParts[2], null, DateTimeStyles.RoundtripKind);
        string sessionKeyC_Service = ticketParts[3];

        if (DateTime.UtcNow > expiryTime)
        {
            Console.WriteLine("[Server] Service Ticket has expired.");
            writer.WriteLine("ACCESS_DENIED|Ticket_Expired");
            return;
        }

        if (serviceName != ServerServiceName)
        {
            Console.WriteLine($"[Server] Service name mismatch. Expected: {ServerServiceName}, Got: {serviceName}");
            writer.WriteLine("ACCESS_DENIED|Invalid_Service_Name");
            return;
        }

        byte[] sessionKeyBytes = Encoding.UTF8.GetBytes(sessionKeyC_Service);

        string decryptedAuth;
        try
        {
            decryptedAuth = DecryptText(encryptedAuthenticator, sessionKeyBytes);
        }
        catch
        {
            writer.WriteLine("ACCESS_DENIED|Invalid_Authenticator");
            return;
        }

        string[] authParts = decryptedAuth.Split('|');
        if (authParts[0] != clientId)
        {
            writer.WriteLine("ACCESS_DENIED|Client_ID_Mismatch");
            return;
        }

        DateTime authTime = DateTime.Parse(authParts[1], null, DateTimeStyles.RoundtripKind);
        if (Math.Abs((DateTime.UtcNow - authTime).TotalMinutes) > 5)
        {
            Console.WriteLine("[Server] Authenticator timestamp is outside acceptable window.");
            writer.WriteLine("ACCESS_DENIED|Authenticator_Expired_Or_Replayed");
            return;
        }

        Console.WriteLine($"[Server] Authentication successful for user: {clientId}");

        string apRepPlain = $"OK|{DateTime.UtcNow.ToString("o")}";
        string encryptedApRep = EncryptText(apRepPlain, sessionKeyBytes);

        writer.WriteLine($"ACCESS_GRANTED|{encryptedApRep}");
    }

    private static string EncryptText(string plainText, byte[] key)
    {
        byte[] bytes = Encoding.UTF8.GetBytes(plainText);
        byte[] encrypted = AES.EncryptFile(bytes, key);
        return Convert.ToBase64String(encrypted);
    }

    private static string DecryptText(string cipherBase64, byte[] key)
    {
        byte[] bytes = Convert.FromBase64String(cipherBase64);
        byte[] decrypted = AES.DecryptFile(bytes, key);
        return Encoding.UTF8.GetString(decrypted);
    }
}