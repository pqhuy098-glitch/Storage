using System.Globalization;
using System.Net;
using System.Net.Sockets;
using System.Text;

public class KDC
{
    private const int PORT = 8888;

    private static readonly byte[] ClientMasterKey = Encoding.UTF8.GetBytes("AlicePassword123");
    private static readonly byte[] KdcSecretKey = Encoding.UTF8.GetBytes("KdcMasterKey123!");
    private static readonly byte[] ServiceKey = Encoding.UTF8.GetBytes("FileServerKey123");

    public static void Main(string[] args)
    {
        TcpListener listener = new TcpListener(IPAddress.Any, PORT);
        listener.Start();
        Console.WriteLine($"[KDC] is running and listening at {PORT}");

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
        string reqType = parts[0];

        if (reqType == "AS_REQ")
        {
            HandleAsReq(parts[1], writer);
        }
        else if (reqType == "TGS_REQ")
        {
            HandleTgsReq(parts[1], parts[2], parts[3], writer);
        }

        client.Close();
    }

    private static void HandleAsReq(string clientId, StreamWriter writer)
    {
        Console.WriteLine($"[KDC - AS] Received AS_REQ from Client: {clientId}");

        string sessionKeyC_TGS = Guid.NewGuid().ToString("N");
        
        string expiryTime = DateTime.UtcNow.AddHours(1).ToString("o");

        string tgtPayload = $"{clientId}|{expiryTime}|{sessionKeyC_TGS}";
        string encryptedTgt = EncryptText(tgtPayload, KdcSecretKey);
        string encryptedSessionKey = EncryptText(sessionKeyC_TGS, ClientMasterKey);

        writer.WriteLine($"{encryptedSessionKey}|{encryptedTgt}");
    }

    private static void HandleTgsReq(string encryptedTgt, string encryptedAuthenticator, string service, StreamWriter writer)
    {
        Console.WriteLine($"[KDC - TGS] Received TGS_REQ for service: {service}");

        string decryptedTgt;
        try
        {
            decryptedTgt = DecryptText(encryptedTgt, KdcSecretKey);
        }
        catch
        {
            writer.WriteLine("ERROR|Invalid_TGT");
            return;
        }

        string[] tgtParts = decryptedTgt.Split('|');
        string clientId = tgtParts[0];
        DateTime expiryTime = DateTime.Parse(tgtParts[1], null, DateTimeStyles.RoundtripKind);
        string sessionKeyC_TGS = tgtParts[2];

        if (DateTime.UtcNow > expiryTime)
        {
            Console.WriteLine("[KDC - TGS] TGT has expired.");
            writer.WriteLine("ERROR|TGT_Expired");
            return;
        }

        byte[] sessionKeyBytes = Encoding.UTF8.GetBytes(sessionKeyC_TGS);

        string decryptedAuth;
        try
        {
            decryptedAuth = DecryptText(encryptedAuthenticator, sessionKeyBytes);
        }
        catch
        {
            writer.WriteLine("ERROR|Invalid_Authenticator_Encryption");
            return;
        }

        string[] authParts = decryptedAuth.Split('|');
        if (authParts[0] != clientId)
        {
            writer.WriteLine("ERROR|Invalid_Client_ID");
            return;
        }

        DateTime authTime = DateTime.Parse(authParts[1], null, DateTimeStyles.RoundtripKind);
        if (Math.Abs((DateTime.UtcNow - authTime).TotalMinutes) > 5)
        {
            Console.WriteLine("[KDC - TGS] Authenticator timestamp is outside acceptable window.");
            writer.WriteLine("ERROR|Authenticator_Expired_Or_Replayed");
            return;
        }

        if (service != "FileServer_Service")
        {
            Console.WriteLine($"[KDC - TGS] Requested service '{service}' does not exist.");
            writer.WriteLine("ERROR|Service_Not_Found");
            return;
        }

        string sessionKeyC_Service = Guid.NewGuid().ToString("N");
        string serviceExpiryTime = DateTime.UtcNow.AddHours(1).ToString("o");

        string ticketPayload = $"{clientId}|{service}|{serviceExpiryTime}|{sessionKeyC_Service}";
        string encryptedServiceTicket = EncryptText(ticketPayload, ServiceKey);
        string encryptedSessionKeyService = EncryptText(sessionKeyC_Service, sessionKeyBytes);

        writer.WriteLine($"{encryptedSessionKeyService}|{encryptedServiceTicket}");
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